@extends('sales.masterPage')
@section('content')
<style type="text/css">
    .theader
    {
        font-size: 11px;
        font-weight: normal;
    }
</style>

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Invoice
                             <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
                            </small>
                        </h2>
                    </div>
                    
                    </div>
                </div>
            </div>
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card" style="font-weight:">
                         <div class="body" id="printMe" >
                         
                            <table width="100%">
                                <tr>
								
                                    <th width="77%" align="left" valign="top">
                                        <span style="font-family: arial;font-size:12;font-weight: bold; text-transform: uppercase; color: #263F93;">
                                            {{ Auth::user()->display_name}} <!-- {{ $resultDistributorInfo->first_name }} --> </span>  
                                    </th>
									
									<th align="right" valign="top" style="text-align: center;margin: 15px;">
									<?php if($resultDistributorInfo->business_type_id == 1 ) { ?>
                                        <span style="font-family: arial;font-size:12;font-weight: bold;"> Wastage Memo (Lighting) </span> 
                                    <?php } elseif($resultDistributorInfo->business_type_id == 2) { ?>
										<span style="font-family: arial;font-size:12;font-weight: bold;"> Wastage Memo (Accessories) </span>  
                                    <?php } ?>
									</th>
					            
								</tr>
                                
                            </table>
                            
							<!-- <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#E0E0E0"> -->
                                <table class="table table-bordered" width="100%" style="margin-top:5px;font-family: arial;font-size:8;font-weight: normal;">
								
                                        <tr>
                                           <th width="45%" align="left" valign="top" style="font-weight: normal;vertical-align:top">
										   <!--<th width="50%" height="49" align="left" valign="top" class="theader" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">-->
                                            Point &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->point_name }}<br />
                                            Code &nbsp;&nbsp;&nbsp;&nbsp; : {{ Auth::user()->sap_code }} <br />
                                            Contact &nbsp;: {{ $resultDistributorInfo->cell_phone }}<br />
											Route &nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->rname }}
                                           <br/>
											Retailer &nbsp;: {{ $resultInvoice->name }}<br />
                                            Contact &nbsp;: {{ $resultInvoice->mobile }}                                            
                                            </th>
											
                                            <!--<th width="50%" align="left" valign="top" class="theader" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">-->
											<th width="55%" align="left" valign="top" style="font-weight: normal;">
                                            Chalan No. &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->chalan_no}}<br />
                                            Chalan Date &nbsp;&nbsp; : {{ $resultInvoice->chalan_date }}<br />
                                            W. Order No. &nbsp; : {{ $resultInvoice->order_no }}<br />
                                            W. Order Date : {{ $resultInvoice->update_date }}<br />
                                            W. Order By &nbsp;&nbsp; : {{ $resultFoInfo->first_name }}<br />
                                            Contact &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp;{{ $resultFoInfo->cell_phone }}
                                            </th>
											
                                        </tr>
                                    
                            </table>

                              
                               
							   <table class="table table-bordered" style="margin-top:-10px;">
                                  <thead>
                                    <tr class="theader">
                                      <th class="headerTable" width="3%" valign="middle">Sl.</th>
                                      <th class="headerTable" width="10%" valign="middle">Wastage Category</th>
                                      <th class="headerTable" width="25%" valign="middle">Wastage Product</th>
                                      <th class="headerTable" width="5%" valign="middle">Qty</th>            
                                      <th class="headerTable" width="5%" valign="middle">Value</th>                                      
                                      <th class="headerTable" width="10%" valign="middle">Replace Category</th>
                                      <th class="headerTable" width="25%" valign="middle">Replace Product</th>
                                      <th class="headerTable" width="5%" valign="middle">Qty</th>            
                                      <th class="headerTable" width="5%" valign="middle">Value</th>                                      
                                    </tr>
                                  </thead>

                                  <tbody class="print-page">
                                            
                                            @if(sizeof($resultCartPro)>0)
                                            @php
                                            $serial   = 1;
                                            $totalWastageQty = 0;
                                            $totalWastageValue = 0;
                                            $totalReplaceQty = 0;
                                            $totalReplaceValue = 0;
                                            @endphp

                                            @foreach($resultCartPro as $items)
                                            @php
                                            $totalWastageQty +=$items->wastage_qty;
                                            $totalWastageValue += $items->p_total_price;
                                            $totalReplaceQty += $items->replace_delivered_qty;
                                            $totalReplaceValue += $items->replace_delivered_value;

                                            $pro = DB::table('tbl_product')->where('id',$items->delivery_product_id)->first();

                                            $cat = DB::table('tbl_product_category')->where('id',$items->delivery_cat_id)->first();

                                            @endphp                                      
                                            <tr>
                                                <th class="rowTableCenter">{{ $serial }} </th>
                                                <th class="rowTableLeft">{{ $items->catname }}</th>
                                                <th class="rowTableLeft">{{ $items->proname }}</th>
                                                <th class="rowTableRight" style="text-align: right;">{{ $items->wastage_qty }}</th>
                                                <th class="rowTableRight" style="text-align: right;">{{ $items->p_total_price }}</th>
                                                <th class="rowTableLeft">{{ $cat->name }}</th>
                                                <th class="rowTableLeft">{{ $pro->name }}</th>
                                                <th class="rowTableRight" >{{ $items->replace_delivered_qty }}</th>
                                                <th class="rowTableRight" >{{ $items->replace_delivered_value }}</th>
                                            </tr>
                                                
                                            @php                               
                                            $serial++;
                                            @endphp
                                            
                                            @endforeach
                                            @endif

                                            <tr class="theader">
                                                <th colspan="3" class="rowTableCenter">Total</th>
                                                <th class="rowTableRight">{{ $totalWastageQty }}</th>
                                                <th class="rowTableRight">{{ number_format($totalWastageValue,2) }}</th>
                                                <th colspan="2" class="rowTableCenter">Total</th>
                                                <th class="rowTableRight">{{ $totalReplaceQty }}</th>
                                                <th class="rowTableRight">{{ number_format($totalReplaceValue,2) }}</th>
                                            </tr>

                                            <tr class="theader">
                                                <th colspan="5"></th>
                                                
                                                <th colspan="3" class="rowTableCenter"> Exceed/Less Amount</th>
                                                
                                                <th class="rowTableRight">{{ number_format( ($totalReplaceValue - $totalWastageValue),0) }}</th>
                                            </tr>   
                                            
                                        </tbody>
                                                                        
                           </table>

                            <table width="100%">

								<tr>
								<td colspan="4" height="40">&nbsp;</td>
								</tr>

								<tr>
								<td align="center"> <div style="border: .3px dotted #000; width: 160px;" > </div>
								<div style="font-family:arial;font-size:8;font-weight: normal;margin-top:5px;margin-left:5px">Delivered By</div> </td>
								
								<td align="center"> <div style="border: .3px dotted #000; width: 170px;" > </div>
								<div style="font-family:arial;font-size:8;font-weight: normal;margin-top:5px;margin-left:10px">Name & Sig. of FO/SFO</div> </td>


								<td align="center"> <div style="border: .3px dotted #000; width: 160px;" > </div>
								<div style="font-family:arial;font-size:8;font-weight: normal;margin-top:5px;margin-right:5px">Received By</div> </td>
								</tr>

                            </table> 
							
							
                        </div>

						@if(Auth::user()->user_type_id==5)
                        <div class="row" style="text-align: center;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                    <i class="material-icons">print</i>
                                    <span>PRINT</span>
                                </button>
                            </div>
                        </div>
                        <p>&nbps;</p>
						 @endif

                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection