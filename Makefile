.PHONY: clean it cs csfix releasecheck

clean:
	[[ ! -f composer.lock ]] || rm composer.lock
	[[ ! -d vendor ]] || rm -rf vendor
	[[ ! -d .build ]] || rm -rf .build

releasecheck: frosh-plugin-upload
	[[ -d .build/store-build ]] || mkdir .build/store-build
	git archive --format=tar HEAD | (cd .build/store-build && tar xf -)
	[[ ! -d .build/store-build/.git ]] || rm -rf .build/store-build/.git
	cp -a .git/ .build/store-build/.git/
	(cd .build/store-build && ../frosh-plugin-upload plugin:zip:dir .)
	.build/frosh-plugin-upload plugin:validate $(shell pwd)/.build/store-build/*.zip

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

frosh-plugin-upload: .build
	[[ -f .build/frosh-plugin-upload ]] || php -r 'copy("https://github.com/FriendsOfShopware/FroshPluginUploader/releases/download/0.3.2/frosh-plugin-upload.phar", ".build/frosh-plugin-upload");'
	[[ -x .build/frosh-plugin-upload ]] || chmod +x .build/frosh-plugin-upload

composer.lock: vendor
