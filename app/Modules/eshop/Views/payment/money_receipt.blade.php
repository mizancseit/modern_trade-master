<style type="text/css">
    body{
        margin:0 !important;
        padding:0 !important;
        background-color: #fff !important;
        color: #111111 !important;
    }
    .table-bordered{color: #111111;}
</style>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/sales/plugins/bootstrap/css/bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{ URL::asset('resources/sales/css/style.css') }}">

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
   
        <div class="body" id="printMe">
            <table width="100%">
                <thead>
                    <tr>
                       <th width="40%" align="left" valign="top">
                        <span style="font-family: arial;font-size:14;font-weight: bold; text-transform: uppercase; color: #263F93;"> {{$outletPayment->name}}  </span>
                        </th>

                        <th align="right" valign="top" style="border:1px solid #EEEEEE;text-align: center;">
                            <span style="font-size: 20px;"> Money Receipt </span>
                        </th>
                        <td align="right" valign="top" style="text-align: center;font-size: 16px;">
                            Date: {{ $outletPayment->trans_date }} 
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" height="20"></td>
                    </tr>
                </thead>
            </table> 
            <table class="table table-bordered">
                <thead>
                      <tr>
                        <th>Payment No.</th>
                        <th>Payment Type</th>
                        <td align="right"><b>Amount</b></td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>{{ $outletPayment->payment_no }}</td>
                        <td>{{ $outletPayment->payment_type }}</td>
                        <td align="right">{{ number_format($outletPayment->payment_amount,2) }} TK.</td>
                      </tr>
                      <tr>
                          <td colspan="2" align="right">VAT</td>
                           
                          <td align="right">0.00 TK</td>
                      </tr>
                      <tr>
                          <td colspan="2" align="right"><b>Total</b></td>
                           
                          <td align="right"><b>{{ number_format($outletPayment->payment_amount,2) }} TK.</b></td>
                      </tr>
                       
                    </tbody>
            </table>
             <table class="table" style="border:0px;">

                <tr>
                        <td colspan="2" height="70" >&nbsp;</td>
                </tr>

                 <tr>
                    <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                        <div class="signature" style="text-align: center; font-family:arial;font-size:10;font-weight: normal;margin-top:5px;margin-left:0px">Prepared By</div>
                    </td>


                    <td align="center"> <div style="border: .3px dotted #000; width: 150px;" > </div>
                        <div class="signature" style="text-align: center; font-family:arial;font-size:10;font-weight: normal;margin-top:5px;margin-right:0px">Received By</div>
                    </td>
                </tr>

         </table>
        </div>
    </div>
</div>