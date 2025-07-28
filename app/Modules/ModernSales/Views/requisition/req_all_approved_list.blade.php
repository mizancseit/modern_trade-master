@extends('ModernSales::masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-9">
                    <h2>
                        REQUISITION APPROVED MANAGE
                        <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition Appproved 
                        </small>
                    </h2>
                </div>
                </div>
            </div>
        </div>
        <form action="{{ URL('/ready-for-billed-bulk') }}" method="POST">
            {{ csrf_field() }}    <!-- token -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>REQUISITION APPROVED LIST</h2>   
                                
                </div>
                
                <div class="body">
                    
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Customer Name</th>
                                <th>Outlet Name</th>
                                <th>Order No</th>
                                <th>Order Date</th>
                                <th>Aprvd By</th>
                                <th>Aprvd Date</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th><input type="checkbox" id="basic_checkbox" name="ToogleCheck" value="" class="filled-in" onclick="toggleCheckBox()">
                                    <label for="basic_checkbox" style="margin-bottom: 0px">Check All </label> 
                                </th>                                
                            </tr>
                        </thead>
                        
                        <tbody>
                        @if(sizeof($resultReqList) > 0)   
                            @php
                            $serial =1;
                            $totalQty = 0;
                            $totalValue = 0;

                            @endphp

                            @foreach($resultReqList as $ReqRow)
                            
                        
                             @php                   
                                $reultUser  = DB::select("SELECT * from users WHERE id = '".$ReqRow->approved_by."'");
                             @endphp                                            
                                
                                           
                            <tr>
                                <th>{{ $serial }}</th>
                                
                                
                                <th>
                                    <a href="{{ URL('/modernorder-details/'.$ReqRow->order_id) }}" title="Click To Details" target="_blank">
                                      {{ $ReqRow->name }}
                                    </a>
                                </th>
                                
                                <th>{{ $ReqRow->partyName }}</th>
                                <th>{{ $ReqRow->order_no }}</th>
                                <th>{{ $ReqRow->order_date }}</th>
                                <th>{{ $reultUser[0]->display_name }}</th>
                                <th>{{ $ReqRow->approved_date }}</th>
                                <th>{{ $ReqRow->req_status }}</th>          
       
                                <th style="text-align: center;"> 
                                    <a href="{{ URL('/modern-reqBilled/'.$ReqRow->order_id) }}" title="Click To Deliver" onClick="return confirm('Are you sure to Billed?')">
                                        Ready for Billed
                                    </a>    
                                </th>
                                <th>
                                    <input type="checkbox" id="basic_checkbox_<?=$ReqRow->order_id?>" name="order[<?=$ReqRow->order_id?>]" value="YES" class="filled-in">
                                    <label for="basic_checkbox_<?=$ReqRow->order_id?>" style="margin-bottom: 0px"></label>
                                </th> 
                            </tr>
                            
                            @php
                            $serial++;
                            @endphp
                            @endforeach
                            <tr>
                                <th colspan="10" >
                                    <div class="col-sm-12" align="right">
                                        <input  type="submit" name="ORDER_ACKNOWLEDGE" value="Bulk Submit" class="btn bg-green btn-block btn-sm waves-effect" style="width: 110px;">
                                    </div>  
                                </th>
                            </tr>

                        @else
                            <tr>
                                <th colspan="9">No record found.</th>
                            </tr>
                        @endif    
                            
                        </tbody>
                    </table>                                  
                </div>

            </div>
        </div>
    </div>
</form> 
</section>
<script type="text/javascript"> 
    function toggleCheckBox() {
        var inputs = document.getElementsByTagName("input");
        for(var i = 0; i < inputs.length; i++)
        {
            if(inputs[i].type == "checkbox")
            {
                if(document.getElementById("basic_checkbox").checked == true)
                {
                    inputs[i].checked = true;
                } else {
                    inputs[i].checked = false;
                }
            }
        }
    }
</script>
@endsection