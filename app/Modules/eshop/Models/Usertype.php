<?php
 
namespace App\Modules\eshop\Models;

use Illuminate\Database\Eloquent\Model;

class Usertype extends Model
{ 
    protected $table = 'tbl_user_type';     
    protected $fillable = ['user_type_id', 'user_type', 'global_company_id', 'is_active', 'executive_id', 'officer_id', 'status'];  
}
