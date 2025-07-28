@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            DEPOT FO Sales 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Point
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
            Depot Sales
        </h2>
    </div>

    <div class="body">
    	
		<div class="table-responsive">
		    
				<form action="{{ URL('/DepotFOSalesSummary') }}" method="get">
					
					<div class="row">
					
					   
                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="fromSalesDate" id="fromdate1" class="form-control" value="" placeholder="Select From Date" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="input-group">
                                <div class="form-line">
                                    <input type="text" name="toSalesDate" id="todate1" class="form-control" value="" placeholder="Select To Date"  readonly="">
                                </div>
                            </div>
                        </div>
						
						<div class="col-sm-3">
                            <select class="form-control show-tick" name="fo_id">
								<option value="">Please Select FO</option>
									@foreach($foList as $rowFO)
										<option value="{{ $rowFO->id }}">{{ $rowFO->display_name }}</option>
									@endforeach
                            </select>
						</div>
						
						
						 <div class="col-sm-2">
            				 <button type="submit" name="submit" class="btn btn-link waves-effect">Search</button>
                        </div>
                       
                    </div>  
				
				</form>	
			
		
		    <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
					    <th>FO Name</th>
                        <th>Route_name</th>
                        <th>Retailer Name</th>
                        <th>Sales Amount</th>
                       
                        
					
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($depotSales) > 0)   
                    @php
                    $serial =1;
                    $totalSales =0;
                    @endphp

                    @foreach($depotSales as $RowDepotSales) 
                    @php                    
                    $totalSales += $RowDepotSales->total_delivery_value;
                    @endphp
					<tr>
                        
						<th>{{$serial}}</th>
                        <th>{{$RowDepotSales->display_name}}</th>
                        <th>{{$RowDepotSales->rname}}</th>
                        <th>{{$RowDepotSales->name}}</th>
                        <th>{{$RowDepotSales->total_delivery_value}}</th>
            			
					</tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                  @endphp                
                     
                </tbody>

                <tfoot>
                   <tr>
                        <th colspan="4" style="text-align: right;">Total Sales :</th>
                        <th colspan="4"> {{ $totalSales }}</th>
                    </tr>
                </tfoot>
                @else                
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif 
                
            </table>
        </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection
