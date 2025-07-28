@extends('sales.masterPage')

@section('content')

<section class="content">
    <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
             


			<div class="row clearfix">

              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Approved Requisition</div>
                            <div class="number">{{ 30 }}</div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Delivered Requisition</div>
                            <div class="number">{{ 25 }}</div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">In  Transit</div>
                            <div class="number">{{ 18}}</div>
                        </div>
                    </div>
                </div>
				
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-2 bg-teal hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Received Requisition</div>
                            <div class="number">{{ 8 }}</div>
                        </div>
                    </div>
                </div>
				
				
			</div>	
			
			
			
    </div>
</section>

@endsection