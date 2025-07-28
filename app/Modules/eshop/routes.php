<?php
Route::group(
    ['middleware' => ['web'], 'module' => 'eshop', 'namespace' => 'App\Modules\eshop\Controllers'], function () {
	//Route::get('/eshop-approved-order-mail/{orderid}/{partyid}/{status}', 'EshopAdminController@eshop_approved_order');

	}
);


Route::get('/eshop-approved-order-mail/{orderid}/{partyid}/{status}', 'App\Modules\eshop\Controllers\EshopAdminController@eshop_approved_order_by_mail');
Route::get('/eshop-approved-success', 'App\Modules\eshop\Controllers\EshopAdminController@eshop_approved_success');

Route::group(
    ['middleware' => ['web', 'auth'], 'module' => 'eshop', 'namespace' => 'App\Modules\eshop\Controllers'], function () {
        Route::get('/eshop', 'EshopSalesController@index');

        /////////////////////Sharifur Rahman part of Start ////////////////////

        // Order for field Executive

        Route::get('/eshop-visit', 'EshopVisitController@eshop_visit');
        Route::get('/eshop-partyList', 'EshopVisitController@eshop_partyList');
        Route::get('/eshop-order-process/{partyid}/{customer_id}', 'EshopVisitController@eshop_order_process');
        Route::get('/eshop-category-products', 'EshopVisitController@eshop_category_products');
        Route::post('/eshop-add-to-cart', 'EshopVisitController@eshop_add_to_cart_products');
        Route::post('/e-shop-csv-file/{partyid}/{customer_id}', 'EshopVisitController@eshop_csv_file_to_cart_products');

		Route::get('/eshop-bucket/{customer_id}/{partyid}', 'EshopVisitController@eshop_bucket');

		Route::post('/eshop-items-edit', 'EshopVisitController@eshop_items_edit');
		Route::post('/eshop-edit-submit', 'EshopVisitController@eshop_items_edit_submit');
		Route::get('/eshop-items-delete/{orderid}/{itemid}/{customer_id}/{partyid}/{catid}', 'EshopVisitController@eshop_items_delete');

		Route::post('/eshop-confirm-order', 'EshopVisitController@eshop_confirm_order');
 
		Route::get('/eshop-delete-order/{orderid}/{partyid}/{customer_id}', 'EshopVisitController@eshop_delete_order');


		// Report for field Executive

		Route::get('/eshop-order-report', 'EshopExecutiveReportController@eshop_order_report');
		Route::post('/eshop-order-report-list', 'EshopExecutiveReportController@eshop_order_report_list');
		Route::get('/eshop-order-details/{orderMainId}', 'EshopExecutiveReportController@eshop_order_details');

		Route::get('/eshop-delivery-report', 'EshopExecutiveReportController@eshop_delivery_report');
		Route::post('/eshop-delivery-report-list', 'EshopExecutiveReportController@eshop_delivery_report_list');
		Route::get('/eshop-delivery-details/{orderMainId}', 'EshopExecutiveReportController@eshop_delivery_details');

		// Not approve sales order

		Route::get('/eshop-order-not-approve', 'EshopVisitController@eshop_order_not_approve');
		Route::post('/eshop-order-not-approve-list', 'EshopVisitController@eshop_order_not_approve_list');
		// Sales order manage
		Route::get('/eshop-order-manage', 'EshopVisitController@eshop_order_manage');
		Route::get('/eshop-bucket-manage/{order_id}/{customer_id}/{partyid}', 'EshopVisitController@eshop_bucket_manage');

		// Visit module
		Route::get('/eshop-visit-order/{party_id}/{customer_id}', 'EshopVisitController@eshop_order_visit');
		Route::post('/eshop-visit-process-submit', 'EshopVisitController@eshop_visit_process_submit');
		Route::get('/eshop-nonvisit/{party_id}/{customer_id}', 'EshopVisitController@eshop_nonvisit');
		Route::post('/eshop-nonvisit-process-submit', 'EshopVisitController@eshop_nonvisit_process_submit');
		// Eshop Admin part

		Route::get('/eshop-approved', 'EshopAdminController@eshop_approved');
		Route::get('/eshop-approved-list', 'EshopAdminController@eshop_approved_list');

		Route::get('/eshop-order-view/{wastageMainId}/{foMainId}', 'EshopAdminController@eshop_order_view');

		Route::get('/eshop-approved-order/{orderid}/{partyid}/{status}', 'EshopAdminController@eshop_approved_order'); 
		//Route::get('/eshop-email-approval/{orderid}/{foMainId}/{partyid}', 'EshopAdminController@eshop_email_approval');
		Route::post('/eshop-email-approval/{orderid}/{foMainId}/{partyid}', 'EshopAdminController@eshop_email_approval');

		Route::post('/eshop-not-approved-remarks-submit', 'EshopAdminController@not_approved_remarks_submit');
		 
		
		// Opening blance start

		Route::get('/eshop-opening-route', 'EshopPaymentControlle@eshop_opening_route');
        Route::get('/eshop-opening-outlet', 'EshopPaymentControlle@eshop_opening_outlet');
        Route::get('/eshop-add-opening-balance', 'EshopPaymentControlle@eshop_add_opening_balance');

        // delivery report part of admin

        Route::get('/eshop-admin-delivery', 'EshopAdminController@ssg_report_order_delivery');
		Route::post('/eshop-admin-delivery-list', 'EshopAdminController@ssg_report_order_list');
		Route::get('/eshop-admin-delivery-details/{orderMainId}', 'EshopAdminController@ssg_order_details');

		// Payment report part of admin

		Route::get('/eshop-admin-payment', 'EshopPaymentControlle@eshop_admin_payment');
		Route::get('/eshop-admin-payment-list', 'EshopPaymentControlle@eshop_admin_payment_list');
		// Credit Adjustment

		Route::get('/eshop-credit-adjustment', 'EshopPaymentControlle@credit_adjustment');
		Route::get('/eshop-credit-adjustment-list', 'EshopPaymentControlle@credit_adjustment_list');
		Route::post('/eshop-credit-adjustment-process', 'EshopPaymentControlle@credit_adjustment_process');


		// Advance Replace start

        Route::get('/eshop-replace', 'EshopReplaceController@eshop_replace');
        Route::get('/eshop-replace-outlet-list', 'EshopReplaceController@eshop_replace_outlet_list');
        Route::get('/eshop-replace-process/{partyid}/{customer_id}', 'EshopReplaceController@eshop_replace_process');
        Route::get('/eshop-replace-category-products', 'EshopReplaceController@eshop_replace_category_products');
        Route::post('/eshop-add-replace-products', 'EshopReplaceController@eshop_add_replace_products');
		Route::get('/eshop-replace-bucket/{customer_id}/{partyid}', 'EshopReplaceController@eshop_replace_bucket');
		Route::post('/eshop-replace-items-edit', 'EshopReplaceController@eshop_replace_items_edit');
		Route::post('/eshop-replace-edit-submit', 'EshopReplaceController@eshop_replace_edit_submit');
		Route::get('/eshop-replace-items-delete/{replace_id}/{itemid}', 'EshopReplaceController@eshop_replace_items_delete');
		Route::post('/eshop-confirm-replace', 'EshopReplaceController@eshop_confirm_replace');
		Route::get('/eshop-delete-replace/{replace_id}/{partyid}/{customer_id}', 'EshopReplaceController@eshop_delete_replace');

		// Not approve Advance Replace

		Route::get('/eshop-replace-not-approve', 'EshopReplaceController@eshop_replace_not_approve');
		Route::post('/eshop-replace-not-approve-list', 'EshopReplaceController@eshop_replace_not_approve_list');

		// Advance Replace Eshop Admin part

		Route::get('/eshop-replace-approved', 'EshopReplaceController@eshop_replace_approved');
		Route::get('/eshop-replace-approved-list', 'EshopReplaceController@eshop_replace_approved_list');

		Route::get('/eshop-replace-view/{wastageMainId}/{foMainId}', 'EshopReplaceController@eshop_replace_view');

		Route::get('/eshop-approved-replace/{orderid}/{partyid}/{status}', 'EshopReplaceController@eshop_approved_replace');

		// Advance Replace Eshop Billing part

		Route::get('/eshop-replace-delivery', 'EshopReplaceController@eshop_replace_delivery');
		Route::get('/eshop-replace-delivery-list', 'EshopReplaceController@eshop_replace_delivery_list');

		Route::get('/eshop-replace-delivery-edit/{DeliveryMainId}/{foMainId}', 'EshopReplaceController@eshop_replace_delivery_edit');
		Route::post('/eshop-replace-delivery-edit-submit', 'EshopReplaceController@eshop_replace_delivery_edit_submit');

		// Advance Replace Delivery Report admin part

		Route::get('/eshop-admin-replace-delivery-report', 'EshopReplaceController@eshop_admin_replace_delivery_report');
		Route::get('/eshop-admin-replace-delivery-report-list', 'EshopReplaceController@eshop_admin_replace_delivery_report_list');
		Route::get('/eshop-admin-replace-delivery-report-details/{orderMainId}', 'EshopReplaceController@eshop_admin_replace_delivery_report_details');

		// Advance Replace Delivery Report Billing part

		Route::get('/eshop-billing-replace-delivery-report', 'EshopReplaceController@eshop_billing_replace_delivery_report');
		Route::get('/eshop-billing-replace-delivery-report-list', 'EshopReplaceController@eshop_billing_replace_delivery_report_list');
		Route::get('/eshop-billing-replace-delivery-report-details/{orderMainId}', 'EshopReplaceController@eshop_billing_replace_delivery_report_details');

		// Advance Replace Delivery Report Executive part

		Route::get('/eshop-replace-delivery-report', 'EshopReplaceController@eshop_replace_delivery_report');
		Route::get('/eshop-replace-delivery-report-list', 'EshopReplaceController@eshop_replace_delivery_report_list');
		Route::get('/eshop-replace-delivery-report-details/{orderMainId}', 'EshopReplaceController@eshop_replace_delivery_report_details');


		// Advance Replace Approved for admin part

		Route::get('/eshop-replace-delivery-approved', 'EshopReplaceController@eshop_replace_delivery_approved');
		Route::get('/eshop-replace-delivery-approved-list', 'EshopReplaceController@eshop_replace_delivery_approved_list');

		Route::get('/eshop-replace-delivery-approved-view/{orderMainId}/{foMainId}', 'EshopReplaceController@eshop_replace_delivery_approved_view');
		Route::get('/eshop-replace-delivery-approved-submit/{orderid}/{customerid}/{status}', 'EshopReplaceController@eshop_replace_delivery_approved_submit');

		// Return start

        Route::get('/eshop-return', 'EshopReturnController@eshop_return');
        Route::get('/eshop-return-outlet-list', 'EshopReturnController@eshop_return_outlet_list');
        Route::get('/eshop-return-process/{partyid}/{customer_id}', 'EshopReturnController@eshop_return_process');
        Route::get('/eshop-return-category-products', 'EshopReturnController@eshop_return_category_products');
        Route::post('/eshop-add-return-products', 'EshopReturnController@eshop_add_return_products');
		Route::get('/eshop-return-bucket/{customer_id}/{partyid}', 'EshopReturnController@eshop_return_bucket');
		Route::post('/eshop-return-items-edit', 'EshopReturnController@eshop_return_items_edit');
		Route::post('/eshop-return-edit-submit', 'EshopReturnController@eshop_return_edit_submit');
		Route::get('/eshop-return-items-delete/{return_id}/{itemid}', 'EshopReturnController@eshop_return_items_delete');
		Route::post('/eshop-confirm-return', 'EshopReturnController@eshop_confirm_return');
		Route::get('/eshop-delete-return/{return_id}/{partyid}/{customer_id}', 'EshopReturnController@eshop_delete_return'); 
		// Not approve Advance Replace

		Route::get('/eshop-return-not-approve', 'EshopReturnController@eshop_return_not_approve');
		Route::post('/eshop-return-not-approve-list', 'EshopReturnController@eshop_return_not_approve_list');

		// Return Eshop Admin part

		Route::get('/eshop-return-approved', 'EshopReturnController@eshop_return_approved');
		Route::get('/eshop-return-approved-list', 'EshopReturnController@eshop_return_approved_list');

		Route::get('/eshop-return-view/{wastageMainId}/{foMainId}', 'EshopReturnController@eshop_return_view');

		Route::get('/eshop-approved-return/{orderid}/{partyid}/{status}', 'EshopReturnController@eshop_approved_return');


		Route::get('/eshop-return-delivery-approved', 'EshopReturnController@eshop_return_delivery_approved');
		Route::get('/eshop-return-delivery-approved-list', 'EshopReturnController@eshop_return_delivery_approved_list');

		Route::get('/eshop-return-delivery-approved-view/{orderMainId}/{foMainId}', 'EshopReturnController@eshop_return_delivery_approved_view');
		Route::get('/eshop-return-delivery-approved-submit/{orderid}/{customerid}/{status}', 'EshopReturnController@eshop_return_delivery_approved_submit');

		// Return Eshop Billing part

		Route::get('/eshop-return-delivery', 'EshopReturnController@eshop_return_delivery');
		Route::get('/eshop-return-delivery-list', 'EshopReturnController@eshop_return_delivery_list');

		Route::get('/eshop-return-delivery-edit/{DeliveryMainId}/{foMainId}', 'EshopReturnController@eshop_return_delivery_edit');
		Route::post('/eshop-return-delivery-edit-submit', 'EshopReturnController@eshop_return_delivery_edit_submit');

		// Return Delivery Report admin part

		Route::get('/eshop-admin-return-delivery-report', 'EshopReturnController@eshop_admin_return_delivery_report');
		Route::get('/eshop-admin-return-delivery-report-list', 'EshopReturnController@eshop_admin_return_delivery_report_list');
		Route::get('/eshop-admin-return-delivery-report-details/{orderMainId}', 'EshopReturnController@eshop_admin_return_delivery_report_details');


		// Return Delivery Report Billing part

		Route::get('/eshop-billing-return-delivery-report', 'EshopReturnController@eshop_billing_return_delivery_report');
		Route::get('/eshop-billing-return-delivery-report-list', 'EshopReturnController@eshop_billing_return_delivery_report_list');
		Route::get('/eshop-billing-return-delivery-report-details/{orderMainId}', 'EshopReturnController@eshop_billing_return_delivery_report_details');

		// Return Delivery Report Executive part

		Route::get('/eshop-return-delivery-report', 'EshopReturnController@eshop_return_delivery_report');
		Route::get('/eshop-return-delivery-report-list', 'EshopReturnController@eshop_return_delivery_report_list');
		Route::get('/eshop-return-delivery-report-details/{orderMainId}', 'EshopReturnController@eshop_return_delivery_report_details');

		/////////////////////Sharifur Rahman part of End////////////////////

		
		/////////////////////Md. Sazzadul islam////////////////////

		Route::post('/eshop-orderDelivery-open-submit', 'EshopDeliveryController@ssg_eshop_open_submit');
		Route::get('/eshop-product-wise-analysis/{id}', 'EshopDeliveryController@product_wise_analysis');
		Route::get('/eshop-reqAllAnalysisList', 'EshopDeliveryController@eshop_delivery_analysis');
		Route::get('/eshop-reqAllAnalysisList-view', 'EshopDeliveryController@eshop_delivery_analysis_list');


		Route::get('/eshop-delivery', 'EshopDeliveryController@eshop_delivery');
		Route::get('/eshop-delivery-list', 'EshopDeliveryController@ssg_delivery_list');
		Route::get('/eshop-delivery-report', 'EshopDeliveryController@eshop_delivery_report');

		///eshop-summary-report
		Route::get('/eshop-summary-report', 'EshopDeliveryController@eshop_summary_report');
		Route::get('/eshop-stock-report', 'EshopDeliveryController@eshop_summary_report');
		Route::get('/eshop-stock-report-download/{sdate?}/{edate?}', 'EshopDeliveryController@eshop_stock_report_download');
		Route::post('/eshop_summary_report_ajax', 'EshopDeliveryController@eshop_summary_report_ajax');///eshop-summary-report
		Route::get('/eshop-customer-summary-report', 'EshopDeliveryController@eshop_customer_wise_summary');
		Route::get('/eshop_customer_summary_report_ajax', 'EshopDeliveryController@eshop_customer_wise_summary_ajax');

		Route::get('/eshop-orderDelivery-edit/{wastageMainId}/{foMainId}', 'EshopDeliveryController@ssg_order_edit');
		Route::post('/eshop-orderDelivery-edit-submit', 'EshopDeliveryController@ssg_Eshop_edit_submit');

		
		Route::get('/eshop-billing-delivery-report', 'EshopDeliveryController@ssg_report_order_delivery');
		Route::post('/eshop-delivery-list', 'EshopDeliveryController@ssg_report_order_list');
		Route::get('/eshop-order-details/{orderMainId}', 'EshopDeliveryController@ssg_order_details');
		//////////////payment////////////////
		
		Route::get('/eshop_outlet_payments', 'EshopPaymentControlle@outlet_payments');
		Route::get('/eshop_outlet-payment-list', 'EshopPaymentControlle@outlet_payment_list');
		Route::post('/eshop_outlet_paymnet_process', 'EshopPaymentControlle@outlet_paymnet_process');
		Route::get('/eshop-outlet-payments-delete/{paymentid}/{type}', 'EshopPaymentControlle@eshop_outlet_payments_delete');
		Route::get('/eshop_admin_payments_con', 'EshopPaymentControlle@admin_payments_con');
		Route::get('/eshop_admin_payments_con_list', 'EshopPaymentControlle@admin_payments_con_list');
		Route::get('/eshop_app_admin_payment/{id}/{status}', 'EshopPaymentControlle@app_admin_payment');

		Route::get('/eshop_app_admin_payment_edit', 'EshopPaymentControlle@app_admin_payment_edit');
		Route::post('/eshop_app_admin_payment_edit_submit', 'EshopPaymentControlle@app_admin_payment_edit_submit');

		Route::get('/eshop_accounts_payments_con', 'EshopPaymentControlle@accounts_payments_con');
		Route::get('/eshop_accounts_payments_con_list', 'EshopPaymentControlle@accounts_payments_con_list');
		Route::post('/eshop_account_payment_receive', 'EshopPaymentControlle@account_payment_receive');
		Route::get('/eshop_accounts_payments_rece_report', 'EshopPaymentControlle@accounts_payments_rece_report');
		Route::get('/eshop_accounts_payments_rece_report_list', 'EshopPaymentControlle@accounts_payments_rece_report_list');

		// Payment ack
		Route::get('/eshop-accounts-payments-ack', 'EshopPaymentControlle@accounts_payments_ack');
		Route::get('/eshop-accounts-payments-ack-list', 'EshopPaymentControlle@accounts_payments_ack_list');
		Route::get('/eshop-accounts-payments-undo/{transaction_id}', 'EshopPaymentControlle@accounts_payments_undo');
		Route::get('/eshop-accounts-payments-verify', 'EshopPaymentControlle@accounts_payments_verify');
		Route::get('/eshop-accounts-payments-verify-list', 'EshopPaymentControlle@accounts_payments_verify_list');

		Route::post('/eshop-accounts-payments-verify-process', 'EshopPaymentControlle@accounts_payments_verify_process');
		//// customer ledger report/////

		Route::get('/eshop-customer-ledger', 'EshopAdminController@customer_ledger');
		Route::post('/eshop-customer-ledger-list', 'EshopAdminController@customer_ledger_list');

		//// customer Stock report/////

		Route::get('/eshop-customer-stock', 'EshopAdminController@customer_stock');
		Route::post('/eshop-customer-stock-list', 'EshopAdminController@customer_stock_list');
		/////// e_shop_customer  ////////
		Route::get('/eshop-customer', 'EshopCustromerController@customer_list');
		Route::post('/eshop-customer-create', 'EshopCustromerController@customer_create');
		Route::post('/eshop-customer-edit', 'EshopCustromerController@customer_edit');


		Route::get('/eshop-customer-active/{id}','EshopCustromerController@eshop_customer_active');
		Route::get('/eshop-customer-inactive/{id}','EshopCustromerController@eshop_customer_inactive');
 
		Route::get('/eshop-customer-delete/{id}','EshopCustromerController@customer_delete');
		//e-shop-customer-edit-process
		Route::post('/eshop-customer-edit-process', 'EshopCustromerController@customer_edit_process');



		///////add user////
		Route::get('/eshop-addUser', 'EshopAdminController@addUser');



			// Analysis Requisition Manage

			//Route::get('/Eshop-reqPendingList', 'BillingRequisition@req_pending_list');
			Route::post('/eshop-reqAcknowledge', 'EshopDeliveryController@req_acknowledge_new');

			// Route::get('/Eshop-reqAllAnalysisList', 'BillingRequisition@req_analysis_list_new');
			Route::get('/eshop-reqOrderAnalysis/{prodid}/{div_id}', 'BillingRequisition@req_order_analysis_new');

			Route::post('/eshop-reqApproved', 'BillingRequisition@req_approved_new');
			Route::get('/eshop-reqAllApprovedList', 'BillingRequisition@req_all_approved_list');

			Route::get('/eshop-reqBilled/{reqid}', 'BillingRequisition@req_billed_process');
			Route::get('/eshop-reqBilledList', 'BillingRequisition@req_billed_list');
			Route::get('/eshop-reqAllBilledList', 'BillingRequisition@req_all_billed_list');
			Route::get('/eshop-reqBillDetails/{reqid}', 'BillingRequisition@req_bill_details_list');

			Route::get('/eshop-reqOpenOrderList/{reqid}', 'BillingRequisition@req_open_order_list');

			Route::get('eshop-export-sales-order', 'BillingRequisition@export_sale_order');
			Route::get('eshop-order-confirm-download', 'BillingRequisition@orderCconfirmDownload');

			// Master list

			Route::get('eshop-manager-list', 'EshopAdminController@manager_list'); 
			Route::get('eshop-executive-list', 'EshopAdminController@executive_list');
			Route::get('eshop-officer-list', 'EshopAdminController@officer_list');

			Route::get('eshop-officer-customer-list', 'EshopAdminController@eshop_officer_customer_list');
			Route::get('eshop-officer-outlet-list', 'EshopAdminController@eshop_customer_outlet_list');

			// --- Sharif start target file upload -- //

			Route::get('/eshop_target_upload','MasterUploadController@Eshop_target_list');
			Route::post('/eshop_target_file_upload','MasterUploadController@EshopTargetUpload');
			Route::post('/eshop-target-edit','MasterUploadController@Eshop_target_edit');
			Route::post('/eshop-target-edit-process','MasterUploadController@Eshop_target_edit_process');
			Route::get('/eshop-target-search','MasterUploadController@Eshop_target_search');

			Route::get('/eshop-target-delete/{id}','MasterUploadController@eshop_target_delete');

			// Supervisor create

			Route::get('/eshop-create-supervisor', 'HierarchyController@eshop_create_supervisor');
			Route::get('/eshop-type-user-list','HierarchyController@get_type_user_list');

			Route::get('/eshop-customer-list', 'EshopAdminController@eshop_customer_list');
			Route::post('/eshop-customer-edit', 'EshopAdminController@eshop_customer_edit');
			Route::post('/eshop-customer-edit-process', 'EshopAdminController@eshop_customer_edit_process');
			Route::post('/eshop-customer-create', 'EshopAdminController@eshop_customer_create');

			Route::get('/eshop-customer-active/{id}','EshopAdminController@eshop_customer_active');
			Route::get('/eshop-customer-inactive/{id}','EshopAdminController@eshop_customer_inactive');

			Route::get('/eshop-outlet-list', 'EshopAdminController@eshop_outlet_list');
			Route::post('/eshop-outlet-edit', 'EshopAdminController@eshop_outlet_edit');
			Route::post('/eshop-outlet-edit-process', 'EshopAdminController@eshop_outlet_edit_process');
			Route::post('/eshop-outlet-create', 'EshopAdminController@eshop_outlet_create');

			Route::get('/eshop-outlet-active/{id}','EshopAdminController@eshop_outlet_active');
			Route::get('/eshop-outlet-inactive/{id}','EshopAdminController@eshop_outlet_inactive');
			Route::get('/eshop-all-outlet-list','EshopAdminController@eshop_all_outlet_list');
			Route::get('/eshop-product-list', 'EshopAdminController@eshop_product_list');
			Route::post('/eshop-product-create', 'EshopAdminController@eshop_product_create');
			Route::post('/eshop-product-edit', 'EshopAdminController@eshop_product_edit');
			Route::post('/eshop-product-edit-process', 'EshopAdminController@eshop_product_edit_process');
			Route::get('/eshop-all-product-list','EshopAdminController@eshop_all_product_list');
			Route::get('/eshop-category-list','EshopAdminController@eshop_category_list');

			Route::get('/eshop-product-active/{id}','EshopAdminController@eshop_product_active');
			Route::get('/eshop-product-inactive/{id}','EshopAdminController@eshop_product_inactive');

			// Manager Part 
    
		    Route::get('/eshop-manager-delivery', 'EshopManagerReportController@ssg_manager_delivery');
			Route::post('/eshop-manager-delivery-list', 'EshopManagerReportController@ssg_manager_delivery_list');
			Route::get('/eshop-manager-delivery-details/{orderMainId}', 'EshopManagerReportController@ssg_manager_delivery_details');


			Route::get('/eshop-manager-replace-delivery-report', 'EshopManagerReportController@eshop_manager_replace_delivery_report');
			Route::get('/eshop-manager-replace-delivery-report-list', 'EshopManagerReportController@eshop_manager_replace_delivery_report_list');
			Route::get('/eshop-manager-replace-delivery-report-details/{orderMainId}', 'EshopManagerReportController@eshop_manager_replace_delivery_report_details');

			// Return Delivery Report Manager part

			Route::get('/eshop-manager-return-delivery-report', 'EshopManagerReportController@eshop_manager_return_delivery_report');
			Route::get('/eshop-manager-return-delivery-report-list', 'EshopManagerReportController@eshop_manager_return_delivery_report_list');
			Route::get('/eshop-manager-return-delivery-report-details/{orderMainId}', 'EshopManagerReportController@eshop_manager_return_delivery_report_details');

			Route::get('/eshop-money-recept-print/{transaction_id}', 'EshopPaymentControlle@eshop_money_receipt');


			// Define Supervisor 

			Route::get('/user_supervisor', 'EshopSupervisorController@define_supervisor');
			Route::get('/get_supervisor/{id}','EshopSupervisorController@get_supervisor');
			Route::post('/supervisor_edit/{id}','EshopSupervisorController@supervisor_edit'); 
			Route::post('/supervisor_save','EshopSupervisorController@supervisor_save');
			Route::get('/supervisor_delete/{id}','EshopSupervisorController@supervisor_delete');
			Route::get('/get_supervisor_list','EshopSupervisorController@get_supervisor_list');


			// Route::get('/user_supervisor', 'EshopSupervisorController@define_supervisor');
			// Route::get('/get_supervisor/{id}','EshopSupervisorController@get_supervisor');
			// Route::post('/supervisor_edit/{id}','EshopSupervisorController@supervisor_edit');
			// Route::get('/get_user_list','EshopSupervisorController@get_user_list');
			// Route::get('/get_supervisor_list','EshopSupervisorController@get_supervisor_list');
			// Route::post('/supervisor_save','EshopSupervisorController@supervisor_save');
			// Route::get('/supervisor_delete/{id}','EshopSupervisorController@supervisor_delete');

			// Delivery Approved for admin part

			Route::get('/eshop-delivery-approved', 'EshopAdminController@eshop_delivery_approved');
			Route::get('/eshop-delivery-approved-list', 'EshopAdminController@eshop_delivery_approved_list');

			Route::get('/eshop-delivery-approved-view/{orderMainId}/{foMainId}', 'EshopAdminController@eshop_delivery_approved_view');
			Route::get('/eshop-approved-delivery2/{orderid}/{customerid}/{status}', 'EshopAdminController@eshop_approved_delivery2');
			Route::post('/eshop-approved-delivery', 'EshopAdminController@eshop_approved_delivery');
		
			Route::get('/eshop-make-invoice', 'EshopAdminController@eshop_make_invoice');
			Route::get('/eshop-make-invoice-list', 'EshopAdminController@eshop_make_invoice_list');
			Route::get('/eshop-make-invoice-view/{orderMainId}/{foMainId}', 'EshopAdminController@eshop_make_invoice_view');
			Route::post('/eshop-make-invoice-stock-out', 'EshopAdminController@eshop_make_invoice_stock_out');
			Route::get('/eshop-make-invoice-stock-out2/{orderMainId}/{foMainId}', 'EshopAdminController@eshop_make_invoice_stock_out2');
			Route::get('/eshop-invoiced', 'EshopAdminController@eshop_invoiced');
			Route::get('/eshop-invoiced-list', 'EshopAdminController@eshop_invoiced_list');
			Route::get('/eshop-invoiced-view/{orderMainId}/{foMainId}', 'EshopAdminController@eshop_invoiced_view');

			Route::get('/eshop-outlet-ledger', 'EshopAdminController@outlate_ledger');
			Route::post('/eshop-outlet-ledger-list', 'EshopAdminController@outlate_ledger_list'); 

			Route::get('/eshop-customer-stock', 'EshopAdminController@customer_stock');
			Route::post('/eshop-customer-stock-list', 'EshopAdminController@customer_stock_list');

			Route::get('/eshop-requisition-status', 'EshopRequisitionController@index');
			Route::get('/eshop-requisition-view/{orderid}/{customerid}', 'EshopRequisitionController@details');


			Route::get('/eshop-partial-delivery-report', 'PartialDelivery@partial_delivery_report');
			Route::get('/eshop-partial-delivery/{orderMainId}', 'PartialDelivery@eshop_delivery_approved_view');
			Route::post('/eshop-partial-delivery-receive', 'PartialDelivery@partial_delivery_received');
			// Route::get('/eshop-requisition-view/{orderid}/{customerid}', 'EshopRequisitionController@details');


			Route::get('/eshop-accounts_payments_download', 'EshopPaymentControlle@accounts_payments_download');
			Route::get('/eshop-accounts_payments_verify_download', 'EshopPaymentControlle@accounts_payments_verify_download');
			Route::get('/eshop-accounts_payments_rece_report_download', 'EshopPaymentControlle@accounts_payments_rece_report_download');


			Route::get('/eshop-categories-list','EshopAdminController@eshop_categories_list');
			Route::post('/eshop-category-create','EshopAdminController@eshop_category_create');
			Route::get('/eshop-all-category-list','EshopAdminController@eshop_all_category_list');
			Route::post('/eshop-category-edit','EshopAdminController@eshop_category_edit');
			Route::post('/eshop-category-update','EshopAdminController@eshop_category_update');
			Route::get('/eshop-category-active/{id}','EshopAdminController@eshop_category_active');
			Route::get('/eshop-category-inactive/{id}','EshopAdminController@eshop_category_inactive');
			//Route::post('/eshop-product-edit', 'EshopAdminController@eshop_product_edit');


			//  Route::get('/requisition', 'EshopRequisition@customer_list');
		 	// 	Route::get('/eshop-party-list', 'EshopRequisition@eshop_party_list');
		 	// 	Route::get('/eshop-requisition-process/{partyid}/{customer_id}', 'EshopRequisition@eshop_order_process');
		 	// 	Route::get('/eshop-category-wise-products', 'EshopRequisition@eshop_category_products');
		 	// 	Route::post('/eshop-requisition-add-to-cart', 'EshopRequisition@eshop_add_to_cart_products');
		 	// 	Route::get('/eshop-carts/{customer_id}/{partyid}', 'EshopRequisition@carts');
    }    
);