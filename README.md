# Sof-Req-Game-Backend
Servicio de back-end para Sof-Req-Game.

## Requisitos
Para construir y ejecutar la aplicación, se requiere:
- Docker

O instalación local de:

- PHP 8.2
- MySQL 8.0
- Apache 2.4

## Levantar la aplicación
1. Clonar el repositorio
2. Crear un archivo .env con las siguientes variables:

```
DB_HOST=
DB_NAME=
DB_USER=
DB_ROOT_PASSWORD=
DB_PASSWORD=

OPENAI_API_KEY=
SOF_REQ_ASSISTANT_ID=

SEED_USER=
SEED_PASSWORD=
```

3. Construir la imagen Docker

```
docker compose build
```

4. Ejecutar la imagen Docker

```
docker compose up -d
```