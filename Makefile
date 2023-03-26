VAR_DIR = var

init: create_dir
	docker-compose up -d && docker-compose exec -u app app composer install

test:
	docker-compose exec -u app app php vendor/bin/phpunit --testdox

run:
	docker-compose exec -u app app php bin/app.php

stop:
	docker-compose down -v

create_dir:
	[ -d $(VAR_DIR) ] || mkdir $(VAR_DIR)
