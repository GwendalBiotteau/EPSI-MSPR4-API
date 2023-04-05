start:
	docker-compose --env-file ./.env.local up -d

# Made for linux users to stop apache & mysql services before running docker
full-start:
	sudo systemctl stop apache2.service && sudo systemctl stop mysql.service && docker-compose --env-file ./.env.local up -d

stop:
	docker-compose --env-file ./.env.local stop

docker_build:
	docker-compose --env-file ./.env.local build

bash:
	docker-compose --env-file ./.env.local exec php zsh

install:
	composer install --ignore-platform-reqs

controller:
	php bin/console make:controller