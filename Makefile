SHELL := /bin/bash
UNAME := $(shell sh -c 'uname -s 2>/dev/null || echo not')
PHP := $(shell which php) $(PHP_EXTRA_ARGS) -derror_reporting=0
COMPOSER := $(PHP) $(shell which composer) $(COMPOSER_EXTRA_ARGS)
CURL := $(shell which curl)
JQ := $(shell which jq)
TAR := $(shell which tar)
GREP := $(shell which grep)
JSON_FILES := $(shell find . -name '*.json' -not -path './vendor/*' -not -path './.build/*')
TRANSLATION_JSON_FILES := $(shell find src -name '*.json' | $(GREP) -v -e '/vendor/' -e '/node_modules/' | $(GREP) -e '/snippet')
TRANSLATION_JSON_FILES__CHECK_TRANSLATION := $(TRANSLATION_JSON_FILES:%=%__CHECK_TRANSLATION)
PHPSTAN_FILE := dev-ops/bin/phpstan/vendor/bin/phpstan
COMPOSER_NORMALIZE_PHAR := https://github.com/ergebnis/composer-normalize/releases/download/2.22.0/composer-normalize.phar
COMPOSER_NORMALIZE_FILE := dev-ops/bin/composer-normalize
COMPOSER_REQUIRE_CHECKER_PHAR := https://github.com/maglnet/ComposerRequireChecker/releases/download/4.6.0/composer-require-checker.phar
COMPOSER_REQUIRE_CHECKER_FILE := dev-ops/bin/composer-require-checker
PHPMD_PHAR := https://github.com/phpmd/phpmd/releases/download/2.13.0/phpmd.phar
PHPMD_FILE := dev-ops/bin/phpmd
COMPOSER_UNUSED_FILE := dev-ops/bin/composer-unused/vendor/bin/composer-unused
PINT_FILE := dev-ops/bin/pint/vendor/bin/pint
PHPCHURN_FILE := dev-ops/bin/php-churn/vendor/bin/churn
PHPUNUHI_DIR := dev-ops/bin/phpunuhi
PHPUNUHI_FILE := $(PHPUNUHI_DIR)/vendor/bin/phpunuhi

ifeq ($(UNAME),Linux)
	SHOPWARE_CLI_ARCHIVE := https://github.com/FriendsOfShopware/shopware-cli/releases/download/0.4.29/shopware-cli_Linux_x86_64.tar.gz
endif
ifeq ($(UNAME),Darwin)
	SHOPWARE_CLI_ARCHIVE := https://github.com/FriendsOfShopware/shopware-cli/releases/download/0.4.29/shopware-cli_Darwin_arm64.tar.gz
endif
SHOPWARE_CLI_FILE := dev-ops/bin/shopware-cli

.DEFAULT_GOAL := help
.PHONY: help
help: ## List useful make targets
	@echo 'Available make targets'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: all
all: clean it ## Cleans up and runs typical tests and style analysis

.PHONY: clean
clean: ## Cleans up all ignored files and directories
	[[ ! -d dev-ops/bin/shopware/vendor ]] || rm -f dev-ops/bin/shopware/vendor
	[[ ! -f composer.lock ]] || rm composer.lock
	[[ ! -d vendor ]] || rm -rf vendor
	[[ ! -d .build ]] || rm -rf .build
	[[ ! -f dev-ops/bin/composer-normalize ]] || rm -f dev-ops/bin/composer-normalize
	[[ ! -f dev-ops/bin/composer-require-checker ]] || rm -f dev-ops/bin/composer-require-checker
	[[ ! -f $(SHOPWARE_CLI_FILE) ]] || rm -f $(SHOPWARE_CLI_FILE)
	[[ ! -d dev-ops/bin/composer-unused/vendor ]] || rm -rf dev-ops/bin/composer-unused/vendor
	[[ ! -d dev-ops/bin/pint/vendor ]] || rm -rf dev-ops/bin/pint/vendor
	[[ ! -f dev-ops/bin/phpmd ]] || rm -f dev-ops/bin/phpmd
	[[ ! -d dev-ops/bin/phpstan/vendor ]] || rm -rf dev-ops/bin/phpstan/vendor
	[[ ! -d dev-ops/bin/php-churn/vendor ]] || rm -rf dev-ops/bin/php-churn/vendor

.PHONY: build-assets
build-assets: $(SHOPWARE_CLI_FILE) ## Builds assets
	$(SHOPWARE_CLI_FILE) extension build .

.PHONY: it
it: cs-fix cs ## Fix code style

.PHONY: cs
cs: cs-style cs-phpstan cs-phpmd cs-soft-require cs-composer-unused cs-composer-normalize cs-json cs-phpchurn cs-translation ## Run every code style check target

.PHONY: cs-style
cs-style: .build $(PINT_FILE) ## Run pint for code style analysis
	[[ -z "${CI}" ]] || $(PHP) $(PINT_FILE) --test --config=dev-ops/pint.json --format=junit > .build/style.junit.xml
	[[ -n "${CI}" ]] || $(PHP) $(PINT_FILE) --test --config=dev-ops/pint.json

.PHONY: cs-phpstan
cs-phpstan: vendor .build $(PHPSTAN_FILE) ## Run phpstan for static code analysis
	[[ -z "${CI}" ]] || $(PHP) $(PHPSTAN_FILE) analyse --level 6 -c dev-ops/phpstan.neon --error-format=junit > .build/phpstan.junit.xml
	[[ -n "${CI}" ]] || $(PHP) $(PHPSTAN_FILE) analyse --level 6 -c dev-ops/phpstan.neon

.PHONY: cs-phpmd
cs-phpmd: vendor .build $(PHPMD_FILE) ## Run php mess detector for static code analysis
	# TODO Re-add rulesets/unused.xml when phpmd fixes false-positive UnusedPrivateField
	$(PHP) $(PHPMD_FILE) --ignore-violations-on-exit src ansi rulesets/codesize.xml,rulesets/naming.xml
	[[ -f .build/phpmd-junit.xslt ]] || $(CURL) https://phpmd.org/junit.xslt -o .build/phpmd-junit.xslt
	$(PHP) $(PHPMD_FILE) src xml rulesets/codesize.xml,rulesets/naming.xml | xsltproc .build/phpmd-junit.xslt - > .build/php-md.junit.xml

.PHONY: cs-composer-unused
cs-composer-unused: vendor $(COMPOSER_UNUSED_FILE) ## Run composer-unused to detect once-required packages that are not used anymore
	$(PHP) $(COMPOSER_UNUSED_FILE) --no-progress

.PHONY: cs-soft-require
cs-soft-require: vendor .build $(COMPOSER_REQUIRE_CHECKER_FILE) ## Run composer-require-checker to detect library usage without requirement entry in composer.json
	$(PHP) $(COMPOSER_REQUIRE_CHECKER_FILE) check --config-file=$(shell pwd)/dev-ops/composer-soft-requirements.json composer.json

.PHONY: cs-composer-normalize
cs-composer-normalize: vendor $(COMPOSER_NORMALIZE_FILE) ## Run composer-normalize for composer.json style analysis
	$(PHP) $(COMPOSER_NORMALIZE_FILE) --diff --dry-run --no-check-lock --no-update-lock composer.json

.PHONY: cs-json
cs-json: $(JSON_FILES) ## Run jq on every json file to ensure they are parsable and therefore valid

.PHONY: cs-translation
cs-translation: .build $(PHPUNUHI_FILE) $(TRANSLATION_JSON_FILES__CHECK_TRANSLATION) ## Run phpunuhi for validating translation files
	[[ -z "${CI}" ]] || $(PHP) $(PHPUNUHI_FILE) $(PHPUNUHI_EXTRA_ARGS) validate --configuration=dev-ops/phpunuhi.xml --report-format=junit --report-output=.build/phpunuhi-report.xml
	[[ -n "${CI}" ]] || $(PHP) $(PHPUNUHI_FILE) $(PHPUNUHI_EXTRA_ARGS) validate --configuration=dev-ops/phpunuhi.xml

.PHONY: cs-phpchurn
cs-phpchurn: vendor .build $(PHPCHURN_FILE) ## Run php-churn for prediction of refactoring cases
	$(PHP) $(PHPCHURN_FILE) run --configuration dev-ops/churn.yml --format text

.PHONY: $(JSON_FILES)
$(JSON_FILES):
	$(JQ) . "$@"

.PHONY: $(TRANSLATION_JSON_FILES__CHECK_TRANSLATION)
$(TRANSLATION_JSON_FILES__CHECK_TRANSLATION):
	@$(GREP) -so -e $(subst __CHECK_TRANSLATION,,$@) $(shell pwd)/dev-ops/phpunuhi.xml

.PHONY: cs-fix ## Run all code style fixer that change files
cs-fix: cs-fix-composer-normalize cs-fix-style cs-fix-translation

.PHONY: cs-fix-composer-normalize
cs-fix-composer-normalize: vendor $(COMPOSER_NORMALIZE_FILE) ## Run composer-normalize for automatic composer.json style fixes
	$(PHP) $(COMPOSER_NORMALIZE_FILE) --diff composer.json

.PHONY: cs-fix-style
cs-fix-style: .build $(PINT_FILE) ## Run pint for automatic code style fixes
	[[ -z "${CI}" ]] || $(PHP) $(PINT_FILE) --config=dev-ops/pint.json --format=junit > .build/fix-style.junit.xml
	[[ -n "${CI}" ]] || $(PHP) $(PINT_FILE) --config=dev-ops/pint.json

.PHONY: cs-fix-translation
cs-fix-translation: .build $(PHPUNUHI_FILE) $(TRANSLATION_JSON_FILES__CHECK_TRANSLATION) ## Run phpunuhi add missing entries in translation files
	$(PHP) $(PHPUNUHI_FILE) fix:structure --configuration=dev-ops/phpunuhi.xml

$(PHPSTAN_FILE): ## Install phpstan executable
	$(COMPOSER) install -d dev-ops/bin/phpstan

$(COMPOSER_NORMALIZE_FILE): ## Install composer-normalize executable
	$(CURL) -L $(COMPOSER_NORMALIZE_PHAR) -o $(COMPOSER_NORMALIZE_FILE)

$(COMPOSER_REQUIRE_CHECKER_FILE): ## Install composer-require-checker executable
	$(CURL) -L $(COMPOSER_REQUIRE_CHECKER_PHAR) -o $(COMPOSER_REQUIRE_CHECKER_FILE)

$(PHPMD_FILE): ## Install phpmd executable
	$(CURL) -L $(PHPMD_PHAR) -o $(PHPMD_FILE)

$(COMPOSER_UNUSED_FILE): ## Install composer-unused executable
	$(COMPOSER) install -d dev-ops/bin/composer-unused

$(PHPCHURN_FILE): ## Install php-churn executable
	$(COMPOSER) install -d dev-ops/bin/php-churn

$(PHPUNUHI_FILE): ## Install phpunuhi executable
	$(COMPOSER) install -d $(PHPUNUHI_DIR)

$(PINT_FILE): ## Install pint executable
	$(COMPOSER) install -d dev-ops/bin/pint

$(SHOPWARE_CLI_FILE): ## Install shopware-cli executable
	$(CURL) -L $(SHOPWARE_CLI_ARCHIVE) -o $(SHOPWARE_CLI_FILE).tar.gz
	cd $(dir $(SHOPWARE_CLI_FILE)) && $(TAR) -xvzf $(notdir $(SHOPWARE_CLI_FILE)).tar.gz shopware-cli
	rm $(SHOPWARE_CLI_FILE).tar.gz

vendor:
	[[ -f vendor/autoload.php ]] || $(COMPOSER) install

.PHONY: .build
.build:
	[[ -d .build ]] || mkdir .build
