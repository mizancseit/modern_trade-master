<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class DistributorStockUploadModel extends Model
{
    protected $table    = 'distributor_inventory';
    protected $fillable = ['point_id','cat_id','product_id','product_qty','inventory_date','inventory_type','global_company_id'];
}
