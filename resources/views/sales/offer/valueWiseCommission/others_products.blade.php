@extends('sales.masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-10">
                    <h2>
                        SPECIAL OFFER VALUE WISE 
                        <small> 
                            <a href="{{ URL('/dashboard') }}"> Dashboard </a> / SPECIAL OFFER VALUE WISE 
                        </small>
                    </h2>
                </div>

                <div class="col-lg-2">

                    <button type="button"  id="ref" class="btn btn-primary waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Offer Design</button>

{{-- <div class="modal-header" style="background-color: #A62B7F">
<h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Offer</h4>
</div> --}}
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
                <form action="{{ URL('/offer/other-save') }}" method="post">
                    {{ csrf_field() }}    <!-- token -->
                    <div class="modal-dialog" role="document">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title" style="color:white;" id="defaultModalLabel">ADD</h4>
                        </div>

                        <div class="modal-content">

                            <div class="modal-body">

                                <div class="row clearfix">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="row clearfix">

                                            <div class="col-sm-12 col-md-6" style="margin-bottom: 0;">
                                                <label for="category">TYPE :*</label>
                                                <div class="form-group ">
                                                    <div class="form-line">
                                                        <select class="form-control show-tick" name="ptype" required>
                                                            <option value="">Select Type</option>
                                                            <option value="1">Lighting</option>
                                                            <option value="2">Accesories</option>
                                                            <option value="3">FAN</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6" style="margin-bottom: 0;">
                                                <label for="category">OFFER GROUP ID :*</label>
                                                <div class="form-group ">
                                                    <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="number" class="form-control" placeholder="Offer ID" name="group_id" required="" />
                                                    </div>
                                                </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-12" style="margin-bottom: 0;">
                                                <label for="category">CATEGORY :*</label>
                                                <div class="form-line" style="height: 195px; overflow: scroll; overflow-x: hidden;">

                                                        @foreach($pcategory as $cname)
                                                            
                                                            <input type="checkbox" id="md_checkbox_21{{ $cname->id }}" name="categorys[]" value="{{ $cname->id }}" class="filled-in chk-col-red" />
                                                            
                                                            <label for="md_checkbox_21{{ $cname->id }}" style="margin-bottom: 0px"> {{ $cname->name }}</label> <br>          
                                                        @endforeach
                                                    </div>
                                            </div>

                                        </div>
                                        <div class="row clearfix">

                                            <div class="col-sm-12 col-md-6" style="margin-bottom: 0;">
                                                <label for="qty">MIN SLAB :*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="number" class="form-control" placeholder="Min Slab" name="min" required="" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-md-6" style="margin-bottom: 0;">
                                                <label for="qty">MAX SLAB :*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="number" class="form-control" placeholder="Max Slab" name="max" required="" />
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-sm-12 col-md-6" style="margin-bottom: 0;">
                                                <label for="qty">COMMISSION:*</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="number" class="form-control" placeholder="Commission" name="commission" required="" />
                                                    </div>
                                                </div>
                                            </div> 

                                            <div class="col-sm-12 col-md-6" style="margin-bottom: 0;">
                                                <label for="category">STATUS :*</label>
                                                <div class="form-group ">
                                                    <div class="form-line">
                                                        <select class="form-control show-tick" name="status" required>
                                                            <option value="1">Active</option>
                                                            <option value="2">Inactive</option>             
                                                        </select>
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
                            <th>Offer ID</th>
                            <th>Min Slab</th>
                            <th>Max Slab</th>               
                            <th>Commission</th>                
                            <th>Business Type</th>                
                            <th>Status</th>                
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($resultRegularOffer) > 0)   
                        @php
                        $i =1;
                        @endphp

                        @foreach($resultRegularOffer as $regularProduct) 
                        <tr>
                            <td>{{$i++ }}</td>                
                            <td>{{$regularProduct->group_id}}</td>
                            <td>{{$regularProduct->min}}</td>
                            <td>{{$regularProduct->max}}</td>
                            <td>{{$regularProduct->commission_rate.'%'}}</td>                
                            <td>{{$regularProduct->business_type}}</td>                
                            <td>
                                @if($regularProduct->status==1)
                                Active
                                @else
                                Inactive
                                @endif
                            </td>                
                            <td>                 
                                <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editSpecialValueWiseProduct('{{ $regularProduct->id }}')" style="width: 70px;">
                                <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteSpecialValueWiseProduct('{{ $regularProduct->id }}')" style="width: 70px; margin-top: 0px;">
                            </td>
                        </tr>


                        @endforeach
                        @else
                        <tr>
                            <th colspan="8">No record found.</th>
                        </tr>
                        @endif     

                    </tbody>
                    <tbody>
                        <tfoot>
                            <tr>
                                <th>SL</th>                
                                <th>Category</th>
                                <th>Min Slab</th>
                                <th>Max Slab</th>               
                                <th>Commission</th>
                                <th>Business Type</th>
                                <th>Status</th>                           
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
