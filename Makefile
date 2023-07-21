build:
	docker build -t burbot .

build-run:
	docker rm --force Burbot; \
	docker run --env-file .env --name Burbot -d burbot
