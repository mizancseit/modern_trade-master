@extends('sales.masterPage')
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <h2>DASHBOARD</h2>
        </div>

        <div class="row clearfix">
            <div class="col-lg-9 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box-4 hover-expand-effect">
                    
                    <div class="content">
                        <div class="text">My Territory</div>
                        <?php $terri=DB::select("select * from  tbl_territory where id=$user->territory_id")?>
                        <div class="number">@if(!empty($terri)) @foreach($terri as $id){{$id->name}}@endforeach @endif</div>
                    </div>
                </div>
            </div>
        </div> 

        <div class="row clearfix">            
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box-4 hover-expand-effect">                    
                    <div class="content">
                        <div class="number" style="padding-top: 20px;">Territory Status </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box-4 hover-expand-effect">                    
                    <div class="content">
                        <div class="number" style="padding-top: 20px;">Performance</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box-4">                    
                    <div class="content">
                        <div class="number" style="padding-top: 20px;"><a href="{{ URL('/report/tsm/retailer') }}"> Retailer List </a></div>
                    </div>
                </div>
            </div>

        </div> 

    </div>
</section>

@endsection