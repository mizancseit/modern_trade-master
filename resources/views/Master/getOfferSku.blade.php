<select  class="form-control show-tick" name="sku" >
<option value="">Select Product</option>
@foreach($product as $product)
<option id="ok" value="{{$product->id}}">{{$product->name}}</option>
@endforeach
</select>


