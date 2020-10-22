<?php
namespace Controller\Api\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\BaseController;
use Engine\Internal\Delivery;
use Service\Me\MeService;

use Repository\Model\User;
use Repository\Model\Wallet;
use Repository\Model\Transaction;

class MeController extends BaseController {

	public function __construct () {
		$this->delivery = new Delivery;
	}

	public function GetAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$service = new MeService($request, $this->delivery);
		$result = $service->getMe($user, new User, new Wallet, new Transaction);
		return $this->deliverJSON($response, $result);
	}

	public function UpdateAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$service = new MeService($request, $this->delivery);
		$result = $service->updateMe($user, $data, new User);
		return $this->deliverJSON($response, $result);
	}
	
}