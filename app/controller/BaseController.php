<?php
namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;
use Engine\Internal\Delivery;


class BaseController {

	public function __construct () {

	}

	public function getUser (Request $request) {
		$token = $request->getHeader('Authorization');
		$explode = explode("Bearer ", $token[0]);
		return getJWTPayload($explode[1], 'user');
	}

	public function deliverJSON ($response, Delivery $delivery) {
		$payload = $this->createPayload($delivery);
		$payload = json_encode($payload);
		$response->getBody()->write($payload);
		return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($delivery->statusCode);
	}
	
	public function deliverHTML ($response, $filename, $args = array()) {
		$renderer = new PhpRenderer(getcwd().'/views/');
		return $renderer->render($response, $filename, $args);
	}

	private function createPayload (Delivery $delivery) {
		$payload = [];
		if ($delivery->hasErrors()) {
			$payload = [
				'errors' => $delivery->errors,
				'success' => $delivery->success
			];
		} else {
			$payload = [
				'data' => $delivery->data,
				'success' => $delivery->success
			];
		}
		return $payload;
	}
}