<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class DivisionModel extends Model
{
     
    /**
    *
    * Create by Zubair Mahmudul Huq
    * Date : 05/02/2018
    * businessStatus = 0 active || 1 inactive
    *
    **/
	public $timestamps = false;

    protected $table = 'tbl_division';
    //protected $fillable = ['div_code', 'div_name', 'div_status', 'company_id', 'create_user', 'create_date', 'update_user', 'update_date'];
    protected $fillable = ['div_code', 'div_name', 'div_status', 'create_user', 'create_date', 'update_user', 'update_date'];
}
