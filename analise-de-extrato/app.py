from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import uuid
from google import genai

from extratores.extrator_pdf import buscar_texto as extrair_pdf
from extratores.extrator_csv import buscar_texto as extrair_csv

from dotenv import load_dotenv
load_dotenv()

app = Flask(__name__)

# Problema: CORS(app) sem restrição libera "Access-Control-Allow-Origin: *"
# para todas as rotas, ou seja, QUALQUER site na internet pode chamar
# esta API a partir do navegador de um visitante e gastar a cota da
# chave do Gemini configurada no servidor.
# Solução: restringe as origens permitidas à(s) origem(ns) reais do
# front-end, configuráveis via variável de ambiente ALLOWED_ORIGIN
# (aceita uma lista separada por vírgulas). Em desenvolvimento local
# (XAMPP/Laragon), o padrão cobre localhost/127.0.0.1 nas portas comuns.
origens_permitidas = os.environ.get(
    "ALLOWED_ORIGIN",
    "http://localhost,http://127.0.0.1,http://localhost:80,http://127.0.0.1:80"
).split(",")
CORS(app, resources={r"/api/*": {"origins": origens_permitidas}})

client = genai.Client(api_key=os.environ.get("GOOGLE_API_KEY"))

# Criando uma pasta temporaria
PASTA_TEMP = os.path.join(os.path.dirname(os.path.abspath(__file__)), "temp")
os.makedirs(PASTA_TEMP, exist_ok=True)

# Limite de tamanho do corpo da requisição (10 MB) para evitar que um
# upload gigante trave o servidor (negação de serviço simples).
app.config['MAX_CONTENT_LENGTH'] = 10 * 1024 * 1024

EXTENSOES_PERMITIDAS = {'pdf', 'csv'}


@app.route('/api/analisar', methods=['POST'])
def analisar():
    arquivo = request.files.get('extrato')  # 'extrato' é o mesmo nome usado no FormData do JS

    if not arquivo or not arquivo.filename:
        return jsonify({"erro": "Nenhum arquivo enviado"}), 400

    nome_original = arquivo.filename
    extensao = nome_original.rsplit('.', 1)[-1].lower() if '.' in nome_original else ''

    if extensao not in EXTENSOES_PERMITIDAS:
        return jsonify({"erro": "Formato não suportado. Envie PDF ou CSV."}), 400

    # Problema: usar arquivo.filename (nome enviado pelo cliente) direto
    # no caminho do arquivo permite Path Traversal (ex.: um nome como
    # "../../../etc/passwd" ou similar escaparia da pasta "temp") e
    # também permite que dois usuários simultâneos sobrescrevam o
    # arquivo um do outro se enviarem o mesmo nome.
    # Solução: gera um nome de arquivo aleatório no servidor, mantendo
    # apenas a extensão já validada contra a whitelist acima.
    nome_seguro = f"{uuid.uuid4().hex}.{extensao}"
    caminho_salvo = os.path.join(PASTA_TEMP, nome_seguro)

    arquivo.save(caminho_salvo)

    try:
        # decide qual extrator usar baseado na extensão
        if extensao == 'pdf':
            texto = extrair_pdf(caminho_salvo)
        else:
            texto = extrair_csv(caminho_salvo)
    except Exception:
        # Não expõe detalhes internos (caminhos, stack trace) ao cliente.
        return jsonify({"erro": "Não foi possível ler o arquivo enviado. Verifique se ele não está corrompido."}), 400
    finally:
        # Garante a remoção do arquivo temporário mesmo se a extração falhar.
        if os.path.exists(caminho_salvo):
            os.remove(caminho_salvo)

    prompt = f"Aqui estão os gastos do usuário:\n{texto}\n\nResuma por categoria e dê 3 dicas financeiras."

    try:
        resposta = client.models.generate_content(
            model="gemini-3.1-flash-lite",
            contents=prompt
        )
    except Exception:
        return jsonify({"erro": "Não foi possível gerar a análise agora. Tente novamente em instantes."}), 502

    return jsonify({"resumo": resposta.text})


if __name__ == '__main__':
    # Problema: debug=True em produção expõe o console interativo do
    # Werkzeug, que permite execução remota de código para quem
    # conseguir acionar um erro não tratado — um risco crítico.
    # Solução: debug só fica ativo se DEBUG=1 for explicitamente
    # definido no ambiente (uso local), nunca por padrão.
    debug_ativo = os.environ.get("DEBUG", "0") == "1"
    app.run(port=5000, debug=debug_ativo)
