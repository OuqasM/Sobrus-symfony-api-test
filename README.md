# Sobrus-symfony-api-test
After cloning the repository, inside the working directory <strong>Sobrus-symfony-api-test</strong>:

1. Run `docker-compose up -d --build` to start the containers.
2. Run `docker-compose exec php composer install` to install the dependencies.
3. Execute the migration files with `docker-compose exec php php bin/console doctrine:migrations:migrate`.
4. Load the data fixtures with `docker-compose exec php php bin/console doctrine:fixtures:load`.
5. Visit `http://localhost:80/` to check if everything is working okay.

Note: `docker-compose` command depends on your Docker Compose version. For Docker Compose v2, use `docker compose` instead.
