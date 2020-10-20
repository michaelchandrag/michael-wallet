<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;
use Repository\Contract\UserContract;

class User extends BaseModel implements UserContract {
    use SoftDeletes;
    protected $table = 'user';

    public function __construct () {

    }

    protected function modifySelectQuery ($query) {
        $query->select(
            'user.id as id',
            'user.name as name',
            'user.email as email',
            'user.phone_number as phone_number',
            'user.lifetime_cash_in_total as lifetime_cash_in_total',
            'user.lifetime_cash_out_total as lifetime_cash_out_total',
            'user.lifetime_total as lifetime_total',
            'user.created_at as created_at',
            'user.updated_at as updated_at',
            'user.deleted_at as deleted_at'
        );
        return $query;
    }

    protected function addFilters ($query, $filters) {
        $availableFilter = [
            'id' => [
                'column' => 'user.id',
                'condition' => '='
            ],
            'email' => [
                'column' => 'user.email',
                'condition' => '='
            ],
            'phone_number' => [
                'column' => 'user.phone_number',
                'condition' => '='
            ],
            'username' => [
                'column' => ['user.phone_number', 'user.email'],
                'condition' => '='
            ],
            'q' => [
                'column' => ['name'],
                'condition' => 'like',
                'prefixValue' => '%',
                'postfixValue' => '%'
            ]
        ];

        $query = $this->addCustomFilter($query, $filters, $availableFilter);
        $query->whereNull('deleted_at');

        return $query;
    }

    public function create($data) {
        $newData = new User;
        foreach ($data as $key => $value) {
            $newData->{$key} = $value;
        }
        $newData->save();
        return $newData->id;
    }
}