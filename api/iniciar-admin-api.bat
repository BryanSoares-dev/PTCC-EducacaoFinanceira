@echo off
setlocal
cd /d "%~dp0.."
if not exist "%~dp0venv\Scripts\python.exe" (
  echo Ambiente virtual nao encontrado.
  echo Execute: py -m venv api\venv ^&^& api\venv\Scripts\pip install -r api\requirements.txt
  pause
  exit /b 1
)
"%~dp0venv\Scripts\python.exe" api\admin_api.py
