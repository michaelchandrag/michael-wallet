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
        $equalFilter = [
            'id' => 'user.id',
            'email' => 'user.email',
            'phone_number' => 'user.phone_number',
            'username' => ['user.email', 'user.phone_number']
        ];
        $likeFilter = [
            'q' => ['user.name']
        ];

        $query = $this->addEqualFilter($query, $filters, $equalFilter);
        $query = $this->addLikeFilter($query, $filters, $likeFilter);
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