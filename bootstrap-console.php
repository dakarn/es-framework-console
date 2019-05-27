<?php

\define('PSR_4', true);
\define('IS_DEV', \is_file(__DIR__ . '/dev.php'));
\define('PATH_APP', __DIR__ . '/app/');

include_once __DIR__ . '/vendor/autoload.php';

$env = \App\ConsoleApp::ENV_PROD;

if (\IS_DEV) {
	$env = \App\ConsoleApp::ENV_DEV;
	include_once 'dev.php';
}

$application = (new \App\ConsoleApp())
	->setEnvironment($env)
	->setApplicationType(\App\ConsoleApp::APP_TYPE_CONSOLE);

$application->run();