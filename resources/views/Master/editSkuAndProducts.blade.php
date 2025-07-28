<select  class="form-control show-tick" id="andProducts" name="andProducts1" >
<option value="">Select Product</option>
@foreach($product as $product)
<option id="ok" value="{{$product->id}}">{{$product->name}}</option>
@endforeach
</select>


