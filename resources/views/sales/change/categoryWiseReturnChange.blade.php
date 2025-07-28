@extends('sales.masterPage')
@section('content')
<style type="text/css">
.trow{ font-weight: normal;}

#hoverNone{
	display: inline-block;
	cursor: not-allowed;
	pointer-events: none;
	opacity:.4;
}

</style>
<section class="content">
	<div class="container-fluid">
		<div class="block-header">
			<div class="row">
				<div class="col-lg-6">
					<h2 style="padding-top: 30px;">
						NEW RETURN & CHNAGE
						<small> 
							<a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/returnorder') }}"> Return </a> / New Return & Change
						</small>
					</h2>
				</div>

		</div>
	</div>

@if(Session::has('success'))
<div class="alert alert-success">
	{{ Session::get('success') }}                        
</div>
@endif 

@if(Session::has('failure'))
<div class="alert alert-danger">
	{{ Session::get('failure') }}                        
</div>
@endif 			
{{-- 
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header"> 
				<h2>@if(sizeof($resRetChangData)>0) {{ $resRetChangData[0]->retName }} @else STORE @endif </h2>                            
			</div>
		</div>
	</div>
</div> --}}

<form action="{{ URL('/confirm-return-change') }}" method="POST">
	{{ csrf_field() }}    <!-- token -->

	<input type="hidden" id="distributor_id" name="distributor_id" value="{{ $distributorID }}">
	<input type="hidden" id="point_id" name="point_id" value="{{ $pointID }}">
	<input type="hidden" id="route_id" name="route_id" value="{{ $routeid }}">
	<input type="hidden" id="retailer_id" name="retailer_id" value="{{ $retailderid }}"> 
	<input type="hidden" id="fo_id" name="fo_id" value="{{ $foID }}"> 

	<input type="hidden" id="return_order_id" name="return_order_id" value="{{ $return_order_id }}">

	<div id="showHiddenDiv">                        

		{{-- Here Product List --}}


		<!-- load product start -->



		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					{{-- <div class="header">                
						<div class="row">
							<div class="col-sm-10">
								<h2>PRODUCTS LIST</h2>
							</div>

							<div class="col-sm-2" style="text-align: right;">
								<button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">Confirm</button>
							</div>

						</div>                           
					</div> --}}
					<div class="body">
						<table width="100%">
                                <tr>
                                    <td width="50%" align="left" valign="top"><b> {{ Auth::user()->display_name }} </b></td>
                                    <td width="50%" align="left" valign="top"><b> Return & Change Chalan </b></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                            <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#E0E0E0">
                                    
                                        <tr>
                                            <th width="50%" height="49" align="left" valign="top" class="theader" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">
                                            Point &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->point_name }}<br />
                                            Code &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ Auth::user()->sap_code }} <br />
                                            Contact &nbsp;&nbsp; : {{ $resultDistributorInfo->cell_phone }}<br />
                                            Route &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->rname }}<br />
                                           
                                            Retailer &nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                            Contact &nbsp;&nbsp;&nbsp; : {{ $resultInvoice->mobile }}                                            
                                            </th>
                                            <th width="50%" align="left" valign="top" class="theader" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">
                                            RCC. Order No. &nbsp;&nbsp; : {{ $resultInvoice->return_order_no }}<br />
                                            RCC. Order Date &nbsp;: {{ $resultInvoice->entry_date }}<br />
                                            RCC. Order By &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->first_name }}<br />
                                            Contact &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }}
                                            </th>
                                        </tr>
                                    
                                </table>

						<table class="table table-bordered table-striped table-hover dataTable js-exportable">
							<thead>
								<tr>
									<th>SL</th>
									<th>Return Category</th>
									<th>Return Product</th>
									<th>Qty</th>
									<th>Value</th>
									<th>Change Category</th>
									<th>Change Product</th>
									<th>Qty</th>		
									<th>Value</th>		

								</tr>
							</thead>

							<tbody>
								@if(sizeof($resRetChangData) > 0)
								@php
								$serial = 1;
								$returnValue = 0;
								$changeValue = 0;
								@endphp

								@foreach($resRetChangData as $products)
								@php

								$returnValue += $products->return_value;
								$changeValue += $products->change_value;
								@endphp
								<tr>
									<th class="trow">{{ $serial }}</th>


									<input type="hidden" id="return_cat_id" name="return_cat_id[]" value="{{ $products->return_cat_id }}">
									<input type="hidden" id="return_product_id" name="return_product_id[]" value="{{ $products->return_product_id }}">
									<input type="hidden" id="return_qty" name="return_qty[]" value="{{ $products->return_qty }}">
									<input type="hidden" id="return_value" name="return_value[]" value="{{ $products->return_value }}">
									{{-- <input type="hidden" id="change_cat_id" name="change_cat_id[]" value="{{ $products->return_cat_id }}"> --}}




									<th class="trow">
										@php
										$changeCat = DB::table('tbl_product_category')
										->where('id',$products->return_cat_id)->first();
										if(sizeof($changeCat)>0)
										{
											echo $changeCat->name;
										}
										@endphp

									</th>

									<th class="trow">
										@if($products->return_qty!=0)
										{{ $products->retProdName }}
										@endif
									</th>

									<th class="trow">
										@if($products->return_qty!=0)
										{{ $products->return_qty }}
										@endif 
									</th>                                            

									<th class="trow">{{ $products->return_value }}</th>                                            
{{-- <th> 

<select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">
<option value="">Select Category</option>
@foreach($resultCategory as $cname)
@if($products->change_cat_id ==  $cname->id)
<option value="{{ $cname->id }}" selected>{{ $cname->name }}</option>
@else
<option value="{{ $cname->id }}">{{ $cname->name }}</option>
@endif
@endforeach
</select>   

</th> --}}
<th>
	@php
	$tbl_return_exception = DB::table('tbl_return_exception')
	->where('cat_id',$products->change_cat_id)->first();

	$array =null;
	if($products->change_cat_id==4)
    {
        $array = array('4','5');
    }
    else if($products->change_cat_id==11)
    {
        $array = array('11','5');
    }
    else if($products->change_cat_id==5)
    {
        $array = array('5','11');
    }
    else if($products->change_cat_id==27 || $products->change_cat_id==29 || $products->change_cat_id==30 || $products->change_cat_id==31 || $products->change_cat_id==35)
                                {
                                    $array = array('27','29','30','31','35');
                                }
                                else if($products->change_cat_id==32)
                                {
                                    $array = array('27','29','30','31','32','35','54','67','68');
                                }
                                else
                                {
                                	$array = array($products->change_cat_id);
                                }

	$resultCategory = DB::table('tbl_product_category')
							->select('id','status','LAF','name','g_name','g_code','avg_price','global_company_id')
							->where('status', '0')
							->where('gid', Auth::user()->business_type_id)
							->where('global_company_id', Auth::user()->global_company_id)
							->whereIn('id', $array)
							->get();

	@endphp

	<div class="form-line" style="width: 170px;">
		@if(sizeof($tbl_return_exception)>0)
		<select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">
			<?php
			foreach($resultCategory as $pname) 
			{

				?>									
				<option value="{{ $pname->id }}" @if($pname->id==$products->change_cat_id) selected="" @endif> {{ $pname->name }} </option>

				<?php
			}
			?>										
		</select>
		@else
		<select class="form-control show-tick" name="change_cat_id[]" onchange="getChangeProduct(this.value,{{$serial}})" required="required">
			<?php
			foreach($resultCategory as $pname) 
			{
				if($pname->id==$products->change_cat_id)
				{
					?>									
					<option value="{{ $pname->id }}" selected=""> {{ $pname->name }} </option>

					<?php
				}
			}
			?>										
		</select>
		@endif

	</div>	

</th>

<th>
	@php
	$depo_price = 0;
// if($products->return_qty!='')
// {
// 	$resultProduct = DB::table('tbl_product')
// 	->select('id','name','depo')
// 	->where('status', '0')
// 	->where('category_id', $products->change_cat_id)
// 	->get();
// }
// else
// {
// 	$resultProduct = DB::table('tbl_product')
// 	->select('id','name','depo')
// 	->where('status', '0')
// 	->where('category_id', $products->change_cat_id)
// 	->get();
// }
	$resultProduct = DB::table('tbl_product')
	->select('id','name','depo')
	->where('status', '0')
	->where('category_id', $products->change_cat_id)
	->get();

	@endphp

	<div class="form-line" id="div_change_product{{$serial}}"  style="width: 170px;">
		<select class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" >
			<option value="">Select Product</option>
			<?php	foreach($resultProduct as $pname) {
				if($products->change_product_id ==  $pname->id) { ?>
				<option value="{{ $pname->id }}" selected="">{{ $pname->name }}</option>

				<?php 	

				if($pname->depo!=''):
					$depo_price = $pname->depo;
				else:
					$depo_price = 0;
				endif;		

			}	else { ?>
			<option value="{{ $pname->id }}">{{ $pname->name }}</option>
		<?php	} 
	} ?>
</select>
<input type="hidden" id="change_prod_price{{$serial}}" name="change_prod_price[]" value="{{$depo_price}}">
</div>	

</th>


<th>
	<input type="text" class="form-control" id="changeQty{{$serial}}" name="change_qty[]" value="{{$products->change_qty}}" maxlength="3" style="width: 60px;" onkeyup="addChange({{$serial}})">
</th>                                            

<th>
	<input type="text" class="form-control" id="changeValue{{$serial}}" name="change_value[]" value="{{$products->change_value}}" maxlength="3" style="width: 60px;">
</th>                                            


</tr>
@php
$serial++;
@endphp

@endforeach

@endif
</tbody>
<tfoot>
	<tr>
		<th colspan="3" style="text-align: right;padding-top:16px;">Return Value Total</th>

		<th colspan="2" align="right" style="text-align: center;">
			<input type="text" class="form-control" id="totalReturnValue" value="{{ $returnValue }}" readonly="" style="text-align: center;">
		</th>
		<
		<th colspan="2" style="text-align: right;padding-top:16px;">Change Value Total</th>

		<th colspan="2" align="right" style="text-align: center;">
			<input type="text" class="form-control" id="totalChangeValue" value="{{ $changeValue }}" readonly="" style="text-align: center;">
		</th>		

	</tr>
</tfoot>
</table>
<p></p>
<div class="row">
	<div class="col-sm-10" style="text-align: right;">
		&nbsp;
	</div>

	<div class="col-sm-2" style="text-align: right;">
		<button type="submit" class="btn bg-pink btn-block btn-lg submit waves-effect">Confirm</button>
	</div>
</div>                    
</div>
</div>
</div>
</div>
{{-- <input type="text" style="color: #000;" id="totalReturnValue" value="{{ $returnValue }}">
<input type="text" style="color: #000;" id="totalChangeValue" value="{{ $changeValue }}"> --}}

<!-- load product end-->




</div>


</form> 

</div>





</section>
@endsection