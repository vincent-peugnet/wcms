include .env
export
PATH := vendor/bin:node_modules/.bin:$(PATH)
GIT_VERSION := $(shell git --no-pager describe --always --tags)

js_sources := $(wildcard src/*.js)
js_bundles := $(js_sources:src/%.js=assets/js/%.bundle.js)
zip_release := $(GIT_VERSION:%=dist/w_cms_%.zip)

all: php_dependencies $(js_bundles)

release:
	release-it

dist: distclean $(zip_release)

dist/w_cms_%.zip: all
	@echo "Building Zip release..."
	mkdir -p $(dir $@)
	git archive --format=zip HEAD -o $@
	zip -d $@ \
		"src*" \
		.gitignore \
		.release-it.json \
		composer.lock \
		Makefile \
		"package*" \
		webpack.config.js
	zip -r $@ \
		assets/js \
		vendor \
		-x "*test*" \
		-x "*docs*"

assets/js/%.bundle.js: src/%.js js_dependencies
	@echo "Building JS Bundles..."
	mkdir -p $(dir $@)
	webpack --env prod

php_dependencies:
	@echo "Installing PHP dependencies..."
	composer install --no-dev --prefer-dist

php_clean:
	@echo "Cleaning PHP..."
	rm -rf vendor

js_dependencies:
	@echo "Installing JS dependencies..."
	npm install

js_clean:
	@echo "Cleaning JS..."
	rm -rf node_modules
	rm -rf $(js_bundles)

clean: php_clean js_clean

distclean:
	rm -rf dist
