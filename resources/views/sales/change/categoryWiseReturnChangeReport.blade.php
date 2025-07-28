@extends('sales.masterPage')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Return & Change
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
                            <thead>
                                <tr>
                                    <th width="70%" align="left" valign="top">
                                        <span class="rowTableChalan">
                                            {{ Auth::user()->display_name}} <!-- {{ $resultDistributorInfo->first_name }} --> </span>  
                                    </th>

                                    <th align="right" valign="top" style="text-align: center;">
                                    <?php if($resultDistributorInfo->business_type_id == 1 ) { ?>
                                        <span class="rowTableChalanRight"> Return & Change Memo (Lighting) </span> 
                                    <?php } elseif($resultDistributorInfo->business_type_id == 2) { ?>
                                        <span class="rowTableChalanRight"> Return & Change Memo (Accessories) </span>  
                                    <?php } ?>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                            <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#E0E0E0">
                                    
                                        <tr>
                                            <th width="50%" height="49" align="left" valign="top" class="rowTableDistributor" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">
                                            Point &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->point_name }}<br />
                                            Code &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ Auth::user()->sap_code }} <br />
                                            Contact &nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->cell_phone }}<br />
                                            Route &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->rname }}<br />
                                           
                                            Retailer &nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                            Contact &nbsp;&nbsp;&nbsp; : {{ $resultInvoice->mobile }}                                            
                                            </th>
                                            <th width="50%" align="left" valign="top" class="rowTableDistributor" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">
                                              Chalan No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->chalan_no }}<br />
                                            Chalan Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->chalan_date }}<br />
                                            RC. Order No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->return_order_no }}<br />
                                            RC. Order Date &nbsp;&nbsp;&nbsp;&nbsp;: {{ $resultInvoice->entry_date }}<br />
                                            RC. Order By &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->first_name }}<br />
                                            Contact &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }}
                                            </th>
                                        </tr>
                                    
                                </table>
                                <p></p>
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th class="headerTable">Sl.</th>
                                      <th class="headerTable">Return Category</th>
                                      <th class="headerTable">Return Product</th>
                                      <th class="headerTable">Qty</th>            
                                      <th class="headerTable">Value</th>                                      
                                      <th class="headerTable">Change Category</th>
                                      <th class="headerTable">Change Product</th>
                                      <th class="headerTable">Qty</th>            
                                      <th class="headerTable">Value</th>                                      
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
                                          
                                            $totalWastageQty +=$items->return_qty;
                                            $totalWastageValue += $items->return_value;
                                            $totalReplaceQty += $items->change_qty;
                                            $totalReplaceValue += $items->change_value;

                                            $pro = DB::table('tbl_product')->where('id',$items->change_product_id)->first();

                                            $cat = DB::table('tbl_product_category')->where('id',$items->change_cat_id)->first();

                                            @endphp                                      
                                            <tr>
                                                <th class="rowTableCenter">{{ $serial }} </th>
                                                <th class="rowTableLeft">{{ $items->catname }}</th>
                                                <th class="rowTableLeft">{{ $items->proname }}</th>
                                                <th class="rowTableRight">{{ number_format($items->return_qty,0) }}</th>
                                                <th class="rowTableRight">{{ number_format($items->return_value,2) }}</th>
                                                <th class="rowTableLeft">@if(!empty($cat)) {{ $cat->name }} @endif</th>
                                                <th class="rowTableLeft">@if(!empty($pro)) {{ $pro->name }} @endif</th>
                                                <th class="rowTableRight">{{ number_format($items->change_qty,0) }}</th>
                                                <th class="rowTableRight">{{ number_format($items->change_value,2) }}</th>
                                            </tr>
                                                
                                            @php                               
                                            $serial++;
                                            @endphp
                                            
                                            @endforeach
                                            @endif

                                            <tr>
                                                <th class="rowTableCenter" colspan="3">Total</th>
                                                <th class="rowTableRight">{{ number_format($totalWastageQty ,0)}}</th>
                                                <th class="rowTableRight">{{ number_format($totalWastageValue,2) }}</th>
                                                <th colspan="2" class="rowTableCenter">Total</th>
                                                <th class="rowTableRight">{{ number_format($totalReplaceQty ,0)}}</th>
                                                <th class="rowTableRight">{{ number_format($totalReplaceValue,2) }}</th>
                                            </tr>

                                            <tr class="rowTableLeft">
                                                <th colspan="5"></th>
                                                
                                                <th colspan="3" class="rowTableCenter">Exceed Amount</th>
                                                
                                                <th  class="rowTableRight">{{ number_format($totalReplaceValue - $totalWastageValue,2) }}</th>
                                            </tr>                                               
                                        </tbody>
                                                                        
                           </table>
                                <br/><br/>
                            <table width="100%">

                             <tr>
                                <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                                    <div style="text-align: center; font-family: arial;font-size:8;font-weight: normal;margin-top:5px;margin-left:0px">Prepared By</div>
                                </td>

                                <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                                    <div style="text-align: center; font-family:arial;font-size:8;font-weight: normal;margin-top:5px;margin-right:0px">Name & Sig. of FO/SFO</div> 
                                </td>

                                <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                                    <div style="text-align: center; font-family:arial;font-size:8;font-weight: normal;margin-top:5px;margin-right:0px">Received By</div> 
                                </td>
                            </tr>

                            </table> 
                        </div>

                        <div class="row" style="text-align: center;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printReport()">
                                    <i class="material-icons">print</i>
                                    <span>PRINT</span>
                                </button>
                            </div>
                        </div>
                        <p>&nbps;</p>

                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection