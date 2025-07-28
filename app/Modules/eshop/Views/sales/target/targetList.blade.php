<div class="card">
    <div class="header">
        <h5>
            About {{ sizeof($targetList) }} results 
        </h5>
    </div>
    <div class="body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                <thead>
                    <tr>
                        <th>SL</th> 
                        <th>Year </th>
                        <th>Month</th>
                        <th>Customer Name</th>
                        <th>Customer Code</th>
                        <th>Officer</th>
                        <th>Executive</th>
                        <th>VALUE</th>
                        <th>Action</th>
                    </tr>
                </thead>
                
                 <tbody>
                    @php
                    $i =1; 
                    $totalValue =0;
                    @endphp
                    @if(sizeof($targetList) > 0)   
                    
                    @foreach($targetList as $targetData) 
                    @php 
                    $totalValue +=$targetData->value;

                     $executive  = DB::table('users') 
                    ->where('id', $targetData->executive_id)    
                    ->first();
                    @endphp
                    <tr>
                        <td>{{$i++ }}</td> 
                        <td>{{ $targetData->year }}</td>
                        <td>{{ date("F", mktime(0, 0, 0, $targetData->month, 10)) }}</td> 
                        <td>{{ $targetData->cusname }}</td> 
                        <td>{{ $targetData->customer_id }}</td> 
                        <td>{{ $targetData->display_name }}</td> 
                        <td>@if(sizeof($executive)>0) {{ $executive->display_name }} @endif</td>
                        <td>{{ $targetData->value}}</td>
                        <td> 
                            <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="targetEdit('{{ $targetData->id }}')" style="width: 70px;">
                            <a href="{{ URL('/eshop-target-delete/'.$targetData->id) }}">
                                <button type="button" class="btn bg-red btn-block btn-sm waves-effect"  style="width: 70px;" onclick="return confirm('Are you sure?')">Delete</button>
                            </a>
                        </td>
                    </tr>


                    @endforeach

                    <tbody>
                    <tfoot>
                        <tr>
                            <th colspan="7">Total</th>
                            <th>{{ number_format($totalValue,2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <tbody>
                        
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