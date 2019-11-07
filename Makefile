include .default.env
include .env
export

PATH := vendor/bin:node_modules/.bin:$(PATH)
override GIT_VERSION := $(shell git --no-pager describe --always --tags)
override CUR_VERSION := $(strip $(shell cat VERSION 2>/dev/null))
PREV_ENV_FILE := /tmp/wcms_prev_env
PREV_ENV=$(strip $(shell cat $(PREV_ENV_FILE) 2>/dev/null))

js_sources := $(wildcard src/*.js)
js_bundles := $(js_sources:src/%.js=assets/js/%.bundle.js)
js_srcmaps := $(js_sources:src/%.js=assets/js/%.bundle.js.map)
zip_release := dist/w_cms_$(GIT_VERSION).zip

all: vendor build

build: VERSION $(PREV_ENV_FILE) $(js_bundles)

watch: node_modules
	webpack --env dev --watch

release: node_modules
	release-it

sentryrelease:
	$(MAKE) .sentryrelease ENV=prod

.sentryrelease: build
	sentry-cli releases new $(GIT_VERSION)
	sentry-cli releases set-commits $(GIT_VERSION) --auto
	sentry-cli releases files $(GIT_VERSION) upload-sourcemaps assets/js --url-prefix '~/assets/js' --rewrite
	sentry-cli releases finalize $(GIT_VERSION)

dist:
	$(MAKE) .dist ENV=prod

.dist: distclean $(zip_release) $(js_srcmaps)

dist/w_cms_%.zip: all
	@echo "Building Zip release..."
	mkdir -p $(dir $@)
# Include git tracked files (everything except ignored) + needed files
	zip -r $@ \
		$(shell git ls-tree -r HEAD --name-only) \
		VERSION \
		assets/js \
		vendor \
		-x "*test*" \
		-x "*docs*"
# Include non-empty git tracked directories (to keep dir permissions)
	zip $@ $(shell git ls-tree -r HEAD --name-only -d)
# Remove non-useful files
	zip -d $@ \
		$(js_sources) \
		$(js_srcmaps) \
		.default.env \
		.gitignore \
		.release-it.json \
		Makefile \
		"composer*" \
		"package*" \
		webpack.config.js

assets/js/%.bundle.js assets/js/%.bundle.js.map: src/%.js node_modules
	@echo "Building JS Bundles..."
	mkdir -p $(dir $@)
	webpack $< -o $@ $(if $(filter $(ENV),prod),--env prod -p,--env dev)

.env:
	cp .default.env .env

# use a force (fake) target to always rebuild this file but have Make
# consider this updated if it was actually rewritten (a .PHONY target
# is always considered new)
VERSION: .FORCE
ifneq ($(CUR_VERSION),$(GIT_VERSION))
	@echo $(GIT_VERSION) > $@
endif

$(PREV_ENV_FILE): .FORCE
ifneq ($(PREV_ENV),$(ENV))
	@echo Build env has changed !
	$(MAKE) buildclean
	@echo $(ENV) > $@
endif

vendor: composer.json composer.lock
	@echo "Installing PHP dependencies..."
	composer install $(if $(filter $(ENV),prod),--no-dev --prefer-dist,)

node_modules: package.json package-lock.json
	@echo "Installing JS dependencies..."
	npm install --loglevel=error

clean: buildclean
	@echo "Cleaning make artifacts..."
	rm -rf vendor
	rm -rf node_modules
	rm -rf VERSION

distclean:
	@echo "Cleaning dist artifacts..."
	rm -rf dist

buildclean:
	@echo "Cleaning build artifacts..."
	rm -rf $(js_bundles)
	rm -rf $(js_srcmaps)

.FORCE: ;

.PHONY: all build watch release sentryrelease .sentryrelease dist .dist clean distclean buildclean
