# Third party integration with TinyUrl

## Project setup
1. `docker compose up --build -d`
2. `docker exec -it ${CONTAINER_NAME} composer install`
3. `docker exec -it ${CONTAINER_NAME} php artisan test`