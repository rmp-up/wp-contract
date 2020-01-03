<?php

// There is an autoload for WP_CLI\\ but not for \WP_CL
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/wp-cli/wp-cli/php/class-wp-cli.php';

\Pretzlaw\WPInt\run_wp();