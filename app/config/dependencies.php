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

$container->set('ApiMeController', function(ContainerInterface $c) {
	return new \Controller\Api\Me\MeController();
});

$container->set('ApiMeWalletController', function(ContainerInterface $c) {
	return new \Controller\Api\Me\MeWalletController();
});

$container->set('ApiMeCategoryController', function(ContainerInterface $c) {
	return new \Controller\Api\Me\MeCategoryController();
});

$container->set('ApiMeTransactionController', function(ContainerInterface $c) {
	return new \Controller\Api\Me\MeTransactionController();
});

// Public Controller
$container->set('WebMainController', function(ContainerInterface $c) {
	return new \Controller\Web\MainController();
});