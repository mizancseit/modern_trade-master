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
                            <div class="text">Total Division</div>
                            <div class="number">{{$activeDivision}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Territory</div>
                            <div class="number">{{$activeTerritory}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Point</div>
                            <div class="number">{{$activePoint}}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Route</div>
                            <div class="number">{{$activeRoute}}</div>
                        </div>
                    </div>
                </div>

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
                            <div class="text">Total Target</div>
                            <div class="number">80,0000</div>
                        </div>
                    </div>
                </div>

                
              <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text">Total Achievement</div>
                            <div class="number">25,0000</div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text"> TOP PERFORMER</div>
                            <div class="number">Mamun:10,0000</div>
                        </div>
                    </div>
                </div> -->

                <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-16">
                    <div class="info-box-4 hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons col-teal">playlist_add_check</i>
                        </div>
                        <div class="content">
                            <div class="text"> IMPROVEMENT REQUIRED FO LIST</div>
                            <div class="number">Rafiq,Sharif,Mamun</div>
                            </div>
                    </div>
                </div> -->




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

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="card">
                        <div class="header">
                            <h2>TOP 10 PERFORMER</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-hover dashboard-task-infos">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Rafiq</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Sharif</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Mamun</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Masud</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Salam</td>
                                        </tr>
                                        
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="card">
                        <div class="header">
                            <h2>TOP 10 LOW PERFORMER</h2>
                            <!-- <ul class="header-dropdown m-r--5">
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Action</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Another action</a></li>
                                        <li><a href="javascript:void(0);" class=" waves-effect waves-block">Something else here</a></li>
                                    </ul>
                                </li>
                            </ul> -->
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-hover dashboard-task-infos">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Rafiq</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Sharif</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Mamun</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Masud</td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
</section>

@endsection