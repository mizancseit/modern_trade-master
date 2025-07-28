<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
/* Load Model Reference Begin */
use App\Models\Sales\RejectreasonModel;
use App\Models\Sales\DivisionModel;
use App\Models\Sales\RetailerModel;
/* Load Model Reference End */
use Hash;

use DB;
use Auth;
use Session;


class MasterSetupCon extends Controller
{
    public function __construct()
    {
       $this->middleware('auth'); // for auth check       
    }
public function company_setup()
{
	
		$selectedMenu   = 'company';         // Required Variable
        $pageTitle      = 'Company List';         // Required Variable
      

		 $company=DB::table('tbl_company')
                        ->select('tbl_company.id','tbl_company.sap_code','tbl_company.name','tbl_company.mobile','tbl_company.address','tbl_company.tnt',
                        	'tbl_global_company.global_company_name')

                        ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'tbl_company.global_company_id')
                        
                        ->get();
                        //dd($company);
          return view('Master.company_setup')->with('company',$company)->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu);            
}

public function company_process(Request $req)
{
 if ($req->isMethod('post')) 
 {
$selectedMenu   = 'company';         // Required Variable
$pageTitle      = 'Add Company';         // Required Variable
      
 $sap_code=$req->input('sap_code');
 $name=$req->input('name');
 $address=$req->input('address');
 $mobile=$req->input('mobile');
 $tnt=$req->input('tnt');
 $user_id=Auth::user()->id;
 $user=DB::table('users')
                        ->where('id', $user_id)
                        ->first();
$userName=$user->email;
$global_company_id=DB::table('tbl_user_details')
                        ->where('user_det_id', $user_id)
                        ->first();
                       
$global_company_id=$global_company_id->global_company_id;



 $company=DB::insert('insert into tbl_company (sap_code, name,address,mobile,tnt,user,global_company_id) values (?,?,?,?,?,?,?)', [$sap_code,$name,$address,$mobile,$tnt,$userName,
                                                                                                                                $global_company_id]);
         return Redirect::to('/company')->with('success', 'Successfully company created.');
       
}
}
public function company_edit(Request $req)
{
$selectedMenu   = 'company';         // Required Variable
$pageTitle      = 'Edit Company'; 
$slID=$req->get('id');
$id=DB::table('tbl_company')
                        ->where('id',$slID)
                        ->first();
 return view('Master.company_edit')->with('id',$id)->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu); 

}
public function company_edit_process(Request $req)
{
if ($req->isMethod('post')) 
 {
$selectedMenu   = 'company';         // Required Variable
$pageTitle      = 'Edit Company';         // Required Variable
      
 $sap_code=$req->input('sap_code');
 $name=$req->input('name');
 $address=$req->input('address');
 $mobile=$req->input('mobile');
 $tnt=$req->input('tnt');
 $user_name=$req->input('user');
 $company= DB::table('tbl_company')->where('id',$req->get('id'))->update(
            [
                'sap_code'         => $sap_code,
                'name'             => $name,
                'address'          => $address,
                'mobile'           => $mobile,
                'tnt'              => $tnt,
                'user'             => $user_name
            ]
        ); 
  
 return Redirect::to('/company')->with('success', 'Successfully Updated  Company.');
}

}

public function company_delete(Request $request)
    {
        $id  = $request->get('id');
        

        $companyDelete = DB::table('tbl_company')->where('id',$request->get('id'))->delete();           
        if ($companyDelete) 
        {
            return 0;
        }
        
      return Redirect::to('/company')->with('success', 'Successfully deleted Company.');
      
    }


//POINT SET UP STARTS

public function point_setup(Request $req)
{

   $selectedMenu   = 'point';         // Required Variable
   $pageTitle      = 'Point List';       // Required Variable
      

         $division=DB::table('tbl_division')->get();
         $point=DB::select("SELECT tbl_company.name as company_name, tbl_point.point_id,tbl_point.point_name,tbl_point.point_division,tbl_point.territory_id, tbl_point.territory_name,tbl_division.div_id,tbl_division.

                           div_name 

                           from tbl_point JOIN tbl_division ON tbl_point.point_division=tbl_division.div_id 
						   LEFT JOIN tbl_company ON tbl_company.id = tbl_point.company_id");
         //dd($division);
		 
		 $companyList = DB::table('tbl_company')->orderBy('name','asc')->get();

          return view('Master.point_setup')->with('division',$division)
		  ->with('point',$point)->with('pageTitle',$pageTitle)
		  ->with('companyList',$companyList)
		  ->with('selectedMenu',$selectedMenu); 
          
          //->with('company',$company)->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu);   
}

//get territory for point
public function get_territory(Request $req)
{
$id=$req->input('id');
$req->session()->put('terrid', $id);


$territory=DB::table('tbl_territory')
           ->where('division',$id)
           ->get();


           return view('Master.getTerri')->with('terri',$territory);
}

public function point_process(Request $req)
{
 if ($req->isMethod('post')) 
 {
$selectedMenu   = 'point';         // Required Variable
$pageTitle      = 'Add Point';         // Required Variable
      
 $point_division=$req->input('point_division');
  $point_name=$req->input('point_name');
 $territory_name=$req->input('name');
 
 $company_id=$req->input('company_id');
 
 $user_id=Auth::user()->id;
$user=DB::table('hbl_users')
                        ->where('id', $user_id)
                        ->first();
$userName=$user->userName;
$terrid=$req->session()->get('terrid');
//echo $terrid;exit;

$territory=DB::table('tbl_territory')
           ->where('id',$terrid)
           ->first();

$point=DB::insert('insert into tbl_point(point_division, point_name,territory_name,territory_id,company_id,create_user) values (?,?,?,?,?,?)', [$point_division, $point_name,
                                                                                                                    $territory_name,$territory->id,$company_id,$userName]);
         return Redirect::to('/newPoint')->with('success', 'Successfully Point created.');
       
}
}

public function point_edit(Request $req)
{
$selectedMenu   = 'point';         // Required Variable
$pageTitle      = 'point territory'; 
$slID=$req->get('id');
//return $slID; exit;
$id=DB::table('tbl_point')
                        ->where('point_id',$slID)
                        ->first();
              //return $id->point_id; exit;        
$division=DB::select("select * from tbl_division where div_id='$id->point_division'");
$divisionList=DB::table('tbl_division') 
                     ->get(); 
//return $divisionList;exit;

return view('Master.point_edit')->with('division',$division)->with('id',$id)->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)
                                    ->with('divisionList',$divisionList); 

}
public function point_edit_process(Request $req)
{
if ($req->isMethod('post')) 
 {
$selectedMenu   = 'point';         // Required Variable
$pageTitle      = 'Point Territory';         // Required Variable
      
$point_name=$req->input('point_name');
$point_division=$req->input('point_division');
$territory_name=$req->input('name');
$id=$req->get('id');
$user_id=Auth::user()->id;
$user=DB::table('hbl_users')
                        ->where('id', $user_id)
                        ->first();
$userName=$user->userName;

$territory_id=DB::table('tbl_territory')->where('name',$territory_name)->first();




 //$idterri=$territory_id->id;
 

$point= DB::table('tbl_point')->where('point_id',$req->get('id'))->update(
            [
               
                'point_name'             => $point_name,
                'point_division'          => $point_division,
                'territory_name'          =>$territory_name,
                'point_id'                =>$id,
              
                'update_user'             =>$userName
               
            ]
        ); 
  
 return Redirect::to('/newPoint')->with('success', 'Successfully Updated  Company.');
}

}


public function point_delete(Request $request)
    {
        $id  = $request->get('id');
        

        $pointDelete = DB::table('tbl_point')->where('point_id',$request->get('id'))->delete();           
        if ($pointDelete) 
        {
            return 0;
        }
        
      return Redirect::to('/newPoint')->with('success', 'Successfully deleted Company.');
      
    }



// Territory SET UP ENDS


public function territory_setup(Request $req)
{

   $selectedMenu   = 'territory';         // Required Variable
   $pageTitle      = 'Territory List';       // Required Variable
      

         $territory=DB::table('tbl_territory')
                        ->select('tbl_company.name as company','tbl_territory.id','tbl_territory.name','tbl_territory.division','tbl_division.div_id','tbl_division.div_name')


                        ->join('tbl_division', 'tbl_territory.division', '=', 'tbl_division.div_id')
                        ->leftjoin('tbl_company', 'tbl_territory.company_id', '=', 'tbl_company.id')
                        
                        ->get();
           $division=DB::table('tbl_division') 
                     ->get();      
                        //dd($territory);
						
		$companyList = DB::table('tbl_company')->orderBy('name','asc')->get();				
                        
          return view('Master.territory_setup')->with('territory',$territory)
												->with('division',$division)->with('pageTitle',$pageTitle)
												->with('division',$division)->with('companyList',$companyList)
									            ->with('selectedMenu',$selectedMenu); 
          
             
}


public function territory_process(Request $req)
{
 if ($req->isMethod('post')) 
 {
 $selectedMenu   = 'territory';         // Required Variable
 $pageTitle      = 'Territory List';       // Required Variable
        
 $name=$req->input('name');
 $division=$req->input('division');
 $company_id=$req->input('company_id');
 //$user_id=Auth::user()->id;
 

 $company=DB::insert('insert into tbl_territory (name,division,company_id) values (?,?,?)', [$name,$division,$company_id]);
 return Redirect::to('/newTerritory')->with('success', 'Successfully territory created.');
       
}
}

public function territory_edit(Request $req)
{
$selectedMenu   = 'territory';         // Required Variable
$pageTitle      = 'Edit territory'; 
$slID=$req->get('id');

$id=DB::table('tbl_territory')
                        ->where('id',$slID)
                        ->first();
						
$division=DB::select("select * from tbl_division where div_id='$id->division'");
$divisionList=DB::table('tbl_division') 
                     ->get(); 
					 
	$companyList = DB::table('tbl_company')->orderBy('name','asc')->get();				 

return view('Master.territory_edit')->with('division',$division)->with('id',$id)->with('pageTitle',$pageTitle)
									->with('selectedMenu',$selectedMenu)
									->with('companyList',$companyList)
                                    ->with('divisionList',$divisionList); 

}

public function territory_edit_process(Request $req)
{
if ($req->isMethod('post')) 
 {
$selectedMenu   = 'territory';         // Required Variable
$pageTitle      = 'Edit Territory';         // Required Variable
      
$name=$req->input('name');
$division=$req->input('division');
$company_id=$req->input('company_id');

 
$company= DB::table('tbl_territory')->where('id',$req->get('id'))->update(
            [
               
                'name'             => $name,
                'division'          => $division,
                'company_id'          => $company_id
               
            ]
        ); 
  
 return Redirect::to('/newTerritory')->with('success', 'Successfully Updated  Company.');
}

}


public function territory_delete(Request $request)
    {
        $id  = $request->get('id');
        

        $territoryDelete = DB::table('tbl_territory')->where('id',$request->get('id'))->delete();           
        if ($territoryDelete) 
        {
            return 0;
        }
        
      return Redirect::to('/newTerritory')->with('success', 'Successfully deleted Company.');
      
    }


// Territory SET UP ENDS

//Route SET UP STARTS

public function route_setup(Request $req)

{
  
  $route_id=$req->get('id');
  $selectedMenu   = 'route';         // Required Variable
  $pageTitle      = 'Edit territory'; 
 // echo  $route_id; 
if($route_id)
{

 //Edit part
//return $route_id;

$point=DB::table('tbl_point')->orderBy('point_name','asc')->get();
$routeyy=DB::select("SELECT * from tbl_route where route_id='$route_id'");
$route=DB::select("SELECT tbl_route.*,tbl_point.*, tbl_company.name as company_name  
from tbl_route JOIN tbl_point ON tbl_route.point_id=tbl_point.point_id 
LEFT JOIN tbl_company ON tbl_company.id =  tbl_route.company_id
WHERE route_id='$route_id'" );
//return view('Master.route_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)->with('tata','tttttaasttttwwwwww');
  echo json_encode($route);                          //->with('point',$point)->with('route',$route)->with('route_id',$route_id)->with('routeyy',$routeyy); 
}

else {
  # code...

//Add part
  

$point=DB::table('tbl_point')->orderBy('point_name','asc')->get();
$route=DB::select('SELECT tbl_route.*,tbl_point.*, tbl_company.name as company_name  
from tbl_route JOIN tbl_point ON tbl_route.point_id=tbl_point.point_id 
LEFT JOIN tbl_company ON tbl_company.id =  tbl_route.company_id');
//$point_id=DB::table('tbl_route')->where('point_id',$point->point_id)->get();

//dd($route);

$companyList = DB::table('tbl_company')->orderBy('name','asc')->get();

return view('Master.route_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)
                              ->with('point',$point)
							  ->with('companyList',$companyList)
							  ->with('route',$route)
							  ->with('route_id',$route_id);
                            }

}




public function route_process(Request $req)
{
 if ($req->isMethod('post')) 
 {
 $point_id=$req->input('point_id');
 $rname=$req->input('rname');
 $details=$req->input('details');
 
 $company_id=$req->input('company_id');
 
 $user_id=Auth::user()->id;
 $user=DB::table('users')
                        ->where('id', $user_id)
                        ->first();
$userName=$user->email;

//echo $req->input('route_id');
//exit;
  //Edit part starts
  if($req->input('route_id'))
  {
    $selectedMenu   = 'route';         // Required Variable
    $pageTitle      = 'Edit route'; 
    $route= DB::table('tbl_route')->where('route_id',$req->input('route_id'))->update(
            [
               
                'point_id'             => $point_id,
                'rname'                 => $rname,
                'details'              => $details,
                'update_user'          =>$userName
               
            ]
        ); 
  
 return Redirect::to('/newRoute')->with('success', 'Successfully Updated Route.');

  }
//Edt part ends
 //Add part starts 
  else{
 $selectedMenu   = 'route';         // Required Variable
 $pageTitle      = 'Route List';       // Required Variable
        
 
$route=DB::insert('insert into tbl_route (point_id,rname,details,company_id,user) values (?,?,?,?,?)', [$point_id,$rname,$details,$company_id,$userName]);
 return Redirect::to('/newRoute')->with('success', 'Successfully Route created.');
}
 //Add part Ends
       
}
}

public function route_delete(Request $request)
    {
        $id  = $request->get('id');
        
        //echo $id;
        $routeDelete = DB::table('tbl_route')->where('route_id',$request->get('id'))->delete();           
        
        return "Successfully deleted Route.";
      //return Redirect::to('/newRoute')->with('success', 'Successfully deleted Route.');
      
    }
//Route SET UP ENDS

// Dirtributor SET UP starts

public function distributor_setup(Request $req)

{
  //return "working"; exit;
  $selectedMenu   = 'distributor';         // Required Variable
  $pageTitle      = 'Edit distributor';
 //return view('Master.drist_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu);

  
  $route_id=$req->get('id');
  //DB::table("delete * from distri_sess");

//DB::table("insert into distri_sess(user_id) values(1)");

 
 //echo  $route_id; 
  //session()->forget('key');
 
if($route_id)
{

 //Edit part
//echo $route_id; exit;

//$req->session()->put('key', $route_id);
//$value = $req->session()->get('key');
//echo $value; exit;
  

$point=DB::table('tbl_point')->orderBy('point_name','asc')->get();
$division=DB::select('SELECT * from tbl_division');
$business_type=DB::table('tbl_business_type')->get();
//$point_id=DB::table('tbl_route')->where('point_id',$point->point_id)->get();

//dd($business_type);
$distri_details=DB::select("SELECT users.*, users.email as email1,tbl_user_details.*,tbl_user_business_scope.*,tbl_distributor_balance.*,tbl_point.point_name,tbl_division.div_name,tbl_business_type.business_type as btype
from users
LEFT JOIN tbl_user_details ON users.id=tbl_user_details.user_id
LEFT JOIN tbl_user_business_scope ON users.id=tbl_user_business_scope.user_id  
LEFT JOIN tbl_distributor_balance ON users.id=tbl_distributor_balance.distributor_id 
JOIN tbl_point on tbl_user_business_scope.point_id=tbl_point.point_id
JOIN tbl_division on tbl_user_business_scope.division_id=tbl_division.div_id
LEFT JOIN tbl_business_type ON users.business_type_id=tbl_business_type.business_type_id
WHERE users.user_type_id=5  AND users.id='$route_id'");

  echo json_encode($distri_details);                          
}

else {
  # code...

//Add part
  
  $companyList = DB::table('tbl_company')->orderBy('name','asc')->get();

$point=DB::table('tbl_point')->orderBy('point_name','asc')->get();
$division=DB::select('SELECT * from tbl_division');
$business_type=DB::table('tbl_business_type')->get();
//$point_id=DB::table('tbl_route')->where('point_id',$point->point_id)->get();

//dd($business_type);
$distri_details=DB::select('SELECT tbl_company.name company_name, users.*,tbl_user_details.*,tbl_user_business_scope.*,tbl_distributor_balance.*
from users
LEFT JOIN tbl_user_details ON users.id=tbl_user_details.user_id
LEFT JOIN tbl_user_business_scope ON users.id=tbl_user_business_scope.user_id  
LEFT JOIN tbl_distributor_balance ON users.id=tbl_distributor_balance.distributor_id 
LEFT JOIN tbl_company ON tbl_company.id=users.company_id 
WHERE users.user_type_id=5');
//$point_id=$distri_details->point_id;

//echo $point_id;exit;
//$point_id=DB::table('tbl_point')->where('point_id',343)->first();
//dd($point_name);


return view('Master.drist_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)
                              ->with('point',$point)->with('division',$division)->with('route_id',$route_id)
                              ->with('companyList',$companyList)
                              ->with('business_type',$business_type)
							  ->with('distri_details',$distri_details);
                            }
                            

}

public function distributor_process(Request $req)
{
  
 if ($req->isMethod('post')) 
 {
 
 $dname=$req->input('dname');
 $sapcode=$req->input('sapcode');
 $address=$req->input('address');
 $point_id=$req->input('point_id');
 $div_id=$req->input('div_id');
 $type=$req->input('type');
 $mobile_no=$req->input('mobile_no');
 $tnt=$req->input('tnt');
 $credit_limit=$req->input('credit_limit');
 $pricing=$req->input('pricing');
 $dob=$req->input('dob');//add dob field to user_details table
 $dob=date('Y-m-d',strtotime($dob));
 $username=$req->input('username');
 $password=Hash::make($req->input('password'));
 $user_type_id=5;
 $email=$req->input('email');
 $business_type_id=$req->input('business_type_id');
 $price_type=$req->input('price_type');
 
 $company_id=$req->input('company_id');
 
 $transaction_date=date('Y-m-d H:i:s');
 //$designation=$req->input('password');


 if($req->input('id'))
  {
   
$selectedMenu   = 'distributor';         // Required Variable
    $pageTitle      = 'Edit distributor';      // Required Variable

    $user= DB::table('users')->where('id',$req->input('id'))->update(
            [
               
                'display_name'             => $dname,
                'sap_code'                 => $sapcode,
                'email'                    => $username,
                'password'                 =>$password,
                'user_type_id'             =>$user_type_id,
                'business_type_id'         =>$business_type_id,
                'company_id'         	   =>$company_id
               
            ]
        ); 

     $userDetails= DB::table('tbl_user_details')->where('user_id',$req->input('id'))->update(
            [
               
                'user_id'             => $req->input('id'),
                'first_name'          => $dname,
                'sap_code'            => $sapcode,
                'current_address'     =>$address,
                'land_phone'          =>$tnt,
                'cell_phone'          =>$mobile_no,
                'email'               =>$email,
                'dob'                 =>$dob
               
            ]
        ); 
       $distriDetails= DB::table('tbl_distributor_balance')->where('distributor_id',$req->input('id'))->update(
            [
               
                'distributor_id'             => $req->input('id'),
                'price_type'          => $price_type,
                'credit_limit'            => $credit_limit,
                'transaction_date'     =>$transaction_date
                
                ]
        ); 

        $bscope= DB::table('tbl_user_business_scope')->where('user_id',$req->input('id'))->update(
            [
               
                'user_id'             => $req->input('id'),
                'point_id'            => $point_id,
                'division_id'         => $div_id
               
                ]
        ); 
  
 return Redirect::to('/newDistributor')->with('success', 'Successfully Updated Route.');
  }
else 
{
 $selectedMenu   = 'distributor';         // Required Variable
 $pageTitle      = 'Edit distributor';      // Required Variable
        
 
$distributor_user=DB::insert('insert into users (display_name,sap_code,email,password,user_type_id,business_type_id,company_id) 
values (?,?,?,?,?,?,?)', [$dname,$sapcode,$username,$password,$user_type_id,$business_type_id,$company_id]);
//$user_id=DB::select("select * from users where email='$username'");
$user_id=DB::table('users')
          ->where('email',$username)
           ->first();
//dd($user_id); exit;
//$distributor_details
$user_id=$user_id->id;

$user_details=DB::insert('insert into tbl_user_details(user_id,first_name,sap_code,current_address,land_phone,cell_phone,email,dob) values(?,?,?,?,?,?,?,?)',[$user_id,$dname,$sapcode,$address,$tnt,$mobile_no,$email,$dob]);

$distributor_details=DB::insert('insert into tbl_distributor_balance(distributor_id,price_type,credit_limit,transaction_date) values(?,?,?,?)',[$user_id,$price_type,$credit_limit,$transaction_date]);

$business_scope=DB::insert('insert into tbl_user_business_scope(user_id,point_id,division_id) values(?,?,?)',[$user_id,$point_id,$div_id]);

return Redirect::to('/newDistributor')->with('success', 'Successfully Distributed created.');
}

}      
}

public function distri_delete(Request $request)
    {
        $id  = $request->get('id');
        
        //echo $id;
        $usersDelete = DB::table('users')->where('id',$request->get('id'))->delete();
        $userDetailsDel= DB::table('tbl_user_details')->where('user_id',$request->get('id'))->delete();
        $ditribalanceDel= DB::table('tbl_distributor_balance')->where('distributor_id',$request->get('id'))->delete(); 
        $businessscopeDel= DB::table('tbl_user_business_scope')->where('user_id',$request->get('id'))->delete();       
        
         
        
        return "Successfully deleted distributor.";
      //return Redirect::to('/newRoute')->with('success', 'Successfully deleted Route.');
      
    }


// Pro Category setup starts
public function procategory_setup(Request $req)
{
 $route_id=$req->get('id');
 $selectedMenu   = 'category';         // Required Variable
 $pageTitle      = 'Product Category';
 // echo  $route_id; 
if($route_id)
{

$proCategory=DB::select("select tbl_product_category.*,tbl_company.name as cname
from tbl_product_category
LEFT JOIN tbl_company ON tbl_product_category.company_id=tbl_company.id
where tbl_product_category.id='$route_id'");

$company=DB::select("select * from tbl_company");
//echo json_encode($proCategory); 
$gname=DB::select('SELECT distinct g_name FROM tbl_product_category where status=0 order by g_name');

echo json_encode(array(
'pro' => $proCategory,                           
'company' => $company,
'gname' => $gname
));

}                     

else 
{
  $pro_gname=DB::select('SELECT distinct g_name FROM tbl_product_category where status=0 order by g_name');
  $categoryDetails=DB::select('SELECT * FROM tbl_product_category');

  $tbl_company=DB::select('SELECT  *  FROM tbl_company where status=0  order by name');
  return view('Master.procategory_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)->with('pro_gname',$pro_gname)
                                                                       ->with('tbl_company',$tbl_company)->with('categoryDetails',$categoryDetails);
}
//Pro category setup ends    

}

public function proCategory_process(Request $req)
{
if ($req->isMethod('post')) 
 {
 

 $g_code=$req->input('g_code');
 $name=$req->input('name');
 $g_name=$req->input('g_name');
 $company_id=$req->input('company_id');
 $avg_price=$req->input('avg_price');
 $user_id=Auth::user()->id;
 $user=DB::table('users')
                        ->where('id', $user_id)
                        ->first();
$user=$user->email;

if($g_name=='Accesories')
{
  $gid=2;
}
else if($g_name=='Lighting')
{
   $gid=1;
}
else if($g_name=='FAN')
{
   $gid=0;
}
else
{
  $gid='';
}

if($req->input('id'))
{

  $selectedMenu   = 'category';         // Required Variable
  $pageTitle      = 'Edit Product Category';

    $user= DB::table('tbl_product_category')->where('id',$req->input('id'))->update(
            [
               
                'g_name'             => $g_name,
                'gid'                => $gid,
                'g_code'             => $g_code,
                'name'               =>$name,
                'company_id'         =>$company_id,
                'avg_price'          =>$avg_price
               
            ]
        ); 
    return Redirect::to('/productCategory')->with('success', 'Successfully Updated Product Category.');
}

else
{
$proCategory=DB::insert("insert into tbl_product_category(g_name,gid,g_code,name,user,company_id,avg_price)
                        values('$g_name',$gid,'$g_code','$name','$user','$company_id','$avg_price')");
return Redirect::to('/productCategory')->with('success', 'Successfully Product Category created.');

}
}

}
public function procategory_delete(Request $request)
    {
        $id  = $request->get('id');
        
        //echo $id;
       $proDetailsDel= DB::table('tbl_product_category')->where('id',$request->get('id'))->delete();
        
       return "Successfully deleted distributor.";
      //return Redirect::to('/newRoute')->with('success', 'Successfully deleted Route.');
      
    }


//Todays merger Product Maung
public function product_setup(Request $req)
{
 $route_id=$req->get('id');
 $selectedMenu   = 'newproduct';         // Required Variable
 $pageTitle      = 'New Product Set Up';
 // echo  $route_id; 
if($route_id)
{
$products=DB::select("select tbl_product.*,tbl_product_category.id as catid,tbl_product_category.g_code,tbl_product_category.g_name
from tbl_product
LEFT JOIN tbl_product_category ON tbl_product.category_id=tbl_product_category.id
where tbl_product.id='$route_id'");

echo json_encode(array(
'products' => $products
));
}

else 
{
$product=DB::select("select tbl_product.id as id, tbl_product.status,active_date,sub_group,product_msg,
                     tbl_product_category.name as category,mrp,depo,distri,tbl_product.unit,
                     tbl_product.name as product,tbl_product.sap_code as sap_code,tbl_product.order_by,
                     commission,tbl_product.unit,tbl_product.factor 
                  from tbl_product  
                  join tbl_product_category on tbl_product.category_id=tbl_product_category.id 
                  order by tbl_product_category.name,tbl_product.name,tbl_product.order_by asc");

 $g_code= DB::select("SELECT id,g_code,name  FROM tbl_product_category order by order_by,name");

return view('Master.product_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)->with('product',$product)->with('gcode',$g_code);
}

}

public function productProcess(Request $req)
{
if ($req->isMethod('post')) 
 {
 

 $group=$req->input('group');
 $companyid=$req->input('companyid');
 $sap_code=$req->input('sap_code');
 $mrp=$req->input('mrp');
 $depo=$req->input('depo');
 $distri=$req->input('distri');
 $unit=$req->input('unit'); 
 $product=$req->input('product');

 /*echo $group."<br>";
 echo $companyid."<br>";
 echo $sap_code."<br>";
 echo $mrp."<br>";
 echo $depo."<br>";
 echo $distri."<br>";
 echo $unit."<br>";*/

 $user_id=Auth::user()->id;
 $user=DB::table('users')
                        ->where('id', $user_id)
                        ->first();
  $user=$user->email;
  //echo $user;

if($req->input('id'))
{
$products= DB::table('tbl_product')->where('id',$req->input('id'))->update(
            [
               
                'category_id'             => $group,
                'companyid'               => $companyid,
                'sap_code'                => $sap_code,
                'mrp'                     => $mrp,
                'depo'                    =>$depo,
                'distri'                  =>$distri,
                'unit'                    =>$unit,
                'name'                    =>$product,
                'user'                    =>$user
               
            ]
        ); 
  return Redirect::to('/productSetup')->with('success', 'Successfully Product Updated.');


}
else
{
  
       $factor=1;
        $productAll= DB::insert("insert into tbl_product (companyid,category_id,sap_code,name,ims_name,unit,user,factor,mrp,depo,distri,realtimeprice) 
          values('$companyid','$group','$sap_code','$product',
          '$product','$unit','$user',$factor,'$mrp','$depo','$distri','$depo')");
        
        return Redirect::to('/productSetup')->with('success', 'Successfully Product created.');
}
}
}

public function productsMaster_delete(Request $request)
    {
        $id  = $request->get('id');
        
      
       $proDetailsDel= DB::table('tbl_product')->where('id',$request->get('id'))->delete();
        
       return "Successfully deleted product category.";
      //return Redirect::to('/newRoute')->with('success', 'Successfully deleted Route.');
      
    }

//Maung User Ends

//Maung User Ends

///////////////////////////////////////// Reject Reseon Begin  ///////////////////////////////////////////////////////
  
  public function rejectreason_setup()
  {
  
    $selectedMenu   = 'rejectreason';         // Required Variable
        $pageTitle      = 'Reason List';        // Required Variable

    $rejectreasonList = DB::table('ims_visit_reason')->get();
        
    return view('Master.rejectreason_setup')->with('rejectreasonList',$rejectreasonList)
                        ->with('pageTitle',$pageTitle)
                        ->with('selectedMenu',$selectedMenu);            
  }

  
  public function rejectreason_process(Request $request)
  {
    if ($request->isMethod('post')) 
    {
      $selectedMenu   = 'rejectreason';         // Required Variable
      $pageTitle      = 'Reason List';        // Required Variable
      
      $RejRes                         = new RejectreasonModel;
      $RejRes->reason           = $request->input('reason');
      $RejRes->type          = $request->input('reason_type');     
      $RejRes->user                 = DB::table('users')->where('id', Auth::user()->id)->first()->email;
      $RejRes->reason_status          = $request->input('reason_status');
      
      $RejRes->save();

      return Redirect::to('/reject_reason_list')->with('success', 'Successfully Reason created.');
         
    }
  }
  
  
  public function rejectreason_edit(Request $request)
  {
    $selectedMenu   = 'rejectreason';         // Required Variable
    $pageTitle      = 'Reason List';        // Required Variable
    
    return view('Master.rejectreason_edit')->with('id',DB::table('ims_visit_reason')->where('id',$request->get('id'))->first())
                         ->with('pageTitle',$pageTitle)
                         ->with('selectedMenu',$selectedMenu); 
  }
  
  
  public function rejectreason_edit_process(Request $request)
  {
    if ($request->isMethod('post')) 
    {
      $selectedMenu   = 'rejectreason';         // Required Variable
      $pageTitle      = 'Reason List';        // Required Variable
      
      
      $RegRow = RejectreasonModel::find($request->input('id'));
      
      $RegRow->reason           = $request->input('reason');
      $RegRow->type          = $request->input('reason_type');     
      $RegRow->user                 = $request->input('user');    
      $RegRow->reason_status          = $request->input('reason_status');
      
      $RegRow->save();
      
      return Redirect::to('/reject_reason_list')->with('success', 'Successfully Updated  Reject Reason.');
    }

  }

  
  public function rejectreason_delete(Request $request)
    {
        $id  = $request->get('id');
        
    /* retrive and delete
    $objReg = RejectreasonModel::find($id);
    $objReg->delete();
    */
    
    /* if id is primary key */
    RejectreasonModel::destroy($id);
    
    
    /* query and delete
    RejectreasonModel::where('id', $id)->delete();
    */
 
        
      return Redirect::to('/reject_reason_list')->with('success', 'Successfully deleted Company.');
      
    }
  
  
  ///////////////////////////////////////// Division Set Up Begin  ///////////////////////////////////////////////////////
  
  public function division_setup()
  {
  
    $selectedMenu   = 'division';         // Required Variable
        $pageTitle      = 'Division List';    // Required Variable

    $divisionList = DB::table('tbl_division')->get();
	
	$divisionList = DB::select('SELECT d.*, c.name FROM tbl_division d LEFT JOIN tbl_company c ON d.company_id = c.id');
	
	$company_list = DB::table('tbl_company')->orderBy('name','asc')->get();
	
        
    return view('Master.division_setup')->with('divisionList',$divisionList)
                        ->with('pageTitle',$pageTitle)
                        ->with('companyList',$company_list)
                        ->with('selectedMenu',$selectedMenu);            
  }

  
  public function division_process(Request $request)
  {
    if ($request->isMethod('post')) 
    {
      $selectedMenu   = 'division';         // Required Variable
      $pageTitle      = 'Division List';    // Required Variable
      
      $DivObj                    = new DivisionModel;
      $DivObj->div_code         = $request->input('div_code');
      $DivObj->div_name         = $request->input('div_name');
      $DivObj->div_status           = $request->input('div_status');     
      
	  $DivObj->company_id           = $request->input('company_id');     
      
      $DivObj->create_user          = Auth::user()->id;    
      $DivObj->create_date          = date('Y-m-d');
    
      $DivObj->save();

      return Redirect::to('/division_list')->with('success', 'Successfully Division created.');
         
    }
  }
  
  
  public function division_edit(Request $request)
  {
    $selectedMenu   = 'division';         // Required Variable
    $pageTitle      = 'Division List';    // Required Variable

	$company_list = DB::table('tbl_company')->orderBy('name','asc')->get();
	
    $division=DB::table('tbl_division')->where('div_id',$request->get('id'))->first();
    
    return view('Master.division_edit')->with('div_row',$division)
                         ->with('companyList',$company_list)
                         ->with('pageTitle',$pageTitle)
                         ->with('selectedMenu',$selectedMenu); 
  }
  
  
  public function division_edit_process(Request $request)
  {
    if ($request->isMethod('post')) 
    {
      $selectedMenu   = 'division';         // Required Variable
      $pageTitle      = 'Division List';    // Required Variable
      
      
      $RegRow = DivisionModel::find($request->input('id'));
      
      $DivObj                         = new DivisionModel;
      $DivObj->div_code         = $request->input('div_code');
      $DivObj->div_name         = $request->input('div_name');
      $DivObj->div_status           = $request->input('div_status');     
      
	  $DivObj->company_id           = $request->input('company_id');     
      
      $DivObj->update_user          = Auth::user()->id;    
      $DivObj->update_date          = date('Y-m-d');
      
      $DivObj->save();
      
      return Redirect::to('/division_list')->with('success', 'Successfully Updated  Division Reason.');
    }

  }

  
  public function division_delete(Request $request)
    {
        $div_id  = $request->get('id');
        
    /* retrive and delete
    $objReg = RejectreasonModel::find($id);
    $objReg->delete();
    */
    
    /* if id is primary key */
    //DivisionModel::destroy($id);
    
    
    /* query and delete
    RejectreasonModel::where('id', $id)->delete();
    */
    
   /* $divUpd = DB::table('tbl_division')->where('div_id',$request->get('div_id'))->update(
          [
            'div_status'              => 1,
            'update_user'       => Auth::user()->id,
            'update_date'       => date('Y-m-d')
          ]
    );*/

     $divUpd = DB::delete("delete from tbl_division where div_id='$div_id'");
 
        
    return Redirect::to('/division_list')->with('success', 'Successfully deleted Division.');
      
    }
  
  
  ///////////////////////////////////////// FO BEGIN  ///////////////////////////////////////////////////////
  
  
  public function fo_setup(Request $req)
  {
      
    $selectedMenu   = 'fo_list';         
    $pageTitle      = 'FO Setup';
      
    //$route_id     = $req->get('id');
    $user_id    = $req->get('id');
  
    if($user_id)
    {

      //$point      =   DB::table('tbl_point')->orderBy('point_name','asc')->get();
      //$division   = DB::select('SELECT * from tbl_division');
      //$business_type  = DB::table('tbl_business_type')->get();
    
      //dd($business_type);
      
      $fo_details=DB::select("SELECT users.*, tbl_user_details.*, tbl_user_business_scope.*, 
      tbl_point.*, tbl_division.*, tbl_business_type.*
      from users
      JOIN tbl_user_details ON users.id=tbl_user_details.user_id
      JOIN tbl_user_business_scope ON users.id=tbl_user_business_scope.user_id
      JOIN tbl_business_type ON users.business_type_id=tbl_business_type.business_type_id
      JOIN tbl_point ON tbl_user_business_scope.point_id = tbl_point.point_id
      JOIN tbl_division ON tbl_user_business_scope.division_id=tbl_division.div_id  
      WHERE users.user_type_id=12  AND users.id='".$user_id."'");

      echo json_encode($fo_details);  
      
    } else {
  
      $point      = DB::table('tbl_point')->orderBy('point_name','asc')->get();
      $division   = DB::select('SELECT * from tbl_division');
      $business_type  = DB::table('tbl_business_type')->get();
    
      //dd($business_type);
      
      $fo_details=DB::select('SELECT users.*, tbl_user_details.*, tbl_user_business_scope.*, 
      tbl_point.*, tbl_division.*, tbl_business_type.*
      from users
      JOIN tbl_user_details ON users.id=tbl_user_details.user_id
      JOIN tbl_user_business_scope ON users.id=tbl_user_business_scope.user_id
      JOIN tbl_business_type ON users.business_type_id=tbl_business_type.business_type_id
      JOIN tbl_point ON tbl_user_business_scope.point_id = tbl_point.point_id
      JOIN tbl_division ON tbl_user_business_scope.division_id=tbl_division.div_id  
      WHERE users.user_type_id=12');
      
      //dd($business_type);

      return view('Master.fo_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)
                              ->with('point',$point)->with('division',$division)//->with('route_id',$route_id)
                              ->with('business_type',$business_type)->with('fo_details',$fo_details);
        }
                            

  }

  
  public function fo_process(Request $req)
  {
    
    $selectedMenu   = 'fo_list';         
    $pageTitle      = 'FO Setup';
    
    if ($req->isMethod('post')) 
    {
     
      $display_name   = $req->input('display_name');
      $sapcode      = $req->input('sapcode');
      $address      = $req->input('address');
      $point_id     = $req->input('point_id');
      $div_id       = $req->input('div_id');
      $type       = $req->input('type');
      $mobile_no      = $req->input('mobile_no');
      $tnt        = $req->input('tnt');
      $dob        = $req->input('dob');//add dob field to user_details table
      $dob        = date('Y-m-d',strtotime($dob));
      
      $username     = $req->input('username');
      $password     = Hash::make($req->input('password'));
      
      $user_type_id   = 12;
      $email        = $req->input('email');
      $business_type_id = $req->input('business_type_id');
    

      if($req->input('id'))
      {
       
      
        $user= DB::table('users')->where('id',$req->input('id'))->update(
          [
             
            'display_name'             => $display_name,
            'sap_code'                 => $sapcode,
            'email'                    => $username,
            'password'                 => $password,
            'user_type_id'             => $user_type_id,
            'business_type_id'         => $business_type_id
             
          ]
        ); 

        $userDetails= DB::table('tbl_user_details')->where('user_id',$req->input('id'))->update(
          [
             
            'user_id'             => $req->input('id'),
            'first_name'          => $display_name,
            'sap_code'            => $sapcode,
            'current_address'     => $address,
            'land_phone'          => $tnt,
            'cell_phone'          => $mobile_no,
            'email'               => $email,
            'dob'                 => $dob
             
          ]
        ); 
         

        $bscope= DB::table('tbl_user_business_scope')->where('user_id',$req->input('id'))->update(
          [
             
            'user_id'             => $req->input('id'),
            'point_id'            => $point_id,
            'division_id'         => $div_id
             
          ]
        ); 
      
        return Redirect::to('/fo_list')->with('success', 'Successfully Updated FO.');
    
      } else {
         
         
        $distributor_user=DB::insert('insert into users (display_name,sap_code,email,password,user_type_id,business_type_id) 
        values (?,?,?,?,?,?)', [$display_name,$sapcode,$username,$password,$user_type_id,$business_type_id]);
      
        $user_id = DB::table('users')->where('email',$username)->first();
        $user_id = $user_id->id;

        $user_details = DB::insert('insert into tbl_user_details(user_id,first_name,sap_code,current_address,land_phone,cell_phone,email,dob) 
        values(?,?,?,?,?,?,?,?)',[$user_id,$display_name,$sapcode,$address,$tnt,$mobile_no,$email,$dob]);
        
        $business_scope = DB::insert('insert into tbl_user_business_scope(user_id,point_id,division_id) 
        values(?,?,?)',[$user_id,$point_id,$div_id]);

        return Redirect::to('/fo_list')->with('success', 'Successfully FO created.');
      }

    }      
  }

  
  public function fo_delete(Request $request)
    {
        $id  = $request->get('id');
        
        //$usersDelete    = DB::table('users')->where('id',$request->get('id'))->delete();
        //$userDetailsDel   = DB::table('tbl_user_details')->where('user_id',$request->get('id'))->delete();
    
    $user= DB::table('users')->where('id',$request->get('id'))->update(
          [
            'is_active'             => 1,
            'update_by'       => Auth::user()->id,
            'update_date'     => date('Y-m-d')
          ]
        );
    
    $userDetails= DB::table('tbl_user_details')->where('user_id',$request->get('id'))->update(
          [
            'is_active'             => 1,
            'update_by'       => Auth::user()->id,
            'update_date'     => date('Y-m-d')
          ]
        ); 
        
    $businessscopeDel = DB::table('tbl_user_business_scope')->where('user_id',$request->get('id'))->delete();       
        
        return "Successfully deleted FO.";
      
    }
  
  
  ///////////////////////////////////////// RETAILER BEGIN  ///////////////////////////////////////////////////////
  
  
  public function retailer_setup(Request $req)
  {
      
    $selectedMenu   = 'retailer_list';         
    $pageTitle      = 'RETAILER Setup';
      
    $retailer_id    = $req->get('id');
  
    if($retailer_id)
    {
    
      $retailer_details=DB::select("SELECT  d.div_name, t.name tname, rt.rname, ret.*
      FROM tbl_retailer ret
      JOIN tbl_division d ON d.div_id = ret.division  
      JOIN tbl_territory t ON t.id = ret.territory
      JOIN tbl_route rt ON ret.rid = rt.route_id
      WHERE ret.retailer_id='".$retailer_id."'");

      echo json_encode($retailer_details);  
      
    } else {
  
      $territory_list   = DB::table('tbl_territory')->get();
      $division     = DB::select('SELECT * from tbl_division');
      $route_list     = DB::table('tbl_route')->get();
    
      //dd($business_type);
	  
	  $companyList = DB::table('tbl_company')->orderBy('name','asc')->get();
      
      $retailer_details=DB::select('SELECT  d.div_name, c.name as company_name, t.name tname, rt.rname, ret.*
      FROM tbl_retailer ret
      JOIN tbl_division d ON d.div_id = ret.division  
      JOIN tbl_territory t ON t.id = ret.territory
      LEFT JOIN tbl_company c ON c.id = ret.company_id
      JOIN tbl_route rt ON ret.rid = rt.route_id order by ret.retailer_id desc limit 50');
      
      //dd($business_type);

      return view('Master.retailer_setup')->with('pageTitle',$pageTitle)->with('selectedMenu',$selectedMenu)
                              ->with('route_list',$route_list)->with('division',$division)
                              ->with('companyList',$companyList)
							  ->with('territory_list',$territory_list)->with('retailer_details',$retailer_details);
        }
                            

  }

  
  public function retailer_process(Request $req)
  {
    
    $selectedMenu   = 'retailer_list';         
    $pageTitle      = 'RETAILER Setup';
    
    if ($req->isMethod('post')) 
    {
     
      $retailer_name    = $req->input('name');
      $sap_code     = $req->input('sap_code');
      $owner        = $req->input('owner');
      $vAddress     = $req->input('vAddress');
      $division     = $req->input('division');
      $rid        = $req->input('rid');
      $territory      = $req->input('territory');
      $mobile       = $req->input('mobile');
      $tnt        = $req->input('tnt');
      $email        = $req->input('email');
      $status       = $req->input('status');
      
	  $company_id    = $req->input('company_id');
      
      $dob        = date('Y-m-d',strtotime($req->input('dob')));
      
      $shop_type      = $req->input('shop_type');
  
      if($req->input('id'))
      {
       
      
        $user= DB::table('tbl_retailer')->where('retailer_id',$req->input('id'))->update(
          [
             
            'name'             => $retailer_name,
            'division'         => $division,
            'territory'        => $territory,
            'rid'              => $rid,
            'shop_type'        => $shop_type,
            'owner'            => $owner,
            'sap_code'         => $sap_code,
            'mobile'           => $mobile,
            'tnt'              => $tnt,
            'email'            => $email,
            'dateandtime'      => date('Y-m-d'),
            'user'             => Auth::user()->id,
            'status'           => $status,
            'dob'              => $dob,
            'company_id'       => $company_id,
            'vAddress'         => $vAddress,
             
          ]
        ); 
  
        return Redirect::to('/retailer_list')->with('success', 'Successfully Updated Retailer List.');
    
      } else {
         
         
        $obJRetailerModel = new RetailerModel();
        
        $obJRetailerModel->name             = $retailer_name;
        $obJRetailerModel->division         = $division;
        $obJRetailerModel->territory        = $territory;
        $obJRetailerModel->rid              = $rid;
        $obJRetailerModel->shop_type        = $shop_type;
        $obJRetailerModel->owner            = $owner;
        $obJRetailerModel->sap_code         = $sap_code;
        $obJRetailerModel->mobile           = $mobile;
        $obJRetailerModel->tnt              = $tnt;
        $obJRetailerModel->email            = $email;
        $obJRetailerModel->dateandtime      = date('Y-m-d');
        $obJRetailerModel->user             = Auth::user()->id;
        $obJRetailerModel->status           = $status;
        $obJRetailerModel->dob              = $dob;
        $obJRetailerModel->company_id       = $company_id;
        $obJRetailerModel->vAddress         = $vAddress;
        
        $obJRetailerModel->save();

        return Redirect::to('/retailer_list')->with('success', 'Successfully Retailer Created.');
      }

    }      
  }

  
  public function retailer_delete(Request $request)
    {
        $retailer_id  = $request->get('id');
        
        //$usersDelete    = DB::table('users')->where('id',$request->get('id'))->delete();
        //$userDetailsDel   = DB::table('tbl_user_details')->where('user_id',$request->get('id'))->delete();
    
    $retDelete = DB::table('tbl_retailer')->where('retailer_id',$retailer_id)->update(
          [
            'status'                  => 1,
            'inactive_user'         => Auth::user()->id,
            'inactive_date_time'      => date('Y-m-d')
          ]
        );
        
        return "Successfully Deleted Retailer.";
      
    }
  
  // Zubair Bhai Ends Retailer//

}

