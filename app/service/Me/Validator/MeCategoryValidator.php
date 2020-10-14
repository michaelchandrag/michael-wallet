<?php
namespace Service\Me\Validator;

use Repository\Contract\UserContract;
use Repository\Contract\CategoryContract;
use Engine\Internal\Delivery;
use Repository\Model\Category;

class MeCategoryValidator {

	public function __construct () {

	}

	public function validateCreateCategory (Delivery $delivery, $payload, UserContract $userRepository) {
		if (!isset($payload['name'])) {
			$delivery->addError(400, 'Category name should not be empty.');
		}

		if (!isset($payload['type'])) {
			$delivery->addError(400, 'Category type should not be empty.');
		}

		if ($delivery->hasErrors()) {
			return $delivery;
		}

		if (!in_array($payload['type'], [Category::TYPE_CASH_IN, Category::TYPE_CASH_OUT])) {
			$delivery->addError(400, 'Category type is invalid');
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

	public function validateUpdateCategory (Delivery $delivery, $categoryId, $user, $payload, CategoryContract $categoryRepository) {
		$filter = [
			'id' => $categoryId,
			'id_user' => $user->id
		];
		$existsCategory = $categoryRepository->findOne($filter);
		if (empty($existsCategory)) {
			$delivery->addError(400, 'Category not found.');
		}

		if (isset($payload['type']) && !in_array($payload['type'], [Category::TYPE_CASH_IN, Category::TYPE_CASH_OUT])) {
			$delivery->addError(400, 'Category type is invalid');
		}

		return $delivery;
	}

}