"""Ponto de entrada WSGI para Waitress/Gunicorn."""
from app import app

application = app
