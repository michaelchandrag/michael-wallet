<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;

abstract class BaseModel extends Model {
	use SoftDeletes;
	protected $table = 'unknown';

	public function __construct () {

	}

	private function addPagination ($query, $filters) {
        // count all data
        $totalData = $query->count();

        $offset = 0; // start of data
        $limit = 10; // end of data
        $page = 1; // current page
        if (isset($filters['data']) && (int)$filters['data'] > 0) {
            $limit = (int)$filters['data'];
        }
        if (isset($filters['page']) && (int)$filters['page'] > 1) {
            $page = (int)$filters['page'];
            $offset = $page*$limit;
        }
        $query->limit($limit);
        $query->offset($offset);
        $totalPage = ceil($totalData/$limit);
        return [
            $this->table => $query->get(),
            'current_page' => $page,
            'limit_data' => $limit,
            'total_page' => $totalPage,
            'total_data' => $totalData
        ];
    }

    private function getQueryBuilder ($filters) {
        $query = DB::table($this->table);
        $query = $this->addFilters($query, $filters);
        return $query;
    }

    protected function addEqualFilter ($query, $filters, $args) {
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

    protected function isAvailable ($filters, $key) {
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
        $result = $this->addPagination($query, $filters);
        return $result;
    }

    public function findOne($filters = [], $plain = false) {
        $query = $this->getQueryBuilder($filters);
        if (!$plain) {
            $query = $this->modifySelectQuery($query);
        }
        return $query->first();
    }

    public function modify($filter, $data = []) {
        $query = $this->getQueryBuilder($filter);
        return $query->update($data);
    }

}