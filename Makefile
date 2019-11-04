include .default.env
include .env
export

PATH := vendor/bin:node_modules/.bin:$(PATH)
GIT_VERSION := $(shell git --no-pager describe --always --tags)

js_sources := $(wildcard src/*.js)
js_bundles := $(js_sources:src/%.js=assets/js/%.bundle.js)
zip_release := dist/w_cms_$(GIT_VERSION).zip

all: vendor build

build: $(js_bundles)

watch: node_modules
	webpack --env dev --watch

release:
	release-it

dist: ENV := prod
dist: distclean $(zip_release)

dist/w_cms_%.zip: all
	@echo "Building Zip release..."
	mkdir -p $(dir $@)
	git archive --format=zip HEAD -o $@
	zip -d $@ \
		"src*" \
		.default.env \
		.gitignore \
		.release-it.json \
		composer.json \
		composer.lock \
		Makefile \
		"package*" \
		webpack.config.js
	zip -r $@ \
		assets/js \
		vendor \
		-x "*test*" \
		-x "*docs*"

assets/js/%.bundle.js: src/%.js node_modules
	@echo "Building JS Bundles..."
	mkdir -p $(dir $@)
ifeq ($(ENV),prod)
	webpack $< -o $@ --env prod -p
else
	webpack $< -o $@ --env dev
endif

.env:
	cp .default.env .env

vendor: composer.json composer.lock
	@echo "Installing PHP dependencies..."
ifeq ($(ENV),prod)
	composer install --no-dev --prefer-dist
else
	composer install
endif

node_modules: package.json package-lock.json
	@echo "Installing JS dependencies..."
	npm install

clean: buildclean
	@echo "Cleaning PHP..."
	rm -rf vendor
	@echo "Cleaning JS..."
	rm -rf node_modules

distclean: buildclean
	rm -rf dist

buildclean:
	@echo "Cleaning build artifacts..."
	rm -rf $(js_bundles)

.PHONY: all build watch release dist clean distclean buildclean
