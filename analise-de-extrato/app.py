from flask import Flask, request, jsonify
from flask_cors import CORS
import os
from google import genai

from extratores.extrator_pdf import buscar_texto as extrair_pdf
from extratores.extrator_csv import buscar_texto as extrair_csv

from dotenv import load_dotenv
load_dotenv()

app = Flask(__name__)
CORS(app)  # permite o navegador (index.php) chamar essa API

client = genai.Client(api_key=os.environ.get("GOOGLE_API_KEY"))

# Criando uma pasta temporaria
PASTA_TEMP = "temp"
os.makedirs(PASTA_TEMP, exist_ok=True)

@app.route('/api/analisar', methods=['POST'])
def analisar():
    arquivo = request.files.get('extrato')  # 'extrato' é o mesmo nome usado no FormData do JS

    if not arquivo:
        return jsonify({"erro": "Nenhum arquivo enviado"}), 400

    nome = arquivo.filename
    extensao = nome.rsplit('.', 1)[-1].lower()

    # Forma o caminho para conseguir abrir o arquivo
    caminho_salvo = os.path.join(PASTA_TEMP, nome)
    arquivo.save(caminho_salvo)

    # decide qual extrator usar baseado na extensão
    if extensao == 'pdf':
        texto = extrair_pdf(caminho_salvo)
    elif extensao == 'csv':
        texto = extrair_csv(caminho_salvo)
    else:
        os.remove(caminho_salvo)
        return jsonify({"erro": "Formato não suportado. Envie PDF ou CSV."}), 400

    os.remove(caminho_salvo)  # apaga o arquivo temporário depois de ler

    prompt = f"Aqui estão os gastos do usuário:\n{texto}\n\nResuma por categoria e dê 3 dicas financeiras."

    resposta = client.models.generate_content(
        model="gemini-3.1-flash-lite",
        contents=prompt
    )

    return jsonify({"resumo": resposta.text})

if __name__ == '__main__':
    app.run(port=5000, debug=True)