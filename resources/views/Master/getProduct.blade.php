<select  class="form-control show-tick" id="productList" name="product">
<option value="">Select Product</option>
@foreach($product as $product)
<option id="ok" value="{{$product->id}}">{{$product->name}}</option>
@endforeach
</select>


