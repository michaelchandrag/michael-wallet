<?php
namespace Service\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Engine\Internal\Delivery;
use Repository\Contract\UserContract;
use Repository\Contract\WalletContract;
use Repository\Contract\TransactionContract;

class MeService {

	private $request;
	private $delivery;

	public function __construct (Request $request, Delivery $delivery) {
		$this->request = $request;
		$this->delivery = $delivery;
	}

	public function getMe ($user, UserContract $userRepository, WalletContract $walletRepository, TransactionContract $transactionRepository) {
		$me = $userRepository->findOne(['id' => $user->id]);
		$wallets = $walletRepository->find(['id_user' => $user->id]);
		$transactions = $transactionRepository->find(['id_user' => $user->id]);
		$me->wallets = $wallets;
		$me->transactions = $transactions;
		$this->delivery->data = $me;
		return $this->delivery;
	}

	/* public function updateReport ($user, UserContract $userRepository, TransactionContract $transactionRepository) {
		$payload = [
			'lifetime_cash_in_total' => 0,
			'lifetime_cash_out_total' => 0,
			'lifetime_total' => 0
		];

		$filters = [
			't.id_user' => $user->id
		];
		$lifetimeReport = $transactionRepository->fetchByCategoryType($filters);
		if (isset($lifetimeReport['cash_in'])) {
			$payload['lifetime_cash_in_total'] = $lifetimeReport['cash_in'];
		}
		if (isset($lifetimeReport['cash_out'])) {
			$payload['lifetime_cash_out_total'] = $lifetimeReport['cash_out'];
		}

		$payload['lifetime_total'] = $payload['lifetime_cash_in_total'] - $payload['lifetime_cash_out_total'];
		$this->updateWallet($walletId, $user, $payload, $walletRepository, $userRepository);
		return true;
	} */
}