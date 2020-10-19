<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;
use Repository\Contract\TransactionContract;

class Transaction extends BaseModel implements TransactionContract {
    use SoftDeletes;

    protected $table = 'transaction';

    protected function modifySelectQuery ($query) {
        $query->select(
            'transaction.id as id',
            'transaction.id_user as id_user',
            'transaction.id_category as id_category',
            'category.name as category_name',
            'category.type as category_type',
            'transaction.id_wallet as id_wallet',
            'wallet.name as wallet_name',
            'transaction.amount as amount',
            'transaction.description as description',
            'transaction.created_at as created_at',
            'transaction.updated_at as updated_at'
        );
        return $query;
    }

    protected function addFilters ($query, $filters) {
        $query->join('wallet', 'wallet.id', '=', 'transaction.id_wallet');
        $query->join('category', 'category.id', '=', 'transaction.id_category');

        $equalFilter = [
            'transaction.id',
            'transaction.id_user',
            'transaction.id_wallet',
            'transaction.id_category'
        ];

        $query = $this->addEqualFilter($query, $filters, $equalFilter);
        $query->whereNull('transaction.deleted_at');

        return $query;
    }

    public function create($data) {
        $newData = new Transaction;
        foreach ($data as $key => $value) {
            $newData->{$key} = $value;
        }
        $newData->save();
        return $newData->id;
    }

    public function fetchByCategoryType ($filters = []) {
        $query = DB::table($this->table);
        $query->select(
            'category.type as type',
            DB::raw('SUM(transaction.amount) as total')
        );
        $query->join('wallet', 'wallet.id', '=', 'transaction.id_wallet');
        $query->join('category', 'category.id', '=', 'transaction.id_category');
        $query->join('user', 'user.id', '=', 'transaction.id_user');
        $query = $this->addFilters($query, $filters);
        $query->groupBy('category.type');
        $result = [];
        foreach ($query->get() as $data) {
            $result[$data->type] = $data->total;
        }
        return $result;
    }
}