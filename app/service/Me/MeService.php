<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Engine\Internal\Delivery;
use Repository\Contract\UserContract;

class MeService {

	private $request;
	private $delivery;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
	}

	public function getMe (UserContract $userRepository) {
		$this->delivery->data = 'ok';
		return $this->delivery;
	}
}