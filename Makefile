# --- HANDLE PARAMS ---
%:
	@:

ARGS = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

# --- DOCKER REGISTRY CONFIG ---
REGISTRY_HOST = ghcr.io
REGISTRY_USERNAME = batmaxou
REGISTRY_PROJECT = beatly
REGISTRY_REPOSITORY_PREFIX = $(REGISTRY_HOST)/${REGISTRY_USERNAME}/$(REGISTRY_PROJECT)

# --- PHP CS FIXER CONFIG ---
PHP_CS_FIXER_CONFIGURATION_FILE = ./.devops/lint/.php-cs-fixer.php
PHP_FIXER_VERSION = 3-php8.4
phpcsfixer = docker run --rm -v `pwd`:/code ghcr.io/php-cs-fixer/php-cs-fixer:${PHP_FIXER_VERSION}


# --- DEV COMMANDS ---
install: up vendor node-modules jwt uploads-dir database init-qdrant
.PHONY: install

init-qdrant:
	@docker compose exec php php bin/console app:init:qdrant
.PHONY: init-qdrant

vendor:
	@docker compose exec php composer install
.PHONY: vendor

node-modules:
	@docker compose exec front npm install
.PHONY: node_modules

up:
	@docker compose up -d
.PHONY: up

down:
	@docker compose down
.PHONY: down

jwt:
	@docker compose exec php php bin/console lexik:jwt:generate-keypair
.PHONY: jwt

uploads-dir:
	@mkdir -p ./api/private/uploads/musics
	@mkdir -p ./api/public/uploads/musics/covers
	@mkdir -p ./api/public/uploads/albums/covers
	@mkdir -p ./api/public/uploads/albums/wallpapers
	@mkdir -p ./api/public/uploads/playlists/covers
	@mkdir -p ./api/public/uploads/playlists/wallpapers
	@mkdir -p ./api/public/uploads/users/avatars
	@mkdir -p ./api/public/uploads/users/wallpapers
	@chmod -R 777 ./api/private/uploads ./api/public/uploads
.PHONY: uploads-dir

clear-uploads:
	@rm -rf ./api/private/uploads/musics/*
	@rm -rf ./api/public/uploads/musics/*
	@rm -rf ./api/public/uploads/albums/*
	@rm -rf ./api/public/uploads/playlists/*
	@rm -rf ./api/public/uploads/users/*
.PHONY: clear-uploads

network:
	@docker network create beatly
.PHONY: network

# --- LINTERS ---
fixcs:
	@$(phpcsfixer) fix --config=$(PHP_CS_FIXER_CONFIGURATION_FILE)
.PHONY: fixcs

phpcs:
	@$(phpcsfixer) fix --config=$(PHP_CS_FIXER_CONFIGURATION_FILE) --dry-run
.PHONY: phpcs

php-lint:
	@${MAKE} phpcs
.PHONY: php-lint

front-lint:
.PHONY: front-lint

# --- PROD REGISTRY COMMANDS ---
build-prod:
	@REGISTRY_REPOSITORY_PREFIX=$(registry_repository_prefix) docker compose -f compose.prod.yaml build $(ARGS)
.PHONY: build-prod

push-prod-%:
	@docker tag $(REGISTRY_REPOSITORY_PREFIX)-$* $(REGISTRY)-$*:latest
	@docker push $(REGISTRY_REPOSITORY_PREFIX)-$*:latest
.PHONY: push-prod-%

push-prod-all:
	@$(MAKE) push-prod-php
	@$(MAKE) push-prod-web
	@$(MAKE) push-prod-front
	@$(MAKE) push-prod-embedder
	@$(MAKE) push-prod-ollama
.PHONY: push-prod-all

# --- PROD DEPLOYMENT COMMANDS ---
up-prod:
	@REGISTRY_REPOSITORY_PREFIX=$(REGISTRY_REPOSITORY_PREFIX) docker compose -f compose.prod.yaml -f compose.override.yaml up -d $(ARGS)
.PHONY: up-prod

down-prod:
	@REGISTRY_REPOSITORY_PREFIX=$(REGISTRY_REPOSITORY_PREFIX) docker compose -f compose.prod.yaml down $(ARGS)
.PHONY: down-prod

pull-prod:
	@git pull -fr
	@REGISTRY_REPOSITORY_PREFIX=$(REGISTRY_REPOSITORY_PREFIX) docker compose -f compose.prod.yaml pull $(ARGS)
.PHONY: pull-prod

deploy:
	@make down-prod $(ARGS)
	@docker image prune -f
	@make pull-prod $(ARGS)
	@make up-prod $(ARGS)
.PHONY: deploy
