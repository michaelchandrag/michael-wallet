<?php
namespace Service\Entry;

use Repository\Contract\UserContract;
use Engine\Internal\Delivery;

class EntryValidator {

	public function __construct () {

	}

	public function validateRegister (Delivery $delivery, $payload, UserContract $userRepository) {
		if (!isset($payload['first_name']) || empty($payload['first_name'])) {
			$delivery->addError(400, 'First name should not be empty.');
		}

		if (!isset($payload['email']) || empty($payload['email'])) {
			$delivery->addError(400, 'Email should not be empty.');
		}

		if (!isset($payload['phone_number']) || empty($payload['phone_number'])) {
			$delivery->addError(400, 'Phone number should not be empty.');
		}

		if (empty($payload['password']) || ($payload['password'] != $payload['confirm_password'])) {
			$delivery->addError(400, 'Something error with the password.');
		}

		if ($delivery->hasErrors()) {
			return $delivery;
		}

		$existsUser = $userRepository->findOne(['email|phone_number' => $payload['email'].'|'.$payload['phone_number']]);
		if (!empty($existsUser)) {
			$delivery->addError(409, 'Email or phone number already registered.');
		}

		if ($delivery->hasErrors()) {
			return $delivery;
		}

		return $delivery;
	}

	public function validateLogin (Delivery $delivery, $payload, UserContract $userRepository) {
		$existsUser = $userRepository->findOne(['email|phone_number' => $payload['username'].'|'.$payload['username']], true);
		if (empty($existsUser)) {
			$delivery->addError(400, 'Email or phone number or password is incorrect.');
			return $delivery;
		}

		if ($existsUser->password != convertToSalt($payload['password'])) {
			$delivery->addError(400, 'Email or phone number or password is incorrect.');
			return $delivery;	
		}

		return $delivery;
	}

}