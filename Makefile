build.container.dev:
	docker run -d --name socialnet-backend-php screxy/socialnet-backend-php:dev
	docker cp socialnet-backend-php:/var/www/vendor/. ./vendor
	docker cp socialnet-backend-php:/var/www/composer.lock ./composer.lock
	docker stop socialnet-backend-php
	docker rm socialnet-backend-php
	docker compose up -d

build-dev: build.container.dev

build-prod:
	docker build -f Dockerfile -t screxy/cringe:dev .

up:
	docker compose up -d

down:
	docker compose down
