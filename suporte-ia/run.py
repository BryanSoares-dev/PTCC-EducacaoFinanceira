import os
from waitress import serve
from app_md import app

if __name__ == '__main__':
    serve(
        app,
        host=os.environ.get('HOST', '127.0.0.1'),
        port=int(os.environ.get('PORT', '5000')),
        threads=int(os.environ.get('WEB_CONCURRENCY', '4')),
    )
