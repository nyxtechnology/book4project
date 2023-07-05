include docker.mk

## app-update:			Update the plataform
.PHONY: app-update
app-update:
	docker-compose up php-install
	docker-compose up php-update

crete-test:
	./vendor/bin/codecept g:cest acceptance ${name}

test:
	@if [ $(db) ]; then\
		INIT_DB=true docker-compose -f docker-compose.test.yml up --abort-on-container-exit;\
	else \
		docker-compose -f docker-compose.test.yml up --abort-on-container-exit; \
	fi

test-clean:
	docker-compose -f docker-compose.test.yml down

test-only:
	docker-compose -f docker-compose.test.yml up  codeception
