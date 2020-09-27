<?php
namespace Engine\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Engine\Internal\Delivery;
use Controller\BaseController;

class BasicMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
    	/* $authorization = $request->getHeader('Authorization');
    	if (empty($authorization)) {
    		$delivery = new Delivery;
    		$delivery->addError(400, 'Authorization token not found');

    		$baseController = new BaseController;
    		return $baseController->deliverJSON($response, $delivery);
    	} else {
    		$response = $handler->handle($request);
	        return $response;	
    	} */
    	$response = $handler->handle($request);
        $existingContent = (string) $response->getBody();
    
        $response->getBody()->write('BEFORE' . $existingContent);
    
        return $response;
    }
}