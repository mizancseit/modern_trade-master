@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        PAYMENT ACKNOWLEDGEMENT LIST
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / ACK list 
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
            <div class="card" style="overflow-y: auto;">
                <div class="header">
                    <h2>PAYMENT ACK LIST</h2>   
                                
                </div>
                
                <div class="body">

    
      
           

    
    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Distributor</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Ack Status</th>
                       
                    </tr>
                </thead>
                 <tbody>
             
			 @if(sizeof($ackList) > 0)   
                    @php
                    $serial =1;
                    @endphp
                    
                    @foreach($ackList as $RowDistriPayment) 
                      <?php $dist=DB::select("select * from users where id=$RowDistriPayment->depot_in_charge");
                      foreach ($dist as $distName) {
                          $dist_user=$distName->display_name;
                      }
                    ?>

                       <tr>        
                        <th>{{$serial}}</th>
                        <th>{{ $dist_user}}</th>
                        <th>{{$RowDistriPayment->trans_amount}}</th>
                        <th>{{$RowDistriPayment->payment_type}}</th>
                        <th>{{$RowDistriPayment->trans_date}}</th>
                        <th>{{$RowDistriPayment->ack_status}}</th>
                        
                      
                        
                       
                    </tr>
                   
                
                 @php
                    $serial++;
                    @endphp
                    @endforeach
                @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                @endif     
               
                </tbody>
                <tfoot>
                   <tr>
                        <th>Sl</th>
                        <th>Distributor</th>
                        <th>Payment Amount</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Ack Status</th>

                       
                    </tr>
                </tfoot>
                <tbody>
             
                </tbody>
            </table>
        </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
    
@endsection
