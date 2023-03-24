init:
	docker-compose up -d && docker-compose exec -u app app composer install

test:
	vendor/bin/phpunit