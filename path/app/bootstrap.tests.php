<?php

if (isset($_ENV['BOOTSTRAP_SCHEMA_UPDATE_ENV'])) {
    passthru(sprintf(
        'php "%s/console" doctrine:schema:update --force --env=%s',
        __DIR__,
        $_ENV['BOOTSTRAP_SCHEMA_UPDATE_ENV']
    ));
}

if (isset($_ENV['BOOTSTRAP_FIXTURES_LOAD_ENV'])) {
    passthru(sprintf(
        'php "%s/console" doctrine:fixtures:load --env=%s --no-interaction',
        __DIR__,
        $_ENV['BOOTSTRAP_FIXTURES_LOAD_ENV']
    ));
}	

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    passthru(sprintf(
        'php "%s/console" cache:clear --env=%s --no-warmup',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']
    ));
}

require __DIR__.'/bootstrap.php.cache';