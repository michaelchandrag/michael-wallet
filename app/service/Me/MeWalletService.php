<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Engine\Internal\Delivery;
use Repository\Contract\WalletContract;
use Repository\Contract\UserContract;
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
		$wallet = $walletRepository->find(['id_user' => $user->id]);
		$this->delivery->data = $wallet;
		return $this->delivery;
	}

	public function createWallet ($payload, WalletContract $walletRepository, UserContract $userRepository) {
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
}