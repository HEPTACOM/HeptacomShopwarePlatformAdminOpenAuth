.PHONY: clean it cs csfix

clean:
	[[ ! -f composer.lock ]] || rm composer.lock
	[[ ! -d vendor ]] || rm -rf vendor
	[[ ! -d .build ]] || rm -rf .build

it: csfix

cs: vendor .build
	vendor/bin/php-cs-fixer fix --dry-run --config=.php_cs.php --diff --verbose

csfix: vendor .build
	vendor/bin/php-cs-fixer fix --config=.php_cs.php --diff --verbose

vendor: composer.json
	composer validate
	composer install

.build:
	mkdir .build

composer.lock: vendor
