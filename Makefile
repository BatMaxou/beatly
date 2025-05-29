REGISTRY_HOST = registry.mbaheb.fr
REGISTRY_PORT = 10104
REGISTRY_PROJECT = beatly
REGISTRY = $(REGISTRY_HOST):$(REGISTRY_PORT)/$(REGISTRY_PROJECT)

### DEV >##
install: rebuild up
	@docker cp $(shell docker compose ps -q php):/srv/vendor ./api
	@docker cp $(shell docker compose ps -q php):/srv/var ./api
	@docker cp $(shell docker compose ps -q front):/app/node_modules ./front
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
##< DEV ###

### REGISTRY >##
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
##< REGISTRY ###

### PROD >##
up-prod:
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml -f compose.override.yaml up -d
.PHONY: up-prod

down-prod:
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml down
.PHONY: stop-prod

pull-prod:
	@REGISTRY=$(REGISTRY) docker compose -f compose.prod.yaml pull
.PHONY: pull-prod
##< PROD ###

