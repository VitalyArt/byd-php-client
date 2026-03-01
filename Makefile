composer-install:
	docker compose run --rm php composer install

run-example:
	docker compose run --rm php php /var/www/html/examples/basic_usage.php
