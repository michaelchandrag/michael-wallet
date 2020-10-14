<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;
use Repository\Contract\TransactionContract;

class Transaction extends Model implements TransactionContract {
    use SoftDeletes;

    protected $table = 'transaction';

    private function getQueryBuilder ($filters) {
        $query = DB::table($this->table.' AS t');
        $query = $this->addFilters($query, $filters);
        return $query;
    }

    private function modifySelectQuery ($query) {
        $query->select(
            't.id as id',
            't.id_user as id_user',
            't.id_category as id_category',
            't.id_wallet as id_wallet',
            't.amount as amount',
            't.description as description',
            't.created_at as created_at',
            't.updated_at as updated_at'
        );
        return $query;
    }

    private function addFilters ($query, $filters) {
        $equalFilter = [
            't.id',
            't.id_user',
            't.id_wallet',
            't.id_category'
        ];

        $query = $this->addEqualFilter($query, $filters, $equalFilter);
        $query->whereNull('t.deleted_at');

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
        $result = $query->first();
        if (!empty($result)) {
            $wallet = new Wallet;
            $result->wallet = $wallet->findOne(['id' => $result->id_wallet]); 

            $category = new Category;
            $result->category = $category->findOne(['id' => $result->id_category]);
        }
        return $result;
    }

    public function create($data) {
        $newData = new Transaction;
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

    public function fetchByCategoryType ($filters = []) {
        $query = DB::table($this->table.' as t');
        $query->select(
            'c.type as type',
            DB::raw('SUM(t.amount) as total')
        );
        $query->join('wallet as w', 'w.id', '=', 't.id_wallet');
        $query->join('category as c', 'c.id', '=', 't.id_category');
        $query->join('user as u', 'u.id', '=', 't.id_user');
        $query = $this->addFilters($query, $filters);
        $query->groupBy('c.type');
        $result = [];
        foreach ($query->get() as $data) {
            $result[$data->type] = $data->total;
        }
        return $result;
    }
}