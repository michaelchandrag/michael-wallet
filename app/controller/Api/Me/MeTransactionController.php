<?php
namespace Controller\Api\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\BaseController;
use Engine\Internal\Delivery;
use Service\Me\MeTransactionService;

use Repository\Model\Transaction;
use Repository\Model\User;
use Repository\Model\Category;
use Repository\Model\Wallet;

class MeTransactionController extends BaseController {

	public function __construct () {
		$this->delivery = new Delivery;
	}

	public function GetTransactionAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$service = new MeTransactionService($request, $this->delivery);
		$result = $service->getTransaction($user, new Transaction);
		return $this->deliverJSON($response, $result);
	}

	public function CreateTransactionAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$data['id_user'] = $user->id;
		$service = new MeTransactionService($request, $this->delivery);
		$result = $service->createTransaction($data, $user, new Transaction, new User, new Wallet, new Category);
		return $this->deliverJSON($response, $result);
	}

	public function UpdateTransactionAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$transactionId = $args['transaction_id'];
		$service = new MeTransactionService($request, $this->delivery);
		$result = $service->updateTransaction($transactionId, $user, $data, new Transaction, new User, new Wallet, new Category);
		return $this->deliverJSON($response, $result);
	}

	public function DeleteTransactionAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$transactionId = $args['transaction_id'];
		$service = new MeTransactionService($request, $this->delivery);
		$result = $service->updateTransaction($transactionId, $user, ['deleted_at' => date('Y-m-d H:i:s')], new Transaction, new User, new Wallet, new Category);
		return $this->deliverJSON($response, $result);
	}
	
}