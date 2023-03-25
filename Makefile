init:
	docker-compose up -d && docker-compose exec -u app app composer install && mkdir var

test:
	docker-compose exec -u app app php vendor/bin/phpunit --testdox

run:
	docker-compose exec -u app app php bin/app.php

stop:
	docker-compose down -v