compile:
	docker-compose run --rm compile > build/solc.json

docs: compile
	docker-compose run --rm docs > README.md