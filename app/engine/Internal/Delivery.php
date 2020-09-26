<?php
namespace Engine\Internal;

class Delivery {

	public $data;
	public $errors;
	public $success;
	public $statusCode;

	public function __construct () {
		$this->data = [];
		$this->errors = [];
		$this->success = true;
		$this->statusCode = 200;
	}

	public function addError ($statusCode, $detail) {
		$error = [
			'code' => $statusCode,
			'detail' => $detail
		];
		$this->errors[] = $error;
		$this->success = false;
		$this->statusCode = $statusCode;
	}

	public function hasErrors () {
		if (count($this->errors) > 0) {
			return true;
		}
		return false;
	}

}