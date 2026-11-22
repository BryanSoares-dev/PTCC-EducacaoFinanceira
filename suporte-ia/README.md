# Assistente de educação financeira

Serviço Flask que responde com base em `AFDE_memoria_IA.md`. O front-end não chama esta porta diretamente: `back-end/chatbot.php` faz a ponte same-origin e protege a chave do Gemini no servidor.

## Execução local

```bash
cd suporte-ia
python -m venv .venv
.venv/Scripts/python -m pip install -r requirements.txt  # Windows
.venv/Scripts/python run.py                              # Windows
# Linux/macOS: .venv/bin/pip install -r requirements.txt && .venv/bin/python run.py
```

Configure `GEMINI_API_KEY` no `.env` da raiz. `GEMINI_MODEL` usa `gemini-2.5-flash-lite` por padrão e `PORT` usa `5000`. O serviço deve permanecer em loopback; não exponha a chave ou a porta publicamente.
