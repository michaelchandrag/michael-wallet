<?php
namespace Controller\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\BaseController;
use Engine\Internal\Delivery;
use Service\Me\MeService;

use Repository\Model\User;

class MeController extends BaseController {

	public function __construct () {
		$this->delivery = new Delivery;
	}

	public function GetAction (Request $request, Response $response, $args) {
		$service = new MeService($request, $this->delivery);
		$result = $service->getMe(new User);
		return $this->deliverJSON($response, $result);
	}
	
}