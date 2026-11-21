"""Executa a API com servidor WSGI de produção.

Windows: python api-admin/run.py
Linux/macOS: gunicorn --chdir api-admin wsgi:application
"""
import os

from waitress import serve

from app import app

if __name__ == "__main__":
    host = os.getenv("HOST", "127.0.0.1")
    port = int(os.getenv("ADMIN_API_PORT", "5001"))
    threads = max(2, int(os.getenv("API_THREADS", "4")))
    serve(app, host=host, port=port, threads=threads, channel_timeout=30, connection_limit=200)
