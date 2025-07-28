<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header">
                <h5>
                    PRODUCT STOCK 
                    <div class="col-sm-12" align="right">
                        <form action="{{url('/dist/stock_export')}}" enctype="multipart/form-data">
                            <input type="submit" name="download" value="DOWNLOAD STOCK" class="btn bg-red btn-block btn-sm waves-effect" style="width: 180px;">
                            <input type="hidden" name="cat_id" value="{{ $catID }}">
                        </form>
                    </div>	
                </h5>
            </div>
            <div class="body">
                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product Name</th>
                            <th>Stock Qty</th>                                      
                            <th>Stock Value</th>                                      
                        </tr>
                    </thead>

                    <tbody>
                        @if(sizeof($stockResult) > 0)   
                        @php
                        $i =1;
                        @endphp
                        @foreach($stockResult as $stockProducts) 

                        <tr>
                            <td>{{$i++ }}</td>

                            <td>{{$stockProducts->name }}</td>
                            <td>{{$stockProducts->stock_qty }}</td>
                            <td>{{$stockProducts->stock_value }}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <th colspan="4">No record found.</th>
                        </tr>
                        @endif     

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>SL</th>
                            <th>Product Name</th>
                            <th>Stock Qty</th>
                            <th>Stock Value</th> 
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>