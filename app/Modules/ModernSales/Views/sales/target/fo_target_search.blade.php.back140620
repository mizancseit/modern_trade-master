<div class="table-responsive">
    <table class="table table-bordered dataTable">
        <thead>
            <tr>
                <th>SL</th>
                <th>Division</th>
                <th>Territory</th>
                <th>Point</th>
                <th>Employee ID</th>
                <th>FO name</th>
                <th>Year </th>
                <th>Month</th>
                <th>Category Name</th>
                <th>Qty</th>
                <th>AVG. VALUE</th>
                <th>TOTAL VALUE</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
            $i =1;
            $qty =0;
            $aveValue =0;
            $totalValue =0;
            @endphp
            @if(sizeof($targetList) > 0)   

            @foreach($targetList as $targetData) 
            @php
            $qty +=$targetData->qty;
            $aveValue +=$targetData->avg_value;
            $totalValue +=$targetData->total_value;
            @endphp
            <tr>
                <td>{{$i++ }}</td>
                <td>{{$targetData->diviName }}</td>
                <td>{{$targetData->terriName }}</td>
                <td>{{$targetData->pointName }}</td>
                <td>{{$targetData->employee_id }}</td>                        
                <td>{{$targetData->display_name }}</td>
                <td>{{ date('Y', strtotime($targetData->start_date)) }}</td>
                <td>{{ date('F', strtotime($targetData->end_date)) }}</td>
                <td>{{$targetData->name}}</td>
                <td>{{$targetData->qty}}</td>
                <td>{{$targetData->avg_value}}</td>
                <td>{{$targetData->total_value}}</td>
                <td>

                    <input type="button" name="product_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="targetEdit('{{ $targetData->id }}')" style="width: 70px;">
                    <input type="button" name="point_delete" id="delete" value="Delete" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="targetDelete('{{ $targetData->id}}')" style="width: 70px; margin-top: 0px;">
                </td>
            </tr>


            @endforeach
            @else
            <tr>
                <th colspan="10">No record found.</th>
            </tr>
            @endif     

        </tbody>
        <tbody>
            <tfoot>
                <tr>
                    <th colspan="9">Total</th>
                    <th>{{ number_format($qty) }}</th>
                    <th>{{ number_format($aveValue) }}</th>
                    <th>{{ number_format($totalValue) }}</th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>

            </table>
        </div>
    </div>
</div>
