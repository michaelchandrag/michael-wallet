<?php
use Slim\Routing\RouteCollectorProxy;

$app->group('/api/v1', function (RouteCollectorProxy $group) {
    
    // public
    $group->get('/hello', 'ApiHelloController:HelloAction');
    $group->get('/error', 'ApiHelloController:ErrorAction');

    // entry
	$group->post('/register', 'ApiEntryController:RegisterAction');
	$group->post('/login', 'ApiEntryController:LoginAction');

    $group->group('/user', function (RouteCollectorProxy $groupUser) {
    	$groupUser->get('', 'ApiUserController:GetAction');
    });

});