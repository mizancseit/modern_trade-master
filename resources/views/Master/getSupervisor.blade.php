<select  class="form-control show-tick" name="supervisor">
<option>Select Name</option>
@foreach($users as $users)
<option id="ok" value="{{$users->id}}">{{$users->display_name}}</option>
@endforeach
</select>


