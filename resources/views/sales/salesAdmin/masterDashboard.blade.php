@extends('sales.masterPage')

@section('content')

<section class="content">
    <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Active Users</div>
                            <div class="number">{{$activeUsers}}</div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Inactive Users</div>
                            <div class="number">{{$inactiveUsers}}</div>
                        </div>
                    </div>
                </div> --}}

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Products</div>
                            <div class="number">{{$activeProducts}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Active Offers</div>
                            <div class="number">{{$activeOffers}}</div>
                        </div>
                    </div>
                </div>
            </div>            
    </div>
</section>

@endsection