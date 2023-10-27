FROM composer AS dependencies

WORKDIR /app
ADD . .

RUN composer install

# Image that reuses the previously built composer artifact (dependencies + autoload)
# Prepares itself to run PHPstan
FROM php:7.4 AS phpstan
WORKDIR /web/module
ADD . .
COPY --from=dependencies /app/vendor/ vendor/

ENTRYPOINT ["/web/module/vendor/bin/phpstan", "--memory-limit=-1"]

