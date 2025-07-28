@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                        ADD CATEGORY
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / {{ $selectedMenu }}
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

        <!-- Exportable Table -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="card">
                    <form action="{{ URL('/admin/except-commission-submit') }}" id="form" method="POST">
                        {{ csrf_field() }}    <!-- token -->

                        <div class="body"> 
                            <div class="input-group">
                                <b>Business Type <span style="color: #FF0000;">*</span></b>
                                <select id="businessType" name="businessType" class="form-control show-tick" required="">
                                    <option value=""> Please Select Business Type </option> 
                                    @foreach($businessType as $type)
                                    <option value="{{ $type->business_type_id }}"> {{ $type->business_type }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group">
                                <b>Category <span style="color: #FF0000;">*</span></b>
                                <select id="categoryId" name="categoryId" class="form-control show-tick" required="" data-live-search="true">
                                    <option value=""> Please Select Category </option> 
                                    @foreach($category as $categorys)
                                    <option value="{{ $categorys->id }}"> {{ $categorys->name }} </option>
                                    @endforeach
                                </select>
                            </div>                            

                            <div class="input-group">                                
                                <b>Status <span style="color: #FF0000;">*</span></b>
                                    <select id="status" name="status" class="form-control show-tick">
                                    <option value="0"> Active </option>
                                    <option value="1"> Inactive </option> 
                                </select>
                            </div>                                                    

                            <div class="row" style="text-align: center;">
                                <div class="col-sm-3">                                        
                                    <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">ADD CATEGORY</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- #END# Exportable Table -->
    </div>
</section>
@endsection