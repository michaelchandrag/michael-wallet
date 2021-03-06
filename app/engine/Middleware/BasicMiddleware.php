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
        try {
            $headers = $request->getHeader('Authorization');
            if (empty($headers)) {
                throw new \Exception('Authorization token not found');
            }
            $explode = explode("Bearer ", $headers[0]);
            $token = $explode[1];
            if (!getJWTPayload($token)) {
                throw new \Exception('Authorization token not found');
            } else {
                $response = $handler->handle($request);
                return $response;   
            }
        } catch (\Exception $e) {
            $delivery = new Delivery;
            $delivery->addError(403, 'Authorization token not found');
            $delivery->statusCode = 403;
            $response = new \Slim\Psr7\Response();
            $baseController = new BaseController;
            return $baseController->deliverJSON($response, $delivery);
        }
    }
}