<div class="card">
    <div class="header">
        <h2>
            Product List
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered dataTable">
                <thead>
                    <tr>
                        <th>SL</th> 
                        <th>Company Code </th>
                        <th>Category Name </th> 
                        <th>Product Name</th> 
                        <th>SAP Code</th> 
                        <th>Depot Price</th> 
                        <th>Distributor Price</th> 
                        <th>MRP Price</th>  
                        <th>Status</th>  
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i =1;
                     
                    @endphp
                    @if(sizeof($resultProduct) > 0)   
                    
                    @foreach($resultProduct as $row) 
                    
                    <tr>
                        <td>{{$i++ }}</td> 
                         <td>{{ $row->companyid }}</td>  
                         <td>{{ $row->cname }}</td>  
                         <td>{{ $row->name }}</td> 
                        <td>{{ $row->sap_code }}</td> 
                        <td>{{ $row->depo }}</td> 
                        <td>{{ $row->distri }}</td> 
                        <td>{{ $row->mrp }}</td>  
                        <td>                       
                            @if($row->status==0)
                           <button type="button" class="btn bg-green btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            @else
                            <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            @endif
                        </td>
                        <td>
                            
                            <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="productEdit('{{ $row->id }}')" style="width: 70px;">
                           
                            @if($row->status==0)
                            <a href="{{ URL('/eshop-product-active/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            </a>
                            @else
                            <a href="{{ URL('/eshop-product-inactive/'.$row->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            </a>
                            @endif
                        </td>
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="5">No record found.</th>
                    </tr>
                    @endif     

                </tbody> 
                    </table>
        </div>
    </div>
</div>