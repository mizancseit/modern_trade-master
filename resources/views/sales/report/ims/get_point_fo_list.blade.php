<select  class="form-control show-tick" name="foID" id="foID">
<option value="">SELECT FO</option>
@foreach($point_fo_list as $fo)
<option id="ok" value="{{$fo->id}}">{{$fo->email}} - {{$fo->display_name}}</option>
@endforeach
</select>


