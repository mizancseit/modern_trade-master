@extends('ModernSales::masterPage')
@section('content')
<section class="content">
  <div class="container-fluid">

    <div class="block-header">
      <div class="row">
        <div class="col-lg-12">
          <h2>
           Outlet Payments List 
           <small> 
             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Point
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
        <div class="row">
          <div class="col-lg-9">
            <h2>
              Outlet Payments
            </h2>

          </div>

        </div>

      </div>

      <div class="body">



        <div id="showHiddenDiv">

          <form action="{{ URL('/app_admin_payment_edit_submit') }}" method="post" id="distriPay">
            {{ csrf_field() }}    <!-- token -->
            <input type="hidden" name="id" value="{{$payments->transaction_id}}">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header" style="background-color: #A62B7F">
                  <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Edit Payment</h4>
                </div>
                <div class="modal-body">

                  <div class="row clearfix">



                    <div class="col-lg-12">

                      <label for="division">Payment Amount:*</label>
                      <div class="form-group">
                        <div class="form-line">
                          <input type="text" class="form-control" value="{{$payments->payment_amount}}" placeholder="Payment Amount" name="payment_amount" />
                        </div>
                      </div>

                      <label for="division">Adjust Amount:*</label>
                      <div class="form-group">
                        <div class="form-line">
                          <input type="text" class="form-control" value="{{$payments->adjust_amount}}" placeholder="Adjust Amount" name="adjust_amount" />
                        </div>
                      </div>
                      <label for="division">Remarks/Note:*</label>
                      <div class="form-group">
                        <div class="form-line">
                          <input type="text" class="form-control" value="{{$payments->ack_remarks}}" placeholder="Remarks" id="remarks" name="ack_remarks" />
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                <button type="button" onclick="distrimodelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
              </div>
            </form>
          </div>

        </div>
      </div>
      <!-- #END# Exportable Table -->
    </div>
  </section>
  <script type="text/javascript">
    function outletPaymentList()
    {  

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

            //alert('in');
            var fromdate    = document.getElementById('fromdate').value;
            var todate      = document.getElementById('todate').value;
           // alert(fromdate);

           $.ajax({
            method: "GET",
            url: '{{url('admin_payments_con_list')}}',
            data: {todate: todate,fromdate: fromdate}
          })
           .done(function (response)
           {
                    //alert(response);
                    $('#showHiddenDiv').html(response);                
                  });

         }
       </script>
       @endsection
