<!-- Default Size -->
@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                        DECLARATION ADD
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Declaration
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
            <!-- #END# Exportable Table -->

            <div class="card">
                <div class="header">
                    <h2>
                        Declaration Set Up
                    </h2>
                </div>
                <br>
                <div class="body">
                    <form action="{{ URL('/dist/was-declaration-process') }}" method="post">
                        {{ csrf_field() }}    <!-- token -->

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="form-line">
                                        <b> Point Name: </b> &nbsp; &nbsp;{{$reqAddList[0]->point_name}} 
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="form-line">
                                        <b> Declaration By: </b>  &nbsp; &nbsp;{{$reqAddList[0]->display_name}} 
                                    </div>
                                </div>
                            </div>
                        </div>		

                        <div class="row clearfix">
                            <div class="col-sm-12">

                                <label for="division">Declaration Serial NO:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" placeholder="Declaration No" name="req_no" 
                                        value="{{'WD'.$reqAddList[0]->sap_code . date('dmY') . $LastReqId[0]->last_req_id }}" readonly />
                                    </div>
                                </div>

                                <label for="division">Declaration  Date:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input type="text" class="form-control" id="fromdate" placeholder="Requisition Date" value="{{ date('Y-m-d H:i:s') }}" name="req_date"
                                        value="" required="" />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <input type="hidden" name="point_id" value="{{$reqAddList[0]->point_id}}">
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                            <button type="button" onclick="modelCloseEdit()" id="clean" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- #END# Exportable Table -->
    </div>
</section>
@endsection		
