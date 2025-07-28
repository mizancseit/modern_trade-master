 <select  class=" form-control show-tick" name="name" required="" >
<option>Select Territory</option>
@foreach($terri as $terrione)
<option id="ok" value="{{$terrione->name}}">{{$terrione->name}}</option>
@endforeach
</select>

