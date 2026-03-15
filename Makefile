.PHONY: cert up down build import

ENV_FILE ?= .env

cert:
	mkdir -p certs
	openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout certs/localhost.key -out certs/localhost.crt -subj "//C=RU\ST=Moscow\L=Moscow\O=MyCompany\CN=localhost"
	@echo "Сертификаты успешно созданы в папке certs/"

build:
	docker-compose --env-file $(ENV_FILE) build

up:
	docker-compose --env-file $(ENV_FILE) up -d

down:
	docker-compose --env-file $(ENV_FILE) down

import:
	docker-compose --env-file $(ENV_FILE) run --rm php-cli php import.php