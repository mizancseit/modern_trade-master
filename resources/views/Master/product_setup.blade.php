@extends('sales.masterPage')
@section('content')
 <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            NEW PRODUCT  INFORMATION
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Product Set Up
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
    <div class="header" >
        <h2>
            New Product Set Up
        </h2>
    </div>

    <div class="body"  >
         
          <button type="button"  id="ref" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal" onclick="hideInput()">Add Product 
          </button>
          <br>
             <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <form action="{{ URL('/productProcess') }}" method="post">
                {{ csrf_field() }}    <!-- token -->
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #A62B7F">
                            <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Product</h4>
                        </div>
                        <div class="modal-body">
                           
                            <div class="row clearfix">

                                <div class="col-sm-12">
                                    
                                   <label for="group">Group:*</label>
                                      <input type="text" class="form-control" id="category_id" required="" disabled="" />
                                    <div class="form-group" id="">
                                        <div class="form-line">

                                        <select class="form-control show-tick" data-live-search="true" name="group" id="id" required="">
                                           
                                        <option value="">Please Select Group</option>
                                        @foreach($gcode as $g_code)
                                       <option value="{{$g_code->id}}">{{$g_code->g_code}}::{{$g_code->name}}</option>
                                       @endforeach
                                        </select>
                                    
                                        </div>
                                    </div>

                                    <label for="group">Company Code:*</label>
                                     <input type="text" class="form-control" id="companyname" required=""  disabled="" />
                                    <div class="form-group" id="">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="companyid" id="companyid" required="">
                                           
                                        <option value="">Please Select Group</option>
                                        <option value="1100">1100</option>
                                       <option value="1200">1200</option>
                                       <option value="1300">1300</option>
                                       <option value="1400">1400</option>
                                       <option value="1500">1500</option>
                                       <option value="1600">1600</option>
                                        </select>

                                        </div>
                                    </div>
                                  
                                   
                                   <label for="division">SAP Code:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="SAP Code" name="sap_code" id="sap_code" required="" />
                                                 
                                         </div>
                                    </div>
                                    <label for="division">Product Name:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Product" name="product" id="product" required="" />
                                                 
                                         </div>
                                    </div>
                                 <label for="division">MRP:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="MRP" name="mrp" id="mrp" required="" />
                                                 
                                         </div>
                                    </div>
                                       <label for="division">Depo:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Depo" name="depo" id="depo" required="" />
                                                 
                                         </div>
                                    </div>
                                      <label for="division">Distri:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Distri" name="distri" id="distri" required="" />
                                                 
                                         </div>
                                    </div>
                                    <label for="group">Unit:*</label>
                                     <div class="form-group" id="">
                                        <div class="form-line">

                                        <select class="form-control show-tick" name="unit" id="unit" required="">
                                           
                                        <option value="unit">Pcs</option>
                                       
                                        </select>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                           <input type="hidden" class="form-control" placeholder="" name="id" id="id"  />
                           <input type="hidden" class="form-control" placeholder="" name="id" id="id1"  />
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
                            <button type="button" onclick="modelClose()" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
            <br>
               <div class="body">
    
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-basic-example dataTable">
                <thead>
                    <tr>
                      <th>Id</th>
                        <th>Product Group</th>
                        <th>Sub Group</th>
                       
                        <th>SAP Code</th>
                        <th>Product Name</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>MRP</th>
                        <th>Trade</th>
                        <th>Distributor</th>
                        
                       <th class="pull-right">Action</th>
                    </tr>
                </thead>
                 <tbody>

                    @foreach($product as $products)
                    <tr>
                       <th>{{$products->id}}</th>
                        <th>{{$products->category}}</th>
                        <th>{{$products->sub_group}}</th>
                        <th>{{$products->sap_code}}</th>
                        <th>{{$products->product}}[{{$products->id}}]</th>
                        <th>{{$products->unit}}</th>
                        <th><?php if($products->status==2)
                          {
                           echo $status='<b>HOLD:'.$products->active_date.'</b>';
                          }
                          elseif($products->status==1)
                          {
                           echo $status='<b>INACTIVE</b>';
                          }
                          else
                          {
                           echo $status='.';
                          }?></th>
                        <th><?php echo number_format($products->mrp,2)?></th>
                        <th><?php echo number_format($products->depo,2)?></th>
                        <th><?php echo number_format($products->distri,2)?></th>
                        
                     <th><input type="button" name="point_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editProductsSetup('{{$products->id}}')" style="width: 70px;"">
                        <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="deleteProductsSetup('{{$products->id}}')" style="width: 70px; margin-top: 0px;"></th>
                    </tr>

                @endforeach
                    
                  
               
                </tbody>
                <tfoot>
                    <tr>
                        <th>Product Group</th>
                        <th>Sub Group</th>
                        
                        <th>SAP Code</th>
                        <th>Product Name</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>MRP</th>
                        <th>Trade</th>
                        <th>Distributor</th>
                        
                        <th class="pull-right">Action</th>
                    </tr>
                </tfoot>
               
            </table>
        </div>
    </div>
    </div>
</div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
@endsection