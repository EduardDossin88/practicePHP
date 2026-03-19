.PHONY: cert up down build import console analyze format

ENV_FILE ?= .env

# Переменная с базовой командой, которая теперь знает, что Докер лежит в папке docker/
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

# Старый скрипт импорта
import:
	$(DC) run --rm php-cli php import.php

# Запуск единой консоли (пример: make console cmd="generate 50")
console:
	$(DC) exec php-fpm php bin/console.php $(cmd)

# Запуск статанализатора (PHPStan 8 уровня)
analyze:
	$(DC) exec -T php-fpm vendor/bin/phpstan analyse -c config/phpstan.neon
# Запуск форматтера (PSR-12)
format:
	$(DC) exec -T php-fpm vendor/bin/php-cs-fixer fix --config=config/.php-cs-fixer.dist.php