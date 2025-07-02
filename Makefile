# --- HANDLE PARAMS ---
%:
	@:

ARGS = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

# --- DOCKER REGISTRY CONFIG ---
REGISTRY_HOST = ghcr.io
REGISTRY_USERNAME = batmaxou
REGISTRY_PROJECT = beatly
REGISTRY = $(REGISTRY_HOST):$(REGISTRY_PORT)/$(REGISTRY_PROJECT)

# --- DEV COMMANDS ---
install: up vendor jwt
.PHONY: install

vendor:
	@docker compose exec php composer install
.PHONY: vendor

node_modules:
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

network:
	@docker network create beatly
.PHONY: network

# --- LINTERS ---
php-lint:
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
	@REGISTRY_REPOSITORY_PREFIX=$(REGISTRY_REPOSITORY_PREFIX) docker compose -f compose.prod.yaml pull $(ARGS)
.PHONY: pull-prod
