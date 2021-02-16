FROM debian:stable-slim

RUN apt-get update \
	&& apt-get install -y subversion rsync git zip php-cli \
	&& apt-get clean -y \
	&& rm -rf /var/lib/apt/lists/*

COPY entrypoint.sh                  /entrypoint.sh
COPY wp-plugin-deploy-functions.php /wp-plugin-deploy-functions.php
COPY wp-plugin-deploy.php           /wp-plugin-deploy.php

ENTRYPOINT ["/entrypoint.sh"]
