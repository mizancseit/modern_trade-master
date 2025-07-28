
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        STOCK REPORT
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Depot / Stock 
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
                <div class="body">
                    <form id="form_validation" method="POST">
                        <select id="categories" class="form-control show-tick" onchange="stockProducts()" data-live-search="true">
                            <option value="">-- Please select category --</option>
							<option value="all"> All Stock </option>
                            @foreach($resultCategory as $categories)
                            <option value="{{ $categories->id }}">{{ $categories->g_code.' : '.$categories->name }}</option>
                            @endforeach                           
                        </select>
                        {{-- <select class="form-control show-tick">
                            <option value="">-- Please select subcategory--</option>             
                        </select> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
           

            <div id="showHiddenDiv">
                <div class="card">
                    <div class="header">
                        <h5>
                            Products Stock 
                        </h5>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                               
                                
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

@endsection