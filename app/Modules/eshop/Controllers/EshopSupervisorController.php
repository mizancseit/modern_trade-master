<?php

namespace App\Modules\eshop\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect; 
use App\Modules\eshop\Models\Supervisor; 
use DB;
use Auth;
use Session;

class EshopSupervisorController extends Controller
{ 
    public function define_supervisor()
    {
        $selectedMenu    = 'Supervisor';                    // Required Variable for menu
        $selectedSubMenu = 'Supervisor List';           // Required Variable for menu
        $pageTitle       = 'Supervisor List'; // Page Slug Title

        $user_type = DB::table('tbl_user_type')->get();
        $management = DB::table('users')->where('user_type_id','5')->get();
        $managers = DB::table('users')->where('user_type_id','6')->get();
        $executive = DB::table('users')->where('user_type_id','3')->get();
        $officer = DB::table('users')->where('user_type_id','7')->get();

        $data = Supervisor::get();    
        return view('eshop::sales/supervisor/supervisor',compact('user_type','management','managers','executive','officer','selectedMenu','selectedSubMenu','pageTitle','data'));
    } 
    public function get_supervisor($id){
        $user_type = DB::table('tbl_user_type')->get();
        $management = DB::table('users')->where('user_type_id','5')->get();
        $managers = DB::table('users')->where('user_type_id','6')->get();
        $executive = DB::table('users')->where('user_type_id','3')->get();
        $officer = DB::table('users')->where('user_type_id','7')->get();
        $data = Supervisor::where('hierarchy_id',$id)->first(); 
        $supervisor = DB::table('users')->where('user_type_id',$data->supervisor_type)->get();
        //print_r($supervisor);
        return view('eshop::sales/supervisor/edit',compact('user_type','management','managers','executive','officer','supervisor','data')); 
    } 
    public function get_supervisor_list(Request $request)
    {
        $id=$request->input('id');

        $users=DB::table('users')
        ->where('user_type_id',$id)
        ->get(); 
        ?>
        <select class="form-control show-tick" name="supervisor_id" data-live-search="true" required="">
            <option value="">Select Name</option>
            <?php foreach($users as $user){ ?>
            <option id="ok" value="<?php echo $user->id; ?>"><?php echo $user->display_name.' ('.$user->email.')'; ?> </option>
            <?php } ?>
        </select> 
        <?php 
    }
    public function supervisor_save(Request $request)
    { 
        // 'hierarchy_id'    => $request->get('hierarchy_id'),
        DB::table('eshop_role_hierarchy')->insert([            
            'supervisor_id'   => $request->get('supervisor_id'),
            'supervisor_type' => $request->get('supervisor_type'),
            'management_id'   => $request->get('management_id'),
            'manager_id'      => $request->get('manager_id'),
            'executive_id'    => $request->get('executive_id'),
            'officer_id'      => $request->get('officer_id')]
        );  
        return redirect('/user_supervisor')->with('success','Supervisor add sucessfully.');
    }
    public function supervisor_edit(Request $request, $id )
    {  
        $data = Supervisor::where('hierarchy_id',$id)->first();                 
        $data->supervisor_id   = $request['supervisor_id'];
        $data->supervisor_type = $request['supervisor_type'];
        $data->management_id   = $request['management_id'];
        $data->manager_id      = $request['manager_id'];
        $data->executive_id    = $request['executive_id'];
        $data->officer_id      = $request['officer_id'];
        $data->update(); 
        return redirect('/user_supervisor')->with('success','Supervisor update sucessfully.');
    }

    public function supervisor_delete($id){        
        DB::table('eshop_role_hierarchy')->where('hierarchy_id',$id)->delete();
        return redirect('/user_supervisor')->with('success','Supervisor delete sucessfully.');
    }
     
}
