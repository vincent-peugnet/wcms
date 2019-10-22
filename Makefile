GIT_VERSION := $(shell git --no-pager describe --always --tags)

js_sources := $(wildcard src/*.js)
js_bundles :=  $(js_sources:src/%.js=assets/js/%.bundle.js)
zip_release := $(GIT_VERSION:%=dist/wcms-%.zip)

release: clean $(zip_release)

dist/wcms-%.zip: php_dependencies $(js_bundles)
	@echo "Building Zip release..."
	mkdir -p $(dir $@)
	zip -r $@ \
		app \
		assets \
		vendor \
		.htaccess \
		index.php \
		LICENSE.md \
		README.md \
		-x "*test*" \
		-x "*docs*"

assets/js/%.bundle.js: src/%.js js_dependencies
	@echo "Building JS Bundles..."
	mkdir -p $(dir $@)
	npm run build

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

clean: php_clean js_clean
