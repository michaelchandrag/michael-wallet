<?php
namespace Service\Entry;

use Psr\Http\Message\ServerRequestInterface as Request;
use Engine\Internal\Delivery;
use Repository\Contract\UserContract;

class EntryService {

	private $request;
	private $delivery;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
	}

	public function register (UserContract $userRepository) {
		$payload = $this->request->getParsedBody();

		if (!isset($payload['first_name']) || empty($payload['first_name'])) {
			$this->delivery->addError(400, 'First name should not be empty.');
		}

		if (!isset($payload['email']) || empty($payload['email'])) {
			$this->delivery->addError(400, 'Email should not be empty.');
		}

		if (!isset($payload['phone_number']) || empty($payload['phone_number'])) {
			$this->delivery->addError(400, 'Phone number should not be empty.');
		}

		if (empty($payload['password']) || ($payload['password'] != $payload['confirm_password'])) {
			$this->delivery->addError(400, 'Something error with the password.');
		}

		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$existsUser = $userRepository->findOne(['email|phone_number' => $payload['email'].'|'.$payload['phone_number']]);
		if (!empty($existsUser)) {
			$this->delivery->addError(409, 'Email or phone number already registered.');
		}

		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$data = [
			'email' => $payload['email'],
			'phone_number' => $payload['phone_number'],
			'password' => convertToSalt($payload['password']),
			'first_name' => $payload['first_name'],
			'last_name' => $payload['last_name']
		];
		$newUserId = $userRepository->create($data);
		$newUser = $userRepository->findOne(['id' => $newUserId]);
		$this->delivery->data = $newUser;
		$this->delivery->success = true;
		return $this->delivery;
	}

	public function login (UserContract $userRepository) {
		$payload = $this->request->getParsedBody();

		$existsUser = $userRepository->findOne(['email|phone_number' => $payload['username'].'|'.$payload['username']], true);
		if (empty($existsUser)) {
			$this->delivery->addError(400, 'Email or phone number or password is incorrect.');
			return $this->delivery;
		}

		if ($existsUser->password != convertToSalt($payload['password'])) {
			$this->delivery->addError(400, 'Email or phone number or password is incorrect.');
			return $this->delivery;	
		}

		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$this->delivery->data = $existsUser;
		$this->delivery->success = true;
		return $this->delivery;
	}
}