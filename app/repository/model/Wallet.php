<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;
use Repository\Contract\WalletContract;

class Wallet extends BaseModel implements WalletContract {
    use SoftDeletes;
    protected $table = 'wallet';

    public function __construct () {

    }

    protected function modifySelectQuery ($query) {
        $query->select(
            'wallet.id as id',
            'wallet.name as name',
            'wallet.lifetime_cash_in_total as lifetime_cash_in_total',
            'wallet.lifetime_cash_out_total as lifetime_cash_out_total',
            'wallet.lifetime_total as lifetime_total',
            'wallet.description as description',
            'wallet.created_at as created_at',
            'wallet.updated_at as updated_at'
        );
        return $query;
    }

    protected function addFilters ($query, $filters) {
        $equalFilter = [
            'id',
            'id_user',
            'name'
        ];
        $likeFilter = [
            'q' => ['name']
        ];

        $query = $this->addEqualFilter($query, $filters, $equalFilter);
        $query = $this->addLikeFilter($query, $filters, $likeFilter);
        $query->whereNull('deleted_at');

        return $query;
    }

    public function create($data) {
        $newData = new Wallet;
        foreach ($data as $key => $value) {
            $newData->{$key} = $value;
        }
        $newData->save();
        return $newData->id;
    }
}