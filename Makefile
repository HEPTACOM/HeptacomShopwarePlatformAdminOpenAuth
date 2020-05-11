.PHONY: clean it cs csfix

clean:
	[[ ! -f composer.lock ]] || rm composer.lock
	[[ ! -d vendor ]] || rm -rf vendor

it: csfix

cs: vendor
	vendor/bin/php-cs-fixer fix --dry-run --config=.php_cs.php --diff --verbose

csfix: vendor
	vendor/bin/php-cs-fixer fix --config=.php_cs.php --diff --verbose

vendor: composer.json
	composer validate
	composer install

composer.lock: vendor
