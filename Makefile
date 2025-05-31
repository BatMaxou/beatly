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
sync-vendor:
	@docker cp $(shell docker compose ps -q php):/srv/vendor ./api
.PHONY: sync-vendor

sync-var:
	@docker cp $(shell docker compose ps -q php):/srv/var ./api
.PHONY: sync-var

sync-node-modules:
	@docker cp $(shell docker compose ps -q front):/app/node_modules ./front
.PHONY: sync-node-modules

install: rebuild up
	@${MAKE} sync-vendor
	@${MAKE} sync-var
	@${MAKE} sync-node-modules
.PHONY: install

rebuild:
	@docker compose build --no-cache
.PHONY: rebuild

up:
	@docker compose up -d
.PHONY: up

down:
	@docker compose down
.PHONY: down

composer-%:
	@docker compose exec php composer $* $(ARGS)
	@$(MAKE) sync-vendor
.PHONY: composer

npm-%:
	@docker compose exec front npm $* $(ARGS)
	@$(MAKE) sync-node-modules
.PHONY: npm

# --- PROD REGISTRY COMMANDS ---
build-prod:
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml build
.PHONY: build-prod

push-prod-%:
	@docker tag $(REGISTRY)-$* $(REGISTRY)-$*:latest
	@docker push $(REGISTRY)-$*:latest
.PHONY: push-prod-%

push-prod-all:
	@$(MAKE) push-prod-php
	@$(MAKE) push-prod-web
	@$(MAKE) push-prod-front
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
