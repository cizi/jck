<?php

$paths = __DIR__ . DIRECTORY_SEPARATOR . 'path.php';
require_once $paths;

require_once(__DIR__ . '/../vendor/PHPMailer-master/PHPMailerAutoload.php');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/Kdyby/forms-replicator/src/Kdyby/Replicator/Container.php';				// nevím roč to autoload nenajde tak udělám require
require __DIR__ . '/../vendor/Kdyby/forms-replicator/src/Kdyby/Replicator/DI/ReplicatorExtension.php';	// nevím roč to autoload nenajde tak udělám require

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;