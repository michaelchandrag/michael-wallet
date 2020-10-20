<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;
use Engine\Internal\Delivery;
use Repository\Contract\CategoryContract;
use Repository\Contract\UserContract;
use Repository\Contract\TransactionContract;
use Service\Me\Validator\MeCategoryValidator;

class MeCategoryService {

	private $request;
	private $delivery;
	private $validator;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
		$this->validator = new MeCategoryValidator;
	}

	public function getCategory ($user, CategoryContract $categoryRepository) {
		$filters = $this->request->getQueryParams();
		$filters['id_user'] = $user->id;
		$category = $categoryRepository->find($filters);
		$this->delivery->data = $category;
		return $this->delivery;
	}

	public function createCategory ($payload, $user, CategoryContract $categoryRepository, UserContract $userRepository) {
		$this->delivery = $this->validator->validateCreateCategory($this->delivery, $payload, $userRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		try {
			DB::beginTransaction();
			
			$newCategoryId = $categoryRepository->create($payload);
			$newCategory = $categoryRepository->findOne(['id' => $newCategoryId]);
			$this->delivery->data = $newCategory;
			$this->delivery->success = true;
			
			DB::commit();
		} catch (\Exception $e) {
			$this->delivery->addError(500, 'Internal Server Error');
			DB::rollBack();
		}

		return $this->delivery;
	}

	public function updateCategory ($categoryId, $user, $payload, CategoryContract $categoryRepository, UserContract $userRepository) {
		$this->delivery = $this->validator->validateUpdateCategory($this->delivery, $categoryId, $user, $payload, $categoryRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		$filterAction = [
			'id' => $categoryId,
			'id_user' => $user->id
		];

		try {
			DB::beginTransaction();
			
			$action = $categoryRepository->modify($filterAction, $payload);
			$category = $categoryRepository->findOne($filterAction);

			$this->delivery->data = $category;
			
			DB::commit();
		} catch (\Exception $e) {
			$this->delivery->addError(500, 'Internal Server Error');
			DB::rollBack();
		}
		return $this->delivery;
	}

	public function updateReport ($user, $idCategory, UserContract $userRepository, CategoryContract $categoryRepository, TransactionContract $transactionRepository) {
		$payload = [
			'lifetime_cash_in_total' => 0,
			'lifetime_cash_out_total' => 0,
			'lifetime_total' => 0,
			'monthly_cash_in_total' => 0,
			'monthly_cash_out_total' => 0,
			'monthly_total' => 0
		];
		
		$lifetimeFilters = [
			'id_category' => $idCategory,
			'id_user' => $user->id
		];
		$lifetimeReport = $transactionRepository->fetchByCategoryType($lifetimeFilters);
		if (isset($lifetimeReport['cash_in'])) {
			$payload['lifetime_cash_in_total'] = $lifetimeReport['cash_in'];
		}
		if (isset($lifetimeReport['cash_out'])) {
			$payload['lifetime_cash_out_total'] = $lifetimeReport['cash_out'];
		}

		$monthlyFilters = [
			'id_category' => $idCategory,
			'id_user' => $user->id,
			'from_transaction_at' => date('Y-m-01 00:00:00'),
			'until_transaction_at' => date('Y-m-t 23:59:59')
		];

		$monthlyReport = $transactionRepository->fetchByCategoryType($monthlyFilters);
		if (isset($monthlyReport['cash_in'])) {
			$payload['monthly_cash_in_total'] = $monthlyReport['cash_in'];
		}
		if (isset($monthlyReport['cash_out'])) {
			$payload['monthly_cash_out_total'] = $monthlyReport['cash_out'];
		}

		$payload['lifetime_total'] = $payload['lifetime_cash_in_total'] - $payload['lifetime_cash_out_total'];
		$payload['monthly_total'] = $payload['monthly_cash_in_total'] - $payload['monthly_cash_out_total'];
		$this->updateCategory($idCategory, $user, $payload, $categoryRepository, $userRepository);
		return true;
	}
}