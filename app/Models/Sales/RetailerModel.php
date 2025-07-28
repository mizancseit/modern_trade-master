<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class RetailerModel extends Model
{
     
    /**
    *
    * Create by Zubair Mahmudul Huq
    * Date : 12/02/2018
    * businessStatus = 0 active || 1 inactive
    *
    **/
	public $timestamps = false;

    protected $table = 'tbl_retailer';
    protected $fillable = ['name', 'division', 'territory', 'rid', 'global_company_id', 'shop_type', 'owner', 'mobile', 'tnt', 
	'email', 'dateandtime', 'user', 'status', 'dob', 'vAddress', 'serial'];
}
