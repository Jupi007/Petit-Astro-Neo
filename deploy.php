<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/sulu.php';
require 'contrib/webpack_encore.php';

use Symfony\Component\Console\Input\InputOption;

option('hostname', null, InputOption::VALUE_REQUIRED, 'Hostname to deploy.');
option('remote-user', null, InputOption::VALUE_REQUIRED, 'Remote user to deploy.');

// Config

set('git_ssh_command', 'ssh');
set('writable_mode', 'chmod');

set('repository', 'git@github.com:Jupi007/Petit-Astro-Neo.git');

set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader --apcu-autoloader --classmap-authoritative');

// Hosts

host('production')
    ->set('hostname', function () {
        return input()->getOption('hostname');
    })
    ->set('remote_user', function () {
        return input()->getOption('remote-user');
    })
    ->set('deploy_path', '~/neo.lepetitastro.fr')
    ->set('branch', function () {
        return input()->getOption('branch') ?: 'production';
    })
    ->set('labels', ['stage' => 'production']);

set('http_user', 'cptcave');
set('http_group', 'cptcave');

// Fixes

// Fixes composer php version
set('bin/composer', function () {
    return which('composer');
});

// Tasks

desc('Optimize Composer Autoloader');
task('deploy:dump_autoload', function () {
    run('cd {{release_or_current_path}} && {{bin/composer}} dump-autoload --no-dev --classmap-authoritative 2>&1');
});

desc('Dump .env to .env.php');
task('deploy:dump_env', function () {
    run('cd {{release_or_current_path}} && {{bin/composer}} dump-env prod');
});

after('deploy:vendors', 'deploy:dump_autoload');
after('deploy:vendors', 'deploy:dump_env');

after('deploy:vendors', 'yarn:install');
after('deploy:vendors', 'webpack_encore:build');

before('deploy:symlink', 'doctrine:schema:validate');
before('deploy:symlink', 'database:migrate');
before('deploy:symlink', 'phpcr:migrate');

after('deploy:failed', 'deploy:unlock');
