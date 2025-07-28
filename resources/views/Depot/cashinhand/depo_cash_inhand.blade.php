@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-8">

                        <h2>
                           Retailer Balance List
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / <a href="{{ URL('/dashboard') }}"> Depot </a> 
                            </small>
                        </h2>
                    </div>
					<!--
                    <div class="col-lg-2">
                        <a href="{{ url('demo/downloadExcel/retailer_balance.csv') }}"><button class="btn btn-primary btn-lg">Demo File Download</button></a>
                    </div>
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal">Add Balance File</button>
                    </div> -->
                     
                </div>

            </div>

            @if(Session::has('success'))
                <div class="alert alert-success">
                {{ Session::get('success') }}                        
                </div>
            @endif

			 <form action="{{ URL('/update_depo_cash_in_hand') }}" method="POST">
                
				{{ csrf_field() }}    <!-- token -->
				
				<div class="row clearfix">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="card">
							<div class="header">                
								<div class="row">
									<div class="col-sm-10">
										<h2>DEPO</h2>
									</div>

									<div class="col-sm-2" style="text-align: right;">
										<button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">UPDATE</button>
									</div>

								</div>                           
							</div>
							<div class="body">
								<table class="table table-bordered table-striped table-hover dataTable js-exportable">
										<thead>
											<tr>
												<th>Depo Name</th>
												<th>Cash in Hand</th>
												<th>Update Cash In Hand</th>
											</tr>
										</thead>
									   

										<tbody>
											@if(sizeof($depotList) > 0)
												
												<tr>
													 <input type="hidden" id="point_id" name="point_id" value="{{ $depotList[0]->point_id }}">
													<th>{{ $depotList[0]->point_name }}</th>
													<th>{{ $depotList[0]->opening_cash_in_hand }}</th>
													<th><input type="number" class="form-control" id="opening_cash_in_hand" name="opening_cash_in_hand" pattern="[1-9]" value="" style="width: 140px;">
													
												   
												</tr>
												
											@endif
										</tbody>
									</table>
									<p></p>
									<div class="row">
										<div class="col-sm-10" style="text-align: right;">
											&nbsp;
										</div>

										<div class="col-sm-2" style="text-align: right;">
											<button type="submit" class="btn bg-pink btn-block btn-lg waves-effect">UPDATE</button>
										</div>
									</div>                    
							</div>
						</div>
					</div>
				</div>	
			
			</form> 		

			
        </div>    
    </section>
@endsection