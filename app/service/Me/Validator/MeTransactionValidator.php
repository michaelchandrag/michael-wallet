<?php
namespace Service\Me\Validator;

use Repository\Contract\UserContract;
use Repository\Contract\TransactionContract;
use Repository\Contract\WalletContract;
use Repository\Contract\CategoryContract;
use Engine\Internal\Delivery;

class MeTransactionValidator {

	public function __construct () {

	}

	public function validateCreateTransaction (Delivery $delivery, $payload, UserContract $userRepository, WalletContract $walletRepository, CategoryContract $categoryRepository) {
		if (!isset($payload['id_user']) && empty($payload['id_user'])) {
			$delivery->addError(400, 'Transaction user should not be empty.');
		}

		if (!isset($payload['id_category']) && empty($payload['id_category'])) {
			$delivery->addError(400, 'Transaction category should not be empty.');
		}

		if (!isset($payload['id_wallet']) && empty($payload['id_wallet'])) {
			$delivery->addError(400, 'Category type should not be empty.');
		}

		if (!isset($payload['amount'])) {
			$delivery->addError(400, 'Transaction amount should not be empty.');
		}

		if ($delivery->hasErrors()) {
			return $delivery;
		}

		$existsUser = $userRepository->findOne(['id' => $payload['id_user']]);
		if (empty($existsUser)) {
			$delivery->addError(400, 'User not found.');
		}

		$existsWallet = $walletRepository->findOne(['id' => $payload['id_wallet'], 'id_user' => $payload['id_user']]);
		if (empty($existsWallet)) {
			$delivery->addError(400, 'Wallet not found.');
		}

		$existsCategory = $categoryRepository->findOne(['id' => $payload['id_category'], 'id_user' => $payload['id_user']]);
		if (empty($existsCategory)) {
			$delivery->addError(400, 'Category not found.');
		}

		if ($delivery->hasErrors()) {
			return $delivery;
		}

		return $delivery;
	}

	public function validateUpdateTransaction (Delivery $delivery, $transactionId, $user, $payload, TransactionContract $transactionRepository, UserContract $userRepository, WalletContract $walletRepository, CategoryContract $categoryRepository) {
		$filter = [
			'id' => $transactionId,
			'id_user' => $user->id
		];
		$existsTransaction = $transactionRepository->findOne($filter);
		if (empty($existsTransaction)) {
			$delivery->addError(400, 'Transaction not found.');
		}

		if (isset($payload['id_wallet'])) {
			$filterWallet = [
				'id' => $payload['id_wallet'],
				'id_user' => $user->id
			];
			$existsWallet = $walletRepository->findOne($filterWallet);
			if (empty($existsWallet)) {
				$delivery->addError(400, 'Wallet not found.');
			}
		}

		if (isset($payload['id_category'])) {
			$filterCategory = [
				'id' => $payload['id_category'],
				'id_user' => $user->id
			];
			$existsCategory = $walletRepository->findOne($filterCategory);
			if (empty($existsCategory)) {
				$delivery->addError(400, 'Category not found.');
			}
		}

		return $delivery;
	}

}