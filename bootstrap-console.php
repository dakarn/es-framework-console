<?php

\define('PSR_4', true);
\define('IS_DEV', \is_file(__DIR__ . '/dev.php'));
\define('PATH_APP', __DIR__ . '/app/');

include_once __DIR__ . '/vendor/autoload.php';

$env = 'PROD';

$application = (new \App\ConsoleApp())
	->setEnvironment($env)
	->setApplicationType(\App\ConsoleApp::APP_TYPE_CONSOLE);

\set_exception_handler(function($e) use($application) {
	$application->outputException($e);
});

\set_error_handler(function($errno, $errstr, $errfile, $errline) use($application) {
	$application->outputError($errno, $errstr, $errfile, $errline);
});

\register_shutdown_function(function() use($application) {
	System\Kernel\ShutdownScript::run();
});

$application->run();