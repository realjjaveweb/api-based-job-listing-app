.PHONY: help
help: ## lists all the *documented* commands
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

app_run: ## starts app containers, installs dependencies and runs frontend in dev mode
	docker compose up --build --detach
	docker compose exec php-fpm composer install --no-interaction
	docker compose exec frontend npm install
	docker compose exec --detach frontend npm run dev
	@echo "Frontend: http://localhost:5173"
	@echo "Backend API: http://localhost:8080"

app_stop: ## stops app containers
	docker compose down

app_build: ## re-builds app containers (with cache)
	docker compose build

app_logs: ## shows logs of app containers (in follow mode)
	docker compose logs -f

test: ## runs PHP tests	
	docker compose exec php-fpm composer test

quality: ## runs static analysis and code style checks
	docker compose exec php-fpm composer phpstan
	docker compose exec php-fpm composer phpcs-check

quality_fix: ## currently only runs code style fix
	docker compose exec php-fpm composer phpcs-fix

install: ## installs dependencies for app and frontend in running containers
	docker compose exec php-fpm composer install --no-interaction
	docker compose exec frontend npm install

frontend_build: ## builds frontend for production
	docker compose exec frontend npm run build

frontend_lint: ## runs ESLint on frontend Vue/JS code
	docker compose exec frontend npm run lint

frontend_lint_fix: ## runs ESLint on frontend Vue/JS code and fixes fixable errors
	docker compose exec frontend npm run lint:fix
	docker compose exec frontend npm run format
