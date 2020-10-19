<?php
namespace Service\Entry;

use Psr\Http\Message\ServerRequestInterface as Request;
use Engine\Internal\Delivery;
use Repository\Contract\UserContract;

class EntryService {

	private $request;
	private $delivery;
	private $entryValidator;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
		$this->entryValidator = new EntryValidator;
	}

	public function register ($data, UserContract $userRepository) {
		$payload = $data;

		$this->delivery = $this->entryValidator->validateRegister($this->delivery, $payload, $userRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$data = [
			'email' => $payload['email'],
			// 'phone_number' => $payload['phone_number'],
			'password' => convertToSalt($payload['password']),
			'name' => $payload['name']
		];
		$newUserId = $userRepository->create($data);
		$newUser = $userRepository->findOne(['id' => $newUserId]);
		$this->delivery->data = $newUser;
		$this->delivery->success = true;
		return $this->delivery;
	}

	public function login ($data, UserContract $userRepository) {
		$payload = $data;

		$this->delivery = $this->entryValidator->validateLogin($this->delivery, $payload, $userRepository);
		if ($this->delivery->hasErrors())
			return $this->delivery;

		$existsUser = $userRepository->findOne(['username' => $payload['username']], true);
		$token = createToken($existsUser);

		$resp = [
			'token' => $token
		];

		$this->delivery->data = $resp;
		$this->delivery->success = true;
		return $this->delivery;
	}
}