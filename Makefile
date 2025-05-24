install:
	@docker compose up -d --build
	@cd api && composer install && cd ..
	@cd front && npm install && cd .. 

up:
	@docker compose up -d

down:
	@docker compose down
