<?php
 
namespace App\Modules\eshop\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{ 
    protected $table = 'eshop_order_details';    
    protected $primaryKey = 'order_det_id';  
}

