SHELL := /bin/bash
include .env
export

.DEFAULT_GOAL := help

help: ## Affiche cette aide
	@grep -hE '^[a-zA-Z_-]+:.*## .*$$' $(firstword $(MAKEFILE_LIST)) | awk 'BEGIN {FS = ":.*## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'

up: ## Démarre la stack (WordPress + base de données)
	docker compose up -d

down: ## Arrête la stack
	docker compose down

logs: ## Affiche les journaux de WordPress
	docker compose logs -f wordpress

cli: ## Ouvre un shell WP-CLI. Ex: make cli CMD="plugin list"
	docker compose run --rm cli wp $(CMD)

install: up ## Installe WordPress et active le thème
	@echo "Attente de la base de données..."
	@until docker compose run --rm cli wp core is-installed --allow-root 2>/dev/null || [ $$? -eq 1 ]; do sleep 2; done
	docker compose run --rm cli wp core install \
		--url="$(WP_URL)" --title="$(WP_TITLE)" \
		--admin_user="$(WP_ADMIN_USER)" --admin_password="$(WP_ADMIN_PASSWORD)" \
		--admin_email="$(WP_ADMIN_EMAIL)" --skip-email
	docker compose run --rm cli wp theme activate grand-lahou
	docker compose run --rm cli wp rewrite structure '/%postname%/'
	docker compose run --rm cli wp option update timezone_string 'Africa/Abidjan'
	docker compose run --rm cli wp option update date_format 'j F Y'
	docker compose run --rm cli wp option update time_format 'H\hi'
	docker compose run --rm cli wp language core install fr_FR --activate
	@echo "Site disponible sur $(WP_URL)"

seed: ## Injecte le contenu de démonstration
	docker compose run --rm cli wp eval-file /tools/seed.php

deploy: ## Déploie le code en ligne (simulation, confirmation, envoi)
	./tools/deploy.sh

deploy-check: ## Montre ce qui serait déployé, sans rien envoyer
	./tools/deploy.sh --check

reset: ## Supprime tout et repart de zéro (destructif)
	docker compose down -v

.PHONY: help up down logs cli install seed deploy deploy-check reset
