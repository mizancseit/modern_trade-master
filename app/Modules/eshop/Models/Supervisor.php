<?php
 
namespace App\Modules\eshop\Models;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{ 

    protected $table = 'eshop_role_hierarchy';    
    protected $primaryKey = 'hierarchy_id';
    protected $fillable = ['supervisor_id', 'supervisor_type', 'management_id', 'manager_id', 'executive_id', 'officer_id', 'status']; 
    
    public function supervisortype()
    {
        return $this->hasOne('App\Modules\eshop\Models\Usertype','user_type_id','supervisor_type');
    } 
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
}
