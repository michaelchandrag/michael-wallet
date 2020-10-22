<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;
use Engine\Internal\Delivery;
use Repository\Contract\UserContract;
use Repository\Contract\WalletContract;
use Repository\Contract\TransactionContract;
use Service\Me\Validator\MeValidator;

class MeService {

	private $request;
	private $delivery;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
		$this->validator = new MeValidator;
	}

	public function getMe ($user, UserContract $userRepository, WalletContract $walletRepository, TransactionContract $transactionRepository) {
		$this->delivery = $this->validator->validateUser($this->delivery, $user, $userRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$me = $userRepository->findOne(['id' => $user->id]);
		$wallets = $walletRepository->find(['id_user' => $user->id]);
		$transactions = $transactionRepository->find(['id_user' => $user->id]);
		$me->wallets = $wallets;
		$me->transactions = $transactions;
		$this->delivery->data = $me;
		return $this->delivery;
	}

	public function updateMe ($user, $payload, UserContract $userRepository) {
		$this->delivery = $this->validator->validateUser($this->delivery, $user, $userRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$filterAction = [
			'id' => $user->id
		];
		try {
			DB::beginTransaction();

			$action = $userRepository->modify($filterAction, $payload);
			$me = $userRepository->findOne($filterAction);
			$this->delivery->data = $me;

			DB::commit();
		} catch (\Exception $e) {
			$this->delivery->addError(500, 'Internal Server Error');
			DB::rollback();
		}

		return $this->delivery;
	}

	public function updateReport ($user, UserContract $userRepository, TransactionContract $transactionRepository) {
		$payload = [
			'lifetime_cash_in_total' => 0,
			'lifetime_cash_out_total' => 0,
			'lifetime_total' => 0
		];

		$lifetimeFilters = [
			'id_user' => $user->id
		];

		$lifetimeReport = $transactionRepository->fetchByCategoryType($lifetimeFilters);
		if (isset($lifetimeReport['cash_in'])) {
			$payload['lifetime_cash_in_total'] = $lifetimeReport['cash_in'];
		}
		if (isset($lifetimeReport['cash_out'])) {
			$payload['lifetime_cash_out_total'] = $lifetimeReport['cash_out'];
		}

		$payload['lifetime_total'] = $payload['lifetime_cash_in_total'] - $payload['lifetime_cash_out_total'];
		$this->updateMe($user, $payload, $userRepository);
		return true;
	} 
}