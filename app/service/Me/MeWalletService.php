<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Engine\Internal\Delivery;
use Repository\Contract\WalletContract;
use Repository\Contract\UserContract;
use Repository\Contract\TransactionContract;
use Service\Me\Validator\MeWalletValidator;

class MeWalletService {

	private $request;
	private $delivery;
	private $validator;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
		$this->validator = new MeWalletValidator;
	}

	public function getWallet ($user, WalletContract $walletRepository) {
		$filters = $this->request->getQueryParams();
		$filters['id_user'] = $user->id;
		$wallet = $walletRepository->find($filters);
		$this->delivery->data = $wallet;
		return $this->delivery;
	}

	public function createWallet ($payload, $user, WalletContract $walletRepository, UserContract $userRepository) {
		$this->delivery = $this->validator->validateCreateWallet($this->delivery, $payload, $userRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$newWalletId = $walletRepository->create($payload);
		$newWallet = $walletRepository->findOne(['id' => $newWalletId]);
		$this->delivery->data = $newWallet;
		$this->delivery->success = true;
		return $this->delivery;
	}

	public function updateWallet ($walletId, $user, $payload, WalletContract $walletRepository, UserContract $userRepository) {
		$this->delivery = $this->validator->validateUpdateWallet($this->delivery, $walletId, $user, $payload, $walletRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$filterAction = [
			'id' => $walletId,
			'id_user' => $user->id
		];
		$action = $walletRepository->modify($filterAction, $payload);
		$wallet = $walletRepository->findOne($filterAction);

		$this->delivery->data = $wallet;
		return $this->delivery;
	}

	public function updateReport ($user, $walletId, UserContract $userRepository, WalletContract $walletRepository, TransactionContract $transactionRepository) {
		$payload = [
			'lifetime_cash_in_total' => 0,
			'lifetime_cash_out_total' => 0,
			'lifetime_total' => 0
		];

		$filters = [
			'id_wallet' => $walletId,
			'id_user' => $user->id
		];

		$lifetimeReport = $transactionRepository->fetchByCategoryType($filters);
		if (isset($lifetimeReport['cash_in'])) {
			$payload['lifetime_cash_in_total'] = $lifetimeReport['cash_in'];
		}
		if (isset($lifetimeReport['cash_out'])) {
			$payload['lifetime_cash_out_total'] = $lifetimeReport['cash_out'];
		}
		
		$payload['lifetime_total'] = $payload['lifetime_cash_in_total'] - $payload['lifetime_cash_out_total'];
		$this->updateWallet($walletId, $user, $payload, $walletRepository, $userRepository);
		return true;
	}
}