<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class RejectreasonModel extends Model
{
     
    /**
    *
    * Create by Zubair Mahmudul Huq
    * Date : 28/01/2018
    * businessStatus = 0 active || 1 inactive
    *
    **/

    protected $table = 'ims_visit_reason';
    protected $fillable = ['reason', 'reason_type', 'user', 'reason_status'];
}
