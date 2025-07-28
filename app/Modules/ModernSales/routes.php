<?php

Route::group(
    ['middleware' => ['web', 'auth'], 'module' => 'ModernSales', 'namespace' => 'App\Modules\ModernSales\Controllers'], function () {
        Route::get('/modernSales', 'ModernSalesController@index');

        /////////////////////Sharifur Rahman part of Start ////////////////////

        // Order for field Executive



        Route::get('/mts-visit', 'ModernVisitController@mts_visit');
        Route::get('/mts-partyList', 'ModernVisitController@mts_partyList');
        Route::get('/mts-order-process/{partyid}/{customer_id}', 'ModernVisitController@mts_order_process');
        Route::get('/mts-category-products', 'ModernVisitController@mts_category_products');
        Route::post('/mts-add-to-cart', 'ModernVisitController@mts_add_to_cart_products');

		Route::get('/mts-bucket/{customer_id}/{partyid}', 'ModernVisitController@mts_bucket');

		Route::post('/mts-items-edit', 'ModernVisitController@mts_items_edit');
		Route::post('/mts-edit-submit', 'ModernVisitController@mts_items_edit_submit');
		Route::get('/mts-items-delete/{orderid}/{itemid}/{customer_id}/{partyid}/{catid}', 'ModernVisitController@mts_items_delete');

		Route::post('/mts-confirm-order', 'ModernVisitController@mts_confirm_order');
 
		Route::get('/mts-delete-order/{orderid}/{partyid}/{customer_id}', 'ModernVisitController@mts_delete_order');


		// Report for field Executive

		Route::get('/mts-order-report', 'ModernExecutiveReportController@mts_order_report');
		Route::post('/mts-order-report-list', 'ModernExecutiveReportController@mts_order_report_list');
		Route::get('/mts-order-details/{orderMainId}', 'ModernExecutiveReportController@mts_order_details');

		Route::get('/mts-delivery-report', 'ModernExecutiveReportController@mts_delivery_report');
		Route::post('/mts-delivery-report-list', 'ModernExecutiveReportController@mts_delivery_report_list');
		Route::get('/mts-delivery-details/{orderMainId}', 'ModernExecutiveReportController@mts_delivery_details');

		// Not approve sales order

		Route::get('/mts-order-not-approve', 'ModernVisitController@mts_order_not_approve');
		Route::post('/mts-order-not-approve-list', 'ModernVisitController@mts_order_not_approve_list');
		// Sales order manage
		Route::get('/mts-order-manage', 'ModernVisitController@mts_order_manage');
		Route::get('/mts-order-manage-list', 'ModernVisitController@mts_order_manageList');
		Route::get('/mts-bucket-delete/{order_id}', 'ModernVisitController@mts_order_manage_delete');
		Route::get('/mts-bucket-manage/{order_id}/{customer_id}/{partyid}', 'ModernVisitController@mts_bucket_manage');
		 Route::get('/mts-order-manage-process/{orderid}/{partyid}/{customer_id}', 'ModernVisitController@mts_order_manage_process');

		Route::post('/mts-manage-add-to-cart', 'ModernVisitController@mts_manage_add_to_cart_products');
		// Visit module
		Route::get('/mts-visit-order/{party_id}/{customer_id}', 'ModernVisitController@mts_order_visit');
		Route::post('/mts-visit-process-submit', 'ModernVisitController@mts_visit_process_submit');
		Route::get('/mts-nonvisit/{party_id}/{customer_id}', 'ModernVisitController@mts_nonvisit');
		Route::post('/mts-nonvisit-process-submit', 'ModernVisitController@mts_nonvisit_process_submit');
		// Modern Admin part

		Route::get('/mts-approved', 'ModernAdminController@mts_approved');
		Route::post('/mts-remark', 'ModernAdminController@mts_remark');
		Route::post('/mts-download-csv', 'ModernAdminController@mtsDownloadCsv');
		Route::get('/mts-approved-list', 'ModernAdminController@mts_approved_list');

		Route::get('/mts-order-view/{wastageMainId}/{foMainId}', 'ModernAdminController@mts_order_view');

		Route::get('/mts-approved-order/{orderid}/{partyid}/{status}', 'ModernAdminController@mts_approved_order');

		Route::post('/not-approved-remarks-submit', 'ModernAdminController@not_approved_remarks_submit');
		

		// Delivery Approved for admin part

		Route::get('/mts-delivery-approved', 'ModernAdminController@mts_delivery_approved');
		Route::get('/mts-delivery-approved-list', 'ModernAdminController@mts_delivery_approved_list');

		Route::get('/mts-delivery-approved-view/{orderMainId}/{foMainId}', 'ModernAdminController@mts_delivery_approved_view');
		Route::get('/mts-approved-delivery/{orderid}/{customerid}/{status}', 'ModernAdminController@mts_approved_delivery');
		
		
		// Opening blance start

		Route::get('/mts-opening-route', 'ModernPaymentControlle@mts_opening_route');
        Route::get('/mts-opening-outlet', 'ModernPaymentControlle@mts_opening_outlet');
        Route::get('/mts-add-opening-balance', 'ModernPaymentControlle@mts_add_opening_balance');

        // delivery report part of admin

        Route::get('/mts-admin-delivery', 'ModernAdminController@ssg_report_order_delivery');
		Route::post('/mts-admin-delivery-list', 'ModernAdminController@ssg_report_order_list');
		Route::get('/mts-admin-delivery-details/{orderMainId}', 'ModernAdminController@ssg_order_details');

		// Sales order approved report part of executive

        Route::get('/mts-admin-sales-order', 'ModernAdminController@ssg_report_sales_order');
		Route::post('/mts-admin-sales-order-list', 'ModernAdminController@ssg_report_sales_order_list');
		Route::get('/mts-admin-sales-order-details/{orderMainId}', 'ModernAdminController@ssg_sales_order_details');

		// Payment report part of admin

		Route::get('/mts-admin-payment', 'ModernPaymentControlle@mts_admin_payment');
		Route::get('/mts-admin-payment-list', 'ModernPaymentControlle@mts_admin_payment_list');
		// Credit Adjustment

		Route::get('/credit-adjustment', 'ModernPaymentControlle@credit_adjustment');
		Route::get('/credit-adjustment-list', 'ModernPaymentControlle@credit_adjustment_list');
		Route::post('/credit-adjustment-process', 'ModernPaymentControlle@credit_adjustment_process');


		// Advance Replace start

        Route::get('/mts-replace', 'ModernReplaceController@mts_replace');
        Route::get('/mts-replace-outlet-list', 'ModernReplaceController@mts_replace_outlet_list');
        Route::get('/mts-replace-process/{partyid}/{customer_id}', 'ModernReplaceController@mts_replace_process');
        Route::get('/mts-replace-category-products', 'ModernReplaceController@mts_replace_category_products');
        Route::post('/mts-add-replace-products', 'ModernReplaceController@mts_add_replace_products');
		Route::get('/mts-replace-bucket/{customer_id}/{partyid}', 'ModernReplaceController@mts_replace_bucket');
		Route::post('/mts-replace-items-edit', 'ModernReplaceController@mts_replace_items_edit');
		Route::post('/mts-replace-edit-submit', 'ModernReplaceController@mts_replace_edit_submit');
		Route::get('/mts-replace-items-delete/{replace_id}/{itemid}', 'ModernReplaceController@mts_replace_items_delete');
		Route::post('/mts-confirm-replace', 'ModernReplaceController@mts_confirm_replace');
		Route::get('/mts-delete-replace/{replace_id}/{partyid}/{customer_id}', 'ModernReplaceController@mts_delete_replace');

		// Not approve Advance Replace

		Route::get('/mts-replace-not-approve', 'ModernReplaceController@mts_replace_not_approve');
		Route::post('/mts-replace-not-approve-list', 'ModernReplaceController@mts_replace_not_approve_list');

		// Advance Replace Modern Admin part

		Route::get('/mts-replace-approved', 'ModernReplaceController@mts_replace_approved');
		Route::get('/mts-replace-approved-list', 'ModernReplaceController@mts_replace_approved_list');

		Route::get('/mts-replace-view/{wastageMainId}/{foMainId}', 'ModernReplaceController@mts_replace_view');

		Route::get('/mts-approved-replace/{orderid}/{partyid}/{status}', 'ModernReplaceController@mts_approved_replace');

		// Advance Replace Modern Billing part

		Route::get('/mts-replace-delivery', 'ModernReplaceController@mts_replace_delivery');
		Route::get('/mts-replace-delivery-list', 'ModernReplaceController@mts_replace_delivery_list');

		Route::get('/mts-replace-delivery-edit/{DeliveryMainId}/{foMainId}', 'ModernReplaceController@mts_replace_delivery_edit');
		Route::post('/mts-replace-delivery-edit-submit', 'ModernReplaceController@mts_replace_delivery_edit_submit');

		// Advance Replace Delivery Report admin part

		Route::get('/mts-admin-replace-delivery-report', 'ModernReplaceController@mts_admin_replace_delivery_report');
		Route::get('/mts-admin-replace-delivery-report-list', 'ModernReplaceController@mts_admin_replace_delivery_report_list');
		Route::get('/mts-admin-replace-delivery-report-details/{orderMainId}', 'ModernReplaceController@mts_admin_replace_delivery_report_details');

		// Advance Replace Delivery Report Billing part

		Route::get('/mts-billing-replace-delivery-report', 'ModernReplaceController@mts_billing_replace_delivery_report');
		Route::get('/mts-billing-replace-delivery-report-list', 'ModernReplaceController@mts_billing_replace_delivery_report_list');
		Route::get('/mts-billing-replace-delivery-report-details/{orderMainId}', 'ModernReplaceController@mts_billing_replace_delivery_report_details');

		// Advance Replace Delivery Report Executive part

		Route::get('/mts-replace-delivery-report', 'ModernReplaceController@mts_replace_delivery_report');
		Route::get('/mts-replace-delivery-report-list', 'ModernReplaceController@mts_replace_delivery_report_list');
		Route::get('/mts-replace-delivery-report-details/{orderMainId}', 'ModernReplaceController@mts_replace_delivery_report_details');


		// Advance Replace Approved for admin part

		Route::get('/mts-replace-delivery-approved', 'ModernReplaceController@mts_replace_delivery_approved');
		Route::get('/mts-replace-delivery-approved-list', 'ModernReplaceController@mts_replace_delivery_approved_list');

		Route::get('/mts-replace-delivery-approved-view/{orderMainId}/{foMainId}', 'ModernReplaceController@mts_replace_delivery_approved_view');
		Route::get('/mts-replace-delivery-approved-submit/{orderid}/{customerid}/{status}', 'ModernReplaceController@mts_replace_delivery_approved_submit');

		// Return start

        Route::get('/mts-return', 'ModernReturnController@mts_return');
        Route::get('/mts-return-outlet-list', 'ModernReturnController@mts_return_outlet_list');
        Route::get('/mts-return-process/{partyid}/{customer_id}', 'ModernReturnController@mts_return_process');
        Route::get('/mts-return-category-products', 'ModernReturnController@mts_return_category_products');
        Route::post('/mts-add-return-products', 'ModernReturnController@mts_add_return_products');
		Route::get('/mts-return-bucket/{customer_id}/{partyid}', 'ModernReturnController@mts_return_bucket');
		Route::post('/mts-return-items-edit', 'ModernReturnController@mts_return_items_edit');
		Route::post('/mts-return-edit-submit', 'ModernReturnController@mts_return_edit_submit');
		Route::get('/mts-return-items-delete/{return_id}/{itemid}', 'ModernReturnController@mts_return_items_delete');
		Route::post('/mts-confirm-return', 'ModernReturnController@mts_confirm_return');
		Route::get('/mts-delete-return/{return_id}/{partyid}/{customer_id}', 'ModernReturnController@mts_delete_return'); 
		// Not approve Advance Replace

		Route::get('/mts-return-not-approve', 'ModernReturnController@mts_return_not_approve');
		Route::post('/mts-return-not-approve-list', 'ModernReturnController@mts_return_not_approve_list');

		// Return Modern Admin part

		Route::get('/mts-return-approved', 'ModernReturnController@mts_return_approved');
		Route::get('/mts-return-approved-list', 'ModernReturnController@mts_return_approved_list');

		Route::get('/mts-return-view/{wastageMainId}/{foMainId}', 'ModernReturnController@mts_return_view');

		Route::get('/mts-approved-return/{orderid}/{partyid}/{status}', 'ModernReturnController@mts_approved_return');


		Route::get('/mts-return-delivery-approved', 'ModernReturnController@mts_return_delivery_approved');
		Route::get('/mts-return-delivery-approved-list', 'ModernReturnController@mts_return_delivery_approved_list');

		Route::get('/mts-return-delivery-approved-view/{orderMainId}/{foMainId}', 'ModernReturnController@mts_return_delivery_approved_view');
		Route::get('/mts-return-delivery-approved-submit/{orderid}/{customerid}/{status}', 'ModernReturnController@mts_return_delivery_approved_submit');

		// Return Modern Billing part

		Route::get('/mts-return-delivery', 'ModernReturnController@mts_return_delivery');
		Route::get('/mts-return-delivery-list', 'ModernReturnController@mts_return_delivery_list');

		Route::get('/mts-return-delivery-edit/{DeliveryMainId}/{foMainId}', 'ModernReturnController@mts_return_delivery_edit');
		Route::post('/mts-return-delivery-edit-submit', 'ModernReturnController@mts_return_delivery_edit_submit');

		// Return Delivery Report admin part

		Route::get('/mts-admin-return-delivery-report', 'ModernReturnController@mts_admin_return_delivery_report');
		Route::get('/mts-admin-return-delivery-report-list', 'ModernReturnController@mts_admin_return_delivery_report_list');
		Route::get('/mts-admin-return-delivery-report-details/{orderMainId}', 'ModernReturnController@mts_admin_return_delivery_report_details');


		// Return Delivery Report Billing part

		Route::get('/mts-billing-return-delivery-report', 'ModernReturnController@mts_billing_return_delivery_report');
		Route::get('/mts-billing-return-delivery-report-list', 'ModernReturnController@mts_billing_return_delivery_report_list');
		Route::get('/mts-billing-return-delivery-report-details/{orderMainId}', 'ModernReturnController@mts_billing_return_delivery_report_details');

		// Return Delivery Report Executive part

		Route::get('/mts-return-delivery-report', 'ModernReturnController@mts_return_delivery_report');
		Route::get('/mts-return-delivery-report-list', 'ModernReturnController@mts_return_delivery_report_list');
		Route::get('/mts-return-delivery-report-details/{orderMainId}', 'ModernReturnController@mts_return_delivery_report_details');

		/////////////////////Sharifur Rahman part of End////////////////////

		
		/////////////////////Md. Sazzadul islam////////////////////

		Route::post('/orderDelivery-open-submit', 'ModernDeliveryController@ssg_modern_open_submit');
		Route::get('/product-wise-analysis/{id}', 'ModernDeliveryController@product_wise_analysis');
		Route::get('/modern-reqAllAnalysisList', 'ModernDeliveryController@modern_delivery_analysis');
		Route::get('/modern-reqAllAnalysisList-view', 'ModernDeliveryController@modern_delivery_analysis_list');

		Route::get('/modern-delivery-list', 'ModernDeliveryController@ssg_delivery_list');


		Route::get('/modern-delivery', 'ModernDeliveryController@modern_delivery'); 

		Route::get('/orderDelivery-edit/{wastageMainId}/{foMainId}', 'ModernDeliveryController@ssg_order_edit');
		Route::post('/orderDelivery-edit-submit', 'ModernDeliveryController@ssg_modern_edit_submit');

		
		Route::get('/moderndelivery', 'ModernDeliveryController@ssg_report_order_delivery');
		Route::post('/moderndelivery-list', 'ModernDeliveryController@ssg_report_order_list');
		Route::get('/modernorder-details/{orderMainId}', 'ModernDeliveryController@ssg_order_details');
		//////////////payment////////////////
		
		Route::get('/outlet_payments', 'ModernPaymentControlle@outlet_payments');
		
		Route::get('/outlet-payment-list', 'ModernPaymentControlle@outlet_payment_list');
		Route::post('/outlet_paymnet_process', 'ModernPaymentControlle@outlet_paymnet_process');
		Route::get('/mts-outlet-payments-delete/{paymentid}/{type}', 'ModernPaymentControlle@mts_outlet_payments_delete');
		Route::get('/admin_payments_con', 'ModernPaymentControlle@admin_payments_con');
		Route::get('/admin_payments_con_list', 'ModernPaymentControlle@admin_payments_con_list');
		Route::get('/app_admin_payment/{id}/{status}', 'ModernPaymentControlle@app_admin_payment');

		Route::get('/app_admin_payment_edit', 'ModernPaymentControlle@app_admin_payment_edit');
		Route::post('/app_admin_payment_edit_submit', 'ModernPaymentControlle@app_admin_payment_edit_submit');

		Route::get('/accounts_payments_con', 'ModernPaymentControlle@accounts_payments_con');
		Route::get('/accounts_payments_con_list', 'ModernPaymentControlle@accounts_payments_con_list');
		Route::get('/accounts_payments_download', 'ModernPaymentControlle@accounts_payments_download');
		Route::get('/accounts_payments_verify_download', 'ModernPaymentControlle@accounts_payments_verify_download');
		Route::get('/accounts_payments_rece_report_download', 'ModernPaymentControlle@accounts_payments_rece_report_download');
		Route::post('/account_payment_receive', 'ModernPaymentControlle@account_payment_receive');
		Route::get('/accounts_payments_rece_report', 'ModernPaymentControlle@accounts_payments_rece_report');
		Route::get('/accounts_payments_rece_report_list', 'ModernPaymentControlle@accounts_payments_rece_report_list');

		// Payment ack
		Route::get('/accounts-payments-ack', 'ModernPaymentControlle@accounts_payments_ack');
		Route::get('/accounts-payments-ack-list', 'ModernPaymentControlle@accounts_payments_ack_list');
		Route::get('/accounts-payments-undo/{transaction_id}', 'ModernPaymentControlle@accounts_payments_undo');
		Route::get('/accounts-payments-verify', 'ModernPaymentControlle@accounts_payments_verify');
		Route::get('/accounts-payments-verify-list', 'ModernPaymentControlle@accounts_payments_verify_list');

		Route::post('/accounts-payments-verify-process', 'ModernPaymentControlle@accounts_payments_verify_process');
		//// customer ledger report/////

		Route::get('/customer-ledger', 'ModernAdminController@customer_ledger');
		Route::post('/customer-ledger-list', 'ModernAdminController@customer_ledger_list');

		//// customer Stock report/////

		Route::get('/customer-stock', 'ModernAdminController@customer_stock');
		Route::post('/customer-stock-list', 'ModernAdminController@customer_stock_list');

		///////add user////
		Route::get('/mts-user-list', 'ModernAdminController@mts_user_list');

		///////add user////
		Route::post('/mts-user-add-process', 'ModernAdminController@mts_user_add_process');
		Route::post('/mts-user-edit', 'ModernAdminController@mts_user_edit');
		Route::post('/mts-user-edit-process', 'ModernAdminController@mts_user_edit_process');
		Route::get('/mts-user-active/{id}','ModernAdminController@mts_user_active');
		Route::get('/mts-user-inactive/{id}','ModernAdminController@mts_user_inactive');
		Route::get('/mts-user-delete/{id}','ModernAdminController@mts_user_delete');


		///////add Bank Account////
		Route::get('/mts-bank-account', 'ModernAdminController@mts_bank_account_list');
		Route::get('/mts-bank-account-list', 'ModernAdminController@mts_bank_account_list');
 
		Route::post('/mts-bank-account-add-process', 'ModernAdminController@mts_bank_account_add_process');
		Route::post('/mts-bank-account-edit', 'ModernAdminController@mts_bank_account_edit');
		Route::post('/mts-bank-account-edit-process', 'ModernAdminController@mts_bank_account_edit_process');
		Route::get('/mts-bank-account-active/{id}/{type}','ModernAdminController@mts_bank_account_active'); 
		Route::get('/mts-bank-account-delete/{id}','ModernAdminController@mts_bank_account_delete');

			// Analysis Requisition Manage

			//Route::get('/modern-reqPendingList', 'BillingRequisition@req_pending_list');
		Route::post('/modern-reqAcknowledge', 'ModernDeliveryController@req_acknowledge_new');

			// Route::get('/modern-reqAllAnalysisList', 'BillingRequisition@req_analysis_list_new');
		Route::get('/modern-reqOrderAnalysis/{prodid}/{div_id}', 'BillingRequisition@req_order_analysis_new');

		Route::post('/modern-reqApproved', 'BillingRequisition@req_approved_new');
		Route::get('/modern-reqAllApprovedList', 'BillingRequisition@req_all_approved_list');

		Route::get('/modern-reqBilled/{reqid}', 'BillingRequisition@req_billed_process'); // this is work for confirm billing 
		Route::post('ready-for-billed-bulk', 'BillingRequisition@readyForBilledBulk');
		Route::get('/modern-reqBilledList', 'BillingRequisition@req_billed_list');
		Route::get('/modern-reqAllBilledList', 'BillingRequisition@req_all_billed_list');
		Route::get('/modern-reqBillDetails/{reqid}', 'BillingRequisition@req_bill_details_list');

		Route::get('/modern-reqOpenOrderList/{reqid}', 'BillingRequisition@req_open_order_list');

		Route::get('modern-export-sales-order', 'BillingRequisition@export_sale_order');

		// Master list

		Route::get('modern-manager-list', 'ModernAdminController@manager_list'); 
		Route::get('modern-executive-list', 'ModernAdminController@executive_list');
		Route::get('modern-officer-list', 'ModernAdminController@officer_list');

		Route::get('mts-officer-customer-list', 'ModernAdminController@mts_officer_customer_list');

		// --- Sharif start target file upload -- //

		Route::get('/modern_target_upload','MasterUploadController@modern_target_list');
		Route::post('/modern_target_file_upload','MasterUploadController@modernTargetUpload');
		Route::post('/modern-target-edit','MasterUploadController@modern_target_edit');
		Route::post('/modern-target-edit-process','MasterUploadController@modern_target_edit_process');
		Route::get('/modern-target-search','MasterUploadController@modern_target_search');
		Route::get('/mts-target-delete/{id}','MasterUploadController@mts_target_delete');


		Route::get('/download-customers', 'MasterUploadController@downloadCustomers');
		Route::get('/customer-last-balance-upload','MasterUploadController@customerBalanceUpload'); 
		Route::post('/customer-last-balance-upload-process','MasterUploadController@customerBalanceUploadSubmit'); 
		Route::get('/customer-last-balance-filter','MasterUploadController@customerBalanceFilter'); 
		// Supervisor create

		Route::get('/create-supervisor', 'HierarchyController@create_supervisor');
		Route::get('/type-user-list','HierarchyController@get_type_user_list');

		Route::get('/mts-customer-list', 'ModernAdminController@customer_list');
		Route::post('/modern-customer-edit', 'ModernAdminController@customer_edit');
		Route::post('/modern-customer-edit-process', 'ModernAdminController@modern_customer_edit_process');
		Route::post('/customer-create', 'ModernAdminController@customer_create');

		Route::get('/mts-customer-active/{id}','ModernAdminController@mts_customer_active');
		Route::get('/mts-customer-inactive/{id}','ModernAdminController@mts_customer_inactive');
		Route::get('/mts-customer-delete/{id}','ModernAdminController@mts_customer_delete');

		Route::get('/mts-outlet-list', 'ModernAdminController@outlet_list');
		Route::get('/mts-outlet-delete/{id}', 'ModernAdminController@mts_outlet_delete');

		Route::post('/modern-outlet-edit', 'ModernAdminController@outlet_edit');
		Route::post('/modern-outlet-edit-process', 'ModernAdminController@modern_outlet_edit_process');
		Route::post('/outlet-create', 'ModernAdminController@outlet_create');

		Route::get('/mts-outlet-active/{id}','ModernAdminController@mts_outlet_active');
		Route::get('/mts-outlet-inactive/{id}','ModernAdminController@mts_outlet_inactive');
		Route::get('/mts-all-outlet-list','ModernAdminController@mts_all_outlet_list');
		Route::get('/mts-product-list', 'ModernAdminController@product_list');
		Route::post('/product-create', 'ModernAdminController@product_create');
		Route::post('/modern-product-edit', 'ModernAdminController@modern_product_edit');
		Route::post('/modern-product-edit-process', 'ModernAdminController@modern_product_edit_process');
		Route::get('/mts-all-product-list','ModernAdminController@mts_all_product_list');
		Route::get('/mts-category-list','ModernAdminController@mts_category_list');

		Route::get('/mts-product-active/{id}','ModernAdminController@mts_product_active');
		Route::get('/mts-product-inactive/{id}','ModernAdminController@mts_product_inactive');
		Route::get('/mts-product-delete', 'ModernAdminController@mts_product_delete');
		// Manager Part 

	    Route::get('/mts-manager-delivery', 'ModernManagerReportController@ssg_manager_delivery');
		Route::post('/mts-manager-delivery-list', 'ModernManagerReportController@ssg_manager_delivery_list');
		Route::get('/mts-manager-delivery-details/{orderMainId}', 'ModernManagerReportController@ssg_manager_delivery_details');

		Route::get('/mts-manager-delivery-ims-wise', 'ModernManagerReportController@ssg_manager_delivery_ims_wise');
		Route::post('/mts-manager-delivery-ims-wise-list', 'ModernManagerReportController@ssg_manager_delivery_list_ims_wise');

		Route::get('/mts-manager-replace-delivery-report', 'ModernManagerReportController@mts_manager_replace_delivery_report');
		Route::get('/mts-manager-replace-delivery-report-list', 'ModernManagerReportController@mts_manager_replace_delivery_report_list');
		Route::get('/mts-manager-replace-delivery-report-details/{orderMainId}', 'ModernManagerReportController@mts_manager_replace_delivery_report_details');

		// Return Delivery Report Manager part

		Route::get('/mts-manager-return-delivery-report', 'ModernManagerReportController@mts_manager_return_delivery_report');
		Route::get('/mts-manager-return-delivery-report-list', 'ModernManagerReportController@mts_manager_return_delivery_report_list');
		Route::get('/mts-manager-return-delivery-report-details/{orderMainId}', 'ModernManagerReportController@mts_manager_return_delivery_report_details');



		Route::get('/mst-supervisor', 'ModernSupervisorController@define_supervisor');
		Route::get('/mst-get_supervisor/{id}','ModernSupervisorController@get_supervisor');
		Route::post('/mst-supervisor_edit/{id}','ModernSupervisorController@supervisor_edit'); 
		Route::post('/mst-supervisor_save','ModernSupervisorController@supervisor_save');
		Route::get('/mst-supervisor_delete/{id}','ModernSupervisorController@supervisor_delete');
		Route::get('/mst-get_supervisor_list','ModernSupervisorController@get_supervisor_list');

		Route::get('/mts-report/sales-report','ModernReportsController@index');
		Route::get('/mts-report/sales-report-list','ModernReportsController@get_sales_reports');
		Route::get('/mts-report/officer-Wise-customer','ModernReportsController@mts_officer_customer_list');
		Route::get('/mts-report/executive-Wise-officer','ModernReportsController@executiveWiseOfficer');

		Route::get('/mts-achievement-sales-report', 'ModernManagerReportController@achievement_sales_report');
		Route::post('/mts-achievement-sales-report-list', 'ModernManagerReportController@achievement_sales_report_list');
		Route::get('/products-upload', 'ModernAdminController@productUpload');
		Route::post('/products-upload', 'ModernAdminController@productsUpload');
		Route::post('/products-uploads', 'ModernAdminController@productsUpload2');
		Route::get('/products-upload-format/{file_name}', 'ModernAdminController@productsUploadFormat');


		Route::get('/mts-category', 'CategoryController@index');
		Route::post('/mts-category', 'CategoryController@create');
		Route::get('/mts-category-edit', 'CategoryController@edit');
		Route::post('/mts-category-edit', 'CategoryController@update');

		Route::get('/inventory-management', 'ModernPaymentControlle@inventoryManagement');
		Route::post('/inventory-management-process', 'ModernPaymentControlle@inventoryManagementProcess');
		Route::get('/mts-product-download', 'ModernPaymentControlle@productDownload');
		Route::get('/mts-inventory-report', 'ModernPaymentControlle@mtsInventoryReport');
		Route::get('/mts-download-stock-report', 'ModernPaymentControlle@downloadInventoryReport');
		
    }
);
