<?php
namespace Service\Me\Validator;

use Repository\Contract\UserContract;
use Repository\Contract\WalletContract;
use Engine\Internal\Delivery;

class MeWalletValidator {

	public function __construct () {

	}

	public function validateCreateWallet (Delivery $delivery, $payload, UserContract $userRepository) {
		if (!isset($payload['name']) || empty($payload['description'])) {
			$delivery->addError(400, 'Wallet name should not be empty.');
		}

		if ($delivery->hasErrors()) {
			return $delivery;
		}

		$existsUser = $userRepository->findOne(['id' => $payload['id_user']]);
		if (empty($existsUser)) {
			$delivery->addError(400, 'User not found.');
		}

		if ($delivery->hasErrors()) {
			return $delivery;
		}

		return $delivery;
	}

	public function validateUpdateWallet (Delivery $delivery, $walletId, $user, $payload, WalletContract $walletRepository) {
		$filter = [
			'id' => $walletId,
			'id_user' => $user->id
		];
		$existsWallet = $walletRepository->findOne($filter);
		if (empty($existsWallet)) {
			$delivery->addError(400, 'Wallet not found.');
		}

		return $delivery;
	}

}