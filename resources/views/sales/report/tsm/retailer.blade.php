
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        Retailer List
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / {{ $selectedSubMenu }}
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
                <div class="header">
                    <h2>FO Wise Retailer List</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row">

                        <div class="col-sm-3">
                            <select id="foID" class="form-control show-tick" data-live-search="true">
                                <option value=""> FO </option> 
                                @foreach($territoryFO as $fos)
                                    <option value="{{ $fos->id }}">{{ $fos->display_name }}</option>
                                @endforeach                                                    
                            </select>
                        </div>                                               

                        <div class="col-sm-2">
                            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="tmsFOWiseRetailerList()">Search</button>
                        </div>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                
            </div>
            
        </div>
    </div>
</section>

@endsection