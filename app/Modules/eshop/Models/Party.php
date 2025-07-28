<?php
 
namespace App\Modules\eshop\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{ 

    protected $table = 'eshop_party_list';    
    protected $primaryKey = 'party_id'; 
}
