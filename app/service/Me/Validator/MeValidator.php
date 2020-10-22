<?php
namespace Service\Me\Validator;

use Repository\Contract\UserContract;
use Engine\Internal\Delivery;

class MeValidator {

	public function __construct () {

	}

	public function validateUser (Delivery $delivery, $user, UserContract $userRepository) {
		$filter = [
			'id' => $user->id,
		];
		$existsUser = $userRepository->findOne($filter);
		if (empty($existsUser)) {
			$delivery->addError(400, 'User not found.');
		}

		return $delivery;
	}

}