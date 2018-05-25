#!make

#include .env
#export $(shell sed 's/=.*//' .env)

.SILENT:

SHELL := /bin/bash

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

ENV="prod"

## This help dialog
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Build the application by running preparation tasks such as composer install
build:
	composer install --dev

## Prepare the application to be ready to run
deploy: build
	./bin/console doctrine:migrations:migrate -vv

## Run a development web server
run_dev_server:
	./bin/console server:start

## Run application test suites
test:
	./vendor/bin/phpunit -vvv
