@extends('eshop::masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2>
                            Requisition Deatils
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href=""> Req Deatils List </a>
                            </small>
                        </h2>
                    </div>
 
                    </div>
                </div>
            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif
            
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">                        

                        @if(sizeof($resultReqPro)>0)
                        <div class="header">
                            <h2>ALL Requisition PRODUCT</h2>                            
                        </div>
                        <div class="body">
						
					<form action="{{ URL('/reqApproved') }}" method="POST">	
					
						{{ csrf_field() }}
				 
                            <table class="table table-bordered">
                                    <thead>
										<tr>
                                            <th colspan="3">Depot Name: &nbsp;{{ $resultReqPro[0]->depot_in_charge }}</th>                  
                                            <th colspan="4">Point Name: &nbsp;{{ $resultReqPro[0]->point_name }}</th>                  
                                            <th colspan="1">Status: &nbsp;{{ $resultReqPro[0]->req_status }}</th>                  
                                        </tr>
										
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Group</th>
                                            <th>Product Name</th>
                                            <th>Stock Qty</th>
											<th>Req Qty</th>
                                            <th>Req Value</th>
											<th>Apprvd Qty</th>
                                            <th>Apprvd Value</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        
                                        

                                       
                                        @php
                                        $serial   = 1;
                                        $count    = 1;
                                        $subTotal = 0;
                                        $totalQty = 0;
                                        $totalWastage = 0;
                                        @endphp
                                        @foreach($resultReqPro as $items)                                       
                                        
										<tr>
                                            <th></th>
                                            <th colspan="9"></th>
                                        </tr>

                                            <tr>
											
												<th>{{ $serial }}</th>
												<th>{{ $items->catname }}</th>
												<th>{{ $items->proname }}</th>
												<th style="text-align: right;">{{ $items->stock_qty }}</th>
												<th style="text-align: right;">{{ $items->req_qnty }}</th>
												<th style="text-align: right;">{{ $items->req_value }}</th>
												
												<th style="text-align: right;">
												<input type="hidden" id="price{{$serial}}" name="price[]" value="{{$items->price}}">
												
													<input type="text" class="form-control" maxlength="4"  
													style="width: 80px;" id="qty{{$serial}}" name="approved_qnty_<?=$items->req_det_id?>" value="{{ $items->req_qnty }}" 
													onkeyup="addAppQty({{$serial}})" />
												</th>
												<th style="text-align: right;">
													<input type="text" class="form-control" class="form-control" maxlength="4" 
													style="width: 80px;" id="value{{$serial}}" name="approved_value_<?=$items->req_det_id?>" value="{{ $items->req_value }}" />
												</th>
												
												<input type="hidden" name="req_det_id[]" value="<?=$items->req_det_id?>">
                                           
										   
                                            </tr>
											
                                            @php
                                            $serial ++;
                                            @endphp
                                                                               
                                        </tr>
                                        
                                        @endforeach
										
								<tr>
									<th colspan="8" >
									 
									    <input type="hidden" name="req_id" id="req_id" value="{{$reqid}}">
									 
										<div class="col-sm-12" align="center">
											<input  type="submit" name="ORDER_APPROVED" value="APPROVED" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
										</div>	
									</th>
								</tr>	
                                        
                                       
                                       
                                    </tbody>
                                    <tfoot>
                                       

                                   
                                </table>
								
					</form>			
                                <p></p>
                               
                               
                               
                        </div>
                        @else
                        
                        <div class="header">
                            <h2>Product EMPTY</h2>                            
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </thead>
                            <tbody>                                        
                                <tr>
                                    <th colspan="7" style="color: #000; text-align: center;" align="center">
                                    <h4>YOUR Requisition PRODUCT IS EMPTY.</h4> <p></p><p></p>

                                    <div class="col-sm-4" style="margin-right: 40px;"></div>
                                   
                                    
                                     </th>                  
                                </tr>
                                <tr>
                                    <th colspan="7"></th>                                                         
                                </tr>
                            </tbody>
                        </table>
                            
                        @endif
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->            
        </div>
    </section>

   

@endsection