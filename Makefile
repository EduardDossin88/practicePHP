up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

format:
	docker compose run --rm app composer format

analyze:
	docker compose run --rm app composer analyze

import:
	docker compose run --rm app php bin/console.php import
