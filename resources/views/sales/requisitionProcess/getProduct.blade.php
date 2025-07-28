<select  class="form-control show-tick" name="change_product_id[]" onchange="getChangeProductPrice(this.value,{{$serial}})" required="" >
<option>Select Product</option>
@foreach($product as $product)
<option id="ok" value="{{$product->id}}">{{$product->name}}</option>
@endforeach
</select>

<input type="hidden" id="change_prod_price{{$serial}}" name="change_prod_price[]" value="0">


