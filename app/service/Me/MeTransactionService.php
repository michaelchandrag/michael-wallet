<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;
use Engine\Internal\Delivery;
use Repository\Contract\TransactionContract;
use Repository\Contract\UserContract;
use Repository\Contract\WalletContract;
use Repository\Contract\CategoryContract;
use Service\Me\Validator\MeTransactionValidator;

class MeTransactionService {

	private $request;
	private $delivery;
	private $validator;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
		$this->validator = new MeTransactionValidator;
	}

	public function getTransaction ($user, TransactionContract $transactionRepository) {
		$filters = $this->request->getQueryParams();
		$filters['id_user'] = $user->id;
		$transaction = $transactionRepository->find($filters);
		$this->delivery->data = $transaction;
		return $this->delivery;
	}

	public function createTransaction ($payload, $user, TransactionContract $transactionRepository, UserContract $userRepository, WalletContract $walletRepository, CategoryContract $categoryRepository) {
		$this->delivery = $this->validator->validateCreateTransaction($this->delivery, $payload, $userRepository, $walletRepository, $categoryRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		try {
			DB::beginTransaction();
			
			$newTransactionId = $transactionRepository->create($payload);
			$newTransaction = $transactionRepository->findOne(['id' => $newTransactionId]);
			
			$walletAction = new MeWalletService($this->request, $this->delivery);
			$walletAction->updateReport($user, $payload['id_wallet'], $userRepository, $walletRepository, $transactionRepository);
			
			$categoryAction = new MeCategoryService($this->request, $this->delivery);
			$categoryAction->updateReport($user, $payload['id_category'], $userRepository, $categoryRepository, $transactionRepository);

			/* $meAction = new MeService($this->request, $this->delivery);
			$meAction->updateReport($user, $userRepository, $transactionRepository); */

			$this->delivery->data = $newTransaction;
			$this->delivery->success = true;
			
			DB::commit();
		} catch (\Exception $e) {
			$this->delivery->addError(500, 'Internal Server Error');
			DB::rollBack();
		}
		return $this->delivery;
	}

	public function updateTransaction ($transactionId, $user, $payload, TransactionContract $transactionRepository, UserContract $userRepository, WalletContract $walletRepository, CategoryContract $categoryRepository) {
		$this->delivery = $this->validator->validateUpdateTransaction($this->delivery, $transactionId, $user, $payload, $transactionRepository, $userRepository, $walletRepository, $categoryRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$filterAction = [
			'id' => $transactionId,
			'id_user' => $user->id
		];

		try {
			DB::beginTransaction();

			$action = $transactionRepository->modify($filterAction, $payload);
			$transaction = $transactionRepository->findOne($filterAction);

			$walletAction = new MeWalletService($this->request, $this->delivery);
			$walletAction->updateReport($user, $transaction->id_wallet, $userRepository, $walletRepository, $transactionRepository);

			$categoryAction = new MeCategoryService($this->request, $this->delivery);
			$categoryAction->updateReport($user, $transaction->id_category, $userRepository, $categoryRepository, $transactionRepository);

			$this->delivery->data = $transaction;
			
			DB::commit();
		} catch (\Exception $e) {
			$this->delivery->addError(500, 'Internal Server Error');
			DB::rollBack();
		}
		return $this->delivery;
	}
}