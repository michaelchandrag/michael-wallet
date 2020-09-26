<?php
use Psr\Container\ContainerInterface as ContainerInterface;

// API controller
$container->set('ApiHelloController', function(ContainerInterface $c) {
	return new \Controller\Api\HelloController();
});
$container->set('ApiUserController', function(ContainerInterface $c) {
	return new \Controller\Api\UserController();
});
$container->set('ApiEntryController', function(ContainerInterface $c) {
	return new \Controller\Api\EntryController();
});

// Public Controller
$container->set('WebMainController', function(ContainerInterface $c) {
	return new \Controller\Web\MainController();
});