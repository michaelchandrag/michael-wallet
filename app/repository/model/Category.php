<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;
use Repository\Contract\CategoryContract;

class Category extends Model implements CategoryContract {
    use SoftDeletes;

    const TYPE_CASH_IN = 'cash_in';
    const TYPE_CASH_OUT = 'cash_out';

    protected $table = 'category';

    private function getQueryBuilder ($filters) {
        $query = DB::table($this->table.' AS c');
        $query = $this->addFilters($query, $filters);
        return $query;
    }

    private function modifySelectQuery ($query) {
        $query->select(
            'c.id as id',
            'c.name as name',
            'c.type as type',
            'c.lifetime_cash_in_total as lifetime_cash_in_total',
            'c.lifetime_cash_out_total as lifetime_cash_out_total',
            'c.lifetime_total as lifetime_total',
            'c.description as description',
            'c.created_at as created_at',
            'c.updated_at as updated_at'
        );
        return $query;
    }

    private function addFilters ($query, $filters) {
        $equalFilter = [
            'id',
            'id_user',
            'name'
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
        $newData = new Category;
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