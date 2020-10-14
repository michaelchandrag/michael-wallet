<?php
namespace Controller\Api\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\BaseController;
use Engine\Internal\Delivery;
use Service\Me\MeService;

use Repository\Model\User;
use Repository\Model\Wallet;

class MeController extends BaseController {

	public function __construct () {
		$this->delivery = new Delivery;
	}

	public function GetAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$service = new MeService($request, $this->delivery);
		$result = $service->getMe($user, new User, new Wallet);
		return $this->deliverJSON($response, $result);
	}
	
}