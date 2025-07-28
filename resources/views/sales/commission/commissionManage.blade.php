@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">
                        <h2>
                            COMMISSION MANAGEMENT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{ $selectedMenu }}
                            </small>
                        </h2>
                    </div>

                    <div class="col-lg-2">
                        <a href="{{ URL('/admin/commission-add') }}">
                            <button type="button" class="btn bg-success btn-block btn-lg waves-effect">ADD COMMISSION</button>
                        </a>
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
                <div class="body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                <tr>
                <th>SL</th>
                <th>Business</th>
                <th>Min Limit</th>
                <th>Max Limit</th>
                <th>Rate</th>
                <th>Status</th>
                <th>Option</th>                                
                </tr>
                </thead>
                <tfoot>
                <tr>
                <th>SL</th>
                <th>Business</th>
                <th>Min Limit</th>
                <th>Max Limit</th>
                <th>Rate</th>
                <th>Status</th>
                <th>Option</th>                        
                </tr>
                </tfoot>
                <tbody>
                @if(sizeof($commission) > 0)   
                @php
                $serial =1;
                $status ='';
                @endphp

                @foreach($commission as $commissions)

                <tr>
                <th>{{ $serial }}</th>
                <th>{{ $commissions->business_type }}</th>
                <th>{{ $commissions->minSlab }}</th>                        
                <th>{{ $commissions->maxSlab }}</th>                        
                <th>{{ $commissions->rat }}</th>                        
                <th> @if($commissions->status==0) Active @else Inactive @endif </th>                        
                <th>
                <a href="{{ URL('/admin/commission-edit/'.$commissions->id)}}">
                    <button type="button" class="btn bg-green waves-effect" title="Click To Edit">Edit</button>
                </a>
                </th>
                                                        
                </tr>
                @php
                $serial++;
                @endphp
                @endforeach
                @else
                <tr>
                <th colspan="6">No record found.</th>
                </tr>
                @endif    

                </tbody>
                </table>
                </div>
                </div>

                </div>
                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection