<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Hash;

use DB;
use Auth;
use Session;


class MasterSetupUser extends Controller
{
    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }

public function userCreate(Request $req)
{
  $selectedMenu   = 'usermgt';         // Required Variable
  $pageTitle      = 'user management';

  $route_id=$req->get('id');
  if($route_id)
{
$usersBasic=DB::select("select users.*,tbl_user_type.user_type_id,tbl_user_type.user_type,tbl_business_type.business_type_id,tbl_business_type.business_type,tbl_global_company.global_company_id as compid ,tbl_global_company.global_company_name
from users
LEFT JOIN tbl_user_type ON users.user_type_id=tbl_user_type.user_type_id
LEFT JOIN tbl_business_type ON users.business_type_id=tbl_business_type.business_type_id
LEFT JOIN tbl_global_company ON users.global_company_id=tbl_global_company.global_company_id
where users.id='$route_id'");

$userTypeList=DB::select("select * from tbl_user_type");

$businessType=DB::select("select * from tbl_business_type");

$company=DB::select("select * from tbl_global_company");

$division=DB::select("select * from tbl_division");
$terri=DB::select("select * from tbl_territory");
$userDetails=DB::select("select * from tbl_user_details where user_det_id='$route_id'");

//Business Scope 
$businessScope=DB::select("SELECT tbl_user_business_scope.*, tbl_division.div_id,tbl_division.div_name,tbl_territory.id as terriid,tbl_territory.name,tbl_point.point_id,tbl_point.point_name from tbl_user_business_scope LEFT JOIN tbl_division ON tbl_user_business_scope.division_id=tbl_division.div_id
LEFT JOIN tbl_territory ON tbl_user_business_scope.territory_id=tbl_territory.id
LEFT JOIN tbl_point ON tbl_user_business_scope.point_id=tbl_point.point_id where tbl_user_business_scope.business_scope_id='$route_id'");
//Business Scope 

echo json_encode(array(
'usersBasic' => $usersBasic,
'userTypeList'=>$userTypeList,
'busiType'=>$businessType,
'company'=>$company,
'scope'=>$businessScope,
'division'=>$division,
'terri'=>$terri,
'userDetails'=>$userDetails
));
}
else
{
//New add 250218 
 $user_id=Auth::user()->id;
 $user=DB::table('users')
                        ->where('id', $user_id)
                        ->first();

  $user_type=$user->user_type_id;

  if($user_type==4){
  $userType=DB::select("select * from tbl_user_type where user_type_id not in(1,2,3)"); 
  }
  else
  {
  $userType=DB::table('tbl_user_type') 
                     ->get(); 
     }     

     //New add 250218           
  $businessType=DB::table('tbl_business_type') 
                     ->get(); 
  $globalCompany=DB::table('tbl_global_company') 
                     ->get();   

   $companyList=DB::table('tbl_company') 
                     ->get();    

  $division=DB::table('tbl_division')->get();
  $point=DB::select("SELECT tbl_point.point_id,tbl_point.point_name,tbl_point.point_division,tbl_point.territory_id, tbl_point.territory_name,tbl_division.div_id,tbl_division.div_name from tbl_point JOIN tbl_division ON tbl_point.point_division=tbl_division.div_id ");

  $point_name=DB::table('tbl_point') 
                     ->get();              
 return view('Master.user_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)->with('userType',$userType)
                                 ->with('businessType',$businessType)->with('globalCompany',$globalCompany)->with('division',$division)->with('point',$point)->with('pointName',$point_name)->with('companyList',$companyList);
}

}
public function userSetup(Request $req)
{
  if ($req->isMethod('post')) 
 {
 
 $username=$req->input('email');
 $password=Hash::make($req->input('password'));
 $display_name=$req->input('display_name');
 $employee_id=$req->input('employee_id');
 $designation=$req->input('designation');
 $business_type_id=$req->input('business_type_id');
 $user_type_id=$req->input('user_type_id');
 $global_company_id=$req->input('global_company_id');
 $sap_code=$req->input('sap_code');
 $entry_by=$user_id=Auth::user()->id;
 $entry_date=date('Y-m-d H:i:s');
 $dojoin=$req->input('doj');
 $doj=date('Y-m-d',strtotime($dojoin));
 }
//echo $username;
 if($req->input('id'))
  {
    
$usersBasic=DB::table('users')->where('id',$req->get('id'))->update(
            [
               
                'email'                 => $username,
                'display_name'          => $display_name,
                'employee_id'           => $employee_id,
                'designation'           =>$designation,
              
                'business_type_id'      =>$business_type_id,
                'user_type_id'          =>$user_type_id,
                'global_company_id'     =>$global_company_id,
                'update_by'              =>$entry_by,
                'update_date'            =>$entry_date,
                'doj'                   =>$doj,
                 'password'             =>$password
               
            ]
        ); 

return Redirect::to('/userCreate')->with('success', 'Successfully Updated User Basic');
  }

else
{
      $req->session()->put('username', $username);

      $user_basic=DB::insert('insert into users (   display_name,employee_id,sap_code,email,password,user_type_id,business_type_id,designation,global_company_id,entry_by,entry_date,doj) values (?,?,?,?,?,?,?,?,?,?,?,?)', [$display_name,$employee_id,$sap_code,$username,$password,$user_type_id,$business_type_id,$designation,$global_company_id,$entry_by,$entry_date,$doj]);
     $userName=$req->session()->get('username');
     $user=DB::table('users')
           ->where('email',$userName)
           ->first();
    // dd($user);
     $user_id=$user->id;
     //dd($user_id);
    
    $user_basic=DB::insert('insert into tbl_user_details(user_id,registration_date)values(?,?)',[$user_id,$entry_date]);
    $user_business_scope=DB::insert('insert into tbl_user_business_scope(user_id)values(?)',[$user_id]);
  return Redirect::to('/userCreate')->with('success', 'Successfully User created.Please insert Business Scope now');
}
}

public function userDetails(Request $req)
{
  if ($req->isMethod('post')) 
 {
 
 $first_name=$req->input('first_name');
 $middle_name=$req->input('middle_name');
 $last_name=$req->input('last_name');
 $sap_code=$req->input('sap_code');
 $land_phone=$req->input('land_phone');
 $cell_phone=$req->input('cell_phone');
 $current_address=$req->input('current_address');
 $permanent_address=$req->input('permanent_address');
 $update_by=$user_id=Auth::user()->id;
 $update_date=date('Y-m-d H:i:s');
 $email=$req->input('email');
 $dob=$req->input('dob');
 $user_id=$req->input('user_id');
 $global_company_id=1;
 $owner_name=$req->input('owner_name');
 }

 if($req->input('id'))
  {
     $userDetails=DB::table('tbl_user_details')->where('user_det_id',$req->get('id'))->update(
            [
               
                'first_name'            => $first_name,
                'middle_name'           => $middle_name,
                'last_name'             => $last_name,
                'owner_name'            =>$owner_name,
              
                'land_phone'            =>$land_phone,
                'cell_phone'            =>$cell_phone,

                'current_address'       =>$current_address,
                'permanent_address'     =>$permanent_address,
                'permanent_address'      =>$permanent_address,
                'update_by'             =>$update_by,
                'update_date'           =>$update_date,
                'dob'                   =>$dob,
                'sap_code'              =>$sap_code,
                'email'                 =>$email
                 
               
            ]
        ); 

return Redirect::to('/userCreate')->with('success', 'Successfully Updated User Details');
  }

else
{
  $user_details=DB::update("UPDATE tbl_user_details set first_name='$first_name',middle_name='$middle_name',last_name='$last_name',owner_name='$owner_name',sap_code='$sap_code',
                           land_phone='$land_phone',cell_phone='$cell_phone',current_address='$current_address',permanent_address='$permanent_address',email='$email', dob='$dob',update_by='$update_by',update_date='$update_date',global_company_id='$global_company_id' where user_id='$user_id'");
return Redirect::to('/userCreate')->with('success', 'Successfully User Deatils created.');
}

}


public function userbasic_delete(Request $request)
    {
        $id  = $request->get('id');

        $username=DB::select("select * from users where id='$id'");
       foreach($username as $name)  
       {
        $userName=$name->id;
       }
        
        
       $userDetails= DB::table('tbl_user_details')->where('user_id',$userName)->delete();
       
       $userScope= DB::table('tbl_user_business_scope')->where('user_id',$userName)->delete(); 

       $userDel= DB::table('users')->where('id',$request->get('id'))->delete();
        
       return "Successfully deleted users.";
      //return Redirect::to('/newRoute')->with('success', 'Successfully deleted Route.');
      
    }


public function user_scope(Request $req)
{
  if ($req->isMethod('post')) 
 {
 
 $point_id=$req->input('point_id');

 $division_id=$req->input('division_id');
 $territory_name=$req->input('name');
 $user_id1=$req->input('user_id1');
 //echo $territory_name;exit;

 $entry_by=$user_id=Auth::user()->id;
 $entry_date=date('Y-m-d H:i:s');
 $global_company_id=1;
 $point_name=DB::select("select * from tbl_point where point_id=$point_id");


foreach($point_name as $point_names)
{
  $pointName=$point_names->point_name;
}
$territory_id=DB::select("select * from tbl_territory where name='$territory_name'");
foreach($territory_id as $territory_no)
{
  $terri_id=$territory_no->id;

}

/*echo $point_id;
echo $division_id;
echo $user_id1;
exit;*/

 if($req->input('id'))
  {
   // echo $territory_name;exit;
DB::table('tbl_user_business_scope')->where('business_scope_id',$req->input('id'))->update(
            [
               
                'point_id'             => $point_id,
                'point_name'           => $pointName,
                'division_id'          =>$division_id,
                'global_company_id'    =>$global_company_id,
                'update_by'             =>$entry_by,
                'update_date'           =>$entry_date,
                'territory_id'         =>$terri_id 
               
            ]
        ); 
return Redirect::to('/userCreate')->with('success', 'Successfully Updated User Scope');
  

  }

else
{
 DB::table('tbl_user_business_scope')->where('user_id',$req->input('user_id1'))->update(
            [
               
                'point_id'             => $point_id,
                'point_name'           => $pointName,
                'division_id'          =>$division_id,
                'global_company_id'    =>$global_company_id,
                'update_by'             =>$entry_by,
                'update_date'           =>$entry_date,
                'territory_id'         =>$terri_id 
               
            ]
        ); 

 
 
 return Redirect::to('/userCreate')->with('success', 'Successfully User Scope created.');
}

}
}
//MAung Get Points 280218
public function get_points(Request $req)
{
$id=$req->input('id');
//$req->session()->put('terrid', $id);
//echo $id; exit;

$points=DB::table('tbl_point')
           ->where('territory_id',1)
           ->get();



           return view('Master.getPoint')->with('points',$points);
}

}

