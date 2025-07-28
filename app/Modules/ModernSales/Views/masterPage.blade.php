<!DOCTYPE html> 
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title> @if($pageTitle!='') {{ 'ABC | '.$pageTitle }} @else ABC | SSG @endif  </title>

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
                   ABC
                    
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
                        <b style="font-size: 16px;"> {{ Auth::user()->display_name }}<!--  {{ ucfirst(session('userFullName')) }} --> </b> </div>
                    <div class="email">
                        @php
                            $roleName = DB::table('tbl_user_type')     
                                    ->where('tbl_user_type.user_type_id', Auth::user()->user_type_id) 
                                    ->first();  
                        @endphp   
                        
                        {{$roleName->user_type}} 

                        {{-- @if(Auth::user()->user_type_id==7)
                            Officer - {{Auth::user()->email}}
                        @elseif(Auth::user()->user_type_id==3)
                            Executive
                        @elseif(Auth::user()->user_type_id==5)
                            Management
                        @elseif(Auth::user()->user_type_id==6)
                            Manager
                        @elseif(Auth::user()->user_type_id==4)
                            Finance Department
                        @elseif(Auth::user()->user_type_id==2)
                            Billing Department
                        @elseif(Auth::user()->user_type_id==1)
                            Administrator
                        @endif --}}
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            @include('ModernSales::layouts.masterMenu')
            <!-- #Menu -->

            <!-- Footer -->
            @include('ModernSales::layouts.masterFooter')
            <!-- #Footer -->
        </aside>
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

        function customerList(rid)
        {
            //alert(rid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = rid; //document.getElementById('route').value;
            
            $.ajax({
                method: "GET",
                url: '{{url('/mts-partyList')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


        function outletList(customerid)
        {
            //alert(rid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var customer = customerid; //document.getElementById('route').value;
            
            $.ajax({
                method: "GET",
                url: '{{url('/mts-partyList')}}',
                data: {customer: customer}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }


        function replaceOutlet(customerid)
        {
            //alert(rid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var customer = customerid; //document.getElementById('route').value;
            
            $.ajax({
                method: "GET",
                url: '{{url('/mts-replace-outlet-list')}}',
                data: {customer: customer}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }





        function allOrderProducts()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var order_id = document.getElementById('order_id').value;
            var categories = document.getElementById('categories').value;
            var retailer_id = document.getElementById('retailer_id').value;
            document.getElementById('cat_id').value=categories;
            

            $.ajax({
                method: "GET",
                url: '{{url('/mts-category-products')}}',
                data: {order_id:order_id,categories: categories,retailer_id:retailer_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }

        function replaceProducts()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var retailer_id = document.getElementById('retailer_id').value;
            document.getElementById('cat_id').value=categories;
            

            $.ajax({
                method: "GET",
                url: '{{url('/mts-replace-category-products')}}',
                data: {categories: categories,retailer_id:retailer_id}
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
        function addDiscount(id)
        {
            var qty     = parseFloat(document.getElementById('qty' + id).value);            
            var price   = parseFloat(document.getElementById('price' + id).value);
            var discount = parseFloat(document.getElementById('discount' + id).value) || 0; // Discount in %

            // Calculate discounted price
            var totalBeforeDiscount = qty * price;
            var discountAmount = (discount / 100) * totalBeforeDiscount;
            var totalAfterDiscount = totalBeforeDiscount - discountAmount;

            document.getElementById('value' + id).value = totalAfterDiscount.toFixed(2); // show 2 decimal places

            // Optional total quantity and total value logic (if needed)
            var totalQtyField = document.getElementById('totalQty');
            var totalValueField = document.getElementById('totalValue');

            if (totalQtyField && totalValueField) {
                var totalQty = parseInt(totalQtyField.value) || 0;
                var totalValue = parseFloat(totalValueField.value) || 0;

                if (qty > 0) {
                    totalQtyField.value = totalQty + qty;
                    totalValueField.value = (totalValue + totalAfterDiscount).toFixed(2);
                }
            }
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


        function editProducts(orderid,itemsid,customer_id,partyid,catid,productid)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/mts-items-edit')}}',
                data: {orderid:orderid,itemsid:itemsid,customer_id:customer_id,partyid:partyid,catid:catid,productid:productid}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }




        function replaceItemEdit(itemsid)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/mts-replace-items-edit')}}',
                data: {itemsid:itemsid}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function returnItemEdit(itemsid)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/mts-return-items-edit')}}',
                data: {itemsid:itemsid}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function itemDelete(orderid,itemsid,customer_id,partyid,catid,productid)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/mts-items-delete')}}',
                data: {orderid:orderid,itemsid:itemsid,customer_id:customer_id,partyid:partyid,catid:catid,productid:productid}
            })
            .done(function (response)
            {
                //alert(response);
                window.location.reload();
               /* $('#defaultModal').modal('show');
                $('#contents').html(response);*/                
            });            
        }


        function allOrder()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                    method: "POST",
                    url: '{{url('/mts-order-report-list')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function notApproveList()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                    method: "POST",
                    url: '{{url('/mts-order-not-approve-list')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function returnNotApproveList()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                    method: "POST",
                    url: '{{url('/mts-return-not-approve-list')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function replaceNotApproveList()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                    method: "POST",
                    url: '{{url('/mts-replace-not-approve-list')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function deliveryList()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                    method: "POST",
                    url: '{{url('/mts-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
		
		
		// Opening balance

        function openingOutletList(rid)
        {
            //alert(rid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var route = rid; //document.getElementById('route').value;
            
            $.ajax({
                method: "GET",
                url: '{{url('/mts-opening-outlet')}}',
                data: {route: route}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }



         // -------------- - Start Sazzadul islam - ------------------
        function allModernOrder()
          {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var customer_id         = document.getElementById('customer_id').value;


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
                    url: '{{url('/modern-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, customer_id: customer_id}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function allModernOrderAnalysis()
          {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var customer_id         = document.getElementById('customer_id').value;


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
                    url: '{{url('/modern-reqAllAnalysisList-view')}}',
                    data: {fromdate: fromdate, todate: todate, customer_id: customer_id}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        function allApprovedOrder()
          {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate        = document.getElementById('fromdate').value;
            var todate          = document.getElementById('todate').value; 
            var executive_id    = document.getElementById('executive_id').value;
            var fos             = document.getElementById('fos').value;


            
                document.getElementById('loadingTimeMasud').style.display='inline';

                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-approved-list')}}',
                    data: {fromdate: fromdate, todate: todate,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
            
        }
        
        function salesOrderManage()
          { 
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate        = document.getElementById('fromdate').value;
            var todate          = document.getElementById('todate').value; 
            var executive_id    = document.getElementById('executive_id').value;
            var fos             = document.getElementById('fos').value;


            
                document.getElementById('loadingTimeMasud').style.display='inline';

                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-order-manage-list')}}',
                    data: {fromdate: fromdate, todate: todate,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
            
        }

        function allApprovedReplace()
          {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var executive_id    = document.getElementById('executive_id').value;
            var fos             = document.getElementById('fos').value;

                document.getElementById('loadingTimeMasud').style.display='inline';
            
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-replace-approved-list')}}',
                    data: {fromdate: fromdate, todate: todate, executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                      document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                 
        }

        function replaceDeliveryApproved()
          {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var executive_id    = document.getElementById('executive_id').value;
            var fos             = document.getElementById('fos').value;

            document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-replace-delivery-approved-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                  
        }

        function returnDeliveryApproved()
          {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var executive_id    = document.getElementById('executive_id').value;
            var fos             = document.getElementById('fos').value;

            document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-return-delivery-approved-list')}}',
                    data: {fromdate: fromdate, todate: todate,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                  
        }

        function allDeliveryReplace()
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
                    url: '{{url('/mts-replace-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function allApprovedDelivery()
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
            var executive_id         = document.getElementById('executive_id').value;


              document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-delivery-approved-list')}}',
                    data: {fromdate: fromdate, todate: todate,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                   document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                   
        }

        function adminDeliveryReport()
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
            var customer_id         = document.getElementById('customer_id').value; 

 
                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "POST",
                    url: '{{url('/mts-admin-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer_id:customer_id, fos: fos}
                })
                .done(function (response)
                {
                    
                  document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
           
        }


        function adminSalesOrderReport()
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
            var customer_id         = document.getElementById('customer_id').value; 

 
                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "POST",
                    url: '{{url('/mts-admin-sales-order-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer_id:customer_id, fos: fos}
                })
                .done(function (response)
                {
                    
                  document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
           
        }

        function adminReplaceDeliveryReport()
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
            var customer         = document.getElementById('customer_id').value;

 
                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-admin-replace-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer, fos: fos}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                  
        }

         function billingReplaceDeliveryReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var customer         = document.getElementById('customer').value; 
            
                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-billing-replace-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
               
        }


        function replaceDeliveryReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            //var fos         = document.getElementById('fos').value;
            var customer         = document.getElementById('customer').value;


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
                    url: '{{url('/mts-replace-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }


        // Return order start

         function returnOutlet(customerid)
        {
            //alert(rid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var customer = customerid; //document.getElementById('route').value;
            
            $.ajax({
                method: "GET",
                url: '{{url('/mts-return-outlet-list')}}',
                data: {customer: customer}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }
        
    function returnProducts()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var categories = document.getElementById('categories').value;
            var retailer_id = document.getElementById('retailer_id').value;
            document.getElementById('cat_id').value=categories;
            

            $.ajax({
                method: "GET",
                url: '{{url('/mts-return-category-products')}}',
                data: {categories: categories,retailer_id:retailer_id}
            })
            .done(function (response)
            {
                //alert(response);
                $('#showHiddenDiv').html(response);                
            });            
        }
        
    function allApprovedreturn()
          {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var executive_id    = document.getElementById('executive_id').value;
            var fos         = document.getElementById('fos').value; 

             
            document.getElementById('loadingTimeMasud').style.display='inline';

                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-return-approved-list')}}',
                    data: {fromdate: fromdate, todate: todate, executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                   document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                 
        }
        
    function allDeliveryreturn()
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
                    url: '{{url('/mts-return-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }
        
    function adminreturnDeliveryReport()
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
            var customer         = document.getElementById('customer_id').value;


             
                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-admin-return-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer,fos: fos}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                
        }

    function billingReturnDeliveryReport()
    {
        //alert(id);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var fromdate    = document.getElementById('fromdate').value;
        var todate      = document.getElementById('todate').value; 
        var customer         = document.getElementById('customer').value;


        
            document.getElementById('loadingTimeMasud').style.display='inline';
            $.ajax({
                method: "GET",
                url: '{{url('/mts-billing-return-delivery-report-list')}}',
                data: {fromdate: fromdate, todate: todate,customer:customer}
            })
            .done(function (response)
            {
                 document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });    
        }


        function returnDeliveryReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            //var fos         = document.getElementById('fos').value;
            var customer         = document.getElementById('customer').value;


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
                    url: '{{url('/mts-return-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

       // Return Order end

        function customer_ledger_list()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var executive_id    = document.getElementById('executive_id').value;
            var fos         = document.getElementById('fos').value;
            var customer_id         = document.getElementById('customer_id').value;
            if(fromdate=="" || todate=="" || executive_id=="" || fos=="" || customer_id=="" )
            {
                alert("Executive, Officer and Customer field Mandatory!")
            }else{

                document.getElementById('loadingTimeMasud').style.display='inline';
 
                $.ajax({
                    method: "POST",
                    url: '{{url('/customer-ledger-list')}}',
                    data: {fromdate: fromdate, todate: todate,executive_id:executive_id, fos:fos, customer_id: customer_id}
                })
                .done(function (response)
                {
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
            }
                
                   
        }

        function customer_stock_list()
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
            var customer_id         = document.getElementById('customer_id').value;

            document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "POST",
                    url: '{{url('/customer-stock-list')}}',
                    data: {fos:fos,customer_id: customer_id,todate:todate,fromdate:fromdate}
                })
                .done(function (response)
                {
                  document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });        
        }

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
                    url: '{{url('/moderndelivery-list')}}',
                    data: {fromdate: fromdate, todate: todate, fos: fos}
                })
                .done(function (response)
                {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                });
            }          
        }

        function accountsPaymentList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate    = document.getElementById('fromdate').value;
                var todate      = document.getElementById('todate').value;
                var customer      = document.getElementById('customer').value;
                var payment_type      = document.getElementById('payment_type').value;
                var payment_from      = document.getElementById('payment_from').value;
               // alert(fromdate);
               //todate: todate,fromdate: fromdate,customer:customer,payment_type:payment_type, payment_from:payment_from

               $.ajax({
                method: "GET",
                url: '{{url('accounts_payments_con_list')}}', 
                data: {todate,fromdate,customer,payment_type,payment_from}
            })
               .done(function (response)
               {
                        //alert(response);
                        $('#showHiddenDiv').html(response);                
                    });

        }
        function accountsPaymentsDownload(){
            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var customer      = document.getElementById('customer').value;
            var payment_type      = document.getElementById('payment_type').value;
            var payment_from      = document.getElementById('payment_from').value;
            let mkurl = 'fromdate='+fromdate+'&toDate='+todate+'&customer='+customer+'&payment_type='+payment_type+'&payment_from='+payment_from;
            console.log('mkurl', mkurl)
            window.location.href = "{{ URL('/accounts_payments_download?')}}"+mkurl; 
        }

        function accountsPaymentAck()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate    = document.getElementById('fromdate').value;
                var todate      = document.getElementById('todate').value;
                var customer      = document.getElementById('customer').value;
                var payment_type      = document.getElementById('payment_type').value;
               // alert(fromdate);

               $.ajax({
                method: "GET",
                url: '{{url('accounts-payments-ack-list')}}',
                data: {todate: todate,fromdate: fromdate,customer:customer,payment_type:payment_type}
            })
               .done(function (response)
               {
                        //alert(response);
                        $('#showHiddenDiv').html(response);                
                    });

        }


        function accountsPaymentVerify()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate    = document.getElementById('fromdate').value;
                var todate      = document.getElementById('todate').value;
                var customer      = document.getElementById('customer').value;
                var payment_type      = document.getElementById('payment_type').value;
               // alert(fromdate);

               $.ajax({
                method: "GET",
                url: '{{url('accounts-payments-verify-list')}}',
                data: {todate: todate,fromdate: fromdate,customer:customer,payment_type:payment_type}
            })
               .done(function (response)
               {
                        //alert(response);
                        $('#showHiddenDiv').html(response);                
                    });

        }

        function accountsPaymentReportList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate    = document.getElementById('fromdate').value;
                var todate      = document.getElementById('todate').value;
                var customer      = document.getElementById('customer').value;
                var payment_type      = document.getElementById('payment_type').value;
               // alert(fromdate);

               $.ajax({
                method: "GET",
                url: '{{url('accounts_payments_rece_report_list')}}',
                data: {todate: todate,fromdate: fromdate,customer:customer,payment_type:payment_type}
            })
               .done(function (response)
               {
                        //alert(response);
                        $('#showHiddenDiv').html(response);                
                    });

        }

        function adminPaymentList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate    = document.getElementById('fromdate').value;
                var todate      = document.getElementById('todate').value;
                var customer_id      = document.getElementById('customer_id').value;
                var payment_type      = document.getElementById('payment_type').value;
                var executive_id    = document.getElementById('executive_id').value;
                var fos             = document.getElementById('fos').value;
                var payment_type             = document.getElementById('payment_type').value;

               // alert(fromdate);
               document.getElementById('loadingTimeMasud').style.display='inline';
               $.ajax({
                method: "GET",
                url: '{{url('admin_payments_con_list')}}',
                data: {todate: todate,fromdate: fromdate,customer_id:customer_id,payment_type:payment_type,executive_id:executive_id,fos:fos,payment_type:payment_type}
            })
               .done(function (response)
               {
                   document.getElementById('loadingTimeMasud').style.display='none';
                        $('#showHiddenDiv').html(response);                
                    });

           }


        function executivePaymentList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate    = document.getElementById('fromdate').value;
                var todate      = document.getElementById('todate').value;
                //var customer      = document.getElementById('customer').value;
                //var payment_type      = document.getElementById('payment_type').value;
               // alert(fromdate);

               $.ajax({
                method: "GET",
                url: '{{url('outlet-payment-list')}}',
                data: {todate: todate,fromdate: fromdate}
            })
               .done(function (response)
               {
                        //alert(response);
                        $('#showHiddenDiv').html(response);                
                    });

           }

        function executiveAdjustmentList()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate    = document.getElementById('fromdate').value;
                var todate      = document.getElementById('todate').value;
                //var customer      = document.getElementById('customer').value;
                //var payment_type      = document.getElementById('payment_type').value;
               // alert(fromdate);

               $.ajax({
                method: "GET",
                url: '{{url('credit-adjustment-list')}}',
                data: {todate: todate,fromdate: fromdate}
            })
               .done(function (response)
               {
                        //alert(response);
                        $('#showHiddenDiv').html(response);                
                    });

           }


        function editPayment(paymentid)
        {
            //alert(itemsID+"__"+pointID+"__"+routeID+"__"+retailderID+"__"+catID);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "GET",
                url: '{{url('/app_admin_payment_edit')}}',
                data: {paymentid:paymentid}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

       function adminPaymentReport()
        {  

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

                //alert('in');
                var fromdate        = document.getElementById('fromdate').value;
                var todate          = document.getElementById('todate').value;
                var customer        = document.getElementById('customer_id').value;
                var payment_type    = document.getElementById('payment_type').value; 
                var fos             = document.getElementById('fos').value;
              
                 document.getElementById('loadingTimeMasud').style.display='inline';
                   $.ajax({
                    method: "GET",
                    url: '{{url('mts-admin-payment-list')}}',
                    data: {fromdate: fromdate, todate: todate,fos: fos,customer:customer,payment_type:payment_type}
                })
                   .done(function (response)
                   {
                      document.getElementById('loadingTimeMasud').style.display='none';
                        $('#showHiddenDiv').html(response);                
                    }); 
           }


        function getBankCharge(id)
        {
            //alert(id);
            var pay_amount = document.getElementById('pay_amount'+id).value;            
            var net_amount = document.getElementById('net_amount'+id).value;            
           
            var bankCharge = pay_amount - net_amount; 
            document.getElementById('bank_charge'+id).value=bankCharge;
     
        }

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
                setTimeout(function(){printWindow.close();}, 1);
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
             $("#bank_div").show(); 
             $("#online_div").hide();
            }
            if(payment_type=="DD")
            {
              $("#bank_div").show();
                $("#bank_child").show();
                $("#ref_div").hide();
                 $("#online_div").hide();
            }
            if(payment_type=="TT")
            {
              $("#bank_div").show();
                $("#bank_child").show();
                $("#ref_div").hide();
                 $("#online_div").hide();
            }
            
            
        }

        function allManager(id)
        {
           
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

             
            $.ajax({
                method: "GET",
                url: '{{URL('/modern-manager-list')}}',
                data: {management_id: id}
            })
            // alert(response);
            .done(function (response)
            { 
                $('#managerDiv').html(response);           
            });            
        }
        
        function allExecutive(id)
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 

            $.ajax({
                method: "GET",
                url: '{{URL('/modern-executive-list')}}',
                data: {manager_id: id}
            })
            .done(function (response)
            { 
                $('#executiveDiv').html(response);            
            });            
        }

        function allOfficer(id)
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 

            $.ajax({
                method: "GET",
                url: '{{URL('/modern-officer-list')}}',
                data: {executive_id: id}
            })
            .done(function (response)
            { 
                $('#officerDiv').html(response);       
            });            
        }

        function allCustomer(id)
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 

            $.ajax({
                method: "GET",
                url: '{{URL('/mts-officer-customer-list')}}',
                data: {officer_id: id}
            })
            .done(function (response)
            { 
                $('#customerDiv').html(response);       
            });            
        }

        // Supervisor Drop Down.

        function userTypeList()
        {
            //alert('Yes Masud');
            var type = document.getElementById('type').value;
            $.ajax({
                method: "GET",
                url: '{{URL('/type-user-list')}}',
                data: {type: type}
            })
            .done(function (response)
            {
                //alert(response);
                $('#userName').html(response);                
            });            
        }

         function adminTargetSearch()
        {
           
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var year    = document.getElementById('year').value;
            var month      = document.getElementById('month').value; 
            var executive_id    = document.getElementById('executive_id').value;
            var fos         = document.getElementById('fos').value; 

             
                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/modern-target-search')}}',
                    data: {year: year, month: month,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                
        }

        function targetEdit(targetid)
        {
            //alert(targetid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/modern-target-edit')}}',
                data: {targetid:targetid}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function customerEdit(customer_id)
        {
            //alert(targetid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/modern-customer-edit')}}',
                data: {customer_id:customer_id}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function bankEdit(bank_id)
        {
            //alert(targetid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/mts-bank-account-edit')}}',
                data: {bank_id:bank_id}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function userEdit(user_id)
        {
            //alert(targetid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/mts-user-edit')}}',
                data: {user_id:user_id}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function outletEdit(party_id)
        {
            //alert(targetid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/modern-outlet-edit')}}',
                data: {party_id:party_id}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function outletSearch()
        {   
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var customer_id    = document.getElementById('customer_id').value; 
            var status         = document.getElementById('status').value;  

             document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-all-outlet-list')}}',
                    data: {customer_id: customer_id, status: status}
                })

                .done(function (response)
                {
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                 
                });
                   
        }


        function productEdit(product_id)
        {
            //alert(targetid);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
            
            $.ajax({
                method: "POST",
                url: '{{url('/modern-product-edit')}}',
                data: {product_id:product_id}
            })
            .done(function (response)
            {
                $('#defaultModal').modal('show');
                $('#contents').html(response);                
            });            
        }

        function productSearch()
        {   
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var channel    = document.getElementById('channel1').value;
            var category      = document.getElementById('category1').value;
            var status         = document.getElementById('status').value;  

             document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-all-product-list')}}',
                    data: {channel: channel, category: category, status: status}
                })

                .done(function (response)
                {
                    document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                 
                });
                   
        }

        function allCategory(id)
        {
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

             
            $.ajax({
                method: "GET",
                url: '{{URL('/mts-category-list')}}',
                data: {channel_id: id}
            })
             //alert(response);
            .done(function (response)
            { 
                $('#categoryDiv').html(response);           
            });            
        }
     
          // -------------- - end Sazzadul islam - ------------------

          // Manager Part
          
        function managerDeliveryReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value; 
            var customer         = document.getElementById('customer_id').value;
            var executive_id         = document.getElementById('executive_id').value; 
            var fos         = document.getElementById('fos').value; 


                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "POST",
                    url: '{{url('/mts-manager-delivery-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                    
                  document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
           
        }
        function managerDeliveryReportImsWise()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value; 
            var customer         = document.getElementById('customer_id').value;
            var executive_id         = document.getElementById('executive_id').value; 
            var fos         = document.getElementById('fos').value; 


                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "POST",
                    url: '{{url('/mts-manager-delivery-ims-wise-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                    
                  document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
           
        }


        function managerReplaceDeliveryReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value; 
            var executive_id         = document.getElementById('executive_id').value;
            var fos         = document.getElementById('fos').value;
            var customer         = document.getElementById('customer_id').value;


                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-manager-replace-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                  
        }

        function managerReturnDeliveryReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
            var executive_id         = document.getElementById('executive_id').value; 
            var fos         = document.getElementById('fos').value;
            var customer         = document.getElementById('customer_id').value;


             
                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "GET",
                    url: '{{url('/mts-manager-return-delivery-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer,executive_id:executive_id,fos: fos}
                })
                .done(function (response)
                {
                     document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
                
        }

        function officerWiseCustomer(id) { 
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 

            $.ajax({
                method: "GET",
                url: '{{URL('/mts-report/officer-Wise-customer')}}',
                data: {officer_id: id}
            })
            .done(function (response)
            { 
                $('#customerDiv').html(response);       
            });            
        }
        function customer_sales_reports(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value; 
            var executive_id= document.getElementById('executive_id').value;
            var fos         = document.getElementById('fos').value;
            var customer    = document.getElementById('customer_id').value; 
            document.getElementById('loadingTimeMasud').style.display='inline';
            $.ajax({
                method: "GET",
                url: '{{url('/mts-report/sales-report-list')}}',
                data: {fromdate: fromdate, todate: todate,customer:customer,executive_id:executive_id, fos: fos}
            })
            .done(function (response)
            {
                 document.getElementById('loadingTimeMasud').style.display='none';
                $('#showHiddenDiv').html(response);                
            });
        }

        function achievementSalesReport()
        {
            //alert(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value; 
            var customer         = document.getElementById('customer_id').value;
            var executive_id         = document.getElementById('executive_id').value;  
            var fos         = document.getElementById('fos').value;

                document.getElementById('loadingTimeMasud').style.display='inline';
                $.ajax({
                    method: "POST",
                    url: '{{url('/mts-achievement-sales-report-list')}}',
                    data: {fromdate: fromdate, todate: todate,customer:customer,executive_id:executive_id, fos: fos}
                })
                .done(function (response)
                {
                    
                  document.getElementById('loadingTimeMasud').style.display='none';
                    $('#showHiddenDiv').html(response);                
                });
           
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
