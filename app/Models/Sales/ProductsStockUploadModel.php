<?php

namespace App\Models\sales;

use Illuminate\Database\Eloquent\Model;

class ProductsStockUploadModel extends Model
{
    protected $table    = 'tbl_sap_stock';
    protected $fillable = ['dDate','company_code','company_name','plant','material_no','material_desc','stock_qty','global_company_id'];
}
