<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class MasterUploadModel extends Model
{
  	protected $table    = 'tbl_fo_target';
    protected $fillable = ['fo_id','cat_id','cat_name','qty','avg_value','total_value','start_date','end_date'];
}
