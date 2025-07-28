<select  class="form-control show-tick" name="invoice_no" >
<option>Select Invoice</option>
@foreach($RetInvoice as $RowInvoice)
<option id="ok" value="{{$RowInvoice->order_no}}">{{$RowInvoice->order_no}}</option>
@endforeach
</select>



