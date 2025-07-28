@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Invoice
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/visit') }}"> Visit </a> / Invoice
                            </small>
                        </h2>
                    </div>
                    
                    </div>
                </div>
            </div>
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                         <div class="body">
                            <table width="100%">
                                    <thead>
                                        <tr>
                                            <th width="74%" height="49" align="left">
                                            <img src="{{URL::asset('resources/sales/images/logo.png')}}" alt="SSG Logo">
                                            </th>
                                            <th width="26%" align="left">
                                            MEMO <br />

											Order No     : 12736-17112521641

                                            </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    </tfoot>
                                    <tbody>
                                        <tr>
                                            <th align="left">
                                            <br>
                                            S. B. TRADE <br />
                                            Point  : Kallyanpur (L)<br />
                                            Route : Kallyanpur<br />
                                            Mobile: 88001611304910

                                            </th>
                                            <th align="left">
                                            Collected By : Repon Mahmud <br />
                                            Cell No         : 8801708137563 <br />
                                            Order Date   : 25-11-2017 02:18:17 pm

                                            </th>
                                        </tr>
                                        <tr>
                                          <th align="left">&nbsp;</th>
                                          <th align="left">&nbsp;</th>
                                        </tr>
                                        <tr>
                                          <th align="left">&nbsp;</th>
                                          <th align="left">&nbsp;</th>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-bordered">
                                  <thead>
                                    <tr>
                                      <th>SL</th>
                                      <th>Product Group</th>
                                      <th>Product Name</th>
                                      <th>Order Qty</th>
                                      <th>Value</th>
                                      <th>Free</th>
                                      <th>Wastage Qty</th>
                                      <th>Replace Qty</th>
                                    </tr>
                                  </thead>
                                  <tfoot>
                                    <tr>
                                      <th colspan="3" align="right">NET AMMOUNT</th>
                                      <th>&nbsp;</th>
                                      <th>19,997</th>
                                      <th>&nbsp;</th>
                                      <th>&nbsp;</th>
                                      <th>&nbsp;</th>
                                    </tr>
                                  </tfoot>
                                  <tbody>
                                    <tr>
                                      <th>1</th>
                                      <th>TUBE</th>
                                      <th>200W CLEAR B-22</th>
                                      <th>10</th>
                                      <th>10000</th>
                                      <th>-</th>
                                      <th>0</th>
                                      <th>-</th>
                                    </tr>
                                    <tr>
                                      <th>2</th>
                                      <th>TUBE</th>
                                      <th>200W CLEAR B-22</th>
                                      <th>10</th>
                                      <th>10000</th>
                                      <th>-</th>
                                      <th>0</th>
                                      <th>-</th>
                                    </tr>
                                    <tr>
                                      <th colspan="3" align="right">Sub Total</th>
                                      <th>20</th>
                                      <th>20000</th>
                                      <th>-</th>
                                      <th>0</th>
                                      <th>-</th>
                                    </tr>
                                    <tr>
                                      <th colspan="3" align="right">Grand Total</th>
                                      <th>20</th>
                                      <th>20000</th>
                                      <th>-</th>
                                      <th>0</th>
                                      <th>-</th>
                                    </tr>
                                    <tr>
                                      <th colspan="3" align="left">MEMO COMMISSION : 2.50%</th>
                                      <th>&nbsp;</th>
                                      <th>3</th>
                                      <th>&nbsp;</th>
                                      <th>&nbsp;</th>
                                      <th>&nbsp;</th>
                                    </tr>
                                  </tbody>
                           </table>
                                <p>&nbsp;</p>
                                <p></p>
                                <div class="row" style="text-align: center;">
                                    <div class="col-sm-12">
                                    	<button type="button" class="btn bg-red waves-effect">
                                            <i class="material-icons">print</i>
                                            <span>PRINT  </span>
                                        </button>
                                    </div>
                                    
                           </div>

                                
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>
@endsection