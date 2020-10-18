<?php
namespace Repository\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as DB;
use Repository\Contract\CategoryContract;

class Category extends BaseModel implements CategoryContract {
    use SoftDeletes;

    const TYPE_CASH_IN = 'cash_in';
    const TYPE_CASH_OUT = 'cash_out';

    protected $table = 'category';

    protected function modifySelectQuery ($query) {
        $query->select(
            'category.id as id',
            'category.name as name',
            'category.type as type',
            'category.lifetime_cash_in_total as lifetime_cash_in_total',
            'category.lifetime_cash_out_total as lifetime_cash_out_total',
            'category.lifetime_total as lifetime_total',
            'category.description as description',
            'category.created_at as created_at',
            'category.updated_at as updated_at'
        );
        return $query;
    }

    protected function addFilters ($query, $filters) {
        $equalFilter = [
            'id',
            'id_user',
            'name'
        ];

        $query = $this->addEqualFilter($query, $filters, $equalFilter);
        $query->whereNull('deleted_at');

        return $query;
    }

    public function create($data) {
        $newData = new Category;
        foreach ($data as $key => $value) {
            $newData->{$key} = $value;
        }
        $newData->save();
        return $newData->id;
    }
}