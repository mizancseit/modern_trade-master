<?php
 
namespace App\Modules\eshop\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{ 
    protected $table = 'users';     
    protected $fillable = ['email', 'password', 'remember_token', 'display_name', 'employee_id', 'designation', 'user_type_id','business_type_id','module_type','sap_code','global_company_id' ,'doj','is_active','entry_by','entry_date','update_by','update_date'];  
}
