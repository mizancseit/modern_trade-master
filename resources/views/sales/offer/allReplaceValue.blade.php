@if($serial==1)
<div id="pointsScroll">
	<table class="table table-bordered">
    	        
        <tbody style="font-size: 11px;">
        	@if(sizeof($resultPoints)>0)
	        	@foreach($resultPoints as $points)
	        	<tr>
	                <th>
	                	
	                		<input type="checkbox" id="basic_checkbox_4{{ $points->point_id }}" name="points[]" value="{{ $points->point_id }}" class="filled-in" onclick="divisionAndPointWiseroutes()" />
	                		<label for="basic_checkbox_4{{ $points->point_id }}" style="margin-bottom: 0px"></label>
	                		<input type="hidden" name="pointsID[]" id="pointsID" value="{{ $points->point_division.'_'.$points->point_id }}">
	                	
	                </th>
	                <th>{{ $points->div_name }} - {{ $points->point_name }}</th>                  
	            </tr>
	            @endforeach
	        @else
	        	<tr>
	                <th colspan="2">No Result</th>
	            </tr>
	        @endif
        </tbody>
    </table>
</div>
@elseif($serial==2)
<div id="pointsScroll">
	<table class="table table-bordered">    	        
        <tbody style="font-size: 11px;">
        	@if(sizeof($resultRoutes)>0)
	        	@foreach($resultRoutes as $routes)
	        	<tr>
	                <th>
	                	
	                		<input type="checkbox" id="md_checkbox_40{{ $routes->route_id }}" name="routes[]" value="{{ $routes->route_id }}" class="filled-in" />
	                		<label for="md_checkbox_40{{ $routes->route_id }}" style="margin-bottom: 0px"></label>
	                		<input type="hidden" name="routesID[]" id="routesID" value="{{ $routes->point_division.'_'.$routes->point_id.'_'.$routes->route_id }}">
	                	
	                </th>
	                <th>{{ $routes->point_name }} - {{ $routes->rname }}</th>                  
	            </tr>
	            @endforeach
	        @else
	        	<tr>
	                <th colspan="2">No Result</th>
	            </tr>
	        @endif
        </tbody>
    </table>
</div>
@elseif($serial==3)
	
	@if(sizeof($resultBundleOfferDetails)>0)
	<table class="table table-bordered">    	        
        <tbody style="font-size: 11px;">        	
        	<tr>
                <th colspan="2" style="background-color: #CCC; text-align: center; font-size: 16px; color: #000;">{{ $resultBundleOfferDetails->vOfferName }}</th>
            </tr>
            <tr>
                <th style="text-align: center;">To Date</th>
                <th style="text-align: center;">From Date</th>
            </tr>
            <tr>
                <th style="text-align: center;"> {{ date('d-m-Y',strtotime($resultBundleOfferDetails->dBeginDate)) }} </th>
                <th style="text-align: center;"> {{ date('d-m-Y',strtotime($resultBundleOfferDetails->dEndDate)) }} </th>
            </tr>

            <!-- Slab List -->

            @if(sizeof($resultBundleOfferSlab)>0)
            <tr>
                <th colspan="2" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;">SLAB</th>
            </tr>

            <tr id="dalert" style="display: none;">
                <th colspan="2">                	
                	<div class="col-sm-12" style="margin-bottom: 0px;">                                        
		                <div class="alert alert-danger alert-dismissible"  style="margin-bottom: 0px;">
			            	Please check at least one of the slab.                     
			            </div>
		            </div>
                </th>
            </tr>

            

            @foreach($resultBundleOfferSlab as $slabs)
            <tr>
                <th style="text-align: center;"> 
                	<div class="demo-radio-button">
                        <input name="slabs" type="radio" id="radio_7{{ $slabs->iId }}" class="radio-col-red" value="{{ $slabs->iId }}" onclick="hoverHidden(this.id)">
                        <label for="radio_7{{ $slabs->iId }}"></label>
                        <input type="hidden" name="slabsPrice" id="slabsPrice{{ $slabs->iId }}" value="$slabs->iMinRange.' - '.$slabs->iMaxRange">
                    </div>
                </th>
                <th style="text-align: center;"> {{ $slabs->iMinRange.' - '.$slabs->iMaxRange }}</th>
            </tr>
            @endforeach
            
            @endif

            <tr>
                <th colspan="2">
                	<input name="type" type="radio" id="radio_777" class="radio-col-red" value="1" onclick="showSlabOrProduct(1)">
                    <label for="radio_777"> SSG Products </label>  
            	</th>            	
            </tr>

            <tr>                
            	<th colspan="2">
                	<input name="type" type="radio" id="radio_788" class="radio-col-red" value="2" onclick="showSlabOrProduct(2)">
                    <label for="radio_788"> Gift </label>  
            	</th>
            </tr>

            <tr>
            	<td colspan="2" id="showSlabOrProduct"></td>
            </tr>

            <tr>
                
                <th colspan="2" style="text-align: left;">
                	<div class="col-lg-4">
                	</div> 

                	<div class="col-lg-3">
                		<button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">Submit</button>
                	</div>                         	    	                                    
                                       
                </th>
            </tr>	       
        </tbody>
    </table>
    @endif


@elseif($serial==4)
<table class="table table-bordered">    	        
    <tbody style="font-size: 11px;">
    	<tr>
            <th colspan="3" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;">GIFT</th>
        </tr>
		<tr>
            <th style="text-align: center; background: #EEEEEE">
            	<input type="text" name="from_slab[]" id="from_slab" class="form-control" style=" background-color: #FFF;" placeholder="Enter Gift Name" onkeypress="hover(this.id)">
            </th>
            <th style="text-align: center; background: #EEEEEE">
            	<input type="number" name="to_slab[]" id="to_slab" class="form-control" style=" background-color: #FFF;" placeholder="Enter Qty"  onkeypress="hover(this.id)">
            </th>
            <th style="text-align: center; background: #EEEEEE">
            	<input type="button" name="add" id="add" value="ADD+" class="btn bg-red btn-block btn-lg waves-effect" onClick="addMoreSlabPro()">
            </th>
        </tr>

        <input type="hidden" name="prof_count" id="prof_count" value="1">
        <tr id="prof_1">
          
        </tr>
	</tbody>
</table>

@elseif($serial==5)
<table class="table table-bordered">    	        
    <tbody style="font-size: 11px;">
    	<tr>
            <th colspan="3" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;">SSG PRODUCT</th>
        </tr>
		<tr>
            <th colspan="3">
            	<div class="form-line">
                    <select name="category" id="category" class="form-control show-tick" data-live-search="true" required="" onchange="ssgCategoryWisePro(this.value)">
                        <option value="">--Select Category--</option>
                        @foreach($resultSSGProductCat as $pro)
                        <option value="{{ $pro->id }}">{{ $pro->name }}</option>
						@endforeach
                    </select>
                </div>
            </th>
        </tr>

        <tr colspan="3">
          <td id="ssgProducts">
          	
          </td>
        </tr>
	</tbody>
</table>

@elseif($serial==6)
<table class="table table-bordered">    	        
    <tbody style="font-size: 11px;">
    	<tr>
            
            <th colspan="23" style="background-color: #CCC; text-align: center; font-size: 14px; color: #000;"> {{ $resultCategory->name }} WISE PRODUCT <br />
            TOTAL PRODUCT : {{ sizeof($resultSSGProduct) }}
        </th>
        </tr>
        @if(sizeof($resultSSGProduct)>0)
        @foreach($resultSSGProduct as $pros)
		<tr>
			<th colspan="2"> {{ $pros->name }}</th>
            <th>
            	<div class="form-line">
            		 <input type="hidden" id="pName{{ $pros->id }}" name="pName[]" class="form-control" value="{{ $pros->id }}">
                    <input type="number" id="pqty{{ $pros->id }}" name="pqty[]" class="form-control" value="" placeholder="Enter Qty" maxlength="8">
                </div>
            </th>
        </tr>
        @endforeach
        @else
        <tr>
			<th colspan="3"> No Product Found</th>
        </tr>
        @endif
	</tbody>
</table>

@elseif($serial==7) 
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Order</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive" style="max-height: 450px; overflow: auto;">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Order Qty</th>
                                <th>Value</th>
                                {{-- <th style="text-align: center;">Print</th> --}}
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultOrderList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($resultOrderList as $orders)
                            @php
                            $totalQty  += $orders->total_qty;
                            $totalValue += $orders->total_value;
                            @endphp                    
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_no }}</th>
                                <th style="font-weight: normal;">{{ $orders->order_date }}</th>
                                <th style="font-weight: normal;">{{ $orders->name }} <br /> {{ $orders->mobile }}</th>
                                <th style="font-weight: normal;">{{ $orders->total_qty }}</th>                        
                                <th style="font-weight: normal;">{{ number_format($orders->total_value,2) }}</th>
                                {{-- <th style="text-align: center;"> 
                                    <a href="{{ URL('/invoice-details/'.$orders->order_id.'/'.$orders->fo_id) }}" target="_blank" title="Click To View Invoice Details">
                                        <img src="{{URL::asset('resources/sales/images/icon/print.png')}}">
                                    </a>
                                </th> --}}
                            </tr>
                            @php
                            $serial++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="4" style="text-align: right;">Grand Total : </th>                        
                                <th>{{ $totalQty }}</th>
                                <th>{{ number_format($totalValue,2) }}</th>
                                {{-- <th></th> --}}
                            </tr>

                        @else
                            <tr>
                                <th colspan="6">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serial==8) 
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Total Retailers</h4>
            </div>

            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive" style="max-height: 450px; overflow: auto;">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                <tr>
                <th>SL</th>
                <th>Retailer Name</th>
                <th>Status</th>                                
                </tr>
                </thead>
                <tfoot>
                <tr>
                <th>SL</th>
                <th>Retailer Name</th>
                <th>Status</th>                        
                </tr>
                </tfoot>
                <tbody>
                @if(sizeof($resultRetailer) > 0)   
                @php
                $serial =1;
                $status ='';
                @endphp

                @foreach($resultRetailer as $retailers)

                <tr>
                <th>{{ $serial }}</th>
                <th>{{ $retailers->name }}</th>                        
                <th>
                    
                    Active
                    

                    <!-- @if($retailers->status==0)
                    <button type="button" class="btn bg-green waves-effect" title="Click To Inactive" data-toggle="modal" data-target="#retilerActiveOrInactive">Active</button>
                    @php
                     $status =1 
                    @endphp                  
                    @else
                    <button type="button" class="btn bg-red waves-effect" title="Click To Active" data-toggle="modal" data-target="#retilerActiveOrInactive">Inactive</button>
                    @php
                     $status =0 
                    @endphp 
                    @endif -->


                    <input type="hidden" name="status" id="status" value="{{$status}}">
                </th>                                        
                </tr>
                @php
                $serial++;
                @endphp
                @endforeach
                @else
                <tr>
                <th colspan="3">No record found.</th>
                </tr>
                @endif    

                </tbody>
                </table>
                </div>
                </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serial==12) 
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Visit</h4>
            </div>

            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive" style="max-height: 450px; overflow: auto;">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>RETAILER NAME</th>
                                        <th>DATE</th>                        
                                        <th>STATUS</th>                        
                                    </tr>
                                </thead>
                                
                                <tbody>
                                @if(sizeof($resultVisitList) > 0)   
                                    @php
                                    $serial =1;                    
                                    @endphp

                                    @foreach($resultVisitList as $visits)                                       
                                    <tr>
                                        <th>{{ $serial }}</th> 
                                        <th>{{ $visits->name }}</th>
                                        <th>{{ date('d M Y', strtotime($visits->date)) }}</th>                                                
                                        <th>
                                            @if($visits->status==3)
                                            Order
                                            @elseif($visits->status==2)
                                            Visit
                                            @elseif($visits->status==1)
                                            Non-visit
                                            @endif
                                        </th>
                                    </tr>
                                    @php
                                    $serial++;
                                    @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <th colspan="4">No record found.</th>
                                    </tr>
                                @endif    
                                    
                                </tbody>
                            </table>
                        </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serial==9) 
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <button type="button" class="close" style="opacity: 0px;">
                    <img src="{{URL::asset('resources/sales/images/icon/print.png')}}" onclick="printReport()" title="Print">
                </button>
                <h4 class="modal-title" id="myModalLabel" >Attendance Summery</h4>
            </div>
        
            <div class="modal-body" style="text-align: center;" id="printMe">
                <div class="table-responsive" style="max-height: 450px; overflow: auto;">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>FO Name</th>
                                <th>Distributor</th>
                                <th>Retailer</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>In Time</th>
                                <th>Out Time</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($attendance) > 0)   
                            @php
                            $serial =1;
                            $totalValue = 0;
                            @endphp

                            @foreach($attendance as $attendances)
                                              
                            <tr>
                                <th style="font-weight: normal;">{{ $serial }}</th>
                                <th style="font-weight: normal;">{{ $attendances->first_name }}</th>
                                <th style="font-weight: normal;">
                                    @php
                                        $dname = DB::table('users')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('id', $attendances->distributor)
                                        ->first();
                                        
                                        if(sizeof($dname)>0)
                                        {
                                            echo $dname->display_name;
                                        }
                                    @endphp
                                </th>
                                <th style="font-weight: normal;">{{ $attendances->retailerName }}</th>
                                <th style="font-weight: normal;">{{ $attendances->location }}</th>
                                <th style="font-weight: normal;">{{ $attendances->date }}</th>
                                <th style="font-weight: normal;">{{ date('H:i', strtotime($attendances->entrydatetime)) }}</th>
                                <th style="font-weight: normal;">
                                    @php
                                        $out = DB::table('ims_attendence')
                                        ->where('global_company_id', Auth::user()->global_company_id)
                                        ->where('foid', Auth::user()->id)
                                        ->where('type', 3)
                                        ->where('date', $attendances->date)
                                        ->groupBy('date')
                                        ->max('entrydatetime');

                                        $outTime = '';
                                        if(sizeof($out)>0)
                                        {
                                            $outTime = date('H:i', strtotime($out));
                                        }
                                    @endphp
                                        
                                    {{ $outTime }}
                                </th>
                            </tr>
                            @php
                            $serial++;
                            $totalValue++;
                            @endphp
                            @endforeach

                            <tr>
                                <th colspan="8" style="text-align: left;">Total Row : {{ $totalValue }}</th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="8">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
@elseif($serial==10)
<!-- Bootstrap Select Css -->
<link href="{{URL::asset('resources/sales/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />
<div class="modal-dialog">
            <div class="modal-content">
        
            <div class="modal-header" style="background-color: #A62B7F">
                <h4 class="modal-title" id="myModalLabel" >Add Depot </h4>
            </div>

            <div class="modal-body" style="text-align: center;" id="printMe">
                <form action="{{ URL('/fo/admin-add') }}" id="form" method="POST">
                            {{ csrf_field() }}    <!-- token -->

                    <div class="input-group">
                        <div class="col-md-12 align-left" style="padding-left:0px;">
                            <b>Name of the Depot <span style="color: #FF0000;">*</span></b>
                            <div class="form-line">
                                <input type="text" id="depotName" name="depotName" class="form-control" value="" placeholder="Enter Depot Name" required="" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 align-left">
                        <div class="row clearfix">
                            <b>Depot Incharge <span style="color: #FF0000;">*</span></b>
                            <select id="incharge" name="incharge" class="form-control show-tick" required="" >
                                @foreach($resultUsers as $incharge)
                                    <option value="{{$incharge->id}}">{{$incharge->display_name}}</option>
                                @endforeach                                         
                            </select>
                        </div>
                    </div>
                    <p>&nbsp;</p>
                    <p></p>

                    <div class="input-group">
                        <div class="col-md-12 align-left" style="padding-left:0px;">
                            <b>Company Name <span style="color: #FF0000;">*</span></b>
                            <div class="form-line">
                                <input type="text" id="companyName" name="companyName" class="form-control" value="{{ session('businessName') }}" disabled="">
                            </div>
                        </div>
                    </div>
                    <p></p>
                    <div class="input-group">
                        <div class="col-md-12 align-left" style="padding-left:0px;">
                            <b>Opening Balance <span style="color: #FF0000;">*</span></b>
                            <div class="form-line">
                                <input type="number" id="openingBalance" name="openingBalance" class="form-control" value="" required="" maxlength="8" >
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>

@elseif($serial==11)

@if(sizeof($resultCategory)>0)
<div id="pointsScroll">
<table class="table table-bordered">
    <thead>
    <tr>
    <th colspan="2" style="text-align: center; background: #EEEEEE">Category</th>
    </tr>
    </thead>

    <tbody  style="font-size: 11px; font-weight: normal;">
    @foreach($resultCategory as $category)
    <tr>
    <th>
    <div class="demo-checkbox">
    <input type="checkbox" id="basic_checkbox_222{{ $category->id }}" name="category[]" value="{{ $category->id }}" class="filled-in" />
    <label for="basic_checkbox_222{{ $category->id }}" style="margin-bottom: 0px"></label>
    </div>
    </th>
    <th>{{ $category->g_code.'::'.$category->name }}</th>                  
    </tr>
    @endforeach
    </tbody>
    </table>
</div>
@else
<table class="table table-bordered">
    <thead>
    <tr>
    <th colspan="2" style="text-align: center; background: #EEEEEE">Category</th>
    </tr>
    </thead>

    <tbody  style="font-size: 11px; font-weight: normal;">
    <tr>
    <th> No Result </th>
    </tr>
    </tbody>
</table>
@endif

@elseif($serial==20)

<select name="products" id="products" class="form-control show-tick" data-live-search="true">
    <option value="">Please Select</option>
    @foreach($resultSSGProduct as $offers)
        <option value="{{ $offers->id }}">
            {{ $offers->name }}
        </option>
    @endforeach
</select>
@endif



