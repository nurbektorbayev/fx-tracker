# default = dev
ENV ?= dev

# docker compose .env file
ENV_FILE = docker/.env.$(ENV).compose

# basic command
COMPOSE = docker compose --env-file $(ENV_FILE)

# exec command for app container
EXEC_APP = $(COMPOSE) exec app


# Docker commands

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down --remove-orphans

restart:
	$(MAKE) down ENV=$(ENV)
	$(MAKE) up ENV=$(ENV)


# Composer

composer:
	$(EXEC_APP) composer install

composer-update:
	$(EXEC_APP) composer update


# Doctrine migrations

migrate:
	$(EXEC_APP) php bin/console doctrine:migrations:migrate --no-interaction


# Logs

logs:
	$(COMPOSE) logs -f

logs-app:
	$(COMPOSE) logs -f app

logs-scheduler:
	$(COMPOSE) logs -f scheduler

logs-db:
	$(COMPOSE) logs -f db


# Shell access

sh:
	$(EXEC_APP) sh
