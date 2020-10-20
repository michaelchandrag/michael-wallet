<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;
use Engine\Internal\Delivery;
use Repository\Contract\WalletContract;
use Repository\Contract\UserContract;
use Repository\Contract\TransactionContract;
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
		$filters = $this->request->getQueryParams();
		$filters['id_user'] = $user->id;
		$wallet = $walletRepository->find($filters);
		$this->delivery->data = $wallet;
		return $this->delivery;
	}

	public function createWallet ($payload, $user, WalletContract $walletRepository, UserContract $userRepository) {
		$this->delivery = $this->validator->validateCreateWallet($this->delivery, $payload, $userRepository);
		if ($this->delivery->hasErrors()) {
			return $this->delivery;
		}

		try {
			DB::beginTransaction();

			$newWalletId = $walletRepository->create($payload);
			$newWallet = $walletRepository->findOne(['id' => $newWalletId]);	
			$this->delivery->data = $newWallet;
			$this->delivery->success = true;

			DB::commit();
		} catch (\Exception $e) {
			$this->delivery->addError(500, 'Internal Server Error');
			DB::rollBack();
		}

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

		try {
			DB::beginTransaction();
	
			$action = $walletRepository->modify($filterAction, $payload);
			$wallet = $walletRepository->findOne($filterAction);

			$this->delivery->data = $wallet;		
			
			DB::commit();
		} catch (\Exception $e) {
			$this->delivery->addError(500, 'Internal Server Error');
			DB::rollBack();
		}
		return $this->delivery;
	}

	public function updateReport ($user, $idWallet, UserContract $userRepository, WalletContract $walletRepository, TransactionContract $transactionRepository) {
		$payload = [
			'lifetime_cash_in_total' => 0,
			'lifetime_cash_out_total' => 0,
			'lifetime_total' => 0,
			'monthly_cash_in_total' => 0,
			'monthly_cash_out_total' => 0,
			'monthly_total' => 0
		];

		$lifetimeFilters = [
			'id_wallet' => $idWallet,
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
			'id_wallet' => $idWallet,
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
		$this->updateWallet($idWallet, $user, $payload, $walletRepository, $userRepository);
		return true;
	}
}