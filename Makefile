composer-install:
	docker compose run --rm php composer install

run-example:
	docker compose run --rm php php /var/www/html/examples/basic_usage.php

.PHONY: docs-install docs-build docs-serve

DOCS_VENV ?= .venv-docs
DOCS_PYTHON ?= python3
DOCS_MKDOCS := $(DOCS_VENV)/bin/mkdocs

docs-install:
	$(DOCS_PYTHON) -m venv $(DOCS_VENV)
	$(DOCS_VENV)/bin/python -m pip install -r docs/requirements.txt

docs-build: docs-install
	php tools/generate-reference.php
	$(DOCS_MKDOCS) build --strict

docs-serve: docs-install
	php tools/generate-reference.php
	$(DOCS_MKDOCS) serve
