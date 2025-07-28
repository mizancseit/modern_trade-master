@extends('sales.masterPage')
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-9">
                        <h2 style="padding-top: 30px;">
                            NON-VISIT 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Non-visit 
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
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header"> 
                            <h2> Non-visit </h2>                            
                        </div>
                        <div class="body">
                            <form id="form_validation" action="{{ URL('/mts-nonvisit-process-submit') }}" method="POST">
                                {{ csrf_field() }}    <!-- token -->

                                <input type="hidden" name="party_id" id="party_id" value="{{ $party_id }}">           
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ $customer_id }}">
                                <select id="reasons" name="reasons" class="form-control show-tick" >
                                    {{-- <option value="">-- Please select reason --</option> --}}
                                    @foreach($resultReason as $reasons)
                                    <option value="{{ $reasons->id }}">{{ $reasons->reason }}</option>
                                    @endforeach                           
                                </select>
                                <div class="input-group">
                                    
                                    <div class="col-md-12 align-left" style="padding-left:0px;">
                                        <div class="form-line">
                                            <textarea class="form-control" name="remarks" id="remarks" placeholder="Remarks" maxlength="1000"></textarea>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="text-align: center;">
                                <div class="col-sm-2">
                                    <button type="submit" id="add" onclick="hideMeVisit()" class="btn bg-pink btn-block btn-lg waves-effect">ADD</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection