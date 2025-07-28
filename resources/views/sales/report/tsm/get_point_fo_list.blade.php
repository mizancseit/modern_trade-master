<select  class="form-control show-tick" name="foID" id="foID">
	<option value="">Select FO</option>
	@foreach($point_fo_list as $fo)
	<option value="{{$fo->id}}">{{$fo->email}} - {{$fo->display_name}}</option>
	@endforeach
</select>


