<?php
 
namespace App\Modules\eshop\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{ 

    protected $table = 'eshop_order';    
    protected $primaryKey = 'order_id';
    
    public function orderdetails()
    {
        return $this->hasMany('App\Modules\eshop\Models\OrderDetails','order_id','order_id');
    } 

    public function customer()
    {
        return $this->hasOne('App\Modules\eshop\Models\Customer','customer_id','customer_id');
    } 
    public function party()
    {
        return $this->hasOne('App\Modules\eshop\Models\Party','party_id','party_id');
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

