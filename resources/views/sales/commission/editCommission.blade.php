@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                        EDIT COMMISSION
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
                    <form action="{{ URL('/admin/commission-update') }}" id="form" method="POST">
                        {{ csrf_field() }}    <!-- token -->

                        <input type="hidden" name="id" id="id" value="{{ $edit->id }}">

                        <div class="body"> 
                            <div class="input-group">
                                <b>Business Type <span style="color: #FF0000;">*</span></b>
                                <select id="businessType" name="businessType" class="form-control show-tick" required="">                                    
                                    @foreach($businessType as $type)
                                    <option value="{{ $type->business_type_id }}" @if($edit->businessType==$type->business_type_id) selected="" @endif>{{ $type->business_type }} </option>
                                    @endforeach
                                </select>
                            </div> 

                            <div class="input-group">
                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <b>Min Limit <span style="color: #FF0000;">*</span></b>
                                    <div class="form-line">
                                        <input type="number" id="minSlab" name="minSlab" class="form-control" placeholder="Enter Min Limit" value="{{ $edit->minSlab }}" required="" maxlength="10">
                                    </div>
                                </div>

                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <b>Max Limit <span style="color: #FF0000;">*</span></b>
                                    <div class="form-line">
                                        <input type="number" id="maxSlab" name="maxSlab" class="form-control" placeholder="Enter Max Limit" value="{{ $edit->maxSlab }}" required="" maxlength="10">
                                    </div>
                                </div>
                            </div>

                            <div class="input-group">
                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <b>Rate <span style="color: #FF0000;">*</span></b>
                                    <div class="form-line">
                                        <input type="number" id="rat" name="rat" class="form-control" placeholder="Enter Rate Number" value="{{$edit->rat}}" required="" maxlength="10">
                                    </div>
                                </div>

                                <div class="col-md-6 align-left" style="padding-left:0px;">
                                    <b>Status <span style="color: #FF0000;">*</span></b>
                                    <select id="status" name="status" class="form-control show-tick">
                                    <option value="0" @if($edit->status==0) selected="" @endif> Active </option>
                                    <option value="1" @if($edit->status==1) selected="" @endif> Inactive </option> 
                                </select>
                                </div>
                            </div>                                                    

                            <div class="row" style="text-align: center;">
                                <div class="col-sm-3">                                        
                                    <button type="submit" id="in" class="btn bg-pink btn-block btn-lg waves-effect">UPDATE COMMISSION</button>
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