# --- HANDLE PARAMS ---
%:
	@:

ARGS = `arg="$(filter-out $@,$(MAKECMDGOALS))" && echo $${arg:-${1}}`

# --- DOCKER REGISTRY CONFIG ---
REGISTRY_HOST = registry.mbaheb.fr
REGISTRY_PORT = 10104
REGISTRY_PROJECT = beatly
REGISTRY = $(REGISTRY_HOST):$(REGISTRY_PORT)/$(REGISTRY_PROJECT)

# --- DEV COMMANDS ---
install: up vendor jwt
.PHONY: install

vendor:
	@docker compose exec php composer install
.PHONY: vendor

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
	@rm -rf ./api/public/uploads/musics/covers/*
	@rm -rf ./api/public/uploads/albums/covers/*
	@rm -rf ./api/public/uploads/albums/wallpapers/*
.PHONY: clear-uploads

# --- PROD REGISTRY COMMANDS ---
build-prod:
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml build $(ARGS)
.PHONY: build-prod

push-prod-%:
	@docker tag $(REGISTRY)-$* $(REGISTRY)-$*:latest
	@docker push $(REGISTRY)-$*:latest
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
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml -f compose.override.yaml up -d
.PHONY: up-prod

down-prod:
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml down
.PHONY: down-prod

pull-prod:
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml pull
.PHONY: pull-prod
