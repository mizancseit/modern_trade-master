@extends('eshop::masterPage')
@section('content')
<style type="text/css">
    label{
        color: black;
    }
</style>
<section class="content">
    <div class="container-fluid">


        @if(Session::has('success'))
        <div class="alert alert-success">
            {{ Session::get('success') }}                        
        </div>
        @endif 


        <form action="{{ URL('/dist/was-req-add-to-product') }}" method="POST">
            {{ csrf_field() }}    <!-- token -->
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #A62B7F">
                        <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add User</h4>
                    </div>
                    <div class="modal-body">

                        <div class="row clearfix">

                            <div class="col-sm-12">
                                <label for="division">Customer Name:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" placeholder="Customer Name" name="customer_name" />
                                    </div>
                                </div>

                                <label for="division">Customer code:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" placeholder="Customer code" name="customer_code" />
                                    </div>
                                </div>

                                <label for="division">SAP code:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" placeholder="SAP code" name="sap_code" />
                                    </div>
                                </div>
                                 <label for="division">Credit limit:*</label>
                                <div class="form-group">
                                    <div class="form-line">
                                        <input required type="text" class="form-control" placeholder="Credit limit" name="credit_limit" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                        <button type="button" onclick="distrimodelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>
                </div>

            </div>
        </form> 

    </div>
</section>
@endsection