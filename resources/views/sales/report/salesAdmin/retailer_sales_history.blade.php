@extends('sales.masterPage') 
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            RETAILER Sales 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Retailer Sales
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
           

            <div class="row clearfix">
               
            
            <!-- #END# Exportable Table -->
			
			
			
      
           <div class="card">
				<div class="header">
					<h2>
						Retailer Sales
					</h2>
				</div>
				
				 <div class="body">         

				  <form action="{{ URL('/PartySalesHistory') }}" method="get">
					
					<div class="row">
					
						<div class="col-sm-2"> 
                            <select class="form-control show-tick" name="year_id" required="">
								<option value="">Select Year</option>
									@foreach($YearList as $rowYearKey => $rowYear)
										<option value="{{ $rowYearKey }}" <?= ($sel_year_id == $rowYearKey)?'selected':'' ?> >{{ $rowYear }}</option>
									@endforeach
                            </select>
						</div>
					
						<div class="col-sm-2">
                            <select class="form-control show-tick" name="month_id" required="">
								<option value="">Select Month</option>
									@foreach($MonthList as $rowMonthKey => $rowMonth)
										<option value="{{ $rowMonthKey }}" <?= ($sel_month_id == $rowMonthKey)?'selected':'' ?>>{{ $rowMonth }}</option>
									@endforeach
                            </select>
						</div>
						
						
					</div>	
					
					<div class="row">	
						<div class="col-sm-4">
                            <select class="form-control show-tick" name="point_id" onchange="getRouteList(this.value)">
								<option value="">Select Point</option>
									@foreach($pointList as $rowPoint)
										<option value="{{ $rowPoint->point_id }}" <?= ($sel_point_id == $rowPoint->point_id)?'selected':'' ?>>{{ $rowPoint->point_name }}</option>
									@endforeach
                            </select>
						</div>

						
						<div class="col-sm-4" id="div_route">
                            <select class="form-control show-tick" name="route_id" onchange="getSalesRetailerList(this.value)">
								<option value="">Select Route</option>
				            </select>
                        </div>
						
                        <div class="col-sm-4" id="div_ratailer">
                            <select class="form-control show-tick" name="ratailer_id">
								<option value="">Select Retailer</option>
				            </select>
                        </div>
						
						 <div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
					</div>	
                       
                    </div>  
				
				</form>	

         
			</div>	
    
        <div class="card">
                    
					<div class="header">
                        <h5>
                           
                        </h5>
                    </div>
		
		<div class="body">
			<div class="table-responsive">
			
			<form action="{{ URL('/ApplySalesCommission') }}" method="POST" enctype="multipart/form-data">
				 {{ csrf_field() }}
			
				                    
									<label for="division">Commission Title:*</label>
									<div class="form-group">
                                        <div class="form-line">
											<input class="form-control"  type="text" id="commission_title" name="commission_title" placeholder="Commisson Title" value="" />
                                        </div>
                                    </div>
									
									<label for="division">Commission Desc:</label>
									<div class="form-group">
                                        <div class="form-line">
											<textarea class="form-control" id="commission_desc" name="commission_desc" placeholder="Commisson Description"> </textarea>
					                    </div>
                                    </div>
									
									
									 <div class="form-group ">
										<input class="form-control" type="file" name="comm_approval_file" />
									 </div>
									
									
									
				
				<table class="table table-bordered table-striped table-hover dataTable js-exportable">
					<thead>
						<tr>
							<th>Sl</th>
							<th>Select</th>
							<th>Point Name</th>
							<th>Route Name</th>
							<th>Retailer Name</th>
							<th>Total Sales</th>
							<th>Comm(%)</th>
							<th>Amount</th>
							<th>Note</th>
						</tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($retSalesHist) > 0)   
                    @php
                    $serial = 1;
					$dynLabel = 1; 
                    $balance = 0;
					$grand_total_sales_value = 0;
                    @endphp

                    @foreach($retSalesHist as $RowRetSalesData) 
					
					
					
                    <tr>
						<th> {{$serial}}</th>
						
						<th> 
							<div>
									<input name="retCommCheck[{{$RowRetSalesData->retailer_id}}]" type="checkbox" id="radio_yes[{{$dynLabel}}]" class="radio-col-red" value="YES">
									<label for="radio_yes[{{$dynLabel}}]"> YES </label>
							</div>        
						</th>
			         	<th>{{$RowRetSalesData->point_name}}</th>
						<th>{{$RowRetSalesData->route_name}}</th>
						<th>{{$RowRetSalesData->retailer_name}}</th>
						
						
						<th>{{$RowRetSalesData->total_sales}}</th>
						<?php $grand_total_sales_value += $RowRetSalesData->total_sales; ?>
						<th>
							<input type="text" size="2" id="sales_com_perc{{$RowRetSalesData->retailer_id}}" name="sales_com_perc[{{$RowRetSalesData->retailer_id}}]" value="" onkeyup="calSalesAmount({{$RowRetSalesData->retailer_id}})" />
						</th>
						
						<th>
							<input type="text" size="8" id="sales_com_amount{{$RowRetSalesData->retailer_id}}" name="sales_com_amount[{{$RowRetSalesData->retailer_id}}]" value="" />
						</th>
						
						<th>
							<textarea id="commission_note{{$RowRetSalesData->retailer_id}}" name="commission_note[{{$RowRetSalesData->retailer_id}}]"> </textarea>
						</th>
						
						
						
						
						<input type="hidden" name="inp_point_id[{{$RowRetSalesData->retailer_id}}]" value="{{$RowRetSalesData->point_id}}"  />
						<input type="hidden" name="inp_route_id[{{$RowRetSalesData->retailer_id}}]" value="{{$RowRetSalesData->route_id}}"  />
						<input type="hidden" name="inp_retailer_id[]" value="{{$RowRetSalesData->retailer_id}}"  />
						
						<input type="hidden" id="retailer_total_sales{{$RowRetSalesData->retailer_id}}" name="retailer_total_sales[{{$RowRetSalesData->retailer_id}}]" value="{{$RowRetSalesData->total_sales}}"  />
						
	                   
                    </tr>
					
					
                   
                
                 @php
                    $serial++;
					$dynLabel++;
                    @endphp
                    @endforeach
					
					<tr>
                        <th colspan="5" style="text-align:right">Grand Total :</th>
                        <th><?php echo number_format($grand_total_sales_value,0); ?></th>
						<th colspan="4"></th>
                    </tr>
					
                @else
                    <tr>
                        <th colspan="9">No record found.</th>
                    </tr>
                @endif     
				
				
					<tr>
					   <th colspan="14">
							<div class="col-sm-8" align="right">
								<input  type="submit" name="applycommission" value="Apply Commission" class="btn bg-green btn-block btn-sm waves-effect" style="width: 150px;">
							</div>  
							
						</th>
						
						
						<input type="hidden" name="inp_year_id" value="{{$sel_year_id}}"  />
						<input type="hidden" name="inp_month_id" value="{{$sel_month_id}}"  />
						
						
						
					</tr>  
				
               
                </tbody>
                <tfoot>
                   <tr>
							<th>Sl</th>
							<th>Select</th>
							<th>Point Name</th>
							<th>Route Name</th>
							<th>Retailer Name</th>
							
							<th>Total Sales</th>
							<th>Comm(%)</th>
							<th>Amount</th>
							<th>Note</th>
                    </tr>
                </tfoot>
                
				</table>
			
			</form>	
        </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
