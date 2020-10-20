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
        $query = $this->addSort($query, $filters);
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
        foreach ($args as $argKey => $argValue) {
            if ($this->isAvailable($filters, $argKey)) {
                if (is_array($argValue)) {
                    $query->where(function ($queryLevel) use ($filters, $argKey, $argValue) {
                        foreach ($argValue as $idx => $value) {
                            if ($idx == 0) {
                                $queryLevel->where($value, '=', $filters[$argKey]);
                            } else {
                                $queryLevel->orWhere($value, '=', $filters[$argKey]);
                            }
                        }
                    });
                } else {
                    $query->where($argValue, '=', $filters[$argKey]);
                }
            }
        }

        return $query;
    }

    protected function addLikeFilter ($query, $filters, $args) {
        foreach ($args as $argKey => $argValue) {
            if ($this->isAvailable($filters, $argKey)) {
                if (is_array($argValue)) {
                    $query->where(function ($queryLevel) use ($filters, $argKey, $argValue) {
                        foreach ($argValue as $idx => $value) {
                            if ($idx == 0) {
                                $queryLevel->where($value, 'like', '%'.$filters[$argKey].'%');
                            } else {
                                $queryLevel->orWhere($value, 'like', '%'.$filters[$argKey].'%');
                            }
                        }
                    });
                } else {
                    $query->where($argValue, 'like', '%'.$filters[$argKey].'%');
                }
            }
        }

        return $query;
    }

    protected function addCustomFilter ($query, $filters, $args) {
        foreach ($args as $argKey => $argValue) {
            if ($this->isAvailable($filters, $argKey)) {
                $value = (isset($argValue['prefixValue']) ? $argValue['prefixValue'] : '') . $filters[$argKey] . (isset($argValue['postfixValue']) ? $argValue['postfixValue'] : '');
                if (is_array($argValue['column'])) {
                    $query->where(function ($queryLevel) use ($filters, $argKey, $argValue, $value) {
                        foreach ($argValue['column'] as $idx => $column) {
                            if ($idx == 0) {
                                $queryLevel->where($column, $argValue['condition'], $value);
                            } else {
                                $queryLevel->orWhere($column, $argValue['condition'], $value);
                            }
                        }
                    });
                } else {
                    $query->where($argValue['column'], $argValue['condition'], $value);
                }
            }
        }

        return $query;
    }

    protected function addSort ($query, $filters) {
        // sort_key , sort_value
        $sortKey = 'created_at';
        $sortValue = 'desc';
        if ($this->isAvailable($filters, 'sort_key')) {
            $sortKey = $filters['sort_key'];
        }
        if ($this->isAvailable($filters, 'sort_value')) {
            $sortValue = $filters['sort_value'];
        }

        $query->orderBy($this->table.'.'.$sortKey, $sortValue);
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