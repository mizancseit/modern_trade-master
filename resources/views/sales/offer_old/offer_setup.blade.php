    @extends('sales.masterPage')
    @section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            REGULAR OFFER PRODUCTS 
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Regular Products
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
                        Offer Set Up
                    </h2>
                </div>

                <div class="body">

                  <button type="button"  id="ref" class="btn btn-primary waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Offer</button>
                  <br>
                  <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                    <form action="{{ URL('offer/offer_setup_save') }}" method="post">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Offer</h4>
                                </div>
                                <div class="modal-body">

                                    <div class="row clearfix">
                                        <div class="col-sm-12 col-md-12">
                                            <div class="row clearfix">
                                                <div class="col-sm-12 col-md-12">
                                                    <label for="category">Product Category :*</label>
                                                    <div class="form-group">
                                                        <div class="form-line">

                                                            <select class="form-control show-tick" name="category" required="" onchange="getRegularProduct(this.value)">
                                                                <option value="">Select Category</option>
                                                                @foreach($pcategory as $cname)
                                                                <option value="{{ $cname->id }}">{{ $cname->name }}</option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-1</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s1" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p1" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-2</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s2" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p2" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-3</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s3" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p3" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-4</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s4"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p4" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-5</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s5" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p5" />
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>


                                            </div>

                                             <div class="row clearfix">
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-6</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s6" />&nbsp;<input type="text" class="form-control" placeholder="P" name="p6"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-7</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s7"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p7"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-8</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s8"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p8"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-9</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s9"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p9"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-md-2">
                                                  <label for="qty">S-10</label>
                                                  <div class="form-line">
                                                            <input type="text" class="form-control" placeholder="Slab" name="s10"/>&nbsp;<input type="text" class="form-control" placeholder="P" name="p10"/>
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 col-md-1">&nbsp; </div>

                                            </div>





                                            
                                              
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
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
                            <th valign="top" rowspan="2">SL</th>
                            <th valign="top" rowspan="2" class="text-center">Name</th>
                            <th colspan="2" class="text-center">S-1</th>
                            <th colspan="2" class="text-center">S-2</th>
                            <th colspan="2" class="text-center">S-3</th>
                            <th colspan="2" class="text-center">S-4</th>
                            <th colspan="2" class="text-center">S-5</th>
                            <th colspan="2" class="text-center">S-6</th>
                            <th colspan="2" class="text-center">S-7</th>
                            <th colspan="2" class="text-center">S-8</th>
                            <th colspan="2" class="text-center">S-9</th>
                            <th colspan="2" class="text-center">S-10</th>
                            <th rowspan="2" colspan="2" class="text-center">Action</th>
                        </tr>
                        <tr>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>
                            <td>Q</td>
                            <td>P</td>

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
                            <td>{{$regularProduct->cName}}</td>
                            <td>{{$regularProduct->s1}}</td>
                            <td>{{$regularProduct->p1}}</td>
                            <td>{{$regularProduct->s2}}</td>
                            <td>{{$regularProduct->p2}}</td>
                            <td>{{$regularProduct->s3}}</td>
                            <td>{{$regularProduct->p3}}</td>
                            <td>{{$regularProduct->s4}}</td>
                            <td>{{$regularProduct->p4}}</td>
                            <td>{{$regularProduct->s5}}</td>
                            <td>{{$regularProduct->p5}}</td>
                            <td>{{$regularProduct->s6}}</td>
                            <td>{{$regularProduct->p6}}</td>
                            <td>{{$regularProduct->s7}}</td>
                            <td>{{$regularProduct->p7}}</td>
                            <td>{{$regularProduct->s8}}</td>
                            <td>{{$regularProduct->p8}}</td>
                            <td>{{$regularProduct->s9}}</td>
                            <td>{{$regularProduct->p9}}</td>
                            <td>{{$regularProduct->s10}}</td>
                            <td>{{$regularProduct->p10}}</td>
                            <td>

                                <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editOfferSetup('{{ $regularProduct->id }}')" style="width: 50px;">
                            </td>
                            <td>

                             <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="offerSetupDelete('{{ $regularProduct->id}}')" style="width: 50px;">
                         </td>
                     </tr>


                     @endforeach
                     @else
                     <tr>
                        <th colspan="13">No record found.</th>
                    </tr>
                    @endif     

                </tbody>
                <tbody>
                    <tfoot>
                        <tr>
                            <th class="text-center">SL</th>
                            <th class="text-center">Name</th>
                            <th colspan="2" class="text-center">S-1</th>
                            <th colspan="2" class="text-center">S-2</th>
                            <th colspan="2" class="text-center">S-3</th>
                            <th colspan="2" class="text-center">S-4</th>
                            <th colspan="2" class="text-center">S-5</th>
                            <th colspan="2" class="text-center">S-6</th>
                            <th colspan="2" class="text-center">S-7</th>
                            <th colspan="2" class="text-center">S-8</th>
                            <th colspan="2" class="text-center">S-9</th>
                            <th colspan="2" class="text-center">S-10</th>
                            <th colspan="2" class="text-center">Action</th>
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
