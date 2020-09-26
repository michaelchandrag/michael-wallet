<?php
namespace Controller\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\BaseController;
use Engine\Internal\Delivery;
use Service\Entry\EntryService;
use Service\Hello\HelloService;
use Repository\Model\User;

class EntryController extends BaseController {

	public function __construct () {
		$this->delivery = new Delivery;
	}

	public function RegisterAction (Request $request, Response $response, $args) {
		$service = new EntryService($request, $this->delivery);
		$result = $service->register(new User);
		return $this->deliverJSON($response, $result);
	}

	public function LoginAction (Request $request, Response $response, $args) {
		$service = new EntryService($request, $this->delivery);
		$result = $service->login(new User);
		return $this->deliverJSON($response, $result);
	}
}
