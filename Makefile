.DEFAULT_GOAL:help

RUN?=docker run --rm --interactive --tty --volume ${PWD}:/app woohoolabs-yin-bundle

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m * make %s\033[0m ## %s\n", $$1, $$2}'

build: ## Build image used to install dependencies and run tests
	docker build --tag=woohoolabs-yin-bundle ${PWD}

install: ## Install dependencies with docker
	$(RUN) composer install

test: ## Run phpspec tests
	$(RUN) vendor/bin/phpspec run