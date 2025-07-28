<!-- Bootstrap Select Css -->
<link href="{{URL::asset('resources/sales/plugins/bootstrap-select/css/bootstrap-select.css')}}" rel="stylesheet" />

@if($serial==1)
	<select id="products" class="form-control show-tick" data-live-search="true">
        <option value="">------ Product ------</option>
        @foreach($resultProduct as $products)
        	<option value="{{ $products->pid }}">{{ $products->pname }}</option>
        @endforeach  
    </select>
@endif