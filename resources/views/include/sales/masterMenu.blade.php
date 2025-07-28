{{-- Master Menu  --}} 

<div class="menu">
    <ul class="list">
    {{-- <li class="header">MAIN NAVIGATION</li> --}}
        @php 
            $selectedSubMenu='';
        @endphp

        <li @if($selectedMenu=='Home') class="active" @endif>
            <a href="{{ URL('/dashboard') }}">
                <i class="material-icons">dashboard</i>
                <span>Dashboard</span>
            </a>
        </li>
        
        @if(session('userType')=='Super Admin')
            
			@if ( Auth::user()->id != '1032') {
				
				
			<li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Master Data Setup</span>
                </a>

                <ul class="ml-menu">
                      <!--Maung Master Menu-->
                
				<li @if($selectedMenu=='globalcompany') class="active" @endif>
					<a href="{{ URL('/globalCompany') }}">
						<i class="material-icons">view_list</i>
						<span>Global Company</span>
					</a>    
                </li>
		
                <li @if($selectedMenu=='division') class="active" @endif>
                    <a href="{{ URL('/division_list') }}">
                        <i class="material-icons">view_list</i>
                        <span>Division</span>
                    </a>    
                </li>

                <li @if($selectedMenu=='territory') class="active" @endif>
                <a href="{{ URL('/newTerritory') }}">
                    <i class="material-icons">view_list</i>
                    <span>Territory</span>
                </a>    
                </li> 

                <li @if($selectedMenu=='point') class="active" @endif>
                <a href="{{ URL('/newPoint') }}">
                    <i class="material-icons">view_list</i>
                    <span>Point</span>
                </a>    
                </li> 
                
                <li @if($selectedMenu=='route') class="active" @endif>
                <a href="{{ URL('/newRoute') }}">
                    <i class="material-icons">view_list</i>
                    <span>Route</span>
                </a>    
                </li> 
                  <li @if($selectedMenu=='distributor') class="active" @endif>
                <a href="{{ URL('/newDistributor') }}">
                    <i class="material-icons">view_list</i>
                    <span>Distributor/Dealer</span>
                </a>    
                </li> 
                 <li @if($selectedMenu=='retailer_list') class="active" @endif>
                    <a href="{{ URL('/retailer_list') }}">
                        <i class="material-icons">view_list</i>
                        <span>Retailer</span>
                    </a>    
                </li>                

                <li @if($selectedMenu=='usermgt') class="active" @endif>
                <a href="{{ URL('/userCreate') }}">
                    <i class="material-icons">view_list</i>
                    <span>User Management</span>
                </a>    
                </li>

                <li @if($selectedMenu=='Supervisor') class="active" @endif>
                <a href="{{ URL('/userSupervisor') }}">
                    <i class="material-icons">view_list</i>
                    <span>Define Supervisor</span>
                </a>    
                </li>  
                <!-- User Mgt Ends-->
                <li @if($selectedMenu=='rejectreason') class="active" @endif>
                    <a href="{{ URL('/reject_reason_list') }}">
                        <i class="material-icons">view_list</i>
                        <span>Reject Reason</span>
                    </a>    
                </li>

                <li @if($selectedMenu=='Targer file upload') class="active" @endif>
                    <a href="{{ URL('/fo_target_upload') }}">
                        <i class="material-icons">view_list</i>
                        <span>Target Upload</span>
                    </a>    
                </li>

                <!--<li @if($selectedMenu=='fo_list') class="active" @endif>
                    <a href="{{ URL('/fo_list') }}">
                        <i class="material-icons">view_list</i>
                        <span>FO List</span>
                    </a>    
                </li>-->
                
               
                </ul>
            </li>

            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Master Product Setup</span>
                </a>
                <ul class="ml-menu">
                    
					<li @if($selectedMenu=='company') class="active" @endif>
						<a href="{{ URL('/company') }}">
							<i class="material-icons">view_list</i>
							<span>Company</span>
						</a>    
					</li>
				
					<li @if($selectedMenu=='category') class="active" @endif>
                        <a href="{{ URL('/productCategory') }}">
                            <i class="material-icons">library_books</i>
                            <span>Category</span>
                        </a>    
                    </li> 

                    <li @if($selectedMenu=='newproduct') class="active" @endif>
                        <a href="{{ URL('/productSetup') }}">
                            <i class="material-icons">library_books</i>
                            <span>Product</span>
                        </a>    
                    </li>

                      <li @if($selectedMenu=='FO Top Ten Report') class="active" @endif>
                <a href="{{ URL('/report/fo/topten') }}">
                    <i class="material-icons">library_books</i>
                    <span>FO Top 10 Report</span>
                </a>            
            </li>
                </ul>
            </li>
			
			
			<li>
			 
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition Management</span>
                </a>
				
                <ul class="ml-menu">
				
					<li @if($selectedMenu=='Requisition Pending') class="active" @endif>
                        <a href="{{ URL('/reqPendingList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Pending Requisition</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Requisition Approved') class="active" @endif>
                        <a href="{{ URL('/reqAllApprovedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Approved Requisition</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Requisition Delivered') class="active" @endif>
                        <a href="{{ URL('/reqAllDeliveredList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Delivered List</span>
                        </a>            
                    </li>
					
					<li @if($selectedMenu=='Requisition Received') class="active" @endif>
                        <a href="{{ URL('/reqAllReceivedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Received List</span>
                        </a>            
                    </li>
					
					<li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
                        <a href="{{ URL('/reqAllCanceledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Canceled Requisition</span>
                        </a>    
                    </li>
					
				</ul>
				
			</li>
			
			
			<li>
			 
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Depot Manage</span>
                </a>
				
                <ul class="ml-menu">
				
					<li @if($selectedSubMenu=='Depot Setup') class="active" @endif>
                        <a href="{{ URL('/depot/depot_list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Depot List</span>
                        </a>    
                    </li>
					
                    <li @if($selectedMenu=='Depot Inventory List') class="active" @endif>
                        <a href="{{ URL('/depot') }}">
                            <i class="material-icons">view_list</i>
                            <span>Inventory</span>
                        </a>            
                    </li>
                    
					<li @if($selectedMenu=='Depot') class="active" @endif>
						<a href="{{ URL('/newDepotPayment') }}">
							<i class="material-icons">view_list</i>
							<span>Depot Payments</span>
						</a>    
					</li>
					
					<li @if($selectedMenu=='Depot Transaction') class="active" @endif>
						<a href="{{ URL('/DepotTransHistory') }}">
							<i class="material-icons">view_list</i>
							<span>Depot Transaction History</span>
						</a>    
					</li>
					
					<li @if($selectedMenu=='Depot Collection') class="active" @endif>
						<a href="{{ URL('/DepotCollection') }}">
							<i class="material-icons">view_list</i>
							<span>Depot Collection</span>
						</a>    
					</li>
					
					
					
                </ul>
            </li>
			
			@endif

            <!--Masud Master Offer setup-->
            <li @if($selectedMenu=='Master Offer') class="active" @endif>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Master Offer Setup</span>
                </a>

                <ul class="ml-menu">
                    <li @if($selectedMenu=='Bundle Offer') class="active" @endif>
                        <a href="{{ URL('/offers/bundle') }}">
                            <i class="material-icons">library_books</i>
                            <span>Offer Manage</span>
                        </a>            
                    </li>
                    <li @if($selectedMenu=='Bundle Offer Product') class="active" @endif>
                        <a href="{{ URL('/offers/bundle-product') }}">
                            <i class="material-icons">library_books</i>
                            <span>Bundle Product</span>
                        </a>            
                    </li>

                    <li @if($selectedSubMenu=='Regular Offer') class="active" @endif>
                        <a href="{{ URL('/offer/regular_offer_products') }}">
                            <i class="material-icons">library_books</i>
                            <span>Regular Category Wise</span>
                        </a>            
                    </li>
                    <li @if($selectedSubMenu=='Regular sku') class="active" @endif>
                        <a href="{{ URL('/offer/regular_sku_products') }}">
                            <i class="material-icons">library_books</i>
                            <span>Regular SKU Wise</span>
                        </a>            
                    </li>
                    
                    <li @if($selectedSubMenu=='Special Offer') class="active" @endif>
                        <a href="{{ URL('/offer/special_offer_products') }}">
                            <i class="material-icons">library_books</i>
                            <span>Exclusive Category Wise</span>
                        </a>            
                    </li>
                    <li @if($selectedSubMenu=='Special sku') class="active" @endif>
                        <a href="{{ URL('/offer/special_sku_products') }}">
                            <i class="material-icons">library_books</i>
                            <span>Exclusive SKU Wise</span>
                        </a>            
                    </li>
                    <li @if($selectedSubMenu=='Others') class="active" @endif>
                        <a href="{{ URL('/offer/other-products') }}">
                            <i class="material-icons">library_books</i>
                            <span>Exclusive Value Wise</span>
                        </a>            
                    </li>
                    {{-- <li @if($selectedSubMenu=='Offer Setup') class="active" @endif>
                        <a href="{{ URL('/offer/regular_special_offer_setup') }}">
                            <i class="material-icons">library_books</i>
                            <span>Master Offer Slab Setup</span>
                        </a>            
                    </li> --}}

                </ul>
            </li>

			
		@if ( Auth::user()->id != '1032') {	
            <li @if($selectedMenu=='Request Manage') class="active" @endif>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span> Retailer Request Manage</span>
                </a>

                <ul class="ml-menu">
                    <li @if($selectedMenu=='New Retailer') class="active" @endif>
                        <a href="{{ URL('/admin/new-retailer') }}">
                            <i class="material-icons">library_books</i>
                            <span>New Retailer</span>
                        </a>            
                    </li>
                    <li @if($selectedMenu=='Activision') class="active" @endif>
                        <a href="{{ URL('/admin/activation') }}">
                            <i class="material-icons">library_books</i>
                            <span> Activision /Inactivation Retailer </span>
                        </a>            
                    </li>
                </ul>
            </li>
			
		@endif;	

            <li @if($selectedMenu=='Commission Manage') class="active" @endif>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span> Commission Manage </span>
                </a>

                <ul class="ml-menu">
                    <li>
                        <a href="{{ URL('/admin/commission') }}">
                            <i class="material-icons">library_books</i>
                            <span>Commission</span>
                        </a>            
                    </li>
                    <li>
                        <a href="{{ URL('/admin/except-commission') }}">
                            <i class="material-icons">library_books</i>
                            <span> Except Category Commission </span>
                        </a>            
                    </li>
                </ul>
            </li>

    
            <!-- Master menu ends-->

            <!-- Maung Slaes Coordinator 250218 -->
        
        
        @elseif(session('userType')=='Sales Coordinator')
            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Master Data Set up</span>
                </a>

                <ul class="ml-menu">
                      <!--Maung Master Menu-->
                    <li @if($selectedMenu=='territory') class="active" @endif>
                    <a href="{{ URL('/newTerritory') }}">
                        <i class="material-icons">view_list</i>
                        <span>Territory Setup</span>
                    </a>    
                    </li> 
                    <li @if($selectedMenu=='point') class="active" @endif>
                    <a href="{{ URL('/newPoint') }}">
                        <i class="material-icons">view_list</i>
                        <span>Point Setup</span>
                    </a>    
                    </li> 
                     <li @if($selectedMenu=='territory') class="active" @endif>
                   
                    <li @if($selectedMenu=='route') class="active" @endif>
                    <a href="{{ URL('/newRoute') }}">
                        <i class="material-icons">view_list</i>
                        <span>Route Setup</span>
                    </a>    
                    </li> 
                      <li @if($selectedMenu=='distributor') class="active" @endif>
                    <a href="{{ URL('/newDistributor') }}">
                        <i class="material-icons">view_list</i>
                        <span>Distributor/Dealer Setup</span>
                    </a>    
                    </li>

                   <!-- <li @if($selectedMenu=='fo_list') class="active" @endif>
                        <a href="{{ URL('/fo_list') }}">
                            <i class="material-icons">view_list</i>
                            <span>FO Set Up</span>
                        </a>    
                    </li>-->
                    
                    <li @if($selectedMenu=='retailer_list') class="active" @endif>
                        <a href="{{ URL('/retailer_list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Retailer List</span>
                        </a>    
                    </li>

                     <li @if($selectedMenu=='usermgt') class="active" @endif>
                    <a href="{{ URL('/userCreate') }}">
                        <i class="material-icons">view_list</i>
                        <span>User Set Up & Management</span>
                    </a>    
                    </li>
                </ul>
            </li>
        @elseif(session('userType')=='Sales Admin')

            {{-- <li @if($selectedMenu=='Report') class="active" @endif>
                <a href="{{ URL('/ims/report') }}">
                    <i class="material-icons">view_list</i>
                    <span>IMS Report</span>
                </a>
            </li> --}}
            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Master Data Set up</span>
                </a>

                <ul class="ml-menu">
                      <!--Maung Master Menu-->
                    <li @if($selectedMenu=='territory') class="active" @endif>
                    <a href="{{ URL('/newTerritory') }}">
                        <i class="material-icons">view_list</i>
                        <span>Territory Setup</span>
                    </a>    
                    </li> 
                    <li @if($selectedMenu=='point') class="active" @endif>
                    <a href="{{ URL('/newPoint') }}">
                        <i class="material-icons">view_list</i>
                        <span>Point Setup</span>
                    </a>    
                    </li> 
                     <li @if($selectedMenu=='territory') class="active" @endif>
                   
                    <li @if($selectedMenu=='route') class="active" @endif>
                    <a href="{{ URL('/newRoute') }}">
                        <i class="material-icons">view_list</i>
                        <span>Route Setup</span>
                    </a>    
                    </li> 
                      <li @if($selectedMenu=='distributor') class="active" @endif>
                    <a href="{{ URL('/newDistributor') }}">
                        <i class="material-icons">view_list</i>
                        <span>Distributor/Dealer Setup</span>
                    </a>    
                    </li>
                    
                    <li @if($selectedMenu=='retailer_list') class="active" @endif>
                        <a href="{{ URL('/retailer_list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Retailer List</span>
                        </a>    
                    </li>

                     <li @if($selectedMenu=='usermgt') class="active" @endif>
                    <a href="{{ URL('/userCreate') }}">
                        <i class="material-icons">view_list</i>
                        <span>User Set Up & Management</span>
                    </a>    
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Report</span>
                </a>

                <ul class="ml-menu">

                    <li @if($selectedMenu=='Order Report') class="active" @endif>
                        <a href="{{ URL('/sa/depot-operation-report') }}">
                            <i class="material-icons">library_books</i>
                            <span>Depot Operation Report</span>
                        </a>            
                    </li>
					
					
					<li @if($selectedMenu=='Retailer Sales History') class="active" @endif>
                        <a href="{{ URL('/PartySalesHistory') }}">
                            <i class="material-icons">library_books</i>
                            <span>Party Sales History</span>
                        </a>            
					</li>
					
                    <li @if($selectedMenu=='FO Performance Report') class="active" @endif>
                        <a href="{{ URL('/sc/fo-performance-report') }}">
                            <i class="material-icons">library_books</i>
                            <span>FO Performance</span>
                        </a>            
                    </li>
					
                    <li @if($selectedMenu=='DB Performance Report') class="active" @endif>
                        <a href="{{ URL('/sc/db-performance-report') }}">
                            <i class="material-icons">library_books</i>
                            <span>DB/DEPOT Performance</span>
                        </a>            
                    </li>
					
					<li @if($selectedMenu=='Visit Frequency Report') class="active" @endif>
                        <a href="{{ URL('/sa/visit-frequency-report') }}">
                            <i class="material-icons">library_books</i>
                            <span>Visit Frequency Report</span>
                        </a>            
                    </li>
                    
                    <li @if($selectedMenu=='Order Report') class="active" @endif>
                        <a href="{{ URL('/sc/order-report') }}">
                            <i class="material-icons">library_books</i>
                            <span>Order Report</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='FO Order Summery') class="active" @endif>
                        <a href="{{ URL('/sc/fo-order-summery') }}">
                            <i class="material-icons">library_books</i>
                            <span>FO Order Summery</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='Date Wise FO Summery') class="active" @endif>
                        <a href="{{ URL('/sc/date-wise-fo-summery') }}">
                            <i class="material-icons">library_books</i>
                            <span>Date Wise FO Summery</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='Order Delivery') class="active" @endif>
                        <a href="{{ URL('/sc/order-summery') }}">
                            <i class="material-icons">library_books</i>
                            <span>Order Delivery</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='Order Vs Delivery') class="active" @endif>
                        <a href="{{ URL('/sc/order-vs-delivery') }}">
                            <i class="material-icons">library_books</i>
                            <span>Order Vs Delivery</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='Delivery Report') class="active" @endif>
                        <a href="{{ URL('/sc/delivery-report') }}">
                            <i class="material-icons">library_books</i>
                            <span>Delivery Report</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='SKU Wise Delivery') class="active" @endif>
                        <a href="{{ URL('/sc/sku-wise-delivery') }}">
                            <i class="material-icons">library_books</i>
                            <span>SKU Wise Delivery</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='SKU Wise Delivery') class="active" @endif>
                        <a href="{{ URL('/sc/sku-wise-delivery') }}">
                            <i class="material-icons">library_books</i>
                            <span>FO Report</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='IMS Summery') class="active" @endif>
                        <a href="{{ URL('/sc/ims-summery') }}">
                            <i class="material-icons">library_books</i>
                            <span>IMS Summery</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='FO Attendance') class="active" @endif>
                        <a href="{{ URL('/report/tsm/fo-attendance') }}">
                            <i class="material-icons">alarm</i>
                            <span>FO Attendance</span>
                        </a>            
                    </li>

                    <li @if($selectedMenu=='FO Target Vs Achivement') class="active" @endif>
                        <a href="{{ URL('/sc/fo-target-vs-achivement') }}">
                            <i class="material-icons">library_books</i>
                            <span>FO Target Vs Achivement</span>
                        </a>            
                    </li>
                </ul>
            </li>
        @elseif(session('userType')=='Management')        

             <li @if($selectedMenu=='Management Report') class="active" @endif>
                <a href="{{ URL('/report/management') }}">
                    <i class="material-icons">library_books</i>
                    <span>Management Report</span>
                </a>            
            </li>
             <!-- <li @if($selectedMenu=='Management FO Report') class="active" @endif>
                <a href="{{ URL('/report/managementFO') }}">
                    <i class="material-icons">library_books</i>
                    <span>FO Order Report</span>
                </a>            
            </li> -->
			
		 @elseif(session('userType')=='BILLING Dept')	
		  
		  <li>
			 
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition Management</span>
                </a>
				
                <ul class="ml-menu">
				
					<li @if($selectedMenu=='Requisition Pending') class="active" @endif>
                        <a href="{{ URL('/reqPendingList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Acknowledge Requisition</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Requisition Analysis') class="active" @endif>
                        <a href="{{ URL('/reqAllAnalysisList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Analysis Requisition</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Requisition Approved') class="active" @endif>
                        <a href="{{ URL('/reqAllApprovedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Open Order</span>
                        </a>    
                    </li>
					
					
					 <li @if($selectedMenu=='Requisition All Delivered') class="active" @endif>
                        <a href="{{ URL('/reqAllBilledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Billed List</span>
                        </a>    
                    </li>
					
					
					
					<li @if($selectedMenu=='Requisition Active') class="active" @endif>
                        <a href="{{ URL('/reqAllActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Order IN-Active</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Requisition IN Active') class="active" @endif>
                        <a href="{{ URL('/reqAllInActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Order Active</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Customar Active') class="active" @endif>
                        <a href="{{ URL('/custAllActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customar In-Active</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Customar IN Active') class="active" @endif>
                        <a href="{{ URL('/custAllInActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customar Active</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
                        <a href="{{ URL('/reqAllCanceledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Canceled Requisition</span>
                        </a>    
                    </li>
					
				</ul>
				
		  </li>
		  
    		<li>
    		 
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Pending Order Management</span>
                </a>
    			
                <ul class="ml-menu">
    			
    				<li @if($selectedMenu=='Pending Order') class="active" @endif>
                        <a href="{{ URL('/PendingOrderList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Pending Order</span>
                        </a>    
                    </li>
    				
    				<li @if($selectedMenu=='Pending Summary') class="active" @endif>
                        <a href="{{ URL('/PendingOrderSummary') }}">
                            <i class="material-icons">view_list</i>
                            <span>Pending Order Summary</span>
                        </a>    
    				</li>
    				
    			</ul>
    			
    		</li>
		  
		    <li>
             
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Wastage Requisition</span>
                </a>
                
                <ul class="ml-menu">
                
                    <li @if($selectedMenu=='Wastage Requisition Pending') class="active" @endif>
                        <a href="{{ URL('/dist/was-req-pending-list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Acknowledge Requisition</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Wastage Requisition Analysis') class="active" @endif>
                        <a href="{{ URL('/dist/was-req-analysis-list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Analysis Requisition</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Wastage Requisition Approved') class="active" @endif>
                        <a href="{{ URL('/dist/was-req-all-approved-list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Open Order</span>
                        </a>    
                    </li>
                    
                    {{-- <li @if($selectedMenu=='Requisition All Delivered') class="active" @endif>
                        <a href="{{ URL('/reqAllDeliveredList') }}">
                            <i class="material-icons">view_list</i>
                            <span>In-Transit List</span>
                        </a>    
                    </li>
                    
                    
                    <li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
                        <a href="{{ URL('/reqAllCanceledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Canceled Requisition</span>
                        </a>    
                    </li> --}}
                    
                </ul>
                
            </li>

            <li>
             
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Free Requisition</span>
                </a>
                
                <ul class="ml-menu">
                
                    <li @if($selectedMenu=='Free Requisition Pending') class="active" @endif>
                        <a href="{{ URL('/dist/free-req-pending-list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Acknowledge Requisition</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Free Requisition Analysis') class="active" @endif>
                        <a href="{{ URL('/dist/free-req-analysis-list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Analysis Requisition</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Free Requisition Approved') class="active" @endif>
                        <a href="{{ URL('/dist/free-req-all-approved-list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Open Order</span>
                        </a>    
                    </li>
                    
                    {{-- <li @if($selectedMenu=='Requisition All Delivered') class="active" @endif>
                        <a href="{{ URL('/reqAllDeliveredList') }}">
                            <i class="material-icons">view_list</i>
                            <span>In-Transit List</span>
                        </a>    
                    </li>
                    
                    
                    <li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
                        <a href="{{ URL('/reqAllCanceledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Canceled Requisition</span>
                        </a>    
                    </li> --}}
                    
                </ul>
                
            </li>	

          <!-- Masud Distributor requisition  -->
            
            <!--Masud distributor requisition ends-->

            <!-- Masud  Distributor Payment-->
           

			 <li>
             
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Payment Acknowledgement</span>
                </a>
                
                <ul class="ml-menu">
                
                    <li @if($selectedMenu=='Depot Payment Acknowledgement') class="active" @endif>
                        <a href="{{ URL('/depotPaymentList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Payment List</span>
                        </a>    
                    </li>
                     <li @if($selectedMenu=='Depot Acknowledgement List') class="active" @endif>
                        <a href="{{ URL('/depotAckList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Payment ACK List</span>
                        </a>    
                    </li>                    
                </ul>                
            </li>
			

            <!-- Masud  Distributor Payment-->
			
			<li>
			 
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Stock Manage</span>
                </a>
				
                <ul class="ml-menu">
					
					<li @if($selectedMenu=='Depo Req') class="active" @endif>
						<a href="{{ URL('/ims/depo-req') }}">
							<i class="material-icons">view_list</i>
							<span>Depot/Distributor Requisition Report</span>
						</a>
					</li>
				
					<li @if($selectedMenu=='Depot Inventory List') class="active" @endif>
                        <a href="{{ URL('/depot') }}">
                            <i class="material-icons">view_list</i>
                            <span>Depot/Distributor Delivery</span>
                        </a>            
                    </li>
				
						
					<li @if($selectedMenu=='Upload Balance') class="active" @endif>
                        <a href="{{ URL('depot/cust_balance_list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Upload Cust Balance</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Upload Stock') class="active" @endif>
                        <a href="{{ URL('depot/stock_list') }}">
                            <i class="material-icons">view_list</i>
                            <span>Upload SSG Stock</span>
                        </a>    
                    </li>
					
				</ul>
				
			</li>
			
			
		@elseif(session('userType')=='Delivery Dept')	
		 
		 <li>
			 
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Delivery Management</span>
                </a>
				
                <ul class="ml-menu">
				
					
					
					<li @if($selectedMenu=='Ready For Delivery') class="active" @endif>
                        <a href="{{ URL('/reqReadyForDelivery') }}">
                            <i class="material-icons">view_list</i>
                            <span>Ready To Deliver</span>
                        </a>    
                    </li>
					
					<li @if($selectedMenu=='Requisition All Delivered') class="active" @endif>
                        <a href="{{ URL('/reqAllDeliveredList') }}">
                            <i class="material-icons">view_list</i>
                            <span>In-Transit</span>
                        </a>            
                    </li>
					
					<li @if($selectedMenu=='Requisition Received') class="active" @endif>
                        <a href="{{ URL('/reqAllReceivedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Received List</span>
                        </a>            
                    </li>
					
					
					
				</ul>
				
			</li>		

        @elseif(session('userType')=='Distributor')

             @if(session('exceptionPoint')=='') {{-- Exception Null Hole (Nationality) --}}

            <li @if($selectedMenu=='Order') class="active" @endif>
                <a href="{{ URL('/order') }}">
                    <i class="material-icons">view_list</i>
                    <span>Order</span>
                </a>            
            </li>

          @else

            <li @if($selectedMenu=='Order') class="active" @endif>
                <a href="{{ URL('/order-exceptional') }}">
                    <i class="material-icons">view_list</i>
                    <span>Order</span>
                </a>            
            </li>
         @endif
            <li @if($selectedMenu=='Wastage') class="active" @endif>
            <a href="{{ URL('/wastage-delivery') }}">
            <i class="material-icons">view_list</i>
            <span>Wastage</span>
            </a>            
            </li>		


            <li>

            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Requisition</span>
            </a>

            <ul class="ml-menu">
			
			@if(Auth::user()->is_active !=2)
            <li @if($selectedMenu=='Requisition Manage') class="active" @endif>
            <a href="{{ URL('/req-manage') }}">
            <i class="material-icons">view_list</i>
            <span>New Requisition</span>
            </a>            
            </li>
			@endif

            <li @if($selectedMenu=='Requisition Send') class="active" @endif>
            <a href="{{ URL('/req-send_list') }}">
            <i class="material-icons">view_list</i>
            <span>Sent List</span>
            </a>            
            </li>

           <!-- 
            <li @if($selectedMenu=='Requisition Acknowledge') class="active" @endif>
            <a href="{{ URL('/reqAcknowledgeList') }}">
            <i class="material-icons">view_list</i>
            <span>Acknowledge List</span>
            </a>            
            </li> -->

            <li @if($selectedMenu=='Requisition Approved') class="active" @endif>
            <a href="{{ URL('/reqApprovedList') }}">
            <i class="material-icons">view_list</i>
            <span>Approved List</span>
            </a>            
            </li>
			
			
            <li @if($selectedMenu=='Requisition Billed') class="active" @endif>
            <a href="{{ URL('/reqBilledList') }}">
            <i class="material-icons">view_list</i>
            <span>Billed List</span>
            </a>            
            </li> 

            <li @if($selectedMenu=='Requisition Delivered') class="active" @endif>
            <a href="{{ URL('/reqDeliveredList') }}">
            <i class="material-icons">view_list</i>
            <span>In-Transit List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Received') class="active" @endif>
            <a href="{{ URL('/reqReceivedList') }}">
            <i class="material-icons">view_list</i>
            <span>Received List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Claim Order') class="active" @endif>
            <a href="{{ URL('/depot/DepotClaim') }}">
            <i class="material-icons">view_list</i>
            <span>GRN Claim</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
            <a href="{{ URL('/reqCanceledList') }}">
            <i class="material-icons">view_list</i>
            <span>Non-Approved List</span>
            </a>            
            </li>

            </ul>
            </li>

            <li @if($selectedMenu=='Wastage Requisition') class="active" @endif>
            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Wastage Requisition</span>
            </a>

            <ul class="ml-menu">
            <li @if($selectedMenu=='Wastage Requisition') class="active" @endif>
            <a href="{{ URL('/dist/was-req-manage') }}">
            <span>New Requisition</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Wastage Requisition Send') class="active" @endif>
            <a href="{{ URL('/dist/was-req-send-list') }}">
            <span>Sent List</span>
            </a>            
            </li>
            <li @if($selectedMenu=='Wastage Requisition Delivered') class="active" @endif>
            <a href="{{ URL('/dist/was-req-delivered-list') }}">
            <span>In-Transit List</span>
            </a>            
            </li>
            <li @if($selectedMenu=='Wastage Declaration') class="active" @endif>
            <a href="{{ URL('/dist/was-declaration-manage') }}">
            <span>Wastage Declaration</span>
            </a>            
            </li>


            {{--  <li @if($selectedMenu=='Requisition Acknowledge') class="active" @endif>
            <a href="{{ URL('/dist/reqAcknowledgeList') }}">
            <span>Acknowledge List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Approved') class="active" @endif>
            <a href="{{ URL('/dist/reqApprovedList') }}">
            <span>Approved List</span>
            </a>            
            </li> --}}

            {{-- <li @if($selectedMenu=='Requisition Delivered') class="active" @endif>
            <a href="{{ URL('/dist/reqDeliveredList') }}">
            <span>In-Transit List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Received') class="active" @endif>
            <a href="{{ URL('/dist/reqReceivedList') }}">
            <span>Received List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
            <a href="{{ URL('/dist/reqCanceledList') }}">
            <span>Non-Approved List</span>
            </a>            
            </li> --}}

            </ul>
            </li>

            <li @if($selectedMenu=='Free Requisition') class="active" @endif>
            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Free Requisition</span>
            </a>

            <ul class="ml-menu">
            <li @if($selectedMenu=='Free Requisition') class="active" @endif>
            <a href="{{ URL('/dist/free-req-manage') }}">
            <span>New Requisition</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Free Requisition Send') class="active" @endif>
            <a href="{{ URL('/dist/free-req-send-list') }}">
            <span>Sent List</span>
            </a>            
            </li>
            <li @if($selectedMenu=='Free Requisition Delivered') class="active" @endif>
            <a href="{{ URL('/dist/free-req-delivered-list') }}">
            <span>In-Transit List</span>
            </a>            
            </li>

            {{--  <li @if($selectedMenu=='Requisition Acknowledge') class="active" @endif>
            <a href="{{ URL('/dist/reqAcknowledgeList') }}">
            <span>Acknowledge List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Approved') class="active" @endif>
            <a href="{{ URL('/dist/reqApprovedList') }}">
            <span>Approved List</span>
            </a>            
            </li> --}}

            {{-- <li @if($selectedMenu=='Requisition Delivered') class="active" @endif>
            <a href="{{ URL('/dist/reqDeliveredList') }}">
            <span>In-Transit List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Received') class="active" @endif>
            <a href="{{ URL('/dist/reqReceivedList') }}">
            <span>Received List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
            <a href="{{ URL('/dist/reqCanceledList') }}">
            <span>Non-Approved List</span>
            </a>            
            </li> --}}

            </ul>
            </li>

            <li @if($selectedMenu=='Free Pending') class="active" @endif>
            <a href="{{ URL('/free-pending') }}">
            <i class="material-icons">view_list</i>
            <span>Free Pending</span>
            </a>            
            </li>	

            <li>

            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Return Manage</span>
            </a>

            <ul class="ml-menu">
            <li @if($selectedMenu=='ReturnOrder') class="active" @endif>
            <a href="{{ URL('/returnorder') }}">
            <i class="material-icons">view_list</i>
            <span>Return & Change</span>
            </a>            
            </li>

            </ul>

            </li>

            <li>             
            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Manage</span>
            </a>                
            <ul class="ml-menu">

            <li @if($selectedSubMenu=='Stock List') class="active" @endif>
            <a href="{{ URL('/depot_stock_list') }}">
            <i class="material-icons">view_list</i>
            <span>Stock List</span>
            </a>            
            </li>

            <li @if($selectedMenu=='Depot') class="active" @endif>
            <a href="{{ URL('/newDepotPayment') }}">
            <i class="material-icons">view_list</i>
            <span>Bank Deposit</span>
            </a>    
            </li>

            <li @if($selectedMenu=='Depot Expense') class="active" @endif>
            <a href="{{ URL('/newDepotCashBook') }}">
            <i class="material-icons">view_list</i>
            <span>Expense</span>
            </a>    
            </li>				


            <li @if($selectedMenu=='Depot Collection') class="active" @endif>
            <a href="{{ URL('/DepotCollection') }}">
            <i class="material-icons">view_list</i>
            <span>Collection</span>
            </a>    
            </li>
            </ul>
            </li>


            <li>             
            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Depot Report</span>
            </a>      
            <ul class="ml-menu">		

            <li @if($selectedMenu=='Depot Cashbook Summary') class="active" @endif>
            <a href="{{ URL('/DepotCashbookSummary') }}">
            <i class="material-icons">view_list</i>
            <span>Cash Book</span>
            </a>    
            </li>


            <li @if($selectedMenu=='Depot Expense Summary') class="active" @endif>
            <a href="{{ URL('/DepotExpenseSummary') }}">
            <i class="material-icons">view_list</i>
            <span>Expense Summary</span>
            </a>    
            </li>
			
			<li @if($selectedMenu=='Depot Ledger') class="active" @endif>
            <a href="{{ URL('/DepotLedger') }}">
            <i class="material-icons">view_list</i>
            <span>Depot Ledger</span>
            </a>    
            </li>

            <li @if($selectedMenu=='Retailer Credit Ledger') class="active" @endif>
            <a href="{{ URL('/RetailerCreditSummary') }}">
            <i class="material-icons">view_list</i>
            <span>Retailer Ledger</span>
            </a>    
            </li> 

            <li @if($selectedMenu=='Retailer Laser') class="active" @endif>
            <a href="{{ URL('/PartyLaser') }}">
            <i class="material-icons">view_list</i>
            <span>Ledger Details</span>
            </a>    
            </li>

            <li @if($selectedMenu=='FO Sales') class="active" @endif>
            <a href="{{ URL('/DepotFOSalesSummary') }}">
            <i class="material-icons">view_list</i>
            <span>FO Sales</span>
            </a>    
            </li>

            </ul>		
            </li>		

            <li @if($selectedMenu=='Report') class="active" @endif>
            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Order Report</span>
            </a>
            <ul class="ml-menu">                    
            <li @if($selectedSubMenu=='Delivery') class="active" @endif>
            <a href="{{ URL('report/delivery') }}">Delivery</a>
            </li> 
            <li @if($selectedSubMenu=='Order Vs Delivery') class="active" @endif>
            <a href="{{ URL('report/order-vs-delivery') }}">Order Vs Delivery</a>
            </li>
            <li @if($selectedSubMenu=='Category Wise Order') class="active" @endif>
            <a href="{{ URL('report/category-wise-order') }}">Category Wise Order</a>
            </li>
            <li @if($selectedSubMenu=='SKU Wise Order') class="active" @endif>
            <a href="{{ URL('report/sku-wise-order') }}">SKU Wise Order</a>
            </li> 
            <li @if($selectedSubMenu=='SKU Wise Delivery') class="active" @endif>
            <a href="{{ URL('report/sku-wise-delivery') }}">SKU Wise Delivery</a>
            </li>
            <li @if($selectedSubMenu=='Commission') class="active" @endif>
            <a href="{{ URL('/report/remaining-commission') }}">Retailer Remaining Commission Report</a>
            </li>                                   
            </ul>
            </li>
            <li @if($selectedMenu=='Wastage Report') class="active" @endif>
            <a href="javascript:void(0);" class="menu-toggle">
            <i class="material-icons">view_list</i>
            <span>Wastage Report</span>
            </a>
            <ul class="ml-menu">

            {{-- <li @if($selectedSubMenu=='Wastage Reqisition') class="active" @endif>
            <a href="{{ URL('report/wastage/distributor/order') }}">Wastage</a>
            </li> --}}
            <li @if($selectedSubMenu=='Wastage Delivery') class="active" @endif>
            <a href="{{ URL('report/wastage/distributor/delivery') }}">Wastage Delivery</a>
            </li>                
            </ul>
            </li>

            <li @if($selectedMenu=='return-change-report') class="active" @endif>             
            <a href="{{ URL('/report/return-change-report') }}">
            <i class="material-icons">view_list</i>
            <span>Return Change Report</span>
            </a>  
            </li>

        @elseif(session('userType')=='SM')

        @elseif(session('userType')=='DSM')

        @elseif(session('userType')=='ASM')

        @elseif(session('userType')=='RSM')

        @elseif(session('userType')=='TSM')

            <li @if($selectedMenu=='FO Wise Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/fo-wise-report') }}">
                    <i class="material-icons">library_books</i>
                    <span>FO Wise Report</span>
                </a>            
            </li>           

            <li @if($selectedMenu=='FO Attendance') class="active" @endif>
                <a href="{{ URL('/report/tsm/fo-attendance') }}">
                    <i class="material-icons">library_books</i>
                    <span>FO Attendance</span>
                </a>            
            </li>

            <li @if($selectedMenu=='DB Wise Requisition Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/db-wise-requisition') }}">
                    <i class="material-icons">library_books</i>
                    <span>DB Wise Requisition Report</span>
                </a>            
            </li>
            <li @if($selectedMenu=='Retailer Ledger Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/retailer-ledger') }}">
                    <i class="material-icons">library_books</i>
                    <span>Retailer Ledger Report</span>
                </a>            
            </li>

            <li @if($selectedMenu=='Daily IMS Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/daily-ims-report') }}">
                    <i class="material-icons">library_books</i>
                    <span>Daily IMS Report</span>
                </a>            
            </li>

            <li @if($selectedMenu=='Monthly IMS Status') class="active" @endif>
                <a href="{{ URL('/report/tsm/monthly-ims-status') }}">
                    <i class="material-icons">library_books</i>
                    <span>Monthly IMS Status</span>
                </a>            
            </li>

             <li @if($selectedMenu=='FO Performance Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/fo-performance-report') }}">
                    <i class="material-icons">library_books</i>
                    <span>Daily FO Performance</span>
                </a>            
            </li>

            <li @if($selectedMenu=='Monthly FO Performance') class="active" @endif>
                <a href="{{ URL('/report/tsm/monthly-fo-performance') }}">
                    <i class="material-icons">library_books</i>
                    <span>Monthly FO Performance</span>
                </a>            
            </li>

            <li @if($selectedSubMenu=='PG Wise Report') class="active" @endif>
                <a href="{{ URL('report/tsm/pg-wise-report') }}">
                     <i class="material-icons">view_list</i>
                    <span>PG Wise Report</span>
                </a>
            </li>
            <li @if($selectedSubMenu=='Stock List') class="active" @endif>
                <a href="{{ URL('/report/tsm/depot_stock_list') }}">
                    <i class="material-icons">view_list</i>
                    <span>DB Wise Stock List</span>
                </a>            
            </li>


       <!-- <li @if($selectedMenu=='FO List Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/folist') }}">
                    <i class="material-icons">library_books</i>
                    <span>FO List</span>
                </a>            
            </li>
            <li @if($selectedMenu=='Attendance Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/foattendance') }}">
                    <i class="material-icons">library_books</i>
                    <span>FO Attendance</span>
                </a>            
            </li>
            <li @if($selectedMenu=='Dist Report') class="active" @endif>
                <a href="{{ URL('/report/tsm/distlist') }}">
                    <i class="material-icons">library_books</i>
                    <span>Distributor List</span>
                </a>            
            </li> -->

        @elseif(session('userType')=='JTSM')   
            
        @elseif(session('userType')=='FO')
            
            @if(session('exceptionPoint')=='') {{-- Exception Null Hole (Nationality) --}}
                <li @if($selectedMenu=='Visit') class="active" @endif>
                    <a href="{{ URL('/visit') }}">
                        <i class="material-icons">view_list</i>
                        <span>Visit</span>
                    </a>            
                </li>
                <li @if($selectedMenu=='Order Manage') class="active" @endif>
                    <a href="{{ URL('/order-manage') }}" >
                        <i class="material-icons">view_list</i>
                        <span>Order Manage</span>
                    </a>
                </li>
            @else
                <li @if($selectedMenu=='Visit') class="active" @endif>
                    <a href="{{ URL('/visit-exception') }}">
                        <i class="material-icons">view_list</i>
                        <span>Visit</span>
                    </a>            
                </li>
                <li @if($selectedMenu=='Order Manage') class="active" @endif>
                    <a href="{{ URL('/order-manage-exception') }}" >
                        <i class="material-icons">view_list</i>
                        <span>Order Manage</span>
                    </a>
                </li>
            @endif
		
    	  <!--
            <li @if($selectedMenu=='ReturnOnly') class="active" @endif>
                <a href="{{ URL('/return-only-product') }}">
                    <i class="material-icons">view_list</i>
                    <span>Return</span>
                </a>            
            </li> -->
    		
            <li @if($selectedMenu=='Return') class="active" @endif>
                <a href="{{ URL('/returnproduct') }}">
                    <i class="material-icons">view_list</i>
                    <span>Return & Change</span>
                </a>            
            </li>
            <li @if($selectedMenu=='Requisition') class="active" @endif>
                <a href="{{ URL('/wastage') }}">
                    <i class="material-icons">view_list</i>
                    <span>Wastage</span>
                </a>            
            </li>        

            
            <li @if($selectedMenu=='Attendance') class="active" @endif>
                <a href="{{ URL('/attendance') }}" >
                    <i class="material-icons">dns</i>
                    <span>Attendance</span>
                </a>
            </li>
            <li @if($selectedMenu=='Utility') class="active" @endif>
                <a href="{{ URL('/utility') }}" >
                    <i class="material-icons">library_books</i>
                    <span>Utility</span>
                </a>
            </li>
            <li @if($selectedSubMenu=='Stock List') class="active" @endif>
                <a href="{{ URL('/depot_stock_list') }}">
                    <i class="material-icons">view_list</i>
                    <span>Stock List</span>
                </a>            
            </li>

            <li @if($selectedMenu=='Admin') class="active" @endif>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Retailer Manage</span>
                </a>
                <ul class="ml-menu">
                    <li>
                        <!-- <a href="{{ URL('/admin') }}">New Retailer</a> -->
                        <a href="{{ URL('/fo/new-retails') }}">New Retailer</a>
                    </li>
                     <li>
                        <a href="{{ URL('/activation') }}"> Activision /InactivationRetailer</a>
                    </li>
                </ul>
            </li>            
			
			<li @if($selectedMenu=='Report') class="active" @endif>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Order Report</span>
                </a>
                <ul class="ml-menu">
                    <li>
                        <a href="{{ URL('report/fo/order') }}">Order</a>
                    </li>
                    <li>
                        <a href="{{ URL('report/fo/delivery') }}">Delivery</a>
                    </li>
                    <li>
                        <a href="{{ URL('report/fo/order-vs-delivery') }}">Order Vs Delivery</a>
                    </li>
                    <!-- <li>
                        <a href="{{ URL('report/fo/category-wise') }}">Category Wise Report</a>
                    </li>
                    <li>
                        <a href="{{ URL('report/fo/product-wise') }}">Product Wise Report</a>
                    </li> -->
                    <li>
                        <a href="{{ URL('report/fo/attendance') }}">Attendance</a>
                    </li>
                     
                    <li>
                        <a href="{{ URL('report/fo/visit') }}">Visit</a>
                    </li>                
                </ul>
            </li>
			<li @if($selectedMenu=='Wastage Report') class="active" @endif>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Wastage Report</span>
                </a>
                <ul class="ml-menu">
                    
                    <li @if($selectedSubMenu=='Wastage Reqisition') class="active" @endif>
                        <a href="{{ URL('report/wastage/fo/order') }}">Wastage</a>
                    </li>
                    <li @if($selectedSubMenu=='Wastage Delivery') class="active" @endif>
                        <a href="{{ URL('report/wastage/fo/delivery') }}">Wastage Delivery</a>
                    </li>                
                </ul>
            </li> 
            
			
			
            <!-- <li @if($selectedMenu=='Admin') class="active" @endif>
                <a href="{{ URL('/admin') }}" >
                    <i class="material-icons">view_list</i>
                    <span>Retailer</span>
                </a>
            </li> -->            

            <li @if($selectedMenu=='return-change-report') class="active" @endif>             
                <a href="{{ URL('/report/return-change-report') }}">
                    <i class="material-icons">view_list</i>
                    <span>Return Change Report</span>
                </a>  
            </li>
           
        @elseif(session('userType')=='IMS Department')

            <li @if($selectedMenu=='Report') class="active" @endif>
                <a href="{{ URL('/ims/report') }}">
                    <i class="material-icons">view_list</i>
                    <span>IMS Report</span>
                </a>
            </li>
            <li @if($selectedMenu=='Report-Delivery') class="active" @endif>
                <a href="{{ URL('/ims/report-delivery') }}">
                    <i class="material-icons">view_list</i>
                    <span>IMS Delivery Report</span>
                </a>
            </li>

            <li @if($selectedMenu=='Distributor Req') class="active" @endif>
                <a href="{{ URL('/ims/dist-req') }}">
                    <i class="material-icons">view_list</i>
                    <span>Distributor Requisition Report</span>
                </a>
            </li>

        @elseif(session('userType')=='Accounts Dept')   
                
                    <!-- Masud  Distributor Payment-->
            <li>
             
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Distributor Payment Verify</span>
                </a>
                
                <ul class="ml-menu">
                
                    <li @if($selectedMenu=='Payment Acknowledge List') class="active" @endif>
                        <a href="{{ URL('/distPaymentAckList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Payment Verify</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Verified List') class="active" @endif>
                        <a href="{{ URL('/verifiedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Payment Verify List</span>
                        </a>    
                    </li>                    
                </ul>   
				
            </li>
			
			<li>
             
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Depot Payment Verify</span>
                </a>
                
                <ul class="ml-menu">
                
                    <li @if($selectedMenu=='Depot Payment Acknowledge List') class="active" @endif>
                        <a href="{{ URL('/depotPaymentAckList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Payment Verify</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Depot Verified List') class="active" @endif>
                        <a href="{{ URL('/depotVerifiedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Payment Verify List</span>
                        </a>    
                    </li>                    
                </ul>   
				
            </li>
			
        @elseif(session('userType')=='System Admin')   
                
            <li @if($selectedMenu=='All Order Manage') class="active" @endif>             
                <a href="{{ URL('/sys/all-invoice') }}">
                    <i class="material-icons">view_list</i>
                    <span>All Invoice</span>
                </a>
            </li>

        @elseif(session('userType')=='EPP')                  
            <li @if($selectedMenu=='Memo wise sales report') class="active" @endif>             
                <a href="{{ URL('/epp/memo-wise-sales') }}">
                    <i class="material-icons">view_list</i>
                    <span>Memo wise sales report</span>
                </a>
            </li>
            
        @endif

        <li class="header"></li>
        <li @if($selectedMenu=='Password') class="active" @endif>
            <a href="{{ URL('/password/change-password') }}">
                <i class="material-icons">person</i>
                <span>Change Password</span>
            </a>            
        </li>

        <li>
            <a href="{{ URL('/logout') }}" onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                <i class="material-icons col-light-blue">donut_large</i>
                <span>Sign Out</span>
            </a>
            <form id="logout-form" action="{{ URL('/logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>

        <li class="header"></li>

    </ul>
</div>



