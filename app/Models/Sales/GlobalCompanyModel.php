<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class GlobalCompanyModel extends Model
{
  /**
    *
    * Create by Zubair Mahmudul Huq
    * Date : 28/02/2018
    *
    **/
	public $timestamps = false;

    protected $table = 'tbl_global_company';
    protected $fillable = ['global_company_name', 'global_company_owner', 'global_company_email', 'global_company_phone',
	'global_company_address', 'is_active', 'created_by', 'created_at', 'updated_by', 'updated_at'];
}
