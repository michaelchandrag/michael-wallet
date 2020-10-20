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
            'category.monthly_cash_in_total as monthly_cash_in_total',
            'category.monthly_cash_out_total as monthly_cash_out_total',
            'category.monthly_total as monthly_total',
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
        $availableFilter = [
            'id' => [
                'column' => 'category.id',
                'condition' => '='
            ],
            'id_user' => [
                'column' => 'category.id_user',
                'condition' => '='
            ],
            'name' => [
                'column' => 'category.name',
                'condition' => '='
            ],
            'type' => [
                'column' => 'category.type',
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
        $newData = new Category;
        foreach ($data as $key => $value) {
            $newData->{$key} = $value;
        }
        $newData->save();
        return $newData->id;
    }
}