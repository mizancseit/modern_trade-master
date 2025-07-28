<?php
 
namespace App\Modules\eshop\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{ 

    protected $table = 'eshop_customer_list';    
    protected $primaryKey = 'customer_id';
     
    public function supervisorname()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','supervisor_type');
    } 
    public function namagement()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','management_id');
    } 
    public function manager()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','manager_id');
    } 
    public function supervisor()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','supervisor_id');
    }
    public function executive()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','executive_id');
    }
    public function officer()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','officer_id');
    }
    public function userfo()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','fo_id');
    }
    public function userdetails()
    {
        return $this->hasOne('App\Modules\eshop\Models\User','id','fo_id');
    } 
}
