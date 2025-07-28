    @extends('sales.masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-10">

                        <h2>
                            Target List 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Target
                            </small>
                        </h2>
                    </div>
                  
                    <div class="col-lg-2">
                        <button type="button"  id="ref" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#defaultModal">Add Depot</button>
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

                <div class="body">

                  <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                    <form action="{{ URL('depot/depot_setup_save') }}" method="post">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Depot</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                        <div class="col-sm-12 col-md-12">
                                         <div class="row clearfix">
                                           <div class="col-sm-12 col-md-6">
                                            <label for="depoName"> Depot Name * </label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" placeholder="Type Depot Name" name="depoName" id="depoName" required="" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                               <label for="in_charge">In-Charge Name :*</label>
                                               <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control show-tick" name="in_charge" required="">
                                                            <option value="">Select In-Charge</option>
                                                            @foreach($in_charge as $incharge)
                                                            <option value="{{ $incharge->id }}">{{ $incharge->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                </div>
                                <div class="row clearfix">
                                    <div class="col-sm-12 col-md-6">
                                        <label for="division">Division :*</label>
                                        <div class="form-group ">
                                            <div class="form-line">
                                                <select class="form-control show-tick" name="division" required="">
                                                    <option value="">Select Division</option>
                                                    @foreach($division as $div)
                                                    <option value="{{ $div->div_id }}">{{ $div->div_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-6">
                                        <label for="location">Location :*</label>
                                        <div class="form-group ">
                                            <div class="form-line">
                                                <input type="text" class="form-control" placeholder="Type Location" name="location" id="location" required="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row clearfix">
                                     <div class="col-sm-12 col-md-6">
                                        <label for="opening_balance">Opening Balance :*</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" placeholder="Type Opening Balance" name="opening_balance" id="opening_balance" required="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                     <label for="current_balance">Current Balance :*</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" placeholder="Type Current Balance" name="current_balance" id="current_balance" required="" />
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="row clearfix">
                                     <div class="col-sm-12 col-md-6">
                                        <label for="current_sales">Current Sales :*</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" placeholder="Type Current Sales" name="current_sales" id="current_sales" required="" />
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-sm-12 col-md-6">
                                     <label for="market_credit">Market Credit :*</label>
                                        <div class="form-group">
                                            <div class="form-line">
                                                <input type="text" class="form-control" placeholder="Type Market Credit" name="market_credit" id="market_credit" required="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                    </div>
            </form>
        </div>
    </div>
</div>
<br>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
        <thead>
            <tr>
                <th>SL</th>
                <th>Depot Name</th>
                <th>Division</th>
                <th>In-Charge Name</th>
                <th>Location</th>
                <th>Current Balance</th>
                <th>Current Sales</th>
                <th>Market Credit</th>
                <th>Opening Balance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if(sizeof($depotSetup) > 0)   
            @php
            $i =1;
            @endphp

            @foreach($depotSetup as $depotList) 
            <tr>
                <td>{{$i++ }}</td>
                <td>{{$depotList->depot_name }}</td>
                <td>{{$depotList->div_name }}</td>
                <td>{{$depotList->display_name}}</td>
                <td>{{$depotList->depot_location}}</td>
                <td>{{$depotList->depot_current_balance}}</td>
                <td>{{$depotList->depot_current_sales}}</td>
                <td>{{$depotList->market_credit}}</td>
                <td>{{$depotList->opening_balance}}</td>
                <td>

                    <input type="button" name="depot_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="depotListEdit('{{ $depotList->depot_id }}')" style="width: 60px;">
                    <input type="button" name="depot_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="depotListDelete('{{ $depotList->depot_id }}')" style="width: 60px;">
                </td>
            </tr>


            @endforeach
            @else
            <tr>
                <th colspan="9">No record found.</th>
            </tr>
            @endif     

        </tbody>
        <tbody>
            <tfoot>
                <tr>
                    <th>SL</th>
                    <th>Depot Name</th>
                    <th>Division</th>
                    <th>In-Charge Name</th>
                    <th>Location</th>
                    <th>Current Balance</th>
                    <th>Current Sales</th>
                    <th>Market Credit</th>
                    <th>Opening Balance</th>
                    <th>Action</th>
                </tr>
            </tfoot>
            <tbody>

            </table>
        </div>
    </div>
</div>
<!-- #END# Exportable Table -->
</div>
</section>
@endsection
