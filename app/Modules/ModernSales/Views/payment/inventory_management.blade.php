@extends('ModernSales::masterPage')
@section('content')
<section class="content">
    <div class="container-fluid">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2> 
                        Inventory Management  
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

            <div class="body">

               <div class="row">
                    <form action="{{ URL('/inventory-management-process') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}    <!-- token -->
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #A62B7F">
                                    <div class="modal-header" style="background-color: #A62B7F">
                                        <div class="row">
                                                                          
                                            <div class="col-lg-8">
                                                <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Upload Inventory</h4>
                                            </div>
                                            <div class="col-lg-4">
                                                <h4 class="modal-title"  style="color:white;"><a href="{{ URL('/mts-product-download') }}">Download Format</a></h4>
                                          </div>
                                        </div>
                                    </div>

                                    
                                    
                                </div>
                                <div class="modal-body">                             
                                    <div class="row clearfix">                                    
                                        <div class="col-sm-12">                                      
                                            <label for="division">CSV Upload:*</label>
                                            <div class="form-group">
                                                <div class="form-line"  > 
                                                <input type="file" name="imported-file">
                                                </div>
                                            </div>   
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="submit" class="btn btn-link bg-pink btn-block btn-lg waves-effect">SAVE</button> 
                                </div>
                            </div>
                        </div>
                    </form> 
                </div> 
            </div>
        </div> 
    </div>
</div>
<!-- #END# Exportable Table -->
</div>
</section>
<script type="text/javascript">
     
     
   </script>
   @endsection
