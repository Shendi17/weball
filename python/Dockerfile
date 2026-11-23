# Construire depuis la racine du dépôt : docker build -f python/Dockerfile -t weball-web .
FROM python:3.12-slim
WORKDIR /app/python
COPY python/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt
COPY python/ .
COPY public/photos /app/public/photos
COPY public/logos /app/public/logos
ENV WEBALL_DATABASE_URL=postgresql+psycopg://weball@ines-postgres/weball
EXPOSE 8000
CMD ["uvicorn", "weball.main:app", "--host", "0.0.0.0", "--port", "8000", "--proxy-headers"]
