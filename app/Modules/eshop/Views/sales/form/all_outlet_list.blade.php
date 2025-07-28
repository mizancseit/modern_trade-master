<div class="card">
    <div class="header">
        <h2>
            Outlet List
        </h2>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered dataTable">
                <thead>
                    <tr>
                        <th>SL</th>  
                        <th>Customer Name</th> 
                        <th>Outlet Name</th> 
                        <th>Mobile No</th> 
                        <th>Address</th>  
                        <th>Type</th>  
                        <th>Status</th>  
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $i =1;
                     
                    @endphp
                    @if(sizeof($resultcus) > 0)   
                    
                    @foreach($resultcus as $row) 
                    
                    <tr>
                        <td>{{$i++ }}</td> 
                         <td>{{ $row->cname }}</td>  
                         <td>{{ $row->pname }}</td> 
                         <td>{{ $row->mobile }}</td> 
                         <td>{{ $row->address }}</td> 
                        <td>{{ $row->route_name }}</td> 
                        <td>                       
                            @if($row->status==0)
                           <button type="button" class="btn bg-green btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            @else
                            <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            @endif
                        </td>
                        <td> 
                            <input type="button" name="edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="outletEdit('{{ $row->party_id }}')" style="width: 70px;">
                            
                            @if($row->status==0)
                            <a href="{{ URL('/eshop-outlet-active/'.$row->party_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">In-Active</button>
                            </a>
                            @else
                            <a href="{{ URL('/eshop-outlet-inactive/'.$row->party_id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;">Active</button>
                            </a>
                            @endif
                        </td>
                    </tr>


                    @endforeach
                    @else
                    <tr>
                        <th colspan="7">No record found.</th>
                    </tr>
                    @endif     

                </tbody> 
                    </table>
        </div>
    </div>
</div>