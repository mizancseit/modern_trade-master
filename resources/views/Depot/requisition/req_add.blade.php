<!-- Default Size -->
@extends('sales.masterPage')
@section('content')
<section class="content">
        <div class="container-fluid">

            <div class="block-header">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>
                            Requisition Add
                            <small> 
                             <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Requisition
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
									Requisition Set Up
								</h2>
							</div>

    
         
         
       
			<br>
       
	   <div class="body">

		<form action="{{ URL('/req-process') }}" method="post">
           
               
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
                                 <b> Requisition By: </b>  &nbsp; &nbsp;{{$reqAddList[0]->display_name}} 
                                </div>
                            </div>
                        </div>
				</div>		
                           
                            <div class="row clearfix">
                                <div class="col-sm-12">
                                  
									<label for="division">Requisition Serial NO:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" placeholder="Requisition No" name="req_no" 
											value="{{ $reqAddList[0]->sap_code . date('dmY') . $LastReqId[0]->last_req_id }}" readonly />
                                        </div>
                                    </div>
									
								
									
									<label for="division">Requisition Date:*</label>
                                    <div class="form-group">
                                        <div class="form-line">
                                            <input type="text" class="form-control" id="fromdate" placeholder="Requisition Date" value="{{ date('d-m-Y') }}" name="req_date"
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
                    