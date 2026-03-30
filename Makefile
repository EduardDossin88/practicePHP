.PHONY: cert up down build console analyze format

ENV_FILE ?= .env

DC = docker compose -p practice -f docker/compose.yml --env-file $(ENV_FILE)

cert:
	mkdir -p certs
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout certs/localhost.key -out certs/localhost.crt -subj "//C=RU\ST=Moscow\L=Moscow\O=MyCompany\CN=localhost"
	@echo "Сертификаты успешно созданы в папке certs/"

build:
	$(DC) build

up:
	$(DC) up -d

down:
	$(DC) down

console:
	$(DC) exec php-fpm php bin/console.php $(cmd)

analyze:
	$(DC) exec -T php-fpm vendor/bin/phpstan analyse -c phpstan.neon
format:
	$(DC) exec -T php-fpm vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php