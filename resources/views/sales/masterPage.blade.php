<!DOCTYPE html> 
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title> @if($pageTitle!='') {{ 'Sales Automation | '.$pageTitle }} @else Sales Automation | SSG @endif  </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon-->
    <link rel="icon" href="{{URL::asset('resources/sales/images/favicon.ico')}}" type="image/x-icon">

    <!-- Google Fonts -->

    
    <link href="{{ URL::asset('resources/sales/css/roboto.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('resources/sales/css/material.css') }}" rel="stylesheet" type="text/css">

    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css"> -->

    <!-- Bootstrap Core Css -->
    <link href="{{URL::asset('resources/sales/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{URL::asset('resources/sales/plugins/node-waves/waves.css')}}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{URL::asset('resources/sales/plugins/animate-css/animate.css')}}" rel="stylesheet" />

    <!-- JQuery DataTable Css -->
    <link href="{{URL::asset('resources/sales/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css')}}" rel="stylesheet">

    <!-- Morris Chart Css-->
    <link href="{{URL::asset('resources/sales/plugins/morrisjs/morris.css')}}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{URL::asset('resources/sales/css/style.css')}}" rel="stylesheet">


    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="{{URL::asset('resources/sales/css/themes/all-themes.css')}}" rel="stylesheet" />

    <!-- Bootstrap Select Css -->
    <link href="{{URL::asset('resources/sales/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />

    <style type="text/css">
        .button {
          background-color: #004A7F;
          -webkit-border-radius: 10px;
          border-radius: 10px;
          border: none;
          color: #FFFFFF;
          cursor: pointer;
          display: inline-block;
          font-family: Arial;
          font-size: 20px;
          padding: 5px 10px;
          text-align: center;
          text-decoration: none;
          -webkit-animation: glowing 1500ms infinite;
          -moz-animation: glowing 1500ms infinite;
          -o-animation: glowing 1500ms infinite;
          animation: glowing 1500ms infinite;
        }
        
        @-webkit-keyframes glowing {
          0% { background-color: #B20000; -webkit-box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; -webkit-box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; -webkit-box-shadow: 0 0 3px #B20000; }
        }

        @-moz-keyframes glowing {
          0% { background-color: #B20000; -moz-box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; -moz-box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; -moz-box-shadow: 0 0 3px #B20000; }
        }

        @-o-keyframes glowing {
          0% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
        }

        @keyframes glowing {
          0% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
          50% { background-color: #FF0000; box-shadow: 0 0 40px #FF0000; }
          100% { background-color: #B20000; box-shadow: 0 0 3px #B20000; }
        }
    </style>

    
</head>

<body onload="replaceContact();showPosition1()" class="theme-red">
    <!-- Page Loader -->
    <!-- <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div> -->
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    {{-- <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="START TYPING...">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div> --}}
    <!-- #END# Search Bar -->
    <!-- Top Bar -->


    <nav class="navbar">
        <div class="container-fluid">

            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                {{-- <img src="{{URL::asset('resources/sales/images/logo.png')}}" width="50" height="50" alt="SSG Logo"> --}} 
                <a class="navbar-brand" href="#"> 
                    @if(session('userType')=='Super Admin')
                        {{ session('businessName') }} | Super Admin
                    @elseif(session('userType')=='Sales Admin')
                        {{ session('businessName') }} | Sales Admin
                    @elseif(session('userType')=='Management')
                        {{ session('businessName') }} | Management
                    @elseif(session('userType')=='Sales Coordinator')
                        {{ session('businessName') }} | Sales Coordinator
					@elseif(session('userType')=='BILLING Dept')
                        {{ session('businessName') }} | Billing Dept
					@elseif(session('userType')=='Delivery Dept')
                        {{ session('businessName') }} | Delivery Dept
                    @elseif(session('userType')=='Accounts Dept')
                        {{ session('businessName') }} | Accounts Dept		
                    @elseif(session('userType')=='Distributor')
                       
						
							@if(session('isDepot')==1) {{-- Depot --}}
							    {{ session('businessName') }} | Depot
							@endif	
							
							@if(session('isDepot')==2) {{-- Depot --}}
							    {{ session('businessName') }} | Distributor	
							@endif
						
						
                    @elseif(session('userType')=='SM')

                    @elseif(session('userType')=='DSM')

                    @elseif(session('userType')=='ASM')

                    @elseif(session('userType')=='RSM')

                    @elseif(session('userType')=='TSM')
                        {{ session('businessName') }}
                    @elseif(session('userType')=='JTSM')   
                        
                    @elseif(session('userType')=='FO')
                        {{ session('businessName') }} | Sales Order
                    @elseif(session('userType')=='IMS Department')
                        {{ session('businessName') }} | IMS PART
                    @elseif(session('userType')=='System Admin')
                        {{ session('businessName') }} | System Admin IT

                    @elseif(session('userType')=='EPP')
                        {{ session('businessName') }}
                    @endif
                    
                </a>
            </div>
            {{-- <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    
                    <li><a href="javascript:void(0);" class="js-search" data-close="true"><i class="material-icons">search</i></a></li>
                    
                </ul>
            </div> --}}
        </div>
    </nav>
    <!-- #Top Bar -->
    <section id="sectionReplace">
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                @if(session('userType')!='FO')
                <div class="image">
                    <img src="{{URL::asset('resources/sales/images/user.png')}}" width="48" height="48" alt="User" />
                </div>
                @endif
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <b style="font-size: 16px;"> {{ ucfirst(session('userFullName')) }} </b> </div>
                    <div class="email">
                        @if(session('userType')=='Super Admin')
                            Management
                        @elseif(session('userType')=='Sales Admin')
                            Sales Admin
                        @elseif(session('userType')=='Management')
                            Management
                        @elseif(session('userType')=='Sales Coordinator')
                            Sales Coordinator
                        @elseif(session('userType')=='Distributor')
						
					       
							@if(session('isDepot')==1) {{-- Depot --}}
							    {{ session('businessName') }} | Depot
							@endif	
							
							@if(session('isDepot')==2) {{-- Depot --}}
							    {{ session('businessName') }} | Distributor	
							@endif
						
						@elseif(session('userType')=='BILLING Dept')
                            Billing	
						@elseif(session('userType')=='Delivery Dept')
                            Delivery
                        @elseif(session('userType')=='Accounts Dept')
                            Accounts
						@elseif(session('userType')=='SM')

                        @elseif(session('userType')=='DSM')

                        @elseif(session('userType')=='ASM')

                        @elseif(session('userType')=='RSM')

                        @elseif(session('userType')=='TSM')
                          Territory Manager
                        @elseif(session('userType')=='JTSM')   
                            
                        @elseif(session('userType')=='FO')
                            @if(session('divisionName')!='' && session('pointName')!='')
                            Division : {{ ucfirst(session('divisionName')) }} <br />
                            Point &nbsp;&nbsp;&nbsp;&nbsp; : {{ ucfirst(session('pointName')) }} <br />
                            @endif
                            Field Officer
                        @elseif(session('userType')=='IMS Department')
                            IMS
                        @elseif(session('userType')=='System Admin')
                            System Admin IT
                        @endif
                    </div>
                    {{-- <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">                            
                            <li><a href="{{ URL('/logout') }}"><i class="material-icons">input</i>Sign Out</a></li>
                        </ul>
                    </div> --}}
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            @include('include.sales.masterMenu')
            <!-- #Menu -->

            <!-- Footer -->
            @include('include.sales.masterFooter')
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
        <!-- Right Sidebar -->
        {{-- <aside id="rightsidebar" class="right-sidebar">
            <ul class="nav nav-tabs tab-nav-right" role="tablist">
                <li role="presentation" class="active"><a href="#skins" data-toggle="tab">SKINS</a></li>
                <li role="presentation"><a href="#settings" data-toggle="tab">SETTINGS</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
                    <ul class="demo-choose-skin">
                        <li data-theme="red" class="active">
                            <div class="red"></div>
                            <span>Red</span>
                        </li>
                        <li data-theme="pink">
                            <div class="pink"></div>
                            <span>Pink</span>
                        </li>
                        <li data-theme="purple">
                            <div class="purple"></div>
                            <span>Purple</span>
                        </li>
                        <li data-theme="deep-purple">
                            <div class="deep-purple"></div>
                            <span>Deep Purple</span>
                        </li>
                        <li data-theme="indigo">
                            <div class="indigo"></div>
                            <span>Indigo</span>
                        </li>
                        <li data-theme="blue">
                            <div class="blue"></div>
                            <span>Blue</span>
                        </li>
                        <li data-theme="light-blue">
                            <div class="light-blue"></div>
                            <span>Light Blue</span>
                        </li>
                        <li data-theme="cyan">
                            <div class="cyan"></div>
                            <span>Cyan</span>
                        </li>
                        <li data-theme="teal">
                            <div class="teal"></div>
                            <span>Teal</span>
                        </li>
                        <li data-theme="green">
                            <div class="green"></div>
                            <span>Green</span>
                        </li>
                        <li data-theme="light-green">
                            <div class="light-green"></div>
                            <span>Light Green</span>
                        </li>
                        <li data-theme="lime">
                            <div class="lime"></div>
                            <span>Lime</span>
                        </li>
                        <li data-theme="yellow">
                            <div class="yellow"></div>
                            <span>Yellow</span>
                        </li>
                        <li data-theme="amber">
                            <div class="amber"></div>
                            <span>Amber</span>
                        </li>
                        <li data-theme="orange">
                            <div class="orange"></div>
                            <span>Orange</span>
                        </li>
                        <li data-theme="deep-orange">
                            <div class="deep-orange"></div>
                            <span>Deep Orange</span>
                        </li>
                        <li data-theme="brown">
                            <div class="brown"></div>
                            <span>Brown</span>
                        </li>
                        <li data-theme="grey">
                            <div class="grey"></div>
                            <span>Grey</span>
                        </li>
                        <li data-theme="blue-grey">
                            <div class="blue-grey"></div>
                            <span>Blue Grey</span>
                        </li>
                        <li data-theme="black">
                            <div class="black"></div>
                            <span>Black</span>
                        </li>
                    </ul>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="settings">
                    <div class="demo-settings">
                        <p>GENERAL SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Report Panel Usage</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Email Redirect</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>SYSTEM SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Notifications</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Auto Updates</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                        <p>ACCOUNT SETTINGS</p>
                        <ul class="setting-list">
                            <li>
                                <span>Offline</span>
                                <div class="switch">
                                    <label><input type="checkbox"><span class="lever"></span></label>
                                </div>
                            </li>
                            <li>
                                <span>Location Permission</span>
                                <div class="switch">
                                    <label><input type="checkbox" checked><span class="lever"></span></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside> --}}
        <!-- #END# Right Sidebar -->
    </section>

    @yield('content')


    <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
        <div id="contents"></div>
    </div>

    <div class="modal fade" id="defaultModalManagement" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="contentsManagement">
            
        </div>
    </div>

    <!-- Model for edit company maung-->
    <div class="modal fade" id="defaultModal1" tabindex="-1" role="dialog">
    <div id="company"></div>
    </div>
    <div class="modal fade" id="defaultModal2" tabindex="-1" role="dialog">
     <div id="territory"></div>
    </div>

     <div class="modal fade" id="defaultModal3" tabindex="-1" role="dialog">
         <div id="point"></div>
     </div>
     <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
         <div id="procategory"></div>
     </div>

     <!-- Today maung Merge-->
      <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
         <div id="productsMaster"></div>
     </div>

<div class="modal fade" id="defaultModalNew" tabindex="-1" role="dialog">
         <div id="routeNew"></div>
     </div>
<!-- Model for edit company maung-->
<!-- Model start for Offer Sharif-->
    
    <div class="modal fade" id="defaultModalRP" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="productsnew"></div>
    </div>

    <div class="modal fade" id="defaultModalTarget" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="target"></div>
    </div>
	
	
	 <div class="modal fade" id="defaultModalDepot" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="depotList"></div>
    </div>
	
	<div class="modal fade" id="defaultModalPayment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="DepotPayment"></div>
    </div>
	
	<div class="modal fade" id="defaultModalCollection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div id="DepotCollection"></div>
    </div>
	
    <!-- Model End for Offer Sharif-->

    <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header" style="background-color: #A62B7F">
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                    <h4 class="modal-title" id="myModalLabel" >Order Delete</h4>
                </div>
            
                <div class="modal-body" style="text-align: center;">
                    <p><h4>Are you sure?</h4></p>
                    <p>You will not be able to recover this imaginary file!</p>
                    <p class="debug-url"></p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No, Cancel</button>
                    <button type="button" class="btn btn-danger btn-ok" onclick="confirmDeleteOrder()">Yes, delete it!</button>
                    
                </div>
            </div>
        </div>
    </div>

    <!--- Masud Rana --->

    <div class="modal fade" id="free-value-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            
                <div class="modal-header" style="background-color: #A62B7F">
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> --}}
                    <h4 class="modal-title" id="myModalLabel" >Order Free Item Delete</h4>
                </div>
            
                <div class="modal-body" style="text-align: center;">
                    <p><h4>Are you sure?</h4></p>
                    <p>You will not be able to recover this imaginary file!</p>
                    <p class="debug-url"></p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger btn-ok" onclick="deleteValueWiseCommission()">Yes, delete it!</button>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="{{URL::asset('resources/sales/plugins/jquery/jquery.min.js')}}"></script>

    <!-- Datepicker -->
    <link rel="stylesheet" href="{{URL::asset('resources/sales/datepicker/jquery-ui.css')}}">
    <script src="{{URL::asset('resources/sales/datepicker/jquery-ui.js')}}"></script>
   <script type="text/javascript">
        $( function() {
            $( "#fromdate" ).datepicker({ changeMonth: true,
          changeYear: true, dateFormat: 'dd-mm-yy' });
    	     $( "#fromdate1" ).datepicker({ changeMonth: true,
          changeYear: true, dateFormat: 'dd-mm-yy' });
                $( "#todate" ).datepicker({ changeMonth: true,
          changeYear: true, dateFormat: 'dd-mm-yy' });
    	   $( "#todate1" ).datepicker({ changeMonth: true,
          changeYear: true, dateFormat: 'dd-mm-yy' });

           $('.sumMasudQty').keyup(function() {
                var sumdata=0;
                $('.sumMasudQty').each(function(){

                    if($(this).val()!="")
                    {
                        sumdata += parseFloat($(this).val());
                    }
                });
                $("#totalQty").html(sumdata);
            });

           $('.sumMasudQtyFree').keyup(function() {
                var sumdata=0;
                $('.sumMasudQtyFree').each(function(){

                    if($(this).val()!="")
                    {
                        sumdata += parseFloat($(this).val());
                    }
                });
                $("#totalFreeQty").html(sumdata);
            });

            } );
    </script>

    <!-- Bootstrap Core Js -->
    <script src="{{URL::asset('resources/sales/plugins/bootstrap/js/bootstrap.js')}}"></script>

    <!-- Select Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/bootstrap-select/js/bootstrap-select.js')}}"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/jquery-slimscroll/jquery.slimscroll.js')}}"></script>


    <!-- Jquery Validation Plugin Css -->
    <script src="{{URL::asset('resources/sales/plugins/jquery-validation/jquery.validate.js')}}"></script>
    <script src="{{URL::asset('resources/sales/js/pages/forms/form-validation.js')}}"></script>

    <!-- Bucket -->
    <script src="{{URL::asset('resources/sales/js/pages/widgets/infobox/infobox-3.js')}}"></script>


   <!-- Waves Effect Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/node-waves/waves.js')}}"></script>

    <!-- Jquery DataTable Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/jquery.dataTables.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/extensions/export/buttons.flash.min.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/extensions/export/jszip.min.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/extensions/export/pdfmake.min.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/extensions/export/vfs_fonts.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-datatable/extensions/export/buttons.html5.min.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/jquery-sparkline/jquery.sparkline.js')}}"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/jquery-countto/jquery.countTo.js')}}"></script>

    <!-- Morris Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/raphael/raphael.min.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/morrisjs/morris.js')}}"></script>

    <!-- ChartJs -->
    <script src="{{URL::asset('resources/sales/plugins/chartjs/Chart.bundle.js')}}"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/flot-charts/jquery.flot.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/flot-charts/jquery.flot.resize.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/flot-charts/jquery.flot.pie.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/flot-charts/jquery.flot.categories.js')}}"></script>
    <script src="{{URL::asset('resources/sales/plugins/flot-charts/jquery.flot.time.js')}}"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="{{URL::asset('resources/sales/plugins/jquery-sparkline/jquery.sparkline.js')}}"></script>

    <!-- Chart Plugins Js -->
    <script src="{{URL::asset('resources/sales/plugins/chartjs/Chart.bundle.js')}}"></script>

    <!-- Custom Js -->
    <!-- <script src="{{URL::asset('resources/sales/js/pages/charts/morris.js')}}"></script> -->
    
    <script src="{{URL::asset('resources/sales/js/admin.js')}}"></script>
    <script src="{{URL::asset('resources/sales/js/pages/charts/chartjs.js')}}"></script>

    <script src="{{URL::asset('resources/sales/js/pages/tables/jquery-datatable.js')}}"></script>    
    <script src="{{URL::asset('resources/sales/js/pages/index.js')}}"></script>
    <script src="{{URL::asset('resources/sales/js/pages/ui/dialogs.js')}}"></script>


    <!-- Demo Js -->
    <script src="{{URL::asset('resources/sales/js/demo.js')}}"></script>


    <!--Custom JavaScript -->

    <script type="text/javascript">
        
          // -------------- - Start Masud Rana - ------------------

        // Image Preview 
        var loadFile = function(event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
        };

        var loadFile1 = function(event) {
            var output = document.getElementById('output1');
            output.src = URL.createObjectURL(event.target.files[0]);
        };

        
		function TuneAllRetailer(rid)
        {
            //alert(rid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = rid; //document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/retailer')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }
		
		function allRetailer()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/retailer')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function allOrderManageProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var retailer_id = document.getElementById('retailer_id').value;
            var order_id = document.getElementById('order_id').value;
            

            $.ajax({
                method: "POST",
                url: '{{url('/visit-order-manage-category-products')}}',
                data: {categories: categories,retailer_id:retailer_id,order_id:order_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function allOrderProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var point_id = document.getElementById('point_id').value;
            var retailer_id = document.getElementById('retailer_id').value;
            

            $.ajax({
                method: "POST",
                url: '{{url('/visit-order-category-products')}}',
                data: {categories: categories,point_id:point_id,retailer_id:retailer_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function allProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var retailer_id = document.getElementById('retailer_id').value;
            

            $.ajax({
                method: "POST",
                url: '{{url('/visit-category-products')}}',
                data: {categories: categories,retailer_id:retailer_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function allProductsFreeCommission()
        {
            //alert(id);
            
            var categories = document.getElementById('categories').value;

            $.ajax({
                method: "GET",
                url: '{{url('/visit-category-products-free')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function addQty(id)
        {
            //alert(id);
            var qty         = document.getElementById('qty'+id).value;            
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
            var wastageQty  = document.getElementById('wastageQty'+id).value;


            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
            var totalWastageQty= '';//document.getElementById('totalWastageQty').value;

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }

        function addQtyWastage(id)
        {
            //alert(id);
            var qty         = document.getElementById('wastageQty'+id).value;            
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
            //var wastageQty  = document.getElementById('wastageQty'+id).value;


            // For Total Value Show 

            //var totalQty    = '';//document.getElementById('totalQty').value;
            //var totalValue  = '';//document.getElementById('totalValue').value;
            //var totalWastageQty= '';//document.getElementById('totalWastageQty').value;

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            // if(totalQty=='0' && qty>0)
            // {
            //     document.getElementById('totalQty').value=qty;
            // }
            // else if(totalQty!='0' && qty >0)
            // {
            //     var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            // }
        }

        function editProducts(itemsID,pointID,routeID,retailderID,catID,skuID)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
				});
            
            $.ajax({
                method: "POST",
                url: '{{url('/items-edit')}}',
                data: {id: itemsID, pointID: pointID, routeID: routeID, retailderID: retailderID,catID: catID,skuID:skuID}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function itemDelete(deleteid)
        {
            //alert(deleteid);
            $('#itemsid').val(deleteid);

            $('#item-delete').modal('show');
        }

        function deleteProducts()
        {
            var itemsid = $('#itemsid').val();
            var itemsID = $('#order_det_id'+itemsid).val();
            var orderID = $('#order_id'+itemsid).val();
            var itemQty = $('#order_qty'+itemsid).val();
            var itemPrice = $('#p_unit_price'+itemsid).val();
            var itemCat = $('#cat_id'+itemsid).val();

            //alert(itemsID+"__"+orderID+"__"+itemQty+"__"+itemPrice+"__"+itemCat);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/items-delete')}}',
                data: {id: itemsID, orderID: orderID, itemQty: itemQty, itemPrice: itemPrice,itemCat:itemCat}
            })
            .done(function (response)
            {
                window.location.reload();       
            });            
        }


        function confirmDeleteOrder(itemsID,orderID,itemQty,itemPrice,itemCat)
        {
            //alert(itemsID+"__"+orderID+"__"+itemQty+"__"+itemPrice);

            var orderID = $('#orderid').val();
            var retailderid = $('#retailderid').val();
            var routeid = $('#routeid').val();

            //alert(orderID+"__"+retailderid+"__"+routeid);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/delete-order')}}',
                data: {orderID: orderID, retailderid: retailderid, routeid: routeid,itemCat: itemCat}
            })
            .done(function (response)
            {
                //alert(response);
                window.location.href = "{{ URL('/order-process') }}"+"/"+retailderid+"/"+routeid;
            });            
        }

        // for distributor order show
        function allOrders()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
		
		
		// for distributor order show
        function allOrdersExceptional()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/order-list-exceptional')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        // for distributor order show
        function freePending()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/free-pending-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function allWastagesDistributor()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/wastage-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        // for order update 
        // for order update 
        function qty(serialid,orderQty)
        {
            var qty         = document.getElementById("qty"+serialid).value;

            if(orderQty >= qty || orderQty <= qty)
            {
                document.getElementById('updateDelivery').disabled = false;
                document.getElementById('confirmDelivery').disabled = true;
            }
            else
            {
                document.getElementById('updateDelivery').disabled = true;
                document.getElementById('confirmDelivery').disabled = false;
            }
                
            if(qty!='')
            {
                //alert(serialid);
                
                var oldqty      = document.getElementById("oldqty"+serialid).value;
                var oldprice    = document.getElementById("oldprice"+serialid).value;

                //One Row
                var price       = document.getElementById("price"+serialid).value;

                // Hidden Grand Totl
                var totalHiddenQty       = document.getElementById("totalHiddenQty").value;
                var totalHiddenPrice     = document.getElementById("totalHiddenPriceSum").value;


                // Start Cal
                var rowtotalPrice   = qty * price;
                document.getElementById("rowPrice"+serialid).innerHTML=rowtotalPrice; 
                document.getElementById("oldqty"+serialid).value=qty; 
                document.getElementById("oldprice"+serialid).value=rowtotalPrice;             


                //Grand Total
                var newQtyTotalRowMinis = parseInt(totalHiddenPrice) - parseInt(oldprice);
                var newQtyTotalRowPlus  = parseInt(newQtyTotalRowMinis) + parseInt(rowtotalPrice);

                document.getElementById("totalPrice").innerHTML=newQtyTotalRowPlus;
                document.getElementById("net_amount").innerHTML=newQtyTotalRowPlus;
                document.getElementById("totalHiddenPriceSum").value=newQtyTotalRowPlus;
            }
        }


        function freeQtyNew(id)
        {
            //alert(id);

            var freeQty        = document.getElementById("freeqty"+id).value;           
            var oldFreeQty     = document.getElementById("oldFreeQty"+id).value;           
            var price          = document.getElementById('free_value'+id).value;
            var oldPrice       = document.getElementById('oldFreeValue'+id).value;

            var totalValueShow = (freeQty * price);
            document.getElementById("freeValueChange"+id).innerHTML=totalValueShow;
            document.getElementById("totalFreeValueM").innerHTML=totalValueShow;

            var totalHiddenFreeValue = document.getElementById("totalHiddenFreeValue").value;

            var newQtyTotalRowMinis = parseInt(totalHiddenFreeValue) - parseInt(oldPrice);
            var newQtyTotalRowPlus  = parseInt(newQtyTotalRowMinis) + parseInt(totalValueShow);

            document.getElementById("totalHiddenFreeValue").value=newQtyTotalRowPlus;

             document.getElementById('value'+id).value=totalValueShow;

        }

        function freeQty(id)
        {

            //LD 
            var qty         = document.getElementById('qty'+id).value; 
            var freeQty     = document.getElementById("freeQty"+id).value;           
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
           
            if(Number(qty) > Number(freeQty))
            {
                alert("Requisition Free Qty must be less than or equal Free Qty.");
                document.getElementById("qty"+id).value = 0;
                document.getElementById("value"+id).value = 0;
                return false;
            } 

            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;        

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }



        //FOR REPORT 

        function allDelivery()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function allOrderVsDelivery()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/order-vs-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function ConfirmDelivery(orderid)
        {
            
            var checked=false;
            var packageName = '';
            var elements = document.getElementsByName("offer_type");

            //alert(elements.value);
            for(var i=0; i < elements.length; i++){
                if(elements[i].checked) {
                    checked = true; 
                    packageName = elements[i].value;          
                }
            }

            if (!checked) {
                alert('Please select offer.');
                return false;
            }
            else if (checked) {
                //alert('Your data right');
            }

            $.ajax({
                method: "GET",
                url: '{{ URL('/confirm-delivery') }}',
                data: {orderid: orderid , offer_type: packageName}
            })
            .done(function (response)
            {
                //alert(response);
                //location.reload();
                window.location.href="{{ URL('/order')}}";                
            });                 
        }

        function allCategoryWiseOrder()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/category-wise-order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function categoryWiseProduct()
        {
            var category = document.getElementById("category").value;
            //alert(category);
            $.ajax({
                    method: "GET",
                    url: '{{url('/report/category-wise-product')}}',
                    data: {category: category}
            })
            .done(function (response)
            {
                //alert(response);
                $('#productsall').html(response);
                //document.getElementById("productsall").innerHTML=response;                
            });
        }

        function categoryWiseProductFo()
        {
            var category = document.getElementById("category").value;
            //alert(category);
            $.ajax({
                    method: "GET",
                    url: '{{url('/report/fo/category-wise-product')}}',
                    data: {category: category}
            })
            .done(function (response)
            {
                //alert(response);
                $('#productsall').html(response);
                //document.getElementById("productsall").innerHTML=response;                
            });
        }

        function allSkuWiseOrder()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;
            var category    = document.getElementById('category').value;
            var products    = document.getElementById('products').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/sku-wise-order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos, category: category, products: products}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function allSkuWiseDelivery()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;
            var category    = document.getElementById('category').value;
            var products    = document.getElementById('products').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/sku-wise-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos, category: category, products: products}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        // For Field Officer Report Start

        function allFoOrder()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routes      = document.getElementById('routes').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/fo/order-list')}}',
                    data: {fromdate: fromdate, todate: todate, routes: routes}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function allFoDelivery()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routes      = document.getElementById('routes').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/fo/delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, routes: routes}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function allFoOrderVsDelivery()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routes      = document.getElementById('routes').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/fo/order-vs-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, routes: routes}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function allFoCategoryWiseOrder()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var category    = document.getElementById('category').value;

            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/fo/category-wise-list')}}',
                    data: {fromdate: fromdate, todate: todate, category: category}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function visitReports()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var orderType   = document.getElementById('orderType').value;

            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/fo/visit-list')}}',
                    data: {fromdate: fromdate, todate: todate, orderType: orderType}
                })
                .done(function (response)
                {
                    $('#showHiddenDiv').html(response);                
                });
            }         
        }

        function allProductWise()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;            
            var category    = document.getElementById('category').value;
            var products    = document.getElementById('products').value;

            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/fo/product-wise-list')}}',
                    data: {fromdate: fromdate, todate: todate, category: category, products: products}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }



        // Print Report 

        function printReport()
        {
            var divContents = $("#printMe").html();//div which have to print
            var printWindow = window.open('', '', 'height=700,width=1080');
            printWindow.document.write('<html><head><title></title>');
            printWindow.document.write('<link href="{{URL::asset('resources/sales/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet">');//external styles
           // printWindow.document.write('<link href="{{URL::asset('resources/sales/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet">');
            printWindow.document.write('<link href="{{URL::asset('resources/sales/css/style.css')}}" rel="stylesheet">');
            printWindow.document.write('<link href="{{URL::asset('resources/sales/css/print.css')}}" rel="stylesheet">');
            printWindow.document.write('</head><body>');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();

            printWindow.onload=function(){
                printWindow.focus();                                         
                printWindow.print();
                printWindow.close();
            }
        }


        // Password Change
        function changePassword()
        {

            var oldPassword     = document.getElementById('oldPassword').value;
            var newPassword     = document.getElementById('newPassword').value;
            var confirmPassword = document.getElementById('confirmPassword').value;

            if(oldPassword=='' || newPassword=='' || confirmPassword=='')
            {
                if(oldPassword=='')
                {
                    document.getElementById('oldPassword').style.borderColor='#FF0000';
                    document.getElementById('oldPassword').focus();
                    return true;
                }
                if(newPassword=='')
                {
                    document.getElementById('newPassword').style.borderColor='#FF0000';
                    document.getElementById('newPassword').focus();
                    return true;
                }
                if(confirmPassword=='')
                {
                    document.getElementById('confirmPassword').style.borderColor='#FF0000';
                    document.getElementById('confirmPassword').focus();
                    return true;
                }
            }
            else if(oldPassword!='' && newPassword!='' && confirmPassword!='')
            {
                //alert('Yes');

                if(newPassword!=confirmPassword)
                {
                   $('#errorMsg').show(); // show error message show
                   $('#errorMsgShow').html('Sorry confirm password not match');
                   return true;
                }
                else
                {
                    $('#errorMsg').hide();

                    $.ajax({
                        method: "GET",
                        url: '{{url('/password/check-password')}}',
                        data: {oldPassword: oldPassword}
                    })
                    .done(function (response)
                    {
                        //alert(response);
                        if(response==0)
                        {
                            $('#errorMsg').show(); // show error message show
                            $('#errorMsgShow').html('Sorry old password not match');
                            return false;
                        }
                        else
                        {
                            $('#errorMsg').hide();
                            //alert(newPassword);

                            $.ajax({
                                method: "GET",
                                url: '{{url('/password/change-password-submit')}}',
                                data: {newPassword: newPassword}
                            })
                            .done(function (response)
                            {
                                //alert(response);
                            
                                $('#successMsg').show(); // show success message show
                                
                                document.getElementById('oldPassword').value='';
                                document.getElementById('newPassword').value='';
                                document.getElementById('confirmPassword').value='';

                                setInterval(logout, 3000);
                            });
                        }
                        //$('#showHiddenDiv').html(response);                
                    });
                }
            }
        }


        // Attendance for Field officer (FO)

        // function getLocation() {
            
        //     if (navigator.geolocation) {
        //         //alert('Yes');
        //         navigator.geolocation.getCurrentPosition(showPosition);
        //     } else {
        //         x.innerHTML = "Geolocation is not supported by this browser.";

        //     }
        // }

        // function showPosition(position) 
        // {    
        //     var  latitude   = position.coords.latitude;
        //     var  longitude  = position.coords.longitude;

        //     attendanceFo(latitude,longitude);
        // }

        function getLocation()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //alert(latitude+"__"+longitude);

            var latitude      = null;
            var longitude     = null;
            var distributor   = document.getElementById('distributor').value;
            var routes        = document.getElementById('routes').value;
            var retailer      = document.getElementById('retailer').value;
            var location      = document.getElementById('location').value;
            var inOutStatus   = document.getElementById('inOutStatus').value;

            if(location=="" || routes=="")
            {
                $('#errorMsg').show('medium');                
                return true;                
            }
            else
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/attendance-in-out')}}',
                    data: {distributor: distributor, routes:routes, retailer: retailer, location: location, inOutStatus: inOutStatus, latitude: latitude, longitude: longitude}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#errorMsg').hide('medium');
                    $('#successMsg').show('medium');                    

                    if (response==1) 
                    {
                      $('#in').hide();
                      $('#out').show();
                      document.getElementById('inOutStatus').value=3;
                      document.getElementById('location').value='';
                      $('#successMsgShow').html(' Successfull');
                      loadAttendanceList(); 
                    }
                    else
                    {
                        $('#in').hide();
                        $('#out').show();
                        document.getElementById('inOutStatus').value=3;
                        document.getElementById('location').value='';
                        $('#successMsgShow').html(' Successfull');
                        loadAttendanceList();
                    }
                                    
                });
            }          
        }

        function loadAttendanceList()
        {
            //alert('IN');
            $("html, body").animate({ scrollTop: 260 }, "slow"); 

            $.ajax({
                method: "GET",
                url: '{{url('/attendance-list')}}',
                //data: {}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv12').html(response);
            });
        }

        // Utility

        function utilityReason()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var type   = document.getElementById('type').value;
            
            if(type=="SUGGESTION")
            {       
                return true;                
            }
            else
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/utility-type')}}',
                    data: {reason: type}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#reasonDiv').html(response);
                });
            }          
        }

        //Admin for FO

        function retailerActiveOrInactive()
        {
            var status   = document.getElementById('status').value;

            $.ajax({
                method: "GET",
                url: '{{url('/fo/admin-active')}}',
                data: {status: status}
            })
            .done(function (response)
            {
                window.location.reload();
            });                      
        }




        // Sales Coordinator 

        function allPoints()
        {
            //alert('Yes');
            var divisions = document.getElementById('divisions').value;
            
            //alert(divisions);
            $.ajax({
                method: "GET",
                url: '{{URL('/sc/points-list')}}',
                data: {divisions: divisions}
            })
            .done(function (response)
            {
                //alert(response);
                $('#pointsDiv').load(response);                
            });            
        }

        function foPerformancePoints()
        {
            //alert('Yes');
            var divisions = document.getElementById('divisions').value;
            
            //alert(divisions);
            $.ajax({
                method: "GET",
                url: '{{URL('/sc/div-points-list')}}',
                data: {divisions: divisions}
            })
            .done(function (response)
            {
               // alert(response);
                $('#fopoints').html(response);                
            });            
        }


        function foPerformanceTerritory()
        {
            //alert('Yes');
            var divisions = document.getElementById('divisions').value;
            
            //alert(divisions);
            $.ajax({
                method: "GET",
                url: '{{URL('/sc/div-territory-list')}}',
                data: {divisions: divisions}
            })
            .done(function (response)
            {
                //alert(response);
                $('#territory').html(response);                
            });            
        }


        function dbPerformancePoints()
        {
            //alert('Yes');
            var territory = document.getElementById('territory').value;
            
            //alert(divisions);
            $.ajax({
                method: "GET",
                url: '{{URL('/sc/territory-points-list')}}',
                data: {territory: territory}
            })
            .done(function (response)
            {
               // alert(response);
                $('#fopoints').html(response);                
            });            
        }

        function dbPerformanceList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //alert('in');
            
            var todate      = document.getElementById('todate').value;
            var channel     = document.getElementById('channel').value;
            var divisions   = document.getElementById('divisions').value;
            var pointsID    = document.getElementById('pointsID').value;
            var territory   = document.getElementById('territory').value;
              $.ajax({
                    method: "POST",
                    url: '{{url('sc/db-performance-list')}}',
                    data: {todate: todate,channel:channel,divisions:divisions,pointsID:pointsID, territory: territory}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
                    
        }

        function getFoPerformanceList(slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/ims/get-point-fo-list')}}',
                data: {point_id: slID}
            })
            .done(function (response)
            {
             $('#foID1').html(response);  
              
            });            
        }

        function foPerformanceList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //alert('in');
            
            var todate      = document.getElementById('todate').value;
			var channel     = document.getElementById('channel').value;
            var divisions   = document.getElementById('divisions').value;
            var pointsID    = document.getElementById('pointsID').value;
            var fos         = document.getElementById('foID').value;
              $.ajax({
                    method: "POST",
                    url: '{{url('sc/fo-performance-list')}}',
                    data: {todate: todate,channel:channel,divisions:divisions,pointsID:pointsID, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
                    
        }

        // Master Offers 

        function offerTypesActive()
        {
            var offerTypes = $('#offerTypes').val();

            //alert(offerTypes);
            if(offerTypes==3)
            {
                document.getElementById('from_slab').disabled = false;
                document.getElementById('to_slab').disabled = false;
                document.getElementById('add').disabled = false;
            }
            else
            {
                document.getElementById('from_slab').disabled = true;
                document.getElementById('to_slab').disabled = true;
                document.getElementById('add').disabled = true;
            }
        }

        // add more slab
        function addMoreSlab()
        {
          //alert("new row working");
          
            //alert("In");
            var from_slab = $('#from_slab').val();
            var to_slab   = $('#to_slab').val();

            if ((from_slab=='' || to_slab=="") || (isNaN(from_slab) < 0 || isNaN(to_slab) < 0))
            {
              if (from_slab=='' || isNaN(from_slab) < 0) 
              {
                document.getElementById('from_slab').style.border ="1px solid #FF0000";
                document.getElementById('from_slab').focus();
                return false;
              }

              if (to_slab=='' || to_slab < 0) 
              {
                document.getElementById('to_slab').style.bordercolor ="1px solid #FF0000";
                document.getElementById('to_slab').focus();
                return false;
              }
            }
            else if((from_slab!='' && to_slab!="") || (from_slab > 0 && to_slab > 0))
            {
              document.getElementById('from_slab').style.bordercolor="";
              document.getElementById('to_slab').style.bordercolor="";
            }

            strCountField = '#prof_count';      
            intFields = $(strCountField).val();
            intFields = Number(intFields);    
            newField = intFields + 1;
            newField1 = intFields + 0;

            strNewField = '<tr class="prof blueBox" id="prof_' + newField + '" style="background: #BFBFBF">\
            <input type="hidden" id="id' + newField + '" name="id' + newField + '" value="-1" />\
            <td><input type="text" id="from_slab1' + newField + '" name="from_slab1[]" value="'+from_slab+'" readonly="" style="padding:5px 10px;"/></td>\
            <td><input type="text" id="to_slab1' + newField + '" name="to_slab1[]" value="'+to_slab+'" readonly=""  style="padding:5px 10px;" /></td>\
            <td align="center" valign="middle" style="background:#F44336"><img src="{{URL::asset('resources/sales/images/icon/ic_delete.png')}}" id="prof_' + newField + '"  value="prof_' + newField + '" onClick="del('+ newField +')" style="cursor:pointer; margin-top:5px;" alt="Delete slab" title="Delete this slab"></td>\
            </tr>\
            <div class="nopass"><!-- clears floats --></div>\
            '
            ;

            $("#prof_" + intFields).after(strNewField);    
            $("#prof_" + newField).slideDown("medium");
            $(strCountField).val(newField);       
            $('#from_slab').val('');
            $('#to_slab').val('');         
        }

        // delete function for slab multi-row
        function del(id)
        {
          //alert(id);
          document.getElementById("from_slab1"+id).value='';
          document.getElementById("to_slab1"+id).value='';
          document.getElementById("prof_"+id).style.display="none";
        }

        function hover(id)
        {
            document.getElementById(id).style.border ="";
        }

        function hoverHidden(id)
        {
            document.getElementById("dalert").style.display ="none";
        }

        //bundle product slab

        // add more slab
        function addMoreSlabPro()
        {
            var from_slab = $('#from_slab').val();
            var to_slab   = $('#to_slab').val();

            var checked=false;
            var slabsPrice = 0;
            var elements = document.getElementsByName("slabs");
            for(var i=0; i < elements.length; i++){
                if(elements[i].checked) {
                    checked = true;                   
                }
            }
            if (!checked) {
                //alert('Your data wrong, some error message');
                $('#dalert').show();
            }
            else if (checked) {
                //alert('Your data right');
                $('#dalert').hide();
            }

            
            if ((from_slab=='' || to_slab=="") || (isNaN(from_slab) < 0 || isNaN(to_slab) < 0))
            {
              if (from_slab=='' || isNaN(from_slab) < 0) 
              {
                document.getElementById('from_slab').style.border ="1px solid #FF0000";
                document.getElementById('from_slab').focus();
                return false;
              }

              if (to_slab=='' || to_slab < 0) 
              {
                document.getElementById('to_slab').style.bordercolor ="1px solid #FF0000";
                document.getElementById('to_slab').focus();
                return false;
              }
            }
            else if((from_slab!='' && to_slab!="") || (from_slab > 0 && to_slab > 0))
            {
              document.getElementById('from_slab').style.bordercolor="";
              document.getElementById('to_slab').style.bordercolor="";
            }

            document.getElementById('from_slab').style.bordercolor="";
            document.getElementById('to_slab').style.bordercolor="";

            
            //var slabsPrice = document.getElementById("slabsPrice").value;

            strCountField = '#prof_count';      
            intFields = $(strCountField).val();
            intFields = Number(intFields);    
            newField = intFields + 1;
            newField1 = intFields + 0;

            strNewField = '<tr class="prof blueBox" id="prof_' + newField + '" style="background: #BFBFBF">\
            <input type="hidden" id="id' + newField + '" name="id' + newField + '" value="-1" />\
            <td><input type="text" id="from_slab1' + newField + '" name="from_slab1[]" value="'+from_slab+'" readonly="" style="padding:5px 10px; background-color:#FFF;" class="form-control"/></td>\
            <td><input type="text" id="to_slab1' + newField + '" name="to_slab1[]" value="'+to_slab+'" readonly=""  style="padding:5px 10px; background-color:#FFF;" class="form-control"/></td>\
            <td align="center" valign="middle" style="background:#F44336"><img src="{{URL::asset('resources/sales/images/icon/ic_delete.png')}}" id="prof_' + newField + '"  value="prof_' + newField + '" onClick="del('+ newField +')" style="cursor:pointer; margin-top:5px;" alt="Delete slab" title="Delete this slab"></td>\
            </tr>\
            <div class="nopass"><!-- clears floats --></div>\
            '
            ;

            $("#prof_" + intFields).after(strNewField);    
            $("#prof_" + newField).slideDown("medium");
            $(strCountField).val(newField);       
            $('#from_slab').val('');
            $('#to_slab').val('');
          
        }

        // delete function for slab multi-row
        function delPro(id)
        {
          //alert(id);
          document.getElementById("from_slab1"+id).value='';
          document.getElementById("to_slab1"+id).value='';
          document.getElementById("prof_"+id).style.display="none";
        }

        // Checked/unchecked commond action for javascript

        // Listen for click on toggle checkbox
        function selectAll(source) 
        {
            checkboxes = document.getElementsByName('divisions[]');
            for(var i in checkboxes)
                checkboxes[i].checked = source.checked;
        }

                
        function divisionWisePoints()
        {
            //alert('Yes');
            var checkItem=document.getElementsByName('divisions[]');
            var m=0;
            var Mdata= new Array();

            for(var i=0; i < checkItem.length; i++)
            {
                if(checkItem[i].checked)
                {
                    Mdata[m]=checkItem[i].value;
                    m++;
                }
            }

            var businessType = $('#offerBusinessTypes').val();

            if(businessType=='')
            {
                $('#dalert').show();
                $('#msgOffer').html('Please select business type.');
                $("html, body").animate({ scrollTop: 560 }, "slow");
                return false;
            }

            $('#dalert').hide();
            //alert(Mdata+"__"+businessType);
            $.ajax({
                method: "GET",
                url: '{{URL('/offers/bundle-points')}}',
                data: {divisions: Mdata, businessType: businessType}
            })
            .done(function (response)
            {
                $('#pointsDiv').html(response);                
            });   
        }

        function offerCategoryActive()
        {
            //alert('IN');
            var businessType = document.getElementById('offerBusinessTypes').value;
            $.ajax({
                method: "GET",
                url: '{{url('/offers/bundle-category')}}',
                data: {businessType: businessType}
            })
            .done(function (response)
            {                
                $('#offerCategory').html(response);                
            });   
        }

        function divisionAndPointWiseroutes()
        {
            //alert('Yes');
            var checkItem=document.getElementsByName('points[]');
            var m=0;
            var Mdata= new Array();

            for(var i=0; i < checkItem.length; i++)
            {
                if(checkItem[i].checked)
                {
                    Mdata[m]=checkItem[i].value;
                    m++;
                }
            }

            $.ajax({
                method: "GET",
                url: '{{URL('/offers/bundle-route')}}',
                data: {points: Mdata}
            })
            .done(function (response)
            {
                $('#routesDiv').html(response);                
            });   
        }

        //offer delete
        function bundleActiveOrInactive()
        {
            var offerid   = document.getElementById('offerid').value;

            $.ajax({
                method: "GET",
                url: '{{url('/offers/bundle-delete')}}',
                data: {offerid: offerid}
            })
            .done(function (response)
            {
                window.location.reload();
            });                      
        }

        function activeInactiveOffers(status,offerid)
        {
            //alert(status+"__"+offerid);
            document.getElementById('offeridM').value=offerid;
            document.getElementById('offerStatusM').value=status;

            $('#BundleOfferActiveOrInactive').modal('show');
        }
        //offer Active or Inactive
        function bundleOfferActiveOrInactive()
        {
            var offerid       = document.getElementById('offeridM').value;
            var offerStatus   = document.getElementById('offerStatusM').value;

            $.ajax({
                method: "GET",
                url: '{{url('/offers/bundle-offer-active')}}',
                data: {offerid: offerid, offerStatus:offerStatus}
            })
            .done(function (response)
            {
                window.location.reload();
            });                      
        }

        function bundleProductActiveOrInactive()
        {
            var offerid   = document.getElementById('offerid').value;

            $.ajax({
                method: "GET",
                url: '{{url('/offers/bundle-product-delete')}}',
                data: {offerid: offerid}
            })
            .done(function (response)
            {
                window.location.reload();
            });                      
        }


        function validate() {
            //The key here is that you get all the "options[]" elements in an array
            var checked=false;
            var elements = document.getElementsByName("divisions[]");
            for(var i=0; i < elements.length; i++){
                if(elements[i].checked) {
                    checked = true;
                }
            }
            if (!checked) {
                //alert('Yada yada yada, some error message');
                $('#dalert').show();
            }
            return checked;
        }


        // for bundle details
        function bundleOffers(offerid)
        {
            //alert(offerid);

            $.ajax({
                method: "GET",
                url: '{{URL('/offers/bundle-offer-details')}}',
                data: {offerid: offerid}
            })
            .done(function (response)
            {
                $('#offerDetils').html(response);                
            });   
        }

        // for bundle type wise slab or ssg product show
        function showSlabOrProduct(typeid)
        {
            //alert(offerid);

            // 1 for SSG Product
            //2 for Others
            $.ajax({
                method: "GET",
                url: '{{URL('/offers/bundle-offer-types')}}',
                data: {typeid: typeid}
            })
            .done(function (response)
            {
                //alert(response);

                $('#showSlabOrProduct').html(response);
                checkEditOffer(response)               
            });   
        }

        function checkEditOffer(response)
        {
            var offerProId = document.getElementById('offerProId').value;

            if(offerProId!='')
            {
               $("#showSlabOrProduct1").show();
               $('#showSlabOrProduct1').html(response);   

               $("#showSlabOrProduct").hide(); 
               $("#hiddenPro").hide();
            }
        }

        // for ssg category wise product show
        function ssgCategoryWisePro(categoryid)
        {
            //alert(offerid);

            $.ajax({
                method: "GET",
                url: '{{URL('/offers/bundle-offer-category-wise-pro')}}',
                data: {categoryid: categoryid}
            })
            .done(function (response)
            {
                $('#ssgProducts').html(response);                
            });   
        }

        function ssgCategoryWisePro1(categoryid)
        {
            //alert(categoryid);

            $.ajax({
                method: "GET",
                url: '{{URL('/offers/bundle-offer-category-wise-pro')}}',
                data: {categoryid: categoryid}
            })
            .done(function (response)
            {
                //alert(response);
                //document.getElementById('ssgProducts').style.display="none";

                $('#ssgProducts').hide();    
                $('#ssgProducts1').html(response);                
            });   
        }


        function showBundleProduct(offerMainId,status)
        {
            var netAmount = $('#netAmount').val(); // memo total net amount
            var orderid   = $('#orderid').val(); // memo total net amount
            //status = 0 is no order number
            //status = 1 is order number
            
            //alert(netAmount);

            $.ajax({
                method: "GET",
                url: '{{URL('/bundle-gifts')}}',
                data: {offerMainId: offerMainId, netAmount:netAmount, status:status, orderid:orderid}
            })
            .done(function (response)
            {
                $('#showBundleProductCon').modal('show');
                $('#showBundleProductContent').html(response);                
            });  
        }


        function giftsAdded(offerid,status)
        {
            //alert(offerid+"__"+status);

            var checked=false;
            var slabsPrice = 0;
            var elements = document.getElementsByName("giftsProduct[]");
            for(var i=0; i < elements.length; i++){
                if(elements[i].checked) {
                    checked = true;                   
                }
            }
            if (!checked) {
                //alert('Your data wrong, some error message');
                $('#dalert').show();
                return false
            }
            else if (checked) {
                //alert('Your data right');
                $('#dalert').hide();
            }

            var retailderid = document.getElementById('retailderid').value;
            var orderid     = document.getElementById('orderid').value;
            var checkItem   = document.getElementsByName('giftsProduct[]');

            //alert(orderid+"__"+status);

            var m=0;
            var Mdata= new Array();

            for(var i=0; i < checkItem.length; i++)
            {
                if(checkItem[i].checked)
                {
                    Mdata[m]=checkItem[i].value;
                    m++;
                }
            }

            $.ajax({
                method: "GET",
                url: '{{URL('/bundle-gifts-added')}}',
                data: {offerid: offerid, giftid:Mdata, retailderid:retailderid, status:status, orderid:orderid}
            })

            .done(function (response)
            {
                window.location.reload();
            });

            /*.done(function (response)
            {
                $('#showBundleProductCon').modal('hide');
                $('#showBundleProductConMsg').modal('show');
                setInterval(function() {
                    location.reload(); 
                }, 1200);
                            
            }); */ 
        }

        function attendanceReports()
        {
           
            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;            
            
            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/report/fo/attendance-list')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function routeWiseRetailers(routesid)
        {
            $.ajax({
                method: "GET",
                url: '{{url('/attendance-retailers')}}',
                data: {routesid: routesid}
            })
            .done(function (response)
            {
                $('#retailerDivM').html(response);                
            });                    
        }

        function dashboardFoOrders(serialid)
        {
            $.ajax({
                method: "GET",
                url: '{{url('/dashboard/fo-orders')}}',
                data: {serialid: serialid}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');                
                $('#contents').html(response);                
            });                    
        }

        function dashboardFoOrdersExcel()
        {
            $.ajax({
                method: "GET",
                url: '{{url('/dashboard/fo-orders-excel')}}',
                data: {orders: ''}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');                
                $('#contents').html(response);                
            });                    
        }

        function dashboardManagement(serialid)
        {
            $.ajax({
                method: "GET",
                url: '{{url('/dashboard/management')}}',
                data: {serialid: serialid}
            })
            .done(function (response)
            {
                //alert(response);
                $('#defaultModalManagement').modal('show');                
                $('#contentsManagement').html(response);                
            });                    
        }


        function totalsFO()
        {
             // for qty

            var itemQty = document.getElementsByName('qty[]');
            var m=0;
            var sumQty = 0;
            var itemQtyArray = new Array();

            for(var i=0; i < itemQty.length; i++)
            {
                if(itemQty[i].value!= "")
                {
                    itemQtyArray[m]=itemQty[i].value;
                    m++;
                }
            }           
            
            sumQty = itemQtyArray.reduce((a, b) => parseInt(a) + parseInt(b), 0);
            document.getElementById('totalQty').value=sumQty;

            // for value

            var itemValue = document.getElementsByName('value[]');
            var mv=0;
            var sumValue = 0;
            var itemvalueArray = new Array();

            for(var i=0; i < itemValue.length; i++)
            {
                if(itemValue[i].value!= "")
                {
                    itemvalueArray[mv]=itemValue[i].value;
                    mv++;
                }
            }           
            
            sumValue = itemvalueArray.reduce((a, b) => parseInt(a) + parseInt(b), 0);
            document.getElementById('totalValue').value=sumValue;

        }

        function totalsWastagesFO()
        {
             // for qty

            var itemQty = document.getElementsByName('wastageQty[]');
            var m=0;
            var sumQty = 0;
            var itemQtyArray = new Array();

            for(var i=0; i < itemQty.length; i++)
            {
                if(itemQty[i].value!= "")
                {
                    itemQtyArray[m]=itemQty[i].value;
                    m++;
                }
            }           
            
            sumQty = itemQtyArray.reduce((a, b) => parseInt(a) + parseInt(b), 0);
            document.getElementById('totalWastage').value=sumQty;

        }

        function deleteSpecialValueWiseProduct(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
                $.ajax({
                    method: "GET",
                    url: '{{url('/offer/other-delete')}}',
                    data: {id: slID}
                })
                location.reload(); 
            }                 
        }

        function editSpecialValueWiseProduct(slID)
        {            
            $.ajax({
                method: "GET",
                url: '{{url('/offer/other-edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               $('#defaultModalRP').modal('show');
               $('#productsnew').html(response);                              
            });            
        }


        

        function showSpecialProductM(offerMainId,status)
        {
            var orderid = document.getElementById('orderid').value;

            $.ajax({
                method: "GET",
                url: '{{URL('/show-special-products')}}',
                data: {orderid: orderid}
            })
            .done(function (response)
            {
                $('#showBundleProductCon').modal('show');
                $('#showBundleProductContent').html(response);                
            });  
        }

        function showRegularProductM(offerMainId,status)
        {
            var orderid = document.getElementById('orderid').value;

            $.ajax({
                method: "GET",
                url: '{{URL('/show-regular-products')}}',
                data: {orderid: orderid}
            })
            .done(function (response)
            {
                $('#showBundleProductCon').modal('show');
                $('#showBundleProductContent').html(response);                
            });  
        }


        //------------------ Return Start------------------------
         
         
        function returnOnlyRouteM()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/return-only-retailer')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        
        function returnOnlyProductsM()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/return-only-category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


        function totalsFOM()
        {
             // for qty

            var itemQty = document.getElementsByName('return_qty[]');
            var m=0;
            var sumQty = 0;
            var itemQtyArray = new Array();

            for(var i=0; i < itemQty.length; i++)
            {
                if(itemQty[i].value!= "")
                {
                    itemQtyArray[m]=itemQty[i].value;
                    m++;
                }
            }           
            
            sumQty = itemQtyArray.reduce((a, b) => parseInt(a) + parseInt(b), 0);
            document.getElementById('totalQty').value=sumQty;

            // for value

            var itemValue = document.getElementsByName('return_value[]');
            var mv=0;
            var sumValue = 0;
            var itemvalueArray = new Array();

            for(var i=0; i < itemValue.length; i++)
            {
                if(itemValue[i].value!= "")
                {
                    itemvalueArray[mv]=itemValue[i].value;
                    mv++;
                }
            }           
            
            sumValue = itemvalueArray.reduce((a, b) => parseInt(a) + parseInt(b), 0);
            document.getElementById('totalValue').value=sumValue;

        }

        function editReturnOnlyProductsM(itemsID)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/return-only-items-edit')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        
        function returnOnlyItemDeleteM(orderID)
        {
            //alert(orderID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/fo/return-only-items-del')}}',
                data: {id: orderID}
            })
            .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            
                location.reload(); 
            }                 
        }

        
        function confirmDeleteOnlyReturnM()
        {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var orderID     = $('#orderid').val();
            var retailderid = $('#retailderid').val();
            var routeid     = $('#routeid').val();

            //alert(orderID+"__"+retailderid+"__"+routeid);

            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/delete-only-return')}}',
                data: {orderID: orderID, retailderid: retailderid, routeid: routeid}
            })
            .done(function (response)
            {
                //alert(response);
                window.location.href = "{{ URL('/fo/return-only-product') }}";
            });            
        }
        
        
        // Change Section
        
        
        // for distributor/depot return only order show
        function allReturnOnlyOrders()
        {
            //alert('hellow');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/return-only-order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
        
        
        //------------------ Return End------------------------


        //------------------ Return & Change Start ------------

        function returnRouteM()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/return-retailer')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function returnProductsM()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/return-category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


        function editReturnProductsM(itemsID)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/return-items-edit')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }


        function returnItemDeleteM(deleteid,orderID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/fo/return-items-del')}}',
                data: {id: deleteid, orderID: orderID}
            })
            .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            
                location.reload(); 
            }                 
        }

        
        function confirmDeleteReturnM()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var orderID = $('#orderid').val();
            var retailderid = $('#retailderid').val();
            var routeid = $('#routeid').val();

            $.ajax({
                method: "POST",
                url: '{{url('/fo/delete-return')}}',
                data: {orderID: orderID, retailderid: retailderid, routeid: routeid}
            })
            .done(function (response)
            {
                window.location.href = "{{ URL('/fo/returnproduct') }}";
            });            
        }
        
        
        // Change Section
        
        
        // for distributor/depot return order show
        function allReturnOrdersM()
        {
            //alert('Hello Masud');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;

            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/fo/return-order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        // for distributor/depot return order show
        function allReturnOrdersMM()
        {
            //alert('Hello Masud');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;

            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/fo/return-change-order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        //------------------ Return & Change End ------------


        //------------------ STOCK --------------------------

        function stockProductsM()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/dist/stock_products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        //------------------ STOCK --------------------------

        //------------------ REQUISITION --------------------

        function allRequisitionProductsM()
        {
            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "GET",
                url: '{{url('/dist/req-category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function editDepotProductsM(itemsID)
        {
                        
            $.ajax({
                method: "GET",
                url: '{{url('/dist/req-items-edit')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                //alert(response);
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function deleteReqProductsM(itemsID)
        {
            //alert(wastageId);

            $.ajax({
                method: "GET",
                url: '{{url('/dist/depot-req-items-delete')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                //alert(response);

                location.reload();       
            });
        }

        function deleteWastageReqProductsM(itemsID,wastageId)
        {
            //alert(wastageId);

            $.ajax({
                method: "GET",
                url: '{{url('/dist/depot-req-items-delete')}}',
                data: {id: itemsID, wastageId:wastageId}
            })
            .done(function (response)
            {
                //alert(response);

                location.reload();       
            });  

        }

        //------------------ REQUISITION --------------------

        function statusProcessM(id)
        {
            //alert(id);
            document.getElementById('statusProcess').value=id;
            //document.getElementById('confirmDelivery').disabled=true;
            $(form).submit();
        }

        // For Field Officer Report Start


        function getFoList(slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/ims/get-point-fo-list')}}',
                data: {point_id: slID}
            })
            .done(function (response)
            {
//alert(response);
             $('#foID1').html(response);  
              
            });            
        }

        function allFoOrderIms()
        {
           
           //alert('IN');

            var fromdate    = document.getElementById('fromdate').value;
            var pointsID    = document.getElementById('pointsID').value;
            var foID        = document.getElementById('foID').value;

            if(fromdate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }                
            }
            else if(fromdate!="")
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/report/ims/order-list')}}',
                    data: {fromdate: fromdate, pointsID: pointsID, foID: foID}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function allFoOrderImsDelivery()
        {
           
            var fromdate    = document.getElementById('fromdate').value;
            var pointsID    = document.getElementById('pointsID').value;
            var foID        = document.getElementById('foID').value;
             var channel        = document.getElementById('channel').value;
              var divisions        = document.getElementById('divisions').value;

            if(fromdate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }                
            }
            else if(fromdate!="")
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/report/ims/order-list-delivery')}}',
                    data: {fromdate: fromdate,channel:channel,divisions:divisions, pointsID: pointsID, foID: foID}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

         function allDistReqIms()
        {
           
           //alert('IN');

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var pointsID    = document.getElementById('pointsID').value;
            var distID      = document.getElementById('distID').value;

            $.ajax({
                method: "GET",
                url: '{{url('/ims/dist-req-list')}}',
                data: {fromdate: fromdate, todate: todate, pointsID: pointsID, distID: distID}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });
        }          
        
		
		 function allDepotReqIms()
        {
           
           //alert('IN');

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var pointsID    = document.getElementById('pointsID').value;
            var distID      = document.getElementById('distID').value;

            $.ajax({
                method: "GET",
                url: '{{url('/ims/depo-req-list')}}',
                data: {fromdate: fromdate, todate: todate, pointsID: pointsID, distID: distID}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });
        }   

        
        //----------------- SPECIAL VALUE WISE COMMISSION ---------------

        function editValueWiseCommission(pid,pqty,pvalue,primaryid)
        {   
            //alert("IN");

            $.ajax({
                method: "GET",
                url: '{{url('/offer/value/other-edit')}}',
                data: {pid: pid,pqty: pqty,pvalue: pvalue,primaryid: primaryid}
            })
            .done(function (response)
            {
               $('#defaultModalRP').modal('show');
               $('#productsnew').html(response);                              
            });            
        }

        function priceChange()
        {
            
            var price = document.getElementById('price').value;
            var qty   = document.getElementById('freeqty').value;
            var totalPrice = qty * price;
            //alert(price+"__"+qty);

            document.getElementById('freeValue').value=totalPrice;

        }

        function itemFreeValueDelete(deleteid)
        {
            //alert(deleteid);
            $('#freevalueid').val(deleteid);

            $('#free-value-delete').modal('show');
        }

        function deleteValueWiseCommission()
        {
            var freeid = $('#freevalueid').val();

            //alert(freeid);

            $.ajax({
                method: "GET",
                url: '{{url('/offer/value/offer-pro-delete')}}',
                data: {freeid: freeid}
            })
            .done(function (response)
            {
                location.reload();       
            });
        }

        function allRetailerCommission()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routeID     = document.getElementById('routeID').value;
            //var category    = document.getElementById('category').value;
            //var products    = document.getElementById('products').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/remaining-commission-list')}}',
                    data: {fromdate: fromdate, todate: todate, routeID: routeID}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function printCount(invoiceid)
        {
            
            var invoicePrint = document.getElementById('invoicePrint').value;
            //alert(invoicePrint);

            document.getElementById('invoicePrint').value=1;
            if(invoicePrint==1)
            {
                return false;
            }                      

            $.ajax({
                method: "GET",
                url: '{{url('/print/invoice-print')}}',
                data: {invoiceid: invoiceid}
            })
            .done(function (response)
            {
                  $('#duplicate1').show();
                  $('#original').hide();        
            });            
        }

        function logout()
        {
            window.location.href="{{ URL('/logout') }}";
        }


        //EPP

        function eppMemoWiseSalesReport()
        {
           
           //alert('IN');

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var memo        = document.getElementById('memo').value;

            $.ajax({
                method: "GET",
                url: '{{url('/epp/memo-wise-sales-list')}}',
                data: {fromdate: fromdate, todate: todate, memo: memo}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });
        }


        // TSM Report


        function foPerformanceDailyList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var todate      = document.getElementById('todate').value;
            var channel     = document.getElementById('channel').value;
            var divisions   = document.getElementById('divistion').value;
            var pointsID    = document.getElementById('pointID').value;
            var fos         = document.getElementById('foID').value;
            
              $.ajax({
                    method: "POST",
                    url: '{{url('report/tsm/fo-performance-list')}}',
                    data: {todate: todate,channel:channel,divisions:divisions,pointsID:pointsID, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
                    
        }


        
        function tmsFOAttendance()
        {
           
           //alert('IN');

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var foID        = document.getElementById('foID').value;

            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }          

            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/fo-attendance-list')}}',
                data: {fromdate: fromdate, todate: todate, foID: foID}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });
        }

        function tmsFOWiseRetailerList()
        {
           
           //alert('IN');

            // var fromdate    = document.getElementById('fromdate').value;
            // var todate      = document.getElementById('todate').value;
            var foID        = document.getElementById('foID').value;

            if(foID=="")
            {
                if(foID=="")
                {
                    document.getElementById('foID').style.borderColor='#FF0000';
                    document.getElementById('foID').focus();
                    return true;
                }                
            }          

            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/retailer-list')}}',
                data: {foID: foID}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });
        }


        /////////////////  Exception Option /////////////////

        function allRetailerExc()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/retailer-exception')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function allOrderProductsExc()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var point_id = document.getElementById('point_id').value;
            var retailer_id = document.getElementById('retailer_id').value;
            var offerTypeExc = document.getElementById('offerTypeExc').value;
            

            $.ajax({
                method: "POST",
                url: '{{url('/visit-order-category-products-exception')}}',
                data: {categories: categories,point_id:point_id,retailer_id:retailer_id,offerTypeExc:offerTypeExc}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


        function editProductsExc(itemsID,pointID,routeID,retailderID,catID,skuID)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/items-edit-exception')}}',
                data: {id: itemsID, pointID: pointID, routeID: routeID, retailderID: retailderID,catID: catID,skuID:skuID}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }


        function itemDeleteExc(deleteid)
        {
            //alert(deleteid);
            $('#itemsid').val(deleteid);

            $('#item-delete').modal('show');
        }

        function deleteProductsExc()
        {
            var itemsid = $('#itemsid').val();
            var itemsID = $('#order_det_id'+itemsid).val();
            var orderID = $('#order_id'+itemsid).val();
            var itemQty = $('#order_qty'+itemsid).val();
            var itemPrice = $('#p_unit_price'+itemsid).val();
            var itemCat = $('#cat_id'+itemsid).val();

            //alert(itemsID+"__"+orderID+"__"+itemQty+"__"+itemPrice+"__"+itemCat);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/items-delete-exception')}}',
                data: {id: itemsID, orderID: orderID, itemQty: itemQty, itemPrice: itemPrice,itemCat:itemCat}
            })
            .done(function (response)
            {
                window.location.reload();       
            });            
        }

        function allOrderManageProductsExc()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var retailer_id = document.getElementById('retailer_id').value;
            var order_id = document.getElementById('order_id').value;
            

            $.ajax({
                method: "POST",
                url: '{{url('/visit-order-manage-category-products-exception')}}',
                data: {categories: categories,retailer_id:retailer_id,order_id:order_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


        function checkReturnValueAndChangeValue()
        {
            var returnValue = parseInt(document.getElementById('totalReturnValue').value);
            var changeValue = parseInt(document.getElementById('totalChangeValue').value);

            //alert(returnValue+"__"+changeValue);

            if((returnValue!=0 && changeValue!=0) && (returnValue <= changeValue))
            {
                //true
                //alert('Yes');
                document.getElementById("myForm").submit();
            }
            else
            {
                alert('Change value must be greater than or equal.');
                return false;
            }
        }

        function checkReturnValueAndChangeValueConfirm()
        {
            var returnValue = parseInt(document.getElementById('returnValue').value);
            var changeValue = parseInt(document.getElementById('changeValue').value);

            //alert(returnValue+"__"+changeValue);

            if((returnValue!=0 && changeValue!=0) && (returnValue <= changeValue))
            {
                //true
                //alert('Yes');
                document.getElementById("myForm").submit();
            }
            else
            {
                alert('Change value must be greater than or equal.');
                return false;
            }
        }

        function allReturnOrdersReports()
        {
            //alert('hellow');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/return-change-report-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function allRetailerNewCreate()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/fo/retailer-all')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


        function managementReport()
        {
            var todate    = document.getElementById('todate').value;

            $.ajax({
                method: "GET",
                url: '{{url('/management/filtering')}}',
                data: {todate: todate}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });  
        }


        function addReqQty(serialid)
        {
            var unitPrice = document.getElementById('price'+serialid).value;
            var qty       = document.getElementById('qty'+serialid).value;

            var totalPrice = (unitPrice * qty).toFixed(2);
            document.getElementById('value'+serialid).value=totalPrice;
        }

        function ssgCategoryWiseProNew(categoryid)
        {
            //alert(offerid);

            $.ajax({
                method: "GET",
                url: '{{URL('/offers/bundle-offer-category-wise-pro-new')}}',
                data: {categoryid: categoryid}
            })
            .done(function (response)
            {
                $('#ssgProducts').html(response);                
            });   
        }

        function showSlabOrProductType(id)
        {            
            if(id==1)
            {
                $('#ssgPro').show();
                $('#ssgGift').hide();
                document.getElementById('category').disabled=false;
            }
            else if(id==2)
            {
                $('#ssgPro').hide();
                $('#ssgGift').show();
                document.getElementById('category').disabled=true;
            }
        }
		
		function allVisitFrequencyReport()
        {
            var year  = document.getElementById('year').value;
            var month = document.getElementById('month').value;
            var point = document.getElementById('point').value;
            var route = document.getElementById('route_id').value;             

            if(year=='' || month=='' || point=='' || route=='')
            {
                alert("All Field Required.");
                return false;
            }
            //alert(route);
            document.getElementById('loadingTimeMasud').style.display='inline';

            $.ajax({
                method: "GET",
                url: '{{url('/sa/visit-frequency-report-list')}}',
                data: {point: point, year: year, month: month, route: route}
            })
            .done(function (response)
            {
                //alert(response);
                document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });                     
        }
		
		


        function tmsDBWiseRequisitionReport()
        {

            var divistion   = document.getElementById('divistion').value;
            var points      = document.getElementById('pointID').value;

            if(points=="")
            {                
                if(points=="")
                {
                    alert('Please select any point');
                    return true;
                }
            }

            document.getElementById('loadingTimeMasud').style.display='inline';
            //alert(divistion+"__"+points);          

            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/db-wise-requisition-list')}}',
                data: {divistion: divistion, points: points}
            })
            .done(function (response)
            {
                //alert(response);
                document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });
        }

        function tmsDBWiseRetailerLedgerReport()
        {
           
            //alert('IN');

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var divistion   = document.getElementById('divistion').value;
            var points      = document.getElementById('pointID').value;
            var route_id    = document.getElementById('routeID').value;

            if(points=="")
            {  
                alert('Route field required.');
                return true;
            }

            //alert(route_id);
            document.getElementById('loadingTimeMasud').style.display='inline';          

            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/retailer-ledger-list')}}',
                data: {fromdate: fromdate, todate: todate, divistion: divistion, points: points, route_id: route_id}
            })
            .done(function (response)
            {
                //alert(response);
                document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });
        }


        function tmsDailyImsReport()
        {
           
            //alert('IN');

            var fromdate    = document.getElementById('fromdate').value;
            // var todate      = document.getElementById('todate').value;
            var divistion   = document.getElementById('divistion').value;
            var points      = document.getElementById('pointID').value;
            var foID        = document.getElementById('foID').value;

            if(fromdate=="" || points=="")
            {  
                alert('All field required');
                return true;
            }

            //alert(fromdate+"__"+divistion+"__"+points+"__"+foID);        
            document.getElementById('loadingTimeMasud').style.display='inline';
            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/daily-ims-report-list')}}',
                data: {fromdate: fromdate, divistion: divistion, points: points, foID: foID}
            })
            .done(function (response)
            {
                //alert(response);
                document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });
        }

        function pgWiseReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var channel     = document.getElementById('channel').value;
            var divistion   = document.getElementById('divistion').value;
            var pointID     = document.getElementById('pointID').value;
            var fos         = document.getElementById('foID').value;
            var category    = document.getElementById('category').value;
            var products    = document.getElementById('productList').value;


            if(fromdate=="" || todate=="" || divistion=="" || pointID=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
                if(divistion=="")
                {
                    document.getElementById('divistion').style.borderColor='#FF0000';
                    document.getElementById('divistion').focus();
                    return true;
                }
                if(pointID=="")
                {
                    document.getElementById('pointID').style.borderColor='#FF0000';
                    document.getElementById('pointID').focus();
                    return true;
                }
                
            }
            else if(fromdate!="" && todate!="" && divistion!="" && pointID!="")
            {
                document.getElementById('loadingTimeMasud').style.display='inline';
                document.getElementById('divistion').style.borderColor='';
                document.getElementById('pointID').style.borderColor='';
                //alert(fromdate+"__"+todate+"__"+products);

                $.ajax({
                    method: "GET",
                    url: '{{url('/report/tsm/pg-wise-report-list')}}',
                    data: {fromdate: fromdate, todate: todate, channel: channel, divistion: divistion, pointID: pointID, fos: fos, category: category, products: products}
                })
                .done(function (response)
                {
                    //alert(response);
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function tmsMonthlyImsReport()
        {
           
            //alert('IN');

            var monthStart    = document.getElementById('monthStart').value;
            var monthEnd      = document.getElementById('monthEnd').value;
            // var todate      = document.getElementById('todate').value;
            var divistion   = document.getElementById('divistion').value;
            var points      = document.getElementById('pointID').value;
            var foID        = document.getElementById('foID').value;

            if(monthStart=="" || monthEnd=="" || divistion=="" || points=="")
            {  
                alert('All field required');
                return true;
            }

            //alert(fromdate+"__"+divistion+"__"+points+"__"+foID);        
            document.getElementById('loadingTimeMasud').style.display='inline';
            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/monthly-ims-status-list')}}',
                data: {monthStart: monthStart,monthEnd: monthEnd, divistion: divistion, points: points, foID: foID}
            })
            .done(function (response)
            {
                //alert(response);
                document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });
        }


        function tsmMonthlyfoPerformanceList()
        {  

            //alert('IN Masud');

            var todate      = document.getElementById('todate').value;
            var fromdate    = document.getElementById('fromdate').value;
            var channel     = document.getElementById('channel').value;
            var divisions   = document.getElementById('divistion').value;
            var pointsID    = document.getElementById('pointID').value;
            var fos         = document.getElementById('foID').value;

            if(pointsID=='')
            {   
                alert('Please select point');
                return false;
            }

            document.getElementById('loadingTimeMasud').style.display='inline';
            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/monthly-fo-performance-list')}}',
                data: {todate: todate, fromdate:fromdate, channel:channel, divisions:divisions, pointsID:pointsID, fos: fos}
            })
            .done(function (response)
            {
                //alert(response);
                document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });
        }



        // Supervisor Drop Down.

        function tsmDivisionWisePoints()
        {
            //alert('Yes Masud');
            var divisions = document.getElementById('divistion').value;
            $.ajax({
                method: "GET",
                url: '{{URL('/report/tsm/division-wise-points')}}',
                data: {divisions: divisions}
            })
            .done(function (response)
            {
                //alert(response);
                $('#foPoints').html(response);                
            });            
        }

        function tsmPointWiseFos(slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/point-wise-fos')}}',
                data: {point_id: slID}
            })
            .done(function (response)
            {
              $('#foDiv').html(response);
            });            
        }



        // division -> point ->route

        function tsmDivisionWisePoints1()
        {
            //alert('Yes Masud');
            var divisions = document.getElementById('divistion').value;
            $.ajax({
                method: "GET",
                url: '{{URL('/report/tsm/division-wise-points1')}}',
                data: {divisions: divisions}
            })
            .done(function (response)
            {
                //alert(response);
                $('#foPoints').html(response);                
            });            
        }

        function tsmPointWiseRoutes(slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/report/tsm/point-wise-route')}}',
                data: {point_id: slID}
            })
            .done(function (response)
            {
              $('#div_route').html(response);
            });            
        }

        // -------------- - End Masud Rana - ------------------



// -------------- - Start Maung Master Data - ------------------

//Maung extra for Route
       function hideInput()
       {
        $("#point_name").hide();
       // $("#rel").hide();
        $("#route_id").hide();
        
        $('#category_id').hide();//today Maung
        $('#companyname').hide();
        
       }

       function hideDis()
       {
        $("#point").hide();
        $("#divisionName").hide();
        $("#busitype").hide();
        $("#pricetype").hide();
       }
       //Extra for routr ends Maung 

        //Maung company ajax starts
        function editCompany(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/company_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               $('#defaultModal1').modal('show');
                $('#company').html(response);  
                              
            });            
        }
        function deleteCompany(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/company_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
                setInterval(function() {
                //alert('Data deleted successfully');
               location.reload(); 
                }, 500);
                       
            });    
            }
            else
            {
                location.reload(); 
            }        
        }
        //Maung company ajax ends

        // function deleteProducts(itemsID,pointID,routeID,retailderID)
        // {
        //     alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID);
        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
            
        //     $.ajax({
        //         method: "POST",
        //         url: '{{url('/items-delete')}}',
        //         data: {id: itemsID, pointID: pointID, routeID: routeID, retailderID: retailderID}
        //     })
        //     .done(function (response)
        //     {
        //         window.reload();          
        //     });            
        // }

 //Maung company ajax starts
        function editTerritory(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/territory_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModal2').modal('show');
                $('#territory').html(response);  
                              
            });            
        }


         function deleteTerritory(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/territory_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
                setInterval(function() {
                alert('Data deleted successfully');
               location.reload(); 
                }, 500);
                       
            });    
            }
            else
            {
                location.reload(); 
            }        
        }

        //get territory for point

         function getTerritory(slID)
        {
           // alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/get_territory')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               // alert(response);
               //$('#defaultModal2').modal('show');
             $('#territory1').html(response);  
             $('#territoryok').html(response);  


                              
            });            
        }

        

        function editPoint(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/point_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModal3').modal('show');
                $('#point').html(response);  

                              
            });            
        }




         function deletePoint(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/point_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
                
                       setInterval(function() {
                alert('Data deleted successfully');
               location.reload(); 
                }, 500);
            });    
            }
            else
            {
                location.reload(); 
            }        
        }
        function modelClose()
        {
         setInterval(function() {
               
               location.reload(); 
                }, 500);
        }
        function modelCloseEdit()
        {
           setInterval(function() {
               
               location.reload(); 
                }, 500);
        }

        
         function deleteRoute(slID)
        {

           // alert(slID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/route_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
              //alert(response);
                setInterval(function() {
                alert('Data deleted successfully');
               location.reload(); 
                }, 500);
                     
            });    
            }
            else
            {
                location.reload(); 
            }        
        }

  function editRoute(slID)
        {
            //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/route_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);
              
               $('#defaultModalNew').modal('show');
               $('#routeNew').html(response);  

                              
            });            
        }

          function deleteDistri(slID)
        {

           //alert(slID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/distri_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
              //alert(response); 
               location.reload();
                     
            });    
            }
            else
            {
                location.reload(); 
            }        
        }

        
		function editDistri(slID)
        {
            //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/distri_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);

               var json = $.parseJSON(response); // create an object with the key of the array
               //alert(json[0]["rname"]);

               var ditri_id=response["id"];
                 //$('#dname').val(id);
                 var id=json[0]["id"];
                var po=json[0]["point_name"];
                //alert(po);

                 $('#dname').val(json[0]["display_name"]);

                 $('#sapcode').val(json[0]["sap_code"]);
                 //$('#pointId').val(json[0]["point_id"]);
                pointName = "point Name:" + json[0]["point_name"];
                divisionName = "Division Name:" + json[0]["div_name"];
                ptype = "Price Type:" + json[0]["price_type"];
                btype = "Business Type:" + json[0]["btype"];




                $('#po').html(pointName);
                $('#divName').html(divisionName);
                $('#ptype').html(ptype);
                $('#btype').html(btype);

               

                //alert(test.html);
                 $('#div_id').val(json[0]["division_id"]);
                 $('#btype').val(json[0]["business_type_id"]);
                 $('#mobile_no').val(json[0]["cell_phone"]);
                 $('#tnt').val(json[0]["land_phone"]);
                 $('#creditlimit').val(json[0]["credit_limit"]);
                 $('#email').val(json[0]["email"]);
                 
                 $('#username').val(json[0]["email1"]);
                 $('#password').val("");
                 $('#address').val(json[0]["current_address"]);
                 $('#distri_id').val(id); 
                 $('#id').val(id);

              // for scroll
              $("html, body").animate({ scrollTop: 160 }, "slow"); 
               // $('#po').append($("<option>").val(po).text(po));

                              
            });            
        }

       // Prduct Category Starts
         function editProCategory(slID)
        {
            //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/proCategory_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);

               var json = $.parseJSON(response); // create an object with the key of the array
             //var test=json.pro[0]["g_code"];
              
              $('#g_code').val(json.pro[0]["g_code"]);
              $('#name').val(json.pro[0]["name"]);
              $('#g_name').val(json.pro[0]["g_name"]);
              $('#company_id').val(json.pro[0]["company_id"]);
              $('#avg_price').val(json.pro[0]["avg_price"]);
              company_id=json.pro[0]["company_id"];
              name=json.pro[0]["cname"];
              $('#id').val(json.pro[0]["id"]);
              type_one=json.pro[0]["g_name"];
              if(name=='null')
              {
                name='';
              }
              //alert(company_id);
             
              $('#defaultModal').modal('show');           

              $('#procategory').html(response);

              $('#company_id').hide();//$("#company_id").html(option);
              //$('#g_name').hide();
              
              var s = '<div class="form-group" id="cid">';
              s += '<div class="form-line">';
              s += '<select class="form-control" name="company_id" id="company_id" required="">';
              s += '<option value="'+global_company_id+'" selected="selected">'+global_company_name+'</option>';
               for(var i=0;i<json.company.length;i++)
              {
                var company=json.company[i]["name"];
                var id=json.company[i]["id"];
              s += '<option value="'+id+'">'+company+'</option>';  
              }
              s += '</select>';
              s += '</div>';
              s += '</div>';

              document.getElementById('cid').innerHTML= s;

              var type ='<div class="form-group" id="type1">';
                  type +='<div class="form-line">';
                  type +='<select class="form-control show-tick" name="g_name" id="g_name" required="">';
                  type +='<option value="">Please Select Type</option>';
                  type +='<option value="'+type_one+'" selected="selected">'+type_one+'</option>';
                    for(var i=0;i<json.company.length;i++)
              {
                var gname=json.gname[i]["g_name"];
              type += '<option value="'+gname+'">'+gname+'</option>';  
              }
                  type +='</select>';
                  type +='</div>';
                  type +='</div>';

                  //alert(type);
           document.getElementById('type1').innerHTML= type;

//               $("#cid").html('<div class="form-group" id="cid"><div class="form-line"><select class="form-control" name="company_id" id="company_id1" required="">
// <option value="">Please Select masud</option></select></div></div>'); 
             


              /*document.getElementById('company_id').innerHTML = '<select class="form-control show-tick" name="company_id" name="company_id" id="company_id" required=""><option value="22" selected="selected">22</option></select>';*/

                 
          });            
        }
       
       function deleteProcategory(slID)
        {

           //alert(slID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/procategory_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
              //alert(response); 
               location.reload();
                     
            });    
            }
            else
            {
                location.reload(); 
            }        
        }


       
       // Prduct Category ends


         //Maung Product Delete Today Merge
       function deleteProductsSetup(slID)
        {

           //alert(slID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/productsMaster_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
              alert(response); 
               location.reload();
                     
            });    
            }
            else
            {
                location.reload(); 
            }        
        }

        function editProductsSetup(slID)
        {
            //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/productsSetup_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);

               var json = $.parseJSON(response); // create an object with the key of the array
             //var test=json.pro[0]["g_code"];

              
              $('#category_id').val(json.products[0]["g_code"]);
              $('#companyname').val(json.products[0]["companyid"]);
              $('#sap_code').val(json.products[0]["sap_code"]);
              $('#company_id').val(json.products[0]["company_id"]);
              $('#product').val(json.products[0]["name"]);
              $('#mrp').val(json.products[0]["mrp"]);
              $('#depo').val(json.products[0]["depo"]);
              $('#distri').val(json.products[0]["distri"]);
              $('#id1').val(json.products[0]["id"]);
              
             
              $('#defaultModal').modal('show');           

              $('#productsMaster').html(response);

              
             

             
            

                 
          });            
        }

       // Maung Product Delete Today Merge

       // USER MGT//


       function deleteUserbasic(slID)
        {

           //alert(slID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/userbasic_delete')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
              alert(response); 
               location.reload();
                     
            });    
            }
            else
            {
                location.reload(); 
            }        
        }


        //Now Adding today Maung
       //Now Adding today Maung
        function editUserbasic(slID)
        {
           // alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/userbasic_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);

               var json = $.parseJSON(response); // create an object with the key of the array
             //var test=json.pro[0]["g_code"];
            
              
             $('#email').val(json.usersBasic[0]["email"]);
              $('#display_name').val(json.usersBasic[0]["display_name"]);
             $('#employee_id').val(json.usersBasic[0]["employee_id"]);
              //$('#designation').val(json.usersBasic[0]["designation"]);
             $('#doj').val(json.usersBasic[0]["doj"]);
             
              var useryType=json.usersBasic[0]["user_type"];
              var typeId=json.usersBasic[0]["user_type_id"];

              var s = ' <div class="input-group input-group-lg" id="test">';
              s += '<p>';
              s += '<b>User Type:*</b>';
              s +=' </p><div class="input-group"> <div class="form-line"><div id="userType">';
              s +=' <select class="show-tick form-control" data-live-search="true" name="user_type_id" ">';
              s += '<option value="'+typeId+'" selected="selected">'+useryType+'</option>';
               for(var i=0;i<json.userTypeList.length;i++)
              {
                var user_type=json.userTypeList[i]["user_type"];
                var id=json.userTypeList[i]["user_type_id"];
              s += '<option value="'+id+'">'+user_type+'</option>';  
              }
              s += '</select>';
              s += '</div>';
              s += '</div>';

              document.getElementById('test').innerHTML= s;




              var busiType=json.usersBasic[0]["business_type"];
              var busitypeId=json.usersBasic[0]["business_type_id"];
            

              var c = ' <div class="row clearfix"> <div class="input-group input-group-lg" id="business_type">';
              c += '<p>';
              c += '<b>Business Type:*</b>';
              c +=' </p><div class="input-group" <div class="form-line"><div id="busiType">';
              c +=' <select class="show-tick form-control" data-live-search="true" name="business_type_id"  ">';
              c += '<option value="'+busitypeId+'" selected="selected">'+busiType+'</option>';
               for(var i=0;i<json.busiType.length;i++)
              {
                var busi_type=json.busiType[i]["business_type"];
                var id=json.busiType[i]["business_type_id"];

              c += '<option value="'+id+'">'+busi_type+'</option>';  
              }
              c += '</select>';
              c += '</div>';
              c += '</div>';

              document.getElementById('business_type').innerHTML= c;

              var name=json.usersBasic[0]["global_company_name"];
              var compid=json.usersBasic[0]["global_company_id"];
           // alert(name);

              var d = ' <div class="row clearfix"> <div class="input-group input-group-lg" id="company">';
             d+= '<p>';
             d+= '<b>Company:*</b>';
             d+=' </p><div class="input-group" <div class="form-line"><div id="companyone">';
           
             d+=' <select class="show-tick form-control" data-live-search="true" name="global_company_id" ">';
               d += '<option value="'+compid+'" selected="selected">'+name+'</option>';
              for(var i=0;i<json.company.length;i++)
              {
                var busi_type=json.company[i]["global_company_name"];
                var id=json.company[i]["global_company_id"];
              d += '<option value="'+id+'">'+busi_type+'</option>';  
              }
              d += '</select>';
              d += '</div>';
              d += '</div>';

              document.getElementById('company').innerHTML= d;
              var id=json.usersBasic[0]["id"]

              $('#id1').val(id);

              // for scroll
              $("html, body").animate({ scrollTop: 160 }, "slow");

               //$("#test").empty();

               //Add designation drop down

                document.getElementById('test').innerHTML= s;

                                  var desig=json.usersBasic[0]["designation"];
                                 var desi='<div class="row clearfix"> <div class="input-group input-group-lg" id="test1">';
                               
                                    desi+='<p>';
                                      desi+= '<b>Designation:*</b>';
                                    desi+='</p>';
                                    desi+='<div class="input-group">';
                                        desi+='<div class="form-line">';
                                            desi+='<div id="desig">';

                                       desi+='</div>';
                                      desi+= '<select class="show-tick" data-live-search="true" name="designation"  ">';
                                        desi+= '<option value="'+desig+'" selected="selected">'+desig+'</option>';
                                       
                                        desi+='<option value="SUPER ADMIN" id="">SUPER ADMIN</option>';
                                        desi+='<option value="SALES COORDINATOR" id="">DITRIBUTOR</option>';
                                       desi+= '<option value="Head Of Sales" id="">Head Of Sales</option>';
                                        desi+='<option value="GM" id="">GM</option>';
                                        desi+='<option value="DGM" id="">DGM</option>';
                                        desi+='<option value="DGM" id="">AGM</option>';
                                        desi+='<option value="Sr. SM" id="">Sr. SM</option>';
                                        desi+='<option value="SALES ADMIN" id="">SALES ADMIN</option>';
                                       desi+= '<option value="SALES COORDINATOR" id="">SALES COORDINATOR</option>';
                                        desi+='<option value="SM" id="">SM</option>';
                                        desi+='<option value="ASM" id="">ASM</option>';
                                        desi+='<option value="DSM" id="">DSM</option>';
                                       desi+= '<option value="ADSM" id="">ADSM</option>';
                                        desi+='<option value="RSM" id="">RSM</option>';
                                       desi+=' <option value="TSM" id="">TSM</option>';
                                        desi+='<option value="JTSM" id="">JTSM</option>';
                                       desi+='<option value="SFO" id="">SFO</option>';
                                        desi+='<option value="FO" id="">FO</option>';
                                        desi+='</select>';
                                        desi+='</div>';
                                desi+='</div>';
                            desi+='</div>';
                            desi+='</div>';
                            document.getElementById('test1').innerHTML=desi;

               //Add designation drop down


  });            
        }

//-----------------------------------Edituser Scope-----------------------------------------------//
function editUserScope(slID)
        {
           //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/userScope_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);
               var json = $.parseJSON(response);
               var idno=json.scope[0]["div_id"];
               var divCall=json.scope[0]["div_name"];
              //alert(idno);
              var divName=' <div class="col-sm-12" id="divName"><label for="division">Division :*</label><div class="form-group"><div class="form-line">';
             divName+= '<div id="divName"></div>';

                                       
              
             divName +=' <select class="show-tick form-control" data-live-search="true" name="division_id"  onchange="getTerritory(this.value)" required="" ">';
             divName += '<option value="'+idno+'" selected="selected">'+divCall+'</option>';
               for(var i=0;i<json.division.length;i++)
              {
                var div_name=json.division[i]["div_name"];
                var divid=json.division[i]["div_id"];
              divName += '<option value="'+divid+'">'+div_name+'</option>';  
              }
              divName += '</select>';
              divName += '</div>';
              divName += '</div>';

              document.getElementById('divName').innerHTML=divName;


              var terridno=json.scope[0]["terriid"];
                    var terrCall=json.scope[0]["name"];
                //alert(terridno);
               //alert(terrCall);             

  var terrName=' <div class="col-sm-12" id="terriName"><div class="form-group"><div class="form-line">';
             terrName += '<option value="'+terridno+'" selected="selected">'+terrCall+'</option>';
             terrName+= '<div id="terriName"><div class="form-line" id="territory1"></div></div>';

                                       
              
            // terrName +=' <select class="show-tick form-control" data-live-search="true" name="division_id"  onchange="" required="" ">';
             
              terrName += '</select>';
              terrName += '</div>';
              terrName += '</div>';

              document.getElementById('terriName').innerHTML=terrName;

           // var id=json.usersBasic[0]["id"]

             $('#id2').val(id);
             // for scroll
            $("html, body").animate({ scrollTop: 160 }, "slow"); 

           // $('#div_name1').hide();
            

           });
        }

       
       // Prduct Category ends

//----------->EDIT USER DETAILS--------------------------------------->//
function editUserDetails(slID)
        {
            //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/userdetails_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
              // alert(response);

               var json = $.parseJSON(response); // create an object with the key of the array
               //var test=json.pro[0]["g_code"];
            
              
             $('#first_name').val(json.userDetails[0]["first_name"]);
              $('#middle_name').val(json.userDetails[0]["middle_name"]);
             $('#last_name').val(json.userDetails[0]["last_name"]);
             
             $('#owner_name').val(json.userDetails[0]["owner_name"]);
             $('#land_phone').val(json.userDetails[0]["land_phone"]);
             $('#cell_phone').val(json.userDetails[0]["cell_phone"]);
             $('#current_address').val(json.userDetails[0]["current_address"]);
             $('#permanent_address').val(json.userDetails[0]["permanent_address"]);
             
             $('#email').val(json.userDetails[0]["email"]);
             $('#dob').val(json.userDetails[0]["dob"]);
             $('#sap_code').val(json.userDetails[0]["sap_code"]);

             
              
              
              var id=json.userDetails[0]["user_det_id"];

              $('#id4').val(id);
              //alert(id);

              // for scroll
              $("html, body").animate({ scrollTop: 160 }, "slow");

               //$("#test").empty();

               //Add designation drop down

               

  });            
        }

//----------->EDIT USER DETAILS--------------------------------------->//     
       // -------------- - Start Maung Ends - ------------------



       
       // Prduct Category ends


       // -------------- - Start Maung Ends - ------------------

         // -------------- - Start Zubair Reject Reason - ------------------
       
      
        function editReason(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/rejectreason_edit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModal1').modal('show');
               $('#company').html(response);  
                              
            });            
        }
        
        function deleteReason(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/rejectreason_delete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                    setInterval(function() {
                    alert('Data deleted successfully');
                    location.reload(); 
                    }, 500);
                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
        
        
    ///////////////////////////////////// fo edit & delete //////////////////////////////////////////   
    
    
     // -------------- - Start Zubair Division ------------------
       
      
        function editDivision(slID)
        {
           // alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/division_edit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModal1').modal('show');
               $('#company').html(response);  
                              
            });            
        }
        
        
        function deleteDivision(slID)
        {
            var delConf=confirm('Are you sure to delete this division?');
            //alert(slID);
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/division_delete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                    setInterval(function() {
                    alert('Division deleted successfully');
                    location.reload(); 
                    }, 500);
                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
        
        
    ///////////////////////////////////// End Zubair Division //////////////////////////////////////////    
    
        function editFO(slID)
        {
            //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/fo_edit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               //alert(response);

                var json = $.parseJSON(response); // create an object with the key of the array
            
                var ditri_id    =   response["id"];
                var id          =   json[0]["id"];
                var po          =   json[0]["point_name"];
            
                $('#display_name').val(json[0]["display_name"]);
                $('#sapcode').val(json[0]["sap_code"]);
                
                pointName = "point Name:" + json[0]["point_name"];
                divisionName = "Division Name:" + json[0]["div_name"];
                btype = "Business Type:" + json[0]["btype"];


                $('#po').html(pointName);
                $('#divName').html(divisionName);
                $('#btype').html(btype);

                //alert(test.html);
                $('#div_id').val(json[0]["division_id"]);
                $('#btype').val(json[0]["business_type_id"]);
                 
                $('#mobile_no').val(json[0]["cell_phone"]);
                $('#tnt').val(json[0]["land_phone"]);
                $('#email').val(json[0]["email"]);
                 
                $('#username').val(json[0]["email"]);
                $('#password').val("");
                $('#address').val(json[0]["current_address"]);
                $('#id').val(id);

              
               // $('#po').append($("<option>").val(po).text(po));

                              
            });            
        }
        
        
        function deleteFO(slID)
        {

            //alert(slID);
            var delConf=confirm('Are sure to delete this FO?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/fo_delete')}}',
                    data: {id: slID}
                })
                .done(function (response)
                {
                  //alert(response); 
                   location.reload();
                         
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
        
        
        
        function editRETAILER(slID)
        {
            //alert(slID);
            var id=slID;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                type:"JSON",
                url: '{{url('/retailer_edit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               //alert(response);

                var json = $.parseJSON(response); // create an object with the key of the array
            
                $('#retailer_name').val(json[0]["name"]);
                //$('#sap_code').val(json[0]["sap_code"]);
        
                $('#divName').html(json[0]["div_name"]);
                $('#routeName').html(json[0]["rname"]);
                $('#pointName').html(json[0]["pname"]);
                
                if(json[0]["shop_type"] == '0')
                    $('#retailerType').html('END-SHOP');
                else if( json[0]["shop_type"] == '1')
                    $('#retailerType').html('Dealer');
            
                if(json[0]["status"] == '0')
                    $('#retailerStatus').html('Active');
                else if( json[0]["status"] == '1')
                    $('#retailerStatus').html('In-Active');

                //alert(test.html);
                $('#owner_name').val(json[0]["owner"]);
                $('#address').val(json[0]["vAddress"]);
                 
                $('#mobile').val(json[0]["mobile"]);
                $('#tnt').val(json[0]["tnt"]);
                $('#email').val(json[0]["email"]);
                 
                $('#datetimepicker1').val(json[0]["dob"]);
                $('#serial').val(json[0]["serial"]);
                
                $('#id').val(id);
                // for scroll
              $("html, body").animate({ scrollTop: 160 }, "slow"); 
                              
            });            
        }
        
        
        function deleteRETAILER(slID)
        {

            //alert(slID);
            var delConf=confirm('Are you sure to delete this RETAILER?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/retailer_delete')}}',
                    data: {id: slID}
                })
                .done(function (response)
                {
                  //alert(response); 
                   location.reload();
                         
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
		
		//------ Zubair GLOBAL COMPANY -------//
		
		function editGlobalCompany(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('/globalCompany_edit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModal1').modal('show');
               $('#company').html(response);  
                              
            });            
        }
		
		
		function deleteGlobalCompany(slID)
        {
            var delConf=confirm('Are you sure to delete this global company?');
            //alert(slID);
            
			if(delConf){
				
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/globalCompany_delete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                    setInterval(function() {
                    alert('Global company deleted successfully');
                    location.reload(); 
                    }, 500);
                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
		
		
		/* Depot Zubair */
		
		function dispBankDetails()
		{
			var payment_type = $("#payment_type").val();
			//alert(payment_type);
			
			if(payment_type=="CHEQUE")
			{
				$("#bank_div").show();
			} else {
				$("#bank_div").hide();
			}
			
		}
		
		function dispBankDetailsEdit()
		{
			var payment_type = $("#payment_type_edit").val();
			//alert(payment_type);
			
			if(payment_type=="CHEQUE")
			{
				$("#bank_div_edit").show();
			} else {
				$("#bank_div_edit").hide();
			}
			
		}

        // Sharif depot/dis payment system

        function depotPaymentList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            //alert('in');
            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
           // alert(fromdate);

              $.ajax({
                    method: "GET",
                    url: '{{url('depot-payment-list')}}',
                    data: {todate: todate,fromdate: fromdate}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
                    
        }
		
		function editDepotPayment(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/depotPaymentEdit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModalPayment').modal('show');
               $('#DepotPayment').html(response);  
                              
            });            
        }
		
        
        function deleteDepotPayment(slID)
        {
            var delConf=confirm('Are sure to delete this Payment?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/paymentDelete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                    setInterval(function() {
                    alert('Data deleted successfully');
                    location.reload(); 
                    }, 500);
                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
		
		
		function editDepotCollection(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/depotCollectionEdit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModalCollection').modal('show');
               $('#DepotCollection').html(response);  
                              
            });            
        }
		
        
        function deleteDepotCollection(slID)
        {
            var delConf=confirm('Are sure to delete this Collection?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/collectionDelete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                    setInterval(function() {
                    alert('Data deleted successfully');
                    location.reload(); 
                    }, 500);
                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
		
		//zubair requisition begin 
		
		function allRequisitionProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/req-category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }
		
		
		function addRecQty(id)
        {
            //alert(id);
            var del_qnty    = document.getElementById('del_qnty'+id).value;            
            var qty         = document.getElementById('qty'+id).value;            
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
          
            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(parseInt(del_qnty) < parseInt(qty))
            {
			   alert('Received Quantiy must be equal/less from delivered quantity');
               document.getElementById('qty'+id).value=del_qnty;
            }
           
        }
		
		function addAppQty(id)
        {
            //alert(id);
            var qty         = document.getElementById('qty'+id).value;            
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
           


            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
         

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }
		
		
		function addRecQty(id)
        {
            //alert(id);
            var qty         = document.getElementById('qty'+id).value;            
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
          
            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
         

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }
		
		
			/* Zubair Retailer Balance */
		
		function retailerListByRoute()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
			
			//alert(route);
            
            $.ajax({
                method: "POST",
                url: '{{url('/route-retailer')}}',
                data: {route: route}
            })
			
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }
		
		function getRouteList(point_ID)
        {
          // alert(point_ID);
		  
		   $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
			
			
			 $.ajax({
                method: "POST",
                url: '{{url('/PointWiseRouteList')}}',
                data: {id: point_ID}
            })
		  
		    
			.done(function (response)
            {
		        $('#div_route').html(response);  
            });            
        }
		
		
		function getSalesRetailerList(route_ID)
        {
           //alert(route_ID);
            $.ajax({
                method: "GET",
                url: '{{url('/RouteWiseRetailerList')}}',
                data: {id: route_ID}
            })
            
			.done(function (response)
            {

             $('#div_ratailer').html(response);  
              
            });            
        }
		
		function getRetailerList(route_ID)
        {
           //alert(route_ID);
            $.ajax({
                method: "GET",
                url: '{{url('/RouteRetaierList')}}',
                data: {id: route_ID}
            })
            
			.done(function (response)
            {

             $('#div_ratailer').html(response);  
              
            });            
        }
		
		
		function getRetailerInvoice(slID)
        {
           //alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/retailer/get_invoice')}}',
                data: {id: slID}
            })
            .done(function (response)
            {

             $('#invoice_no').html(response);  
              
            });            
        }
		
		function getRetailerInvoiceEdit(slID)
        {
           //alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/retailer/get_invoice')}}',
                data: {id: slID}
            })
            .done(function (response)
            {

             $('#invoice_no_edit').html(response);  
              
            });            
        }
		
		
		
		//------ZUBAIR Return STARTS-------//
		 
		 
		  // --------   For Return -----------

		
		function returnOnlyRoute()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/return-only-retailer')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

		
		function returnOnlyProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/return-only-category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }




        function editReturnOnlyProducts(itemsID)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/return-only-items-edit')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        /*function wastageItemDelete(deleteid)
        {
            
            var delConf=confirm('Are sure to delete this item?');
            if(delConf)
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/wastage-items-delete')}}',
                    data: {id: deleteid}
                })
                .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            }
        }*/

        function returnOnlyItemDelete(orderID)
        {
			//alert(orderID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/return-only-items-del')}}',
                data: {id: orderID}
            })
            .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            
                location.reload(); 
            }
                 
        }

        
		function confirmDeleteOnlyReturn()
        {

            var orderID = $('#orderid').val();
            var retailderid = $('#retailderid').val();
            var routeid = $('#routeid').val();

            //alert(orderID+"__"+retailderid+"__"+routeid);

            
            $.ajax({
                method: "POST",
                url: '{{url('/delete-only-return')}}',
                data: {orderID: orderID, retailderid: retailderid, routeid: routeid}
            })
            .done(function (response)
            {
                //alert(response);
                window.location.href = "{{ URL('/return-process') }}"+"/"+retailderid+"/"+routeid;
            });            
        }
		
		
		// Change Section
		
		
		// for distributor/depot return only order show
        function allReturnOnlyOrders()
        {
            //alert('hellow');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/return-only-order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
		
		
    //------ZUBAIR Return end-------//
		
		
		//------ZUBAIR Return & Change STARTS-------//
		 
		 
		  // --------   For Return & Change -----------

		function addReturn(id)
        {
            //alert(id);
            var qty         = document.getElementById('returnQty'+id).value;            
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('returnValue'+id).value;
          
            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
         

            
            var totalValueShow = (qty * price);            
            document.getElementById('returnValue'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }
		
		
		function addChange(id)
        {
            //alert(id);
            var qty         = document.getElementById('changeQty'+id).value;            
            var price       = document.getElementById('change_prod_price'+id).value;
            var value       = document.getElementById('changeValue'+id).value;
          
            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
         

            
            var totalValueShow = (qty * price);            
            document.getElementById('changeValue'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }
		
		
		function getChangeProduct(cat_ID,slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/return_change/get_product')}}',
                data: {id: cat_ID, slID: slID}
            })
            .done(function (response)
            {

             $('#div_change_product'+slID).html(response);  
              
            });            
        }
		
		function getChangeProductPrice(pro_ID,slID)
        {
           //alert(slID);
		   
            $.ajax({
                method: "GET",
                url: '{{url('/return_change/get_product_price')}}',
                data: {id: pro_ID}
            })
            .done(function (response)
            {
				//alert(response);

             $('#change_prod_price'+slID).val(response);  
              
            });   
						
        }
		
		
		function returnRoute()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/return-retailer')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

		
		function returnProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var return_id = document.getElementById('return_id').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/return-category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }




        function editReturnProducts(itemsID)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/return-items-edit')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        /*function wastageItemDelete(deleteid)
        {
            
            var delConf=confirm('Are sure to delete this item?');
            if(delConf)
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/wastage-items-delete')}}',
                    data: {id: deleteid}
                })
                .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            }
        }*/

        function returnItemDelete(deleteid,orderID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/return-items-del')}}',
                data: {id: deleteid, orderID: orderID}
            })
            .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            
                location.reload(); 
            }
                 
        }

        
		function confirmDeleteReturn()
        {

            var orderID = $('#orderid').val();
            var retailderid = $('#retailderid').val();
            var routeid = $('#routeid').val();

            //alert(orderID+"__"+retailderid+"__"+routeid);

            
            $.ajax({
                method: "POST",
                url: '{{url('/delete-return')}}',
                data: {orderID: orderID, retailderid: retailderid, routeid: routeid}
            })
            .done(function (response)
            {
                //alert(response);
                window.location.href = "{{ URL('/return-process') }}"+"/"+retailderid+"/"+routeid;
            });            
        }
		
		
		// Change Section
		
		
		// for distributor/depot return order show
        function allReturnOrders()
        {
            //alert('hellow');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var fos         = document.getElementById('fos').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/return-order-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
		
		
    //------ZUBAIR Return & Change end-------//
	
	
	 //------ZUBAIR Depot Cashbook start-------//
	
	
		function editDepotCashbook(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/depotCashBookEdit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModalPayment').modal('show');
               $('#DepotPayment').html(response);  
                              
            });            
        }
		
        
        function deleteDepotCashbook(slID)
        {
            var delConf=confirm('Are sure to delete this Expense?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/cashbookDelete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                    setInterval(function() {
                    alert('Data deleted successfully');
                    location.reload(); 
                    }, 500);
                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
		
		
		//------SHARIF WASTAGE STARTS-------//
		
		
		//------ZUBAIR Distributor Cashbook start-------//
	
	
		function editDistributorCashbook(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/distCashBookEdit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModalPayment').modal('show');
               $('#DepotPayment').html(response);  
                              
            });            
        }
		
        
        function deleteDistributorCashbook(slID)
        {
            var delConf=confirm('Are sure to delete this Expense?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/DistCashbookDelete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                    setInterval(function() {
                    alert('Data deleted successfully');
                    location.reload(); 
                    }, 500);
                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }
		
		 
		 
		  // --------   For wastage -----------

     function wastageRoute()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = document.getElementById('route').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/wastage-retailer')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

    function wastageProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/wastage-category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }




        function editWastageProducts(itemsID,pointID,routeID,retailderID,catID)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/wastage-items-edit')}}',
                data: {id: itemsID, pointID: pointID, routeID: routeID, retailderID: retailderID,catID: catID}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        /*function wastageItemDelete(deleteid)
        {
            
            var delConf=confirm('Are sure to delete this item?');
            if(delConf)
            {
                $.ajax({
                    method: "GET",
                    url: '{{url('/wastage-items-delete')}}',
                    data: {id: deleteid}
                })
                .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            }
        }*/

        function wastageItemDelete(deleteid,orderID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/wastage-items-del')}}',
                data: {id: deleteid, orderID: orderID}
            })
            .done(function (response)
                {
                    $('#defaultModal').modal('show');
                    $('#contents').html(response);                
                });
            
                location.reload(); 
            }
                 
        }

        function confirmDeleteWastage()
        {

            var orderID = $('#orderid').val();
            var retailderid = $('#retailderid').val();
            var routeid = $('#routeid').val();

            //alert(orderID+"__"+retailderid+"__"+routeid);

            
            $.ajax({
                method: "POST",
                url: '{{url('/delete-wastage')}}',
                data: {orderID: orderID, retailderid: retailderid, routeid: routeid}
            })
            .done(function (response)
            {
                //alert(response);
                window.location.href = "{{ URL('/wastage-process') }}"+"/"+retailderid+"/"+routeid;
            });            
        }
		
		
		function allFoWastage()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routes      = document.getElementById('routes').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/wastage/fo/order-list')}}',
                    data: {fromdate: fromdate, todate: todate, routes: routes}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function printWastageReport()
        {
            var divContents = $("#printMe").html();//div which have to print
            var printWindow = window.open('', '', 'height=700,width=900');
            printWindow.document.write('<html><head><title></title>');
            printWindow.document.write('<link href="{{URL::asset('resources/sales/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet">');//external styles
			printWindow.document.write('<link href="{{URL::asset('resources/sales/css/style.css')}}" rel="stylesheet">');
            printWindow.document.write('<link href="{{URL::asset('resources/sales/css/print.css')}}" rel="stylesheet">');
            printWindow.document.write('</head><body>');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();

            printWindow.onload=function(){
            printWindow.focus();                                         
            printWindow.print();
            printWindow.close();
            }
        }

        function wastageFoDelivery()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routes      = document.getElementById('routes').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/wastage/fo/delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, routes: routes}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        /// Wastage report for distributor


        function distributorWastageCollect()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routes      = document.getElementById('routes').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/wastage/distributor/order-list')}}',
                    data: {fromdate: fromdate, todate: todate, routes: routes}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function distributorWastageDelivery()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var routes      = document.getElementById('routes').value;


            if(fromdate=="" || todate=="")
            {
                if(fromdate=="")
                {
                    document.getElementById('fromdate').style.borderColor='#FF0000';
                    document.getElementById('fromdate').focus();
                    return true;
                }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(fromdate!="" && todate!="")
            {
                $.ajax({
                    method: "POST",
                    url: '{{url('/report/wastage/distributor/delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, routes: routes}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
		
    //------Wastage end-------//
	
		
    
      

       //------SHARIF OFFER STARTS-------//

        function getAndProduct(slID)
        {
          //alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/offer/get_sku_and_products')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);
            $('#andProducts').html(response); 
            $('#andProducts2').html(response); 
              
            });            
        }
       
       
        function getSkuProduct(slID)
        {
          //alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/offer/get_sku')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
               //alert(response);
            $('#sku').html(response);  
             $('#sku1').html(response);  
              
            });            
        }
        
        function editSpecialSku(slID,offerGroupId,skucatid,andCat,andpid)
        {
            //alert(slID);
            
            $.ajax({
                method: "GET",
                url: '{{url('/sales/offer/special_sku_products_edit')}}',
                data: {id: slID, offerGroupId: offerGroupId, catid: skucatid, andCat: andCat, and_pid:andpid}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModalRP').modal('show');
               $('#productsnew').html(response);

                              
            });            
        }

        function deleteSpecialSku(slID)
                {
                    var delConf=confirm('Are sure to delete this item?');
                    if(delConf){
                   
                    $.ajax({
                        method: "GET",
                        url: '{{url('/offer/special_sku_product_delete')}}',
                        data: {id: slID}
                    })
                        location.reload(); 
                    }
                         
                }

        function getRegularProduct(slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/offer/get_product')}}',
                data: {id: slID}
            })
            .done(function (response)
            {

             $('#product').html(response);  
              
            });            
        }

        function editRegularSku(slID,offerGroupId,skucatid,andCat,andpid)
        {
            //alert(slID);
            
            $.ajax({
                method: "GET",
                url: '{{url('/sales/offer/regular_sku_products_edit')}}',
                data: {id: slID, offerGroupId: offerGroupId, catid: skucatid, andCat: andCat, and_pid:andpid}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModalRP').modal('show');
               $('#productsnew').html(response);

                              
            });            
        }


        function deleteRegularSku(slID)
                {
                    var delConf=confirm('Are sure to delete this item?');
                    if(delConf){
                   
                    $.ajax({
                        method: "GET",
                        url: '{{url('/offer/regular_sku_product_delete')}}',
                        data: {id: slID}
                    })
                        location.reload(); 
                    }
                         
                }


         function getProductEdit(slID)
        {
            //alert(catid);
            $.ajax({
                method: "GET",
                url: '{{url('/offer/get_product')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
              
             $('#productedit').html(response);  
              
            });            
        }

        function editRegularProduct(slID,catid,and_cat_id)
        {
            //alert(slID);
            
            $.ajax({
                method: "GET",
                url: '{{url('/sales/offer/regular_products_edit')}}',
                data: {id: slID, offerGroupId: catid,and_cat: and_cat_id}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModalRP').modal('show');
               $('#productsnew').html(response);

                              
            });            
        }

        function editSpecialProduct(slID,catid,and_cat_id)
        {
            //alert(slID);
            
            $.ajax({
                method: "GET",
                url: '{{url('/sales/offer/special_products_edit')}}',
                data: {id: slID, offerGroupId: catid,and_cat: and_cat_id}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModalRP').modal('show');
               $('#productsnew').html(response);

                              
            });            
        }

        function deleteRegularProduct(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/offer/deleteProduct')}}',
                data: {id: slID}
            })
                location.reload(); 
            }
                 
        }

         function deleteSpecialProduct(slID)
                {
                    var delConf=confirm('Are sure to delete this item?');
                    if(delConf){
                   
                    $.ajax({
                        method: "GET",
                        url: '{{url('/offer/specialProductDelete')}}',
                        data: {id: slID}
                    })
                        location.reload(); 
                    }
                         
                }

        function editOfferSetup(slID)
        {
            //alert(slID);
            
            $.ajax({
                method: "GET",
                url: '{{url('/sales/offer/offer_setup_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModalRP').modal('show');
               $('#productsnew').html(response);

                              
            });            
        }


        function offerSetupDelete(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/offer/offerSetupDelete')}}',
                data: {id: slID}
            })
                location.reload(); 
            }
                 
        }

        /// Target edit

        function targetEdit(slID)
        {
          //alert(slID);
            
            $.ajax({
                method: "GET",
                url: '{{url('/fo_target_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModalTarget').modal('show');
               $('#target').html(response);
                              
            });            
        }

        function targetDelete(slID)
        {
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/targetDelete')}}',
                data: {id: slID}
            })
                location.reload(); 
            }
                 
        }
		
		// -------------- - Start Depot Sharif - --------------
        
        function depotListEdit(slID)
        {
         // alert(slID);
            
            $.ajax({
                method: "GET",
                url: '{{url('/depot/depot_list_edit')}}',
                data: {id: slID}
            })
            .done(function (response)
            {
                //alert(response);
               $('#defaultModalDepot').modal('show');
               $('#depotList').html(response);
                              
            });            
        }

        function depotListDelete(slID)
        {
            //alert(slID);
            var delConf=confirm('Are sure to delete this item?');
            if(delConf){
           
            $.ajax({
                method: "GET",
                url: '{{url('/depot/deleteDepotList')}}',
                data: {id: slID}
            })
             location.reload();  
            }
                 
        }

        function depotList()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var division_id = document.getElementById('division').value;

            //alert(division_id);
            
            $.ajax({
                method: "GET",
                url: '{{url('/depot_div_list')}}',
                data: {div_id: division_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }
       
       function depotProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/category-products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

       function addStockQty(id)
        {
            //alert(id);
            var qty         = document.getElementById('qty'+id).value;            
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;


            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
           

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }


        function stockProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            
            $.ajax({
                method: "POST",
                url: '{{url('/stock_products')}}',
                data: {categories: categories}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

// TSM Stock by Sharif
        function tsmStockProducts()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var pointsID = document.getElementById('pointID').value;
            //var categories = document.getElementById('categories').value;
            

            //alert(pointsID+"__"+categories);

            if(pointsID=='' || categories=='')
            {
                alert('Please select point and category');
                return false;
            }
            document.getElementById('loadingTimeMasud').style.display='inline';

            $.ajax({
                method: "POST",
                url: '{{url('/report/tsm/stock_products')}}',
                data: {categories: categories,pointsID: pointsID}
            })
            .done(function (response)
            {
                //alert(response);
                document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });            
        }

// -------------- - End Sharif - --------------



        /// Start Dashboard for Distributor --- of Sharif

         function dashboardDistributorOrders(serialid)
        {
            $.ajax({
                method: "GET",
                url: '{{url('/dashboard/distributor-orders')}}',
                data: {serialid: serialid}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');                
                $('#contents').html(response);                
            });                    
        }
		
		/// Sharif Depot edit 

function editDepotProducts(itemsID)
        {
            //alert(itemsID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "GET",
                url: '{{url('depot/req-items-edit')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                //alert(response);
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

function deleteDepotProducts(itemsID)
        {
            $.ajax({
                method: "GET",
                url: '{{url('depot/depot-req-items-delete')}}',
                data: {id: itemsID}
            })
            .done(function (response)
            {
                location.reload();       
            });  

        }




// -------------- - End Sharif - --------------

//-----------------Masud Distributor Payment---------------//
function distriBankDetails()
        {
            var payment_type = $("#payment_type").val();
            //alert(payment_type);
            
            if(payment_type=="CHEQUE")
            {
                $("#bank_div").show();
                $("#bank_child").show();
                $("#ref_div").hide();
                 $("#online_div").hide();
            } else {
                $("#bank_div").hide();
                 $("#bank_child").hide();
            }
            if(payment_type=="ON-LINE")
            {
                 $("#bank_div").hide();
                 $("#ref_div").hide();
                 $("#online_div").show();
                 $("#bank_child").show();
            }
            if(payment_type=="CASH")
            {
            $("#ref_div").hide();
            }
            if(payment_type=="PAY-ORDER")
            {
             $("#ref_div").show();
            }
            if(payment_type=="DD")
            {
             $("#ref_div").show();
            }
            if(payment_type=="TT")
            {
             $("#ref_div").show();
            }
            
            
        }
        function editDistriPayment(slID)
        {
            //alert(slID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            $.ajax({
                method: "POST",
                url: '{{url('/distriPaymentEdit')}}',
                data: {id: slID}
            })
            
            .done(function (response)
            {
               $('#defaultModalPayment').modal('show');
               $('#DepotPayment').html(response);  
                              
            });            
        }

         function deleteDistriPayment(slID)
        {
            var delConf=confirm('Are sure to delete this Payment?');
            
            if(delConf){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method: "GET",
                    url: '{{url('/paymentDistriDelete')}}',
                    data: {id: slID}
                })
                
                .done(function (response)
                {
                    //alert(response);
                  
                    alert('Data deleted successfully');
                   
                  window.location='{{url('/newDistriPayment')}}'


                           
                });    
            }
            else
            {
                location.reload(); 
            }        
        }

        function distriBankDetailsEdit()
        {
            var payment_type = $("#payment_type_edit").val();
            //alert(payment_type);
            
            if(payment_type=="CHEQUE")
            {
                $("#bank_div_edit").show();
                $("#bank_childs").show();
                $("#ref_div").hide();
                $("#online_divs").hide();
            } else {
                $("#bank_div_edit").hide();
                $("#bank_childs").hide();
            }
             
            if(payment_type=="ON-LINE")
            {
                 $("#bank_div_edit").hide();
                 $("#ref_div").hide();
                 $("#online_divs").show();
                 $("#bank_childs").show();
            }
           

            if(payment_type=="CASH")
            {
            $("#ref_no").hide();
            }
            if(payment_type=="PAY-ORDER")
            {
                //alert(payment_type);

             $("#ref_no").show();
            }
            if(payment_type=="DD")
            {
             $("#ref_no").show();
            }
            if(payment_type=="TT")
            {
             $("#ref_no").show();
            }
            
           
            
        }
        function distrimodelClose()
        {
         $("#distriPay")[0].reset();
        }
        function  distriCloseEdit()
        {
         $("#distriedit")[0].reset();
        }

        
//-----------------Masud Distributor Payment Ends---------------//


//-----------------Sharif Distributor Wastage Requisition Start---------------//

// wastage declaration start

function declarationQty(id)
        {
            //alert(id);
            var qty         = document.getElementById('qty'+id).value;           
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
           
            /*if(Number(qty) > Number(wastageQty))
                {
                    alert("Requisition Qty must be less than or equal Wastage Qty.");
                    document.getElementById("qty"+id).value = 0;
                    document.getElementById("value"+id).value = 0;
                    return false;
                } */

            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
         

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }

        function wastageDeclarationProducts()
        {
            var categories = document.getElementById('categories').value;
            var point_id = document.getElementById('point_id').value;

            
            $.ajax({
                method: "GET",
                url: '{{url('/dist/was-declaration-category-products')}}',
                data: {categories: categories,point_id:point_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


// For Distributor wastage requisition 

        function wastageQty(id)
        {
            //alert(id);
            var qty         = document.getElementById('qty'+id).value; 
            var wastageQty  = document.getElementById("wastageQty"+id).value;           
            var price       = document.getElementById('price'+id).value;
            var value       = document.getElementById('value'+id).value;
           
           if(Number(qty) > Number(wastageQty))
                {
                    alert("Requisition Qty must be less than or equal Wastage Qty.");
                    document.getElementById("qty"+id).value = 0;
                    document.getElementById("value"+id).value = 0;
                    return false;
                } 

            // For Total Value Show 

            var totalQty    = '';//document.getElementById('totalQty').value;
            var totalValue  = '';//document.getElementById('totalValue').value;
         

            
            var totalValueShow = (qty * price);            
            document.getElementById('value'+id).value=totalValueShow;

            if(totalQty=='0' && qty>0)
            {
                document.getElementById('totalQty').value=qty;
            }
            else if(totalQty!='0' && qty >0)
            {
                var grandTotalQty = parseInt(totalQty) + parseInt(qty);
            }
        }

		function wastageRequisitionProducts()
        {
            var categories = document.getElementById('categories').value;
            var point_id = document.getElementById('point_id').value;

            
            $.ajax({
                method: "GET",
                url: '{{url('/dist/was-req-category-products')}}',
                data: {categories: categories,point_id:point_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }
		
// For Distributor Free requisition 

        
		
		//------------------ REQUISITION --------------------

        

        function freeRequisitionProducts()
        {
            var categories = document.getElementById('categories').value;
            var point_id = document.getElementById('point_id').value;
            
            $.ajax({
                method: "GET",
                url: '{{url('/dist/free-req-category-products')}}',
                data: {categories: categories,point_id:point_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }



        // define supervisor by sharif

        function getUser(slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/get_user_list')}}',
                data: {id: slID}
            })
            .done(function (response)
            {

             $('#point').html(response);  
              
            });            
        }

        function getSupervisor(slID)
        {
          // alert(slID);
            $.ajax({
                method: "GET",
                url: '{{url('/get_supervisor_list')}}',
                data: {id: slID}
            })
            .done(function (response)
            {

             $('#supervisor').html(response);  
              
            });            
        }

        function deleteSupervisor(slID)
                {
                    var delConf=confirm('Are sure to delete this item?');
                    if(delConf){
                   
                    $.ajax({
                        method: "GET",
                        url: '{{url('/supervisor_delete')}}',
                        data: {id: slID}
                    })
                        location.reload(); 
                    }
                         
                }


        function allDepotReport()
        {
            //alert('hello');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = '';//document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;

            if(todate=="")
            {
                // if(fromdate=="")
                // {
                //     document.getElementById('fromdate').style.borderColor='#FF0000';
                //     document.getElementById('fromdate').focus();
                //     return true;
                // }
                if(todate=="")
                {
                    document.getElementById('todate').style.borderColor='#FF0000';
                    document.getElementById('todate').focus();
                    return true;
                }
            }
            else if(todate!="")
            {
                document.getElementById('loadingTimeMasud').style.display='inline';

                $.ajax({
                    method: "GET",
                    url: '{{url('/sa/depot-operation-report-list')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
		
		
		function getBankCharge(id)
        {
            //alert(id);
            var pay_amount = document.getElementById('pay_amount'+id).value;            
            var net_amount = document.getElementById('net_amount'+id).value;            
           
            var bankCharge = pay_amount - net_amount;     
			
            document.getElementById('bank_charge'+id).value=bankCharge;
     
        }
		
		
		function calSalesAmount(id)
        {
            //alert(id);
            var retailer_total_sales = document.getElementById('retailer_total_sales'+id).value;            
            var sales_com_perc = document.getElementById('sales_com_perc'+id).value;            
           
            var comAmount = (retailer_total_sales * sales_com_perc)/100;     
			
            document.getElementById('sales_com_amount'+id).value=comAmount;
     
        }


		
       
    </script>

    @if(Auth::user()->user_type_id==3 && Request::segment(1)=='dashboard')
    <script type="text/javascript">
        // Chart JS

        function getChartJs(type) {
        var config = null;

        if (type === 'line') {
            config = {
                type: 'line',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "My First dataset",
                        data: [65, 59, 80, 81, 56, 55, 40],
                        borderColor: 'rgba(0, 188, 212, 0.75)',
                        backgroundColor: 'rgba(0, 188, 212, 0.3)',
                        pointBorderColor: 'rgba(0, 188, 212, 0)',
                        pointBackgroundColor: 'rgba(0, 188, 212, 0.9)',
                        pointBorderWidth: 1
                    }, {
                            label: "My Second dataset",
                            data: [28, 48, 40, 19, 86, 27, 90],
                            borderColor: 'rgba(233, 30, 99, 0.75)',
                            backgroundColor: 'rgba(233, 30, 99, 0.3)',
                            pointBorderColor: 'rgba(233, 30, 99, 0)',
                            pointBackgroundColor: 'rgba(233, 30, 99, 0.9)',
                            pointBorderWidth: 1
                        }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        else if (type === 'bar') {
            config = {
                type: 'bar',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "Octorber", "November", "December"],
                    datasets: [{
                        label: "Month Wise Sales",
                        data: [{{ $totalAchivementJan }}, {{ $totalAchivementFeb }}, {{ $totalAchivementMar }}, {{ $totalAchivementApr }}, {{ $totalAchivementMay }}, {{ $totalAchivementJun }}, {{ $totalAchivementJul }}, {{ $totalAchivementAug }}, {{ $totalAchivementSep }}, {{ $totalAchivementOct }}, {{ $totalAchivementNov }}, {{ $totalAchivementDec }}],
                        backgroundColor: 'rgba(0, 188, 212, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        else if (type === 'radar') {
            config = {
                type: 'radar',
                data: {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "My First dataset",
                        data: [65, 25, 90, 81, 56, 55, 40],
                        borderColor: 'rgba(0, 188, 212, 0.8)',
                        backgroundColor: 'rgba(0, 188, 212, 0.5)',
                        pointBorderColor: 'rgba(0, 188, 212, 0)',
                        pointBackgroundColor: 'rgba(0, 188, 212, 0.8)',
                        pointBorderWidth: 1
                    }, {
                            label: "My Second dataset",
                            data: [72, 48, 40, 19, 96, 27, 100],
                            borderColor: 'rgba(233, 30, 99, 0.8)',
                            backgroundColor: 'rgba(233, 30, 99, 0.5)',
                            pointBorderColor: 'rgba(233, 30, 99, 0)',
                            pointBackgroundColor: 'rgba(233, 30, 99, 0.8)',
                            pointBorderWidth: 1
                        }]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        else if (type === 'pie') {
            config = {
                type: 'pie',
                data: {
                    datasets: [{
                        data: [{{ $yearlyTarget }}, {{ $totalAchivement }}],
                        backgroundColor: [
                            "rgb(255,0,0)",
                            "rgb(0,128,0)"
                        ],
                    }],
                    labels: [
                        "Target",
                        "Achievement"
                    ]
                },
                options: {
                    responsive: true,
                    legend: false
                }
            }
        }
        return config;
    }
    </script>
    @endif
	
	@php
		
			$urls ='';
			if(Request::segment(2)=='visit-frequency-report')
			{
				$urls = Request::segment(2);
			}
			else if(Request::segment(1)=='depotPaymentList')
			{
				$urls = Request::segment(1);
			}
			else if(Request::segment(1)=='depotAckList')
			{
				$urls = Request::segment(1);
			}
			else if(Request::segment(1)=='depotPaymentAckList')
			{
				$urls = Request::segment(1);
			}
			else if(Request::segment(1)=='depotVerifiedList')
			{
				$urls = Request::segment(1);
			}
            else if(Request::segment(3)=='daily-ims-report')
            {
                $urls = Request::segment(3);
            }
            else if(Request::segment(2)=='depot-operation-report')
            {
                $urls = Request::segment(2);
            }
            
	@endphp
	
    <input type="hidden" id="menuURL" value="{{ $urls }}">

    <script type="text/javascript">
        function replaceContact() //By Default function load.
        {
            var menuURL = document.getElementById('menuURL').value;

            if(menuURL=='visit-frequency-report' || menuURL=='depotPaymentList' 
					|| menuURL=='depotAckList' || menuURL=='depotPaymentAckList' || menuURL=='depotVerifiedList' || menuURL=='daily-ims-report' || menuURL=='depot-operation-report' )
            {
                var element = document.getElementById("sectionReplace");
                    element.classList.add("mystyle");
            }


            var element1 = document.getElementById("contentReplace");
                element1.classList.add("mystyle1");

        }

        function activeMeu()
        {            

            var onlyMenuValue = document.getElementById('onlyMenuValue').value;

            if(onlyMenuValue==0) // show menu
            {
                document.getElementById("sectionReplace").classList.remove('mystyle');
                document.getElementById("contentReplace").classList.remove('mystyle1');

                var element = document.getElementById("sectionReplace");
                    element.classList.add("mystyle-back");

                var element1 = document.getElementById("contentReplace");
                    element1.classList.add("mystyle1-back");


                document.getElementById('onlyMenu').innerHTML='MENU HIDE';
                document.getElementById('onlyMenuValue').value=1;

            }
            else if(onlyMenuValue==1) // hide menu
            {   
                //alert('Masud');
                document.getElementById("sectionReplace").classList.remove('mystyle-back');
                document.getElementById("contentReplace").classList.remove('mystyle1-back');

                document.getElementById('onlyMenu').innerHTML='MENU SHOW';
                document.getElementById('onlyMenuValue').value=0;
                replaceContact();
            }
        }
    </script>
    <style type="text/css">
        .mystyle
        {
            display: none;
        }

        .mystyle-back
        {
            display: block;
        }

        .mystyle1
        {
            margin: 100px 15px 0 20px !important;
        }
        .mystyle1-back
        {
            margin: 100px 15px 0 315px !important;
        }
    </style>
	
</body>

</html>