<div id="showHiddenDiv"> 
        <div class="card">
            <div class="header">
                <h5>
                    About {{ sizeof($allDepot) }} results 
                </h5>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover js-basic-example dataTable">
                        <thead>
                            <tr style="font-size: 11px; font-weight: normal;">
                                <th colspan="31" style="text-align: center; font-size: 15px;"> 
                                @if($month=='01')
                                    January
                                @elseif($month=='01')
                                    February
                                @elseif($month=='02')
                                    February
                                @elseif($month=='03')
                                    March
                                @elseif($month=='04')
                                    April
                                @elseif($month=='05')
                                    May
                                @elseif($month=='06')
                                    June
                                @elseif($month=='07')
                                    July
                                @elseif($month=='08')
                                    August
                                @elseif($month=='09')
                                    September
                                @elseif($month=='10')
                                    October
                                @elseif($month=='11')
                                    November
                                @elseif($month=='12')
                                    December
                                @endif

                                {{ $year }}</th>                            
                            </tr>

                            <tr style="font-size: 11px;">
                                <th>SL</th>
                                <th>Depot</th>
                                <th>Retailer</th>
                                <th>01</th>
                                <th>02</th>
                                <th>03</th>
                                <th>04</th>
                                <th>05</th>
                                <th>06</th>
                                <th>07</th>
                                <th>08</th>
                                <th>09</th>
                                <th>10</th>
                                <th>11</th>
                                <th>12</th>
                                <th>13</th>
                                <th>14</th>
                                <th>15</th>
                                <th>16</th>
                                <th>17</th>
                                <th>18</th>
                                <th>19</th>
                                <th>20</th>
                                <th>21</th>
                                <th>22</th>
                                <th>23</th>
                                <th>24</th>
                                <th>25</th>
                                <th>26</th>
                                <th>27</th>
                                <th>28</th>
                                <th>29</th>
                                <th>30</th>
                                <th>31</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if(sizeof($allDepot)>0)
                                @php
                                $serial = 1;
                                @endphp
                                @foreach($allDepot as $points)
                                <tr style="font-size: 11px;">
                                    <th> {{ $serial }}</th>
                                    <th> @if($serial==1) {{ $points->point_name }} @endif</th>
                                        <th> {{ $points->name }} </th>
                                        @php 
                                        $count  = date('Y-m'.'-01');
                                        $count2 = date('Y-m'.'-31');

                                            for($i=01;$i<=31;$i++)
                                            {
                                                if($i>=1 && $i<=9)
                                                {
                                                    $m = '0'.$i;   
                                                }
                                                else
                                                {
                                                    $m = $i;
                                                }
                                        @endphp
                                        <th>
                                            @php
                                        $day =  $year.'-'.$month.'-'.$m;
                                        $visitCount = DB::table('ims_tbl_visit_order')->select('retailerid','entrydate')
                                                    ->where('retailerid', $points->retailer_id)
                                                    ->whereBetween(DB::raw("(DATE_FORMAT(entrydate,'%Y-%m-%d'))"), array($day, $day))
                                                    ->count();

                                        echo $visitCount;                                            
                                        @endphp
                                        </th>
                                        @php                                        
                                            }
                                        @endphp
                                </tr>
                                @php
                                $serial ++;
                                @endphp
                                @endforeach
                            @endif

                        </tbody>                                
                        
                    </table>
                </div>
            </div>
        </div>
    </div>