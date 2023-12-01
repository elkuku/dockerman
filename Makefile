SHELL := /bin/bash

tests: export APP_ENV=test
tests:
	symfony php bin/phpunit --testdox $@
	vendor/bin/phpstan
	vendor/bin/rector process --dry-run
	#tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run --diff
.PHONY: tests
citests: export APP_ENV=test
citests:
	bin/phpunit --testdox
	vendor/bin/phpstan
	vendor/bin/rector process --dry-run
	#tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run
