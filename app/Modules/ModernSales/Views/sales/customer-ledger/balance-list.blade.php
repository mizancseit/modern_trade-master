 <div class="card">
     <div class="body">
         <div class="table-responsive">
             <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                 <thead>
                     <tr>
                         <th>SL</th>
                         <th>Customer Name</th>
                         <th>Upload Date</th>
                         <th>Balance</th>
                     </tr>
                 </thead>

                 <tbody>
                     @php
                         $i = 1;
                     @endphp
                     @if (sizeof($balance_list) > 0)
                         @foreach ($balance_list as $balance)
                             <tr>
                                 <td>{{ $i++ }}</td>
                                 <td>{{ $balance->name }}</td>
                                 <td>{{ date('d-m-Y', strtotime($balance->submitted_date)) }}</td>
                                 <td>{{ $balance->last_balance }}</td>
                             </tr>
                         @endforeach
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
