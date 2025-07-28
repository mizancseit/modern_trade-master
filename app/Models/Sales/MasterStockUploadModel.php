<?php

namespace App\Models\sales;

use Illuminate\Database\Eloquent\Model;

class MasterStockUploadModel extends Model
{
    protected $table    = 'depot_inventory';
    protected $fillable = ['point_id','cat_id','product_id','product_qty','inventory_date','inventory_type','global_company_id'];
}
