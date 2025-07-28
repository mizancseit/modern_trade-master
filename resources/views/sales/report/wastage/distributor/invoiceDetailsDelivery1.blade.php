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
                                    <td width="50%" align="left" valign="top"><b> {{ Auth::user()->display_name }} </b></td>
                                    <td width="50%" align="left" valign="top"><b> Wastage Chalan </b></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                            <table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#E0E0E0">
                                    
                                        <tr>
                                            <th width="50%" height="49" align="left" valign="top" class="theader" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">
                                            Point &nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->point_name }}<br />
                                            Code &nbsp;&nbsp;&nbsp;&nbsp; : {{ Auth::user()->sap_code }} <br />
                                            Route &nbsp;&nbsp;&nbsp; : {{ $resultDistributorInfo->rname }}<br />
                                            Mobile &nbsp; : {{ $resultDistributorInfo->cell_phone }}<br />
                                            To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->name }}<br />
                                            Mobile &nbsp; : {{ $resultInvoice->mobile }}                                            
                                            </th>
                                            <th width="50%" align="left" valign="top" class="theader" style="padding-bottom: 10px; padding-top: 10px; padding-left: 10px;">
                                            Chalan No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultInvoice->chalan_no}}<br />
                                            Chalan Date &nbsp;&nbsp; : {{ $resultInvoice->chalan_date }}<br />
                                            W. Order No &nbsp;&nbsp; : {{ $resultInvoice->order_no }}<br />
                                            W. Order Date : {{ $resultInvoice->update_date }}<br />
                                            W. Order By &nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->first_name }}<br />
                                            Cell No &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $resultFoInfo->cell_phone }}
                                            </th>
                                        </tr>
                                    
                                </table>
                                <p>&nbsp;</p>
                                <table class="table table-bordered">
                                  <thead>
                                    <tr class="theader">
                                      <th>SL</th>
                                      <th>Wastage Category</th>
                                      <th>Wastage Product</th>
                                      <th style="text-align: right;">Qty</th>            
                                      <th style="text-align: right;">Value</th>                                      
                                      <th>Replace Category</th>
                                      <th>Replace Product</th>
                                      <th style="text-align: right;">Qty</th>            
                                      <th style="text-align: right;">Value</th>                                      
                                    </tr>
                                  </thead>

                                  <tbody>
                                            
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
                                            $serial   = 1;
                                            $totalWastageQty +=$items->wastage_qty;
                                            $totalWastageValue += $items->p_total_price;
                                            $totalReplaceQty += $items->replace_delivered_qty;
                                            $totalReplaceValue += $items->replace_delivered_value;

                                            $pro = DB::table('tbl_product')->where('id',$items->delivery_product_id)->first();

                                            $cat = DB::table('tbl_product_category')->where('id',$items->delivery_cat_id)->first();

                                            @endphp                                      
                                            <tr>
                                                <th class="theader">{{ $serial }} </th>
                                                <th class="theader">{{ $items->catname }}</th>
                                                <th class="theader">{{ $items->proname }}</th>
                                                <th class="theader" style="text-align: right;">{{ $items->wastage_qty }}</th>
                                                <th class="theader" style="text-align: right;">{{ $items->p_total_price }}</th>
                                                <th class="theader">{{ $cat->name }}</th>
                                                <th class="theader">{{ $pro->name }}</th>
                                                <th class="theader" style="text-align: right;">{{ $items->replace_delivered_qty }}</th>
                                                <th class="theader" style="text-align: right;">{{ $items->replace_delivered_value }}</th>
                                            </tr>
                                                
                                            @php                               
                                            $serial++;
                                            @endphp
                                            
                                            @endforeach
                                            @endif

                                            <tr class="theader">
                                                <th colspan="3" style="text-align: center;">Total</th>
                                                <th style="text-align: right;">{{ $totalWastageQty }}</th>
                                                <th style="text-align: right;">{{ number_format($totalWastageValue,2) }}</th>
                                                <th colspan="2" style="text-align: center;">Total</th>
                                                <th style="text-align: right;">{{ $totalReplaceQty }}</th>
                                                <th style="text-align: right;">{{ number_format($totalReplaceValue,2) }}</th>
                                            </tr>

                                            <tr class="theader">
                                                <th colspan="5"></th>
                                                
                                                <th colspan="3" style="text-align: center;"> Exceed/Less Amount</th>
                                                
                                                <th style="text-align: right;">{{ $totalWastageValue - $totalReplaceValue }}</th>
                                            </tr>                                               
                                        </tbody>
                                                                        
                           </table>

                            <table width="100%">

                            <tr>
                            <td colspan="4" height="40">&nbsp;</td>
                            </tr>

                            <tr>
                            <td> <div style="border: .5px solid #CCC; width: 160px;" > </div>
                            <div style="font-size:13px;margin-top:5px;margin-left:38px">Delivered By</div> </td>


                            <td align="right"> <div style="border: .5px solid #CCC; width: 160px;" > </div>
                            <div style="font-size:13px;margin-top:5px;margin-right:40px">Received By</div> </td>
                            </tr>

                            </table> 
                        </div>

                        <div class="row" style="text-align: center;">
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-red waves-effect" onclick="printWastageReport()">
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