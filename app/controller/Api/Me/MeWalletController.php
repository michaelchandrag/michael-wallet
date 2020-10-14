<?php
namespace Controller\Api\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\BaseController;
use Engine\Internal\Delivery;
use Service\Me\MeWalletService;

use Repository\Model\Wallet;
use Repository\Model\User;

class MeWalletController extends BaseController {

	public function __construct () {
		$this->delivery = new Delivery;
	}

	public function GetWalletAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$service = new MeWalletService($request, $this->delivery);
		$result = $service->getWallet($user, new Wallet);
		return $this->deliverJSON($response, $result);
	}

	public function CreateWalletAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$data = [
			'name' => $data['name'],
			'description' => $data['description'],
			'id_user' => $user->id
		];
		$service = new MeWalletService($request, $this->delivery);
		$result = $service->createWallet($data, $user, new Wallet, new User);
		return $this->deliverJSON($response, $result);
	}

	public function UpdateWalletAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$walletId = $args['wallet_id'];
		$service = new MeWalletService($request, $this->delivery);
		$result = $service->updateWallet($walletId, $user, $data, new Wallet, new User);
		return $this->deliverJSON($response, $result);
	}

	public function DeleteWalletAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$walletId = $args['wallet_id'];
		$service = new MeWalletService($request, $this->delivery);
		$result = $service->updateWallet($walletId, $user, ['deleted_at' => date('Y-m-d H:i:s')], new Wallet, new User);
		return $this->deliverJSON($response, $result);
	}
	
}