<?php
use Slim\Routing\RouteCollectorProxy;
use Engine\Middleware\BasicMiddleware;

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

    // me
    $group->group('/me', function (RouteCollectorProxy $groupMe) {
    	$groupMe->get('', 'ApiMeController:GetAction');

        // wallet
        $groupMe->group('/wallet', function (RouteCollectorProxy $groupWallet) {
            $groupWallet->get('', 'ApiMeWalletController:GetWalletAction');
            $groupWallet->post('', 'ApiMeWalletController:CreateWalletAction');
            $groupWallet->post('/{wallet_id}', 'ApiMeWalletController:UpdateWalletAction');
            $groupWallet->post('/delete/{wallet_id}', 'ApiMeWalletController:DeleteWalletAction');
        });

        // category
        $groupMe->group('/category', function (RouteCollectorProxy $groupCategory) {
            $groupCategory->get('', 'ApiMeCategoryController:GetCategoryAction');
            $groupCategory->post('', 'ApiMeCategoryController:CreateCategoryAction');
            $groupCategory->post('/{category_id}', 'ApiMeCategoryController:UpdateCategoryAction');
            $groupCategory->post('/delete/{category_id}', 'ApiMeCategoryController:DeleteCategoryAction');
        });

        // transaction
        $groupMe->group('/transaction', function (RouteCollectorProxy $groupTransaction) {
            $groupTransaction->get('', 'ApiMeTransactionController:GetTransactionAction');
            $groupTransaction->post('', 'ApiMeTransactionController:CreateTransactionAction');
            $groupTransaction->post('/{transaction_id}', 'ApiMeTransactionController:UpdateTransactionAction');
            $groupTransaction->post('/delete/{transaction_id}', 'ApiMeTransactionController:DeleteTransactionAction');
        });


    })->add(new BasicMiddleware());

});