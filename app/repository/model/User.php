<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;
use Repository\Contract\UserContract;

class User extends Model implements UserContract {
    use SoftDeletes;
    protected $table = 'user';

    private function getQueryBuilder ($filters) {
        $query = DB::table($this->table.' AS u');
        $query = $this->addFilters($query, $filters);
        return $query;
    }

    private function modifySelectQuery ($query) {
        $query->select(
            'u.id as id',
            'u.name as name',
            'u.email as email',
            'u.phone_number as phone_number',
            'u.lifetime_cash_in_total as lifetime_cash_in_total',
            'u.lifetime_cash_out_total as lifetime_cash_out_total',
            'u.lifetime_total as lifetime_total',
            'u.created_at as created_at',
            'u.updated_at as updated_at',
            'u.deleted_at as deleted_at'
        );
        return $query;
    }

    private function addFilters ($query, $filters) {
        $equalFilter = [
            'id',
            'email',
            'phone_number',
            'email|phone_number'
        ];

        $query = $this->addEqualFilter($query, $filters, $equalFilter);
        $query->whereNull('deleted_at');

        return $query;
    }

    private function addEqualFilter ($query, $filters, $args) {
        foreach ($args as $arg) {
            if ($this->isAvailable($filters, $arg)) {
                if (strpos($arg, "|") !== false) {
                    $query->where(function ($queryLevel) use ($filters, $arg) {
                        $keys = explode("|", $arg);
                        $values = explode("|", $filters[$arg]);
                        foreach ($keys as $idx => $value) {
                            if ($idx == 0) {
                                $queryLevel->where($keys[$idx], $values[$idx]);
                            } else {
                                $queryLevel->orWhere($keys[$idx], $values[$idx]);
                            }
                        }
                    }); 
                } else {
                    $query->where($arg, '=', $filters[$arg]);        
                }
            }
        }

        return $query;
    }

    private function isAvailable ($filters, $key) {
        if (isset($filters[$key]) && !empty($filters[$key])) {
            return true;
        }
        return false;
    }

    public function find($filters = [], $plain = false) {
        $query = $this->getQueryBuilder($filters);
        if (!$plain) {
            $query = $this->modifySelectQuery($query);
        }
        return $query->get();
    }

    public function findOne($filters = [], $plain = false) {
        $query = $this->getQueryBuilder($filters);
        if (!$plain) {
            $query = $this->modifySelectQuery($query);
        }
        return $query->first();
    }

    public function create($data) {
        $newData = new User;
        foreach ($data as $key => $value) {
            $newData->{$key} = $value;
        }
        $newData->save();
        return $newData->id;
    }

    public function modify($filter, $data = []) {
        $query = $this->getQueryBuilder($filter);
        return $query->update($data);
    }
}