@extends('ModernSales::masterPage')
@section('content')
<section class="content">
  <div class="container-fluid">

    <div class="block-header">
      <div class="row">
        <div class="col-lg-12">
          <h2>
           Customer Payments List 
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
              Customer Payments
            </h2>

          </div>

        </div>

      </div>

      <div class="body">

       <div class="row">
        <div class="col-sm-2">
          <div class="input-group">
            <div class="form-line">
              <input type="text" name="fromdate" id="fromdate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
            </div>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="input-group">
            <div class="form-line">
              <input type="text" name="toDate" id="todate" class="form-control" value="{{ date('d-m-Y') }}" placeholder="Select To Date"  readonly="">
            </div>
          </div>
        </div>
        <div class="col-sm-4">
              <select id="executive_id" name="executive_id" class="form-control show-tick"> 
                  @foreach($managementlist as $row)
                      <option value="{{ $row->id }}">{{$row->display_name }}</option>
                  @endforeach                                                    
              </select>
          </div>

          <div class="col-sm-4">
              <select id="fos" name="fos" class="form-control show-tick" data-live-search="true" onchange="allCustomer(this.value)">
                  <option value="">-- Select Officer--</option> 
                  @foreach($officerlist as $row)
                      <option value="{{ $row->id }}">{{ $row->email.' : '.$row->display_name }}</option>
                  @endforeach                                                   
              </select>
          </div> 
          
          
      </div>
        <div class="row"> 

            <div class="col-sm-4" id="customerDiv">
                <select id="customer_id" class="form-control show-tick" data-live-search="true" required="">
                    <option value="">-- Select Customer--</option> 
                                                                       
                </select>
            </div>
            
            <div class="col-sm-4">
                <select id="payment_type" class="form-control show-tick" data-live-search="true">
                    <option value="">-- Select Type --</option> 
                    <option value="payment">Payment</option>
                    <option value="adjustment">Adjustment</option>
                                                                      
                </select>
            </div>
          <div class="col-sm-2">
            <button type="button" class="btn bg-pink btn-block btn-lg waves-effect" onclick="adminPaymentList()">Search</button>
          </div>
           <div class="col-sm-2 text-center">                        
               <img src="{{URL::asset('resources/sales/images/loading.gif')}}" id="loadingTimeMasud" style="display: none;">
            </div>
        </div>
      </div>


      <div id="showHiddenDiv">
 

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
