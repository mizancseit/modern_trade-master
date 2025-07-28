{{-- Master Menu  --}} 

<div class="menu">
    <ul class="list">
    {{-- <li class="header">MAIN NAVIGATION</li> --}}
        @php 
            $selectedSubMenu='';
        @endphp 
        <li @if($selectedMenu=='Home') class="active" @endif>
            <a href="{{ URL('/') }}">
                <i class="material-icons">dashboard</i>
                <span>Dashboard</span>  
            </a>
        </li>

        {{-- Field Executive part --}}

        @php
           //dd(Auth::user()->module_type.'-'.Auth::user()->user_type_id);
        @endphp

        @if(Auth::user()->module_type==3 && Auth::user()->user_type_id==7)

        <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition Manage</span>
                </a>
            <ul class="ml-menu">
                <li @if($selectedMenu=='Visit') class="active" @endif>
                    <a href="{{ URL('/requisition') }}">
                        <i class="material-icons">view_list</i>
                        <span>Make Requisition</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Replace') class="active" @endif>
                    <a href="{{ URL('/eshop-replace') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Return') class="active" @endif>
                    <a href="{{ URL('/eshop-return') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>
            </ul>
        </li>
        <li @if($selectedMenu=='Order Manage') class="active" @endif>
            <a href="{{ URL('/eshop-order-manage') }}">
                <i class="material-icons">view_list</i>
                <span>Sales Order Manage</span>
            </a>    
        </li>
        <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Not Approved Manage</span>
                </a>
            <ul class="ml-menu">
                 <li @if($selectedMenu=='Order Not approve') class="active" @endif>
                    <a href="{{ URL('/eshop-order-not-approve') }}">
                        <i class="material-icons">view_list</i>
                        <span>Sales Order</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Replace Not approve') class="active" @endif>
                    <a href="{{ URL('/eshop-replace-not-approve') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Return Not approve') class="active" @endif>
                    <a href="{{ URL('/eshop-return-not-approve') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return</span>
                    </a>    
                </li>
            </ul>
        </li> 
        <li @if($selectedMenu=='Outlet pay') class="active" @endif>
            <a href="{{ URL('/eshop_outlet_payments') }}">
                <i class="material-icons">view_list</i>
                <span>Payment Collection</span>
            </a>    
        </li> 
        {{-- <li @if($selectedMenu=='E-Shop Customer') class="active" @endif>
            <a href="{{ URL('/e-shop-customer') }}">
                <i class="material-icons">view_list</i>
                <span>E-Shop Customer</span>
            </a>    
        </li> --}}
        {{-- <li @if($selectedMenu=='Credit Adjustment') class="active" @endif>
            <a href="{{ URL('/credit-adjustment') }}">
                <i class="material-icons">view_list</i>
                <span>Payment Adjustment</span>
            </a>    
        </li> --}}
         

        <li @if($selectedMenu=='Sales Report') class="active" @endif>
            <a href="javascript:void(0);" class="menu-toggle">
                <i class="material-icons">view_list</i>
                <span>Report</span>
            </a>

            <ul class="ml-menu">
                      <!-- Master Menu-->
                <li @if($selectedSubMenu=='Order Report') class="active" @endif>
                    <a href="{{ URL('/eshop-order-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Order Report</span>
                    </a>    
                </li>
                <li @if($selectedSubMenu=='Delivery Report') class="active" @endif>
                    <a href="{{ URL('/eshop-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Delivery Report</span>
                    </a>    
                </li>
 
                <li @if($selectedMenu=='Replace Approve') class="active" @endif>
                    <a href="{{ URL('/eshop-replace-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                
                <li @if($selectedMenu=='Advance Replace Report') class="active" @endif>
                    <a href="{{ URL('/eshop-replace-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace Confirm</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Return Approve') class="active" @endif>
                    <a href="{{ URL('/eshop-return-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Advance Return Report') class="active" @endif>
                    <a href="{{ URL('/eshop-return-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Confirm</span>
                    </a>    
                </li>
			</ul>
        </li>
        @endif

        <!-- Billing part -->
        
        @if(Auth::user()->module_type==3  && Auth::user()->user_type_id==2)
            <li>
             
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition Management</span>
                </a>
                
                <ul class="ml-menu">
                
                    <li @if($selectedMenu=='Requisition Pending') class="active" @endif>
                        <a href="{{ URL('/eshop-modern-delivery') }}">
                            <i class="material-icons">view_list</i>
                            <span>Acknowledge Sales Order</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition Analysis') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllAnalysisList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Analysis Sales Order</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition Approved') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllApprovedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Open Sales Order</span>
                        </a>    
                    </li>
                    
                    
                     <li @if($selectedMenu=='Requisition All Delivered') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllBilledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Billed List</span>
                        </a>    
                    </li>
                    
                    
                    
                    <!-- <li @if($selectedMenu=='Requisition Active') class="active" @endif>
                        <a href="{{ URL('/modern-reqAllActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Order IN-Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition IN Active') class="active" @endif>
                        <a href="{{ URL('/modern-reqAllInActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Order Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Customar Active') class="active" @endif>
                        <a href="{{ URL('/modern-custAllActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customar In-Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Customar IN Active') class="active" @endif>
                        <a href="{{ URL('/modern-custAllInActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customar Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
                        <a href="{{ URL('/modern-reqAllCanceledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Canceled Requisition</span>
                        </a>    
                    </li> -->
                    
                </ul>
                
          </li>
          <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition</span>
                </a>
            <ul class="ml-menu">     
               <!--  <li @if($selectedMenu=='Delivery') class="active" @endif>
                    <a href="{{ URL('/modern-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Acknowledge Sales Order</span>
                    </a>    
                </li> -->
                
                <li @if($selectedMenu=='Replace Delivery') class="active" @endif>
                    <a href="{{ URL('/eshop-replace-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Return Delivery') class="active" @endif>
                    <a href="{{ URL('/eshop-return-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>
            </ul>
        </li>
        <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Report</span>
                </a>
            <ul class="ml-menu">   
                
                <li @if($selectedMenu=='Report') class="active" @endif>
                    <a href="{{ URL('/eshop-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Delivery Report</span>
                    </a>    
                </li>
                 <li @if($selectedMenu=='Advance Replace Report') class="active" @endif>
                    <a href="{{ URL('/eshop-billing-replace-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Advance Return Report') class="active" @endif>
                    <a href="{{ URL('/eshop-billing-return-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>
            </ul>
        </li>

        @endif
        @if(Auth::user()->module_type==3  && Auth::user()->user_type_id==5)
            
                    <li @if($selectedMenu=='Report') class="active" @endif>
                        <a href="{{ URL('/eshop-admin-delivery') }}">
                            <i class="material-icons">view_list</i>
                            <span>Sales Order</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Advance Replace Report') class="active" @endif>
                        <a href="{{ URL('/eshop-admin-replace-delivery-report') }}">
                            <i class="material-icons">view_list</i>
                            <span>Advance Replace</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Advance Return Report') class="active" @endif>
                        <a href="{{ URL('/eshop-admin-return-delivery-report') }}">
                            <i class="material-icons">view_list</i>
                            <span>Return Order</span>
                        </a>    
                    </li>

                    <li @if($selectedMenu=='Ledger') class="active" @endif>
                        <a href="{{ URL('/eshop-customer-ledger') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customer ledger</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Stock') class="active" @endif>
                        <a href="{{ URL('/eshop-customer-stock') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customer Balance</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Customer payment') class="active" @endif>
                        <a href="{{ URL('/eshop-admin-payment') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customer Payment</span>
                        </a>    
                    </li>
                
        @endif
        @if(Auth::user()->module_type==3  && Auth::user()->user_type_id==6)

            <li @if($selectedMenu=='globalcompany') class="active" @endif>
                <a href="{{ URL('/eshop-opening-route') }}">
                    <i class="material-icons">view_list</i>
                    <span>Opening Balance</span>
                </a>    
            </li>
            {{-- <li @if($selectedMenu=='Targer file upload') class="active" @endif>
                <a href="{{ URL('/eshop_target_upload') }}">
                    <i class="material-icons">view_list</i>
                    <span>Target Upload</span>
                </a>    
            </li> --}}
            <li @if($selectedMenu=='Customer List') class="active" @endif>
                <a href="{{ URL('/eshop-customer-list') }}">
                    <i class="material-icons">view_list</i>
                    <span>Customer</span>
                </a>    
            </li>
            <li @if($selectedMenu=='Outlet List') class="active" @endif>
                <a href="{{ URL('/eshop-outlet-list') }}">
                    <i class="material-icons">view_list</i>
                    <span>Outlet List</span>
                </a>    
            </li>

            <li @if($selectedMenu=='Product List') class="active" @endif>
                <a href="{{ URL('/eshop-product-list') }}">
                    <i class="material-icons">view_list</i>
                    <span>Product List</span>
                </a>    
            </li>
            <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Report</span>
                </a>
                <ul class="ml-menu">
                    <li @if($selectedMenu=='Report') class="active" @endif>
                        <a href="{{ URL('/eshop-manager-delivery') }}">
                            <i class="material-icons">view_list</i>
                            <span>Sales Order</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Advance Replace Report') class="active" @endif>
                        <a href="{{ URL('/eshop-manager-replace-delivery-report') }}">
                            <i class="material-icons">view_list</i>
                            <span>Advance Replace</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Advance Return Report') class="active" @endif>
                        <a href="{{ URL('/eshop-manager-return-delivery-report') }}">
                            <i class="material-icons">view_list</i>
                            <span>Return Order</span>
                        </a>    
                    </li>

                    <li @if($selectedMenu=='Ledger') class="active" @endif>
                        <a href="{{ URL('/eshop-customer-ledger') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customer ledger</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Stock') class="active" @endif>
                        <a href="{{ URL('/eshop-customer-stock') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customer Balance</span>
                        </a>    
                    </li>
                    <li @if($selectedMenu=='Customer payment') class="active" @endif>
                        <a href="{{ URL('/eshop-admin-payment') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customer Payment</span>
                        </a>    
                    </li>
                </ul>
            </li>
            <li @if($selectedMenu=='Supervisor') class="active" @endif>
                <a href="{{ URL('/user_supervisor') }}">
                    <i class="material-icons">view_list</i>
                    <span>Define Supervisor</span>
                </a>    
            </li>
        @endif
        @if(Auth::user()->module_type==3  && Auth::user()->user_type_id==3)
         <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition</span>
                </a>
            <ul class="ml-menu">
                <li @if($selectedMenu=='Delivery') class="active" @endif>
                    <a href="{{ URL('/eshop-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Sales Order</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Replace Approve') class="active" @endif>
                    <a href="{{ URL('/eshop-replace-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Return Approve') class="active" @endif>
                    <a href="{{ URL('/eshop-return-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>
                {{-- <li @if($selectedMenu=='Delivery') class="active" @endif>
                    <a href="{{ URL('/eshop-return-order') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li> --}}                                         
            </ul>
        </li>
        <li>
            <a href="{{ URL('/eshop-requisition-status') }}" >
                    <i class="material-icons">view_list</i>
                    <span>Requisition Status</span>
                </a></li>
        <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Billing Confirmation</span>
                </a>
            <ul class="ml-menu">
                <li @if($selectedMenu=='Delivery Approved') class="active" @endif>
                    <a href="{{ URL('/eshop-delivery-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Sales Order</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Make Invoice') class="active" @endif>
                    <a href="{{ URL('/eshop-make-invoice') }}">
                        <i class="material-icons">view_list</i>
                        <span>Make Invoice </span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Invoice List') class="active" @endif>
                    <a href="{{ URL('/eshop-invoiced') }}">
                        <i class="material-icons">view_list</i>
                        <span>Invoice List</span>
                    </a>    
                </li>
                <!-- <li @if($selectedMenu=='Replace Delivery Approved') class="active" @endif>
                    <a href="{{ URL('/eshop-replace-delivery-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Return Delivery Approved') class="active" @endif>
                    <a href="{{ URL('/eshop-return-delivery-approved') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li> -->
            </ul>
        </li>
        {{-- <li @if($selectedMenu=='globalcompany') class="active" @endif>
            <a href="{{ URL('/eshop-opening-route') }}">
                <i class="material-icons">view_list</i>
                <span>Opening Balance</span>
            </a>    
        </li> --}}
        <li @if($selectedMenu=='Outlet pay') class="active" @endif>
            <a href="{{ URL('/eshop_admin_payments_con') }}">
                <i class="material-icons">view_list</i>
                <span>Payment</span>
            </a>    
        </li>
        {{-- <li @if($selectedMenu=='Targer file upload') class="active" @endif>
            <a href="{{ URL('/modern_target_upload') }}">
                <i class="material-icons">view_list</i>
                <span>Target Upload</span>
            </a>    
        </li> --}}

        <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Report</span>
                </a>
            <ul class="ml-menu">
                <li @if($selectedMenu=='Report') class="active" @endif>
                    <a href="{{ URL('/eshop-admin-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Sales Order</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Stock report sku wise') class="active" @endif>
                    <a href="{{ URL('/eshop-summary-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Stock report sku wise</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Advance Replace Report') class="active" @endif>
                    <a href="{{ URL('/eshop-admin-replace-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Advance Return Report') class="active" @endif>
                    <a href="{{ URL('/eshop-admin-return-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>

                <li @if($selectedMenu=='Ledger') class="active" @endif>
                    <a href="{{ URL('/eshop-customer-ledger') }}">
                        <i class="material-icons">view_list</i>
                        <span>Customer ledger</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Stock') class="active" @endif>
                    <a href="{{ URL('/eshop-customer-stock') }}">
                        <i class="material-icons">view_list</i>
                        <span>Customer Balance</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Customer payment') class="active" @endif>
                    <a href="{{ URL('/eshop-admin-payment') }}">
                        <i class="material-icons">view_list</i>
                        <span>Customer Payment</span>
                    </a>    
                </li>
            </ul>
        </li>
        
		
        
        @endif

        @if(Auth::user()->module_type==3 && Auth::user()->user_type_id==4)
         <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Payment Acknowledgement</span>
                </a>
            <ul class="ml-menu">
                <li @if($selectedMenu=='Outlet pay') class="active" @endif>
                    <a href="{{ URL('/eshop_accounts_payments_con') }}">
                        <i class="material-icons">view_list</i>
                        <span>Payment receive</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Payments Ack') class="active" @endif>
                    <a href="{{ URL('/eshop-accounts-payments-ack') }}">
                        <i class="material-icons">view_list</i>
                        <span>Payment Ack list</span>
                    </a>    
                </li>
            </ul>
        </li>
         <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Payment Verify</span>
                </a>
            <ul class="ml-menu">
                <li @if($selectedMenu=='Outlet pay') class="active" @endif>
                    <a href="{{ URL('/eshop-accounts-payments-verify') }}">
                        <i class="material-icons">view_list</i>
                        <span>Payment Verify</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Outlet report') class="active" @endif>
                    <a href="{{ URL('/eshop_accounts_payments_rece_report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Payment Verify Report</span>
                    </a>    
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->user_type_id==8)
            <li>
             
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition Management</span>
                </a>
                
                <ul class="ml-menu">
                
                    <li @if($selectedMenu=='Requisition Pending') class="active" @endif>
                        <a href="{{ URL('/eshop-delivery') }}">
                            <i class="material-icons">view_list</i>
                            <span>Acknowledge Sales Order</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition Analysis') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllAnalysisList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Analysis Sales Order</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition Approved') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllApprovedList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Open Sales Order</span>
                        </a>    
                    </li>
                    
                    
                     <li @if($selectedMenu=='Requisition All Delivered') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllBilledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Billed List</span>
                        </a>    
                    </li>
                    
                    
                    
                    <!-- <li @if($selectedMenu=='Requisition Active') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Order IN-Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition IN Active') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllInActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Order Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Customar Active') class="active" @endif>
                        <a href="{{ URL('/eshop-custAllActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customar In-Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Customar IN Active') class="active" @endif>
                        <a href="{{ URL('/eshop-custAllInActiveList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Customar Active</span>
                        </a>    
                    </li>
                    
                    <li @if($selectedMenu=='Requisition Canceled') class="active" @endif>
                        <a href="{{ URL('/eshop-reqAllCanceledList') }}">
                            <i class="material-icons">view_list</i>
                            <span>Canceled Requisition</span>
                        </a>    
                    </li> -->
                    
                </ul>
                
          </li>
          <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Requisition</span>
                </a>
            <ul class="ml-menu">     
               <!--  <li @if($selectedMenu=='Delivery') class="active" @endif>
                    <a href="{{ URL('/eshop-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Acknowledge Sales Order</span>
                    </a>    
                </li> -->
                
                <li @if($selectedMenu=='Replace Delivery') class="active" @endif>
                    <a href="{{ URL('/eshop-replace-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Return Delivery') class="active" @endif>
                    <a href="{{ URL('/eshop-return-delivery') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>
            </ul>
        </li>
        <li> <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">view_list</i>
                    <span>Report</span>
                </a>
            <ul class="ml-menu">   
                
                <li @if($selectedMenu=='Delivery Report') class="active" @endif>

                    <a href="{{ URL('/eshop-billing-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Delivery Report</span>
                    </a>    
                </li>

                <li @if($selectedMenu=='Products Wise Summary') class="active" @endif>
                    <a href="{{ URL('/eshop-summary-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Products Wise Summary </span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Customer Wise Summary') class="active" @endif>
                    <a href="{{ URL('/eshop-customer-summary-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Customer Wise Summary </span>
                    </a>    
                </li>
                 <li @if($selectedMenu=='Advance Replace Report') class="active" @endif>
                    <a href="{{ URL('/eshop-billing-replace-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Advance Replace</span>
                    </a>    
                </li>
                <li @if($selectedMenu=='Advance Return Report') class="active" @endif>
                    <a href="{{ URL('/eshop-billing-return-delivery-report') }}">
                        <i class="material-icons">view_list</i>
                        <span>Return Order</span>
                    </a>    
                </li>
            </ul>
        </li>

        @endif   
        
        <li class="header"></li>
       {{--  <li @if($selectedMenu=='Password') class="active" @endif>
            <a href="{{ URL('/password/change-password') }}">
                <i class="material-icons">person</i>
                <span>Change Password</span>
            </a>            
        </li> --}}

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



