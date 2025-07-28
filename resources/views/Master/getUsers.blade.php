<div class="form-line" style="height: 195px; overflow: scroll; overflow-x: hidden;">
@foreach($point as $point)
<input type="checkbox" id="point{{$point->point_id}}" name="point[]"
               value="{{$point->point_id}}" />
                <label for="point{{$point->point_id}}">{{$point->point_name}}</label></br>
@endforeach
</div>


