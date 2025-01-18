FROM ubuntu:24.04

ARG DEBIAN_FRONTEND=noninteractive

COPY ./src /usr/src/burbot/src
COPY ./index.php ./.en[v] ./composer.* /usr/src/burbot/

WORKDIR /usr/src/burbot

RUN apt-get update
RUN apt-get install php-cli php-xml composer php-bcmath php-curl -y
RUN composer install
RUN composer dump-autoload -o

CMD [ "php", "./index.php" ]
