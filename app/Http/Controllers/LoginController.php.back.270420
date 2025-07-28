<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\ThrottlesLogins;
// use Illuminate\Support\Facades\Hash;

use DB;
use Auth;

class LoginController extends Controller
{

    /**
    *
    * Created by Md. Masud Rana
    * Date : 04/12/2017
    *
    **/

    public function ssg_register()
    {
        return view('registrationPage');
    }

    public function ssg_master_login(Request $request)
    {
    	// default (12345) : $2y$10$pVJ5ageXK3d7ya.ELwFN.uzpYwIi3ojhjTxHx2iMqP2yL4Dnh8bPu

        $username	= $request->get('userEmail');
        $password   = $request->get('userPassword'); 
        $remember   = $request->get('rememberme'); 

        $checkAccess = DB::table('users')
                    ->select('email','is_active','email','user_type_id')
                    ->where('email', $username)
                    ->where('is_active', 0)
                    ->first();

        if(sizeof($checkAccess) > 0)
        {
            DB::table('login_activities')->insert(
                    [
                         
                        'login_id'              => $username,
                        'last_login_ip'         => request()->ip(),
                        'last_login_at'         => date('Y-m-d h:i:s'),
                        'hostname'              => $request->getHttpHost(),
                       
                    ]
                );
            if($checkAccess->user_type_id==12) // Field Officer (FO)
            {
                
                $checkAccessFO = DB::table('users')
                    ->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_user_details.user_id','tbl_user_business_scope.user_id')
                    ->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
                    ->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
                    ->join('tbl_user_details', 'tbl_user_details.user_id', '=', 'users.id')
                    ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
                    ->where('users.email', $username)
                    ->where('users.is_active', 0)
					->where('tbl_user_business_scope.is_active', 0)
                    ->first();
                   
                if (sizeof($checkAccessFO) > 0 ) 
                {  
                    //dd($checkAccess->user_type_id);
                    if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                    {
                        return Redirect::to('/');
                    } 
                    else
                    {
                       return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                    }
                }
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }
            elseif($checkAccess->user_type_id==5) // Distributor (D)
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
					/* Firts time login redirect modifiied version */
				   
				  
					if (session('userType')=='')
						{
						
							$commontSessionData = DB::table('users')
										->select('users.*','tbl_user_type.user_type_id','tbl_user_type.user_type','tbl_business_type.business_type_id','tbl_global_company.global_company_id','tbl_global_company.global_company_name')

										->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
										->join('tbl_user_type', 'tbl_user_type.user_type_id', '=', 'users.user_type_id')
										->join('tbl_business_type', 'tbl_business_type.business_type_id', '=', 'users.business_type_id')
										->where('users.email', Auth::user()->email)                    
										->first();

							//dd($commontSessionData);
							
							session()->put('businessName', $commontSessionData->global_company_name);
							session()->put('userType', $commontSessionData->user_type);
							session()->put('userFullName', $commontSessionData->display_name);
							session()->put('userTypeId', $commontSessionData->user_type_id);

							if(Auth::user()->user_type_id=='12' || Auth::user()->user_type_id=='5')
							{
								$commontSessionDataM = DB::table('users')
										->select('users.*','bscope.point_id','tbl_point.point_id','tbl_point.is_depot')

										->join('tbl_user_business_scope AS bscope', 'users.id', '=', 'bscope.user_id')

										->join('tbl_point', 'bscope.point_id', '=', 'tbl_point.point_id') 
										->where('users.email', Auth::user()->email)                    
										->first();

								//dd($commontSessionDataM);

								session()->put('isDepot', $commontSessionDataM->is_depot);

								if(sizeof($commontSessionDataM) >0 )
								{
									$pointID = $commontSessionDataM->point_id;
								   
									$exceptionPoint = DB::table('tbl_exception')->where('ex_point_id', $pointID)
											->first();
									if(sizeof($exceptionPoint)>0)
									{
										session()->put('exceptionPoint', $exceptionPoint->ex_point_id);
									}
									else
									{
										session()->put('exceptionPoint', '');
									}
								}
								else
								{
								   session()->put('exceptionPoint', '');
								}
							}

						}
						else if (session('userType')!='')
						{
							if (session('userTypeId')==12 && session('userPointId')=='') // for field officer (Fo)
							{
								$resultFo = DB::table('users')
										->select('users.id','users.email','tbl_user_type.user_type','tbl_user_business_scope.point_id','tbl_user_business_scope.division_id')
										 ->join('tbl_user_type', 'users.user_type_id', '=', 'tbl_user_type.user_type_id')
										 ->join('tbl_user_business_scope', 'tbl_user_business_scope.user_id', '=', 'users.id')
										 ->join('tbl_global_company', 'tbl_global_company.global_company_id', '=', 'users.global_company_id')
										 ->where('tbl_user_type.user_type_id', 12) // 5 for distributor
										 ->where('users.id', Auth::user()->id)
										 //->where('tbl_user_business_scope.point_id', $pointID)
										 ->where('users.is_active', 0) // 0 for active
										 ->where('tbl_global_company.global_company_id', Auth::user()->global_company_id)
										 ->first();
							}
						}
						
						
				   
						 if(session('exceptionPoint')=='')
						 {
							return Redirect::to('/order');
						 } else {
							return Redirect::to('/order-exceptional');
						 }
				
					
                    //return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }
            elseif($checkAccess->user_type_id==1) // Super admin but user is admin
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            } 
            elseif($checkAccess->user_type_id==2) // Admin (A)
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            } 
            elseif($checkAccess->user_type_id==4) // Sales Coordinator
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }
            elseif($checkAccess->user_type_id==2) // Sales Admin
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }  
            elseif($checkAccess->user_type_id==6) // SM
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            } 

            elseif($checkAccess->user_type_id==7) // DSM
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            } 

            elseif($checkAccess->user_type_id==8) // ASM
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }  


            elseif($checkAccess->user_type_id==9) // RSM
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }

            elseif($checkAccess->user_type_id==10) // TSm
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }

            elseif($checkAccess->user_type_id==11) // JTSM
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            } 

             elseif($checkAccess->user_type_id==3) // MGT
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }  

			 elseif($checkAccess->user_type_id==15) // Billing
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }    

			elseif($checkAccess->user_type_id==16) // Delivery
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }
            elseif($checkAccess->user_type_id==17) // Delivery
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            }
            elseif($checkAccess->user_type_id==18 || $checkAccess->user_type_id==19 || $checkAccess->user_type_id==20) // Accounts & System Admin, EPP
            {
                 //dd($checkAccess->user_type_id);
                if (Auth::attempt(['email' => $username, 'password' => $password, 'is_active' => 0],$remember )) 
                {
                    return Redirect::to('/');
                } 
                else
                {
                   return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username or password.');
                }
            } 

            		

        }
        else
        {
            return Redirect::back()->withInput($request->all)->with('msg', 'Invalid username.');
        }    
	} 
}
