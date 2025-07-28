<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class RegistrationModel extends Model
{
     
    /**
    *
    * Create by Md. Masud Rana
    * Date : 04/12/2017
    * businessStatus = 0 active || 1 inactive
    *
    **/

    protected $table = 'users';
    protected $fillable = ['businessOwnerName', 'businessName', 'email', 'password', 'businessPhone', 'businessStatus', 'businessAgreeStatus', 'created_at'];
}
