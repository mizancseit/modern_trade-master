@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Report / Delivery 
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
                    <h2>Products Uploads</h2>                            
                </div>
                
                <div class="body">                    
                    <div class="row"> 
                        <form action="{{ URL('products-upload') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}  
                            <div class="col-sm-12 col-md-12"> 
                                <label for="imported-file">File upload:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="file" class="form-control" placeholder="file-name" name="imported-file" autocomplete="off" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group">
                                    <div class="form-line">
                                        <button type="submit" name="submit" class="btn btn-link waves-effect">Upload</button>
                                    </div>
                                </div>
                            </div> 
                        </form>
                    </div>                                  
                </div>
            </div>

            <div id="showHiddenDiv">
                
            </div>
            
        </div>
    </div>
</section>

@endsection 
