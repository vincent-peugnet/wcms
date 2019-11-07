include .default.env
include .env
export

PATH := vendor/bin:node_modules/.bin:$(PATH)
override GIT_VERSION := $(shell git --no-pager describe --always --tags)
override CUR_VERSION := $(strip $(shell cat VERSION))

js_sources := $(wildcard src/*.js)
js_bundles := $(js_sources:src/%.js=assets/js/%.bundle.js)
js_srcmaps := $(js_sources:src/%.js=assets/js/%.bundle.js.map)
zip_release := dist/w_cms_$(GIT_VERSION).zip

all: vendor build

build: VERSION $(js_bundles)

watch: node_modules
	webpack --env dev --watch

release:
	release-it

dist: distclean $(zip_release) $(js_srcmaps)

dist/%: ENV := prod
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
		VERSION \
		assets/js \
		vendor \
		-x "*test*" \
		-x "*docs*"

assets/js/%.bundle.js assets/js/%.bundle.map: src/%.js node_modules
	@echo "Building JS Bundles..."
	mkdir -p $(dir $@)
	webpack $< -o $@ $(if $(filter $(ENV),prod),--env prod -p,--env dev)

.env:
	cp .default.env .env

# use a force (fake) target to always rebuild this file but have Make
# consider this updated if it was actually rewritten (a .PHONY target
# is always considered new)
VERSION: FORCE
ifneq ($(CUR_VERSION),$(GIT_VERSION))
	@echo $(GIT_VERSION) > VERSION
endif

vendor: composer.json composer.lock
	@echo "Installing PHP dependencies..."
	composer install $(if $(filter $(ENV),prod),--no-dev --prefer-dist,)

node_modules: package.json package-lock.json
	@echo "Installing JS dependencies..."
	npm install --loglevel=error

clean: buildclean
	@echo "Cleaning PHP..."
	rm -rf vendor
	@echo "Cleaning JS..."
	rm -rf node_modules

distclean: buildclean
	rm -rf dist

buildclean:
	@echo "Cleaning build artifacts..."
	rm -rf VERSION
	rm -rf $(js_bundles)
	rm -rf $(js_srcmaps)

FORCE: ;

.PHONY: all build watch release dist clean distclean buildclean
