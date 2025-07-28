<select id="category1" name="category1" class="form-control" data-live-search="true">
    <option value="">-- Select Category --</option> 
     @foreach($cat as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
    @endforeach  
</select>


