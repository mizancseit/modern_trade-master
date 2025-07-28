<?php

/*
|--------------------------------------------------------------------------
| Sales Automation Web Routes
|--------------------------------------------------------------------------
*/
/**
*
* Created by Md. Masud Rana
* Date : 30/11/2017
* Date : 08/01/2018
*
**/

Route::get('/', 'RedirectController@index');
Route::get('/forgot-password', 'Sales\DefaultController@ssg_forgot_password');
//Route::get('/invoice', 'Sales\DefaultController@ssg_invoice');

/*
|--------------------------------------------------------------------------
| Registration Routes
|--------------------------------------------------------------------------
*/

Route::get('/register', 'RegistrationController@ssg_register');
Route::post('/register-done', 'RegistrationController@ssg_register_done');

/*
|--------------------------------------------------------------------------
| Login Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', 'LoginController@ssg_master_login');
Route::get('/login', 'LoginController@ssg_master_login');


/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', 'Sales\DashboardController@default_dashboard');
Route::get('/dashboard/management','Sales\DashboardController@ssg_management');
Route::post('/logout', 'Sales\DashboardController@ssg_master_logout');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::get('/profile', 'Sales\DashboardController@default_profile');

/*
|--------------------------------------------------------------------------
| Field Officers Routes
|--------------------------------------------------------------------------
*/


// For Visit Routes

Route::get('/visit', 'Sales\VisitController@ssg_visit');
Route::post('/retailer', 'Sales\VisitController@ssg_retailer');
Route::get('/order-process/{retailderid}/{routeid}', 'Sales\VisitController@ssg_order_process');
Route::post('/visit-category-products', 'Sales\VisitController@ssg_category_products');
Route::post('/visit-order-category-products', 'Sales\VisitController@ssg_order_category_products');
Route::post('/visit-order-manage-category-products', 'Sales\VisitController@ssg_order_manage_category_products');
Route::post('/add-to-cart', 'Sales\VisitController@ssg_add_to_cart_products');
Route::get('/bucket/{pointid}/{routeid}/{retailderid}/{partial_id}', 'Sales\VisitController@ssg_bucket');

Route::get('/bucket_offer/{pointid}/{routeid}/{retailderid}/{partialOrder}', 'Sales\VisitController@ssg_bucket_offer');

Route::post('/confirm-order',  'Sales\VisitController@ssg_confirm_order');
Route::get('/close-order/{order_id}/{partialOrder}', 'Sales\VisitController@ssg_order_closed');
Route::get('/close-order-manage/{order_id}/{partialOrder}', 'Sales\VisitController@ssg_order_manage_closed');

//Route::post('/delete-order', 'Sales\VisitController@ssg_delete_order');
Route::get('/delete-order/{orderID}/{retailderid}/{routeid}/{partialOrder}', 'Sales\VisitController@ssg_delete_order');

Route::post('/items-edit', 'Sales\VisitController@ssg_items_edit');
Route::post('/edit-submit', 'Sales\VisitController@ssg_items_edit_submit');
Route::post('/items-delete', 'Sales\VisitController@ssg_items_items_delete');

Route::get('/visit-process/{retailderid}/{routeid}', 'Sales\VisitController@ssg_visit_process_order');
Route::post('/visit-process-submit', 'Sales\VisitController@ssg_visit_process_submit');
Route::get('/nonvisit-process/{retailderid}/{routeid}', 'Sales\VisitController@ssg_nonvisit_process_order');
Route::post('/nonvisit-process-submit', 'Sales\VisitController@ssg_nonvisit_process_submit');


// For Order Manage

Route::get('/order-manage', 'Sales\VisitController@ssg_order_manage');
Route::get('/invoice-details/{orderMainId}/{foMainId}', 'Sales\VisitController@ssg_invoice_details_order');
Route::get('/invoice-edit/{orderId}/{retailderid}/{routeid}/{partial_order_id}', 'Sales\VisitController@ssg_order_edit_process');
Route::post('/add-to-edit-cart', 'Sales\VisitController@ssg_add_to_edit_cart_products');
Route::get('/bucket-edit/{orderid}/{pointid}/{routeid}/{retailderid}/{partial_id}', 'Sales\VisitController@ssg_bucket_edit');

// Sharif For Wastage Routes

Route::get('/wastage', 'Sales\WastageController@wastage');
Route::post('/wastage-retailer', 'Sales\WastageController@wastage_retailer');
Route::get('/wastage-process/{retailderid}/{routeid}', 'Sales\WastageController@wastage_process');
Route::post('/wastage-category-products', 'Sales\WastageController@wastage_products');
Route::post('/wastage-add-to-cart', 'Sales\WastageController@wastage_add_to_cart_products');
Route::get('/wastage-bucket/{pointid}/{routeid}/{retailderid}', 'Sales\WastageController@wastage_bucket');
Route::post('/wastage-items-edit', 'Sales\WastageController@wastage_items_edit');
Route::post('/wastage-items-edit-submit', 'Sales\WastageController@wastage_items_edit_submit');
Route::get('/wastage-items-del', 'Sales\WastageController@wastage_items_delete');
Route::get('/confirm-wastage/{orderpid}/{orderid}/{retailderid}/{routeid}/{pointid}/{distributorID}', 'Sales\WastageController@ssg_confirm_wastage');
Route::get('/delete-wastage/{orderid}/{retailderid}/{routeid}', 'Sales\WastageController@ssg_delete_wastage');

// End Wastage Routes



// For Distributor Wastage Delivery

Route::get('/wastage-delivery', 'Sales\WastageDistributorController@ssg_wastage');
Route::get('/wastage-list', 'Sales\WastageDistributorController@ssg_wastage_list');
Route::get('/wastage-edit/{wastageMainId}/{foMainId}', 'Sales\WastageDistributorController@ssg_wastage_edit');
Route::post('/wastage-edit-submit', 'Sales\WastageDistributorController@ssg_wastage_edit_submit');
Route::get('/invoice-wastage/{wastageMainId}/{foMainId}', 'Sales\WastageDistributorController@ssg_invoice_wastage');


//Wastage FO Report

Route::get('/report/wastage/fo/order', 'Sales\FoWastageReportController@wastage_report_fo_order');
Route::post('/report/wastage/fo/order-list', 'Sales\FoWastageReportController@wastage_report_fo_order_list');
Route::get('/report/wastage/fo/invoice-details/{orderMainId}', 'Sales\FoWastageReportController@wastage_order_details');

Route::get('/report/wastage/fo/delivery', 'Sales\FoWastageReportController@wastage_report_fo_delivery');
Route::post('/report/wastage/fo/delivery-list', 'Sales\FoWastageReportController@wastage_report_fo_delivery_list');
Route::get('/report/wastage/fo/invoice-details-delivery/{orderMainId}', 'Sales\FoWastageReportController@wastage_report_fo_delivery_details');

// End Wastage Routes

// Start Distributor wastage report



Route::get('/report/wastage/distributor/order', 'Sales\DistributorWastageReportController@wastage_report_fo_order');
Route::post('/report/wastage/distributor/order-list', 'Sales\DistributorWastageReportController@wastage_report_fo_order_list');
Route::get('/report/wastage/distributor/invoice-details/{orderMainId}', 'Sales\DistributorWastageReportController@wastage_order_details');

Route::get('/report/wastage/distributor/delivery', 'Sales\DistributorWastageReportController@wastage_report_fo_delivery');
Route::post('/report/wastage/distributor/delivery-list', 'Sales\DistributorWastageReportController@wastage_report_fo_delivery_list');
Route::get('/report/wastage/distributor/invoice-details-delivery/{orderMainId}', 'Sales\DistributorWastageReportController@wastage_report_fo_delivery_details');

// End Distributor wastage report



// Attendence
Route::get('/attendance', 'Sales\AttendenceController@ssg_attendance');
Route::get('/attendance-retailers', 'Sales\AttendenceController@ssg_attendance_retailers');
Route::post('/attendance-in-out', 'Sales\AttendenceController@ssg_attendance_inout');
Route::get('/attendance-list', 'Sales\AttendenceController@ssg_attendance_list');


// Utility

Route::get('/utility', 'Sales\UtilityController@ssg_utility');
Route::get('/utility-type', 'Sales\UtilityController@ssg_utility_type');
Route::post('/utility-add', 'Sales\UtilityController@ssg_utility_add');

/*
|--------------------------------------------------------------------------
| Distributor Officers Routes
|--------------------------------------------------------------------------
*/

// For Order

Route::get('/order', 'Sales\DistributorController@ssg_order');
Route::post('/order-list', 'Sales\DistributorController@ssg_order_list');
Route::get('/order-edit/{orderMainId}/{foMainId}/{orderPart}', 'Sales\DistributorController@ssg_order_edit');
Route::post('/order-edit-submit', 'Sales\DistributorController@ssg_order_edit_submit');
Route::get('/invoice/{orderMainId}/{foMainId}', 'Sales\DistributorController@ssg_invoice_order');
Route::get('/invoice-partial/{orderMainId}/{foMainId}/{orderPart}', 'Sales\DistributorController@ssg_invoice_order_partial');
Route::get('/confirm-delivery', 'Sales\DistributorController@order_confirm_delivery');

Route::get('/close-distributor-order/{order_id}/{partialOrder}', 'Sales\DistributorController@ssg_distributor_order_closed');

Route::get('/order-check/{orderMainId}/{foMainId}', 'Sales\DistributorController@ssg_order_check');


// For Order Exceptional

Route::get('/order-exceptional', 'Sales\DistributorController@ssg_order_exceptional');
Route::post('/order-list-exceptional', 'Sales\DistributorController@ssg_order_list_exceptional');
Route::get('/order-edit-exceptional/{orderMainId}/{foMainId}/{orderPart}', 'Sales\DistributorController@ssg_order_edit_exceptional');
Route::post('/order-edit-submit-exceptional', 'Sales\DistributorController@ssg_order_edit_submit_exceptional');
Route::get('/invoice-exceptional/{orderMainId}/{foMainId}', 'Sales\DistributorController@ssg_invoice_order_exceptional');
Route::get('/invoice-partial-exceptional/{orderMainId}/{foMainId}/{orderPart}', 'Sales\DistributorController@ssg_invoice_order_partial_exceptional');
Route::get('/confirm-delivery-exceptional', 'Sales\DistributorController@order_confirm_delivery_exceptional');
Route::get('/order-check-exceptional/{orderMainId}/{foMainId}', 'Sales\DistributorController@ssg_order_check_exceptional');
Route::get('/close-distributor-order-exceptional/{order_id}/{partialOrder}', 'Sales\DistributorController@ssg_distributor_order_closed_exceptional');

// For Order end Exceptional

// free pending for depo

Route::get('/free-pending', 'Sales\DistributorController@ssg_free_pending');
Route::post('/free-pending-list', 'Sales\DistributorController@ssg_free_pending_list');
Route::get('/free-pending-edit/{orderMainId}/{foMainId}', 'Sales\DistributorController@ssg_free_pending_edit');
Route::post('/free-pending-edit-submit', 'Sales\DistributorController@ssg_free_pending_edit_submit');





/*
|--------------------------------------------------------------------------
| Reports Routes
|--------------------------------------------------------------------------
*/

// Distributor Delivery Report

Route::get('/report/delivery', 'Sales\DistributorReportController@ssg_report_order_delivery');
Route::post('/report/delivery-list', 'Sales\DistributorReportController@ssg_report_order_list');
Route::get('/report/order-details/{orderMainId}', 'Sales\DistributorReportController@ssg_order_details');

/* RollBack Delivery TO Order */
Route::get('/report/order-rollback-details/{orderMainId}', 'Sales\DistributorReportController@ssg_rollback_order_details');
Route::get('/rollback-delivery', 'Sales\DistributorReportController@delivery_rollback');


// Distributor Order Vs Delivery Report 

Route::get('/report/order-vs-delivery', 'Sales\DistributorReportController@ssg_report_order_vs_delivery');
Route::post('/report/order-vs-delivery-list', 'Sales\DistributorReportController@ssg_report_order_vs_delivery_list');

// Distributor Category Wise Order Report 

Route::get('/report/category-wise-order', 'Sales\DistributorReportController@ssg_report_category_wise_order');
Route::post('/report/category-wise-order-list', 'Sales\DistributorReportController@ssg_report_category_wise_order_list');

// Distributor SKU Wise Order Report 

Route::get('/report/sku-wise-order', 'Sales\DistributorReportController@ssg_report_sku_wise_order');
Route::get('/report/category-wise-product', 'Sales\DistributorReportController@ssg_report_category_wise_product_list');
Route::post('/report/sku-wise-order-list', 'Sales\DistributorReportController@ssg_report_sku_wise_order_list');

// Distributor SKU Wise Delivery Report 

Route::get('/report/sku-wise-delivery', 'Sales\DistributorReportController@ssg_report_sku_wise_delivery');
Route::post('/report/sku-wise-delivery-list', 'Sales\DistributorReportController@ssg_report_sku_wise_delivery_list');




// Field Officer (Fo) Order Report

Route::get('/report/fo/order', 'Sales\FoReportController@ssg_report_fo_order');
Route::post('/report/fo/order-list', 'Sales\FoReportController@ssg_report_fo_order_list');
Route::get('/report/fo/invoice-details/{orderMainId}', 'Sales\FoReportController@ssg_order_details');

// Field Officer (Fo) Delivery Report

Route::get('/report/fo/delivery', 'Sales\FoReportController@ssg_report_fo_delivery');
Route::post('/report/fo/delivery-list', 'Sales\FoReportController@ssg_report_fo_delivery_list');
Route::get('/report/fo/invoice-details-delivery/{orderMainId}', 'Sales\FoReportController@ssg_report_fo_delivery_details');

// Field Officer (Fo) Order Vs Delivery Report

Route::get('/report/fo/order-vs-delivery', 'Sales\FoReportController@ssg_report_fo_order_vs_delivery');
Route::post('/report/fo/order-vs-delivery-list', 'Sales\FoReportController@ssg_report_fo_order_vs_delivery_list');

// Field Officer (Fo) Category Wise Order Report 

Route::get('/report/fo/category-wise', 'Sales\FoReportController@ssg_report_fo_category_wise_order');
Route::post('/report/fo/category-wise-list', 'Sales\FoReportController@ssg_report_fo_category_wise_list');

// Field Officer (Fo) Category Wise Order Report 

Route::get('/report/fo/product-wise', 'Sales\FoReportController@ssg_report_fo_product_wise');
Route::get('/report/fo/category-wise-product', 'Sales\FoReportController@ssg_report_fo_category_wise_product_list');
Route::post('/report/fo/product-wise-list', 'Sales\FoReportController@ssg_report_fo_product_wise_list');

// Field Officer (Fo) Attendance Report

Route::get('/report/fo/attendance', 'Sales\FoReportController@ssg_report_fo_attendance');
Route::get('/report/fo/attendance-list', 'Sales\FoReportController@ssg_report_fo_attendance_list');


// Field Officer (Fo) Visit Report

Route::get('/report/fo/visit', 'Sales\FoReportController@ssg_report_fo_visit');
Route::post('/report/fo/visit-list', 'Sales\FoReportController@ssg_report_fo_visit_list');

// Sharif dashboard report

Route::get('/report/fo/delivery/{startDate}/{endDate}', 'Sales\FoReportController@dashboard_report_fo_delivery');

/*
|--------------------------------------------------------------------------
| Sales Coordinator Routes
|--------------------------------------------------------------------------
*/

Route::get('/sc/order-report', 'Sales\SalesCoordinatorController@ssg_order_report');
Route::get('/sc/points-list', 'Sales\SalesCoordinatorController@ssg_points_list');
Route::get('/sc/fos-list', 'Sales\SalesCoordinatorController@ssg_fos_list');

// FO Performance
Route::get('/sc/fo-performance-report', 'Sales\SalesCoordinatorController@ssg_fo_performance_report');
Route::post('/sc/fo-performance-list', 'Sales\SalesCoordinatorController@ssg_fo_performance_list');
Route::get('/sc/div-points-list', 'Sales\SalesCoordinatorController@ssg_div_points_list');
Route::get('/sc/div-territory-list', 'Sales\SalesCoordinatorController@ssg_div_territory_list');
Route::get('/sc/territory-points-list', 'Sales\SalesCoordinatorController@ssg_territory_points_list');

// DB Performance
Route::get('/sc/db-performance-report', 'Sales\SalesCoordinatorController@ssg_db_performance_report');
Route::post('/sc/db-performance-list', 'Sales\SalesCoordinatorController@ssg_db_performance_list');

/*
|--------------------------------------------------------------------------
| Sales Admin Routes
|--------------------------------------------------------------------------
*/

//Route::get('/sa/order-report', 'Sales\SalesCoordinatorController@ssg_order_report');




/*
|--------------------------------------------------------------------------
| Master Offer Set Up Routes
|--------------------------------------------------------------------------
*/


// Bundle Offer Management

Route::get('/offers/bundle', 'Sales\MasterOfferController@ssg_bundle_offer');
Route::get('/offers/bundle-add', 'Sales\MasterOfferController@ssg_bundle_offer_add');
Route::get('/offers/bundle-category', 'Sales\MasterOfferController@ssg_bundle_category'); // new
Route::get('/offers/bundle-points', 'Sales\MasterOfferController@ssg_bundle_division_wise_points');
Route::get('/offers/bundle-route', 'Sales\MasterOfferController@ssg_bundle_point_wise_routes');

Route::post('/offers/bundle-submit', 'Sales\MasterOfferController@ssg_bundle_offer_submit');
Route::get('/offers/bundle-edit/{offerid}', 'Sales\MasterOfferController@ssg_bundle_offer_edit');
Route::post('/offers/bundle-update', 'Sales\MasterOfferController@ssg_bundle_offer_update');
Route::get('/offers/bundle-delete', 'Sales\MasterOfferController@ssg_bundle_offer_delete');
Route::get('/offers/bundle-offer-active', 'Sales\MasterOfferController@ssg_bundle_offer_active_inactive');


// Bundle Offer Product Management

Route::get('/offers/bundle-product', 'Sales\MasterOfferController@ssg_bundle_product');
Route::get('/offers/bundle-product-add', 'Sales\MasterOfferController@ssg_bundle_product_add');
Route::get('/offers/bundle-offer-details', 'Sales\MasterOfferController@ssg_bundle_offer_details');
Route::get('/offers/bundle-offer-types', 'Sales\MasterOfferController@ssg_bundle_offer_types');
Route::get('/offers/bundle-offer-category-wise-pro', 'Sales\MasterOfferController@ssg_bundle_offer_category_wise_pro');

Route::post('/offers/bundle-offer-pro-submit', 'Sales\MasterOfferController@ssg_bundle_offer_pro_submit');

Route::get('/offers/bundle-offer-pro-edit/{id}', 'Sales\MasterOfferController@ssg_bundle_offer_pro_edit');
Route::post('/offers/bundle-offer-pro-update', 'Sales\MasterOfferController@ssg_bundle_offer_pro_update');

Route::get('/offers/bundle-product-delete', 'Sales\MasterOfferController@ssg_bundle_product_delete');






/*
|--------------------------------------------------------------------------
| Master Data Set Up|
|--------------------------------------------------------------------------
*/

//Master Data

//Master Data supervisor setup by sharif

Route::get('/userSupervisor', 'Master\MasterSetupCon@define_supervisor');

Route::get('/get_user_list','Master\MasterSetupCon@get_user_list');
Route::get('/get_supervisor_list','Master\MasterSetupCon@get_supervisor_list');
Route::post('/supervisor_save','Master\MasterSetupCon@supervisor_save');
Route::get('/supervisor_delete','Master\MasterSetupCon@supervisor_delete');


// Maung Company Set up starts

Route::get('/company','Master\MasterSetupCon@company_setup');
Route::post('/company_process','Master\MasterSetupCon@company_process');
Route::post('/company_edit','Master\MasterSetupCon@company_edit');
Route::post('/companyEditProcess','Master\MasterSetupCon@company_edit_process');
Route::get('/company_delete','Master\MasterSetupCon@company_delete');

// Maung Company Set up ends

//Maung point set up starts
Route::get('/newPoint','Master\MasterSetupCon@point_setup');
Route::post('/get_territory','Master\MasterSetupCon@get_territory');
Route::post('/point_process','Master\MasterSetupCon@point_process');

Route::post('/pointEditProcess','Master\MasterSetupCon@point_edit_process');
Route::get('/point_delete','Master\MasterSetupCon@point_delete');
Route::post('/point_edit','Master\MasterSetupCon@point_edit');
//Maung point set up starts


//Maung territory set up starts
Route::get('/newTerritory','Master\MasterSetupCon@territory_setup');
Route::post('/territory_process','Master\MasterSetupCon@territory_process');
Route::post('/territory_edit','Master\MasterSetupCon@territory_edit');
Route::post('/territoryEditProcess','Master\MasterSetupCon@territory_edit_process');
Route::get('/territory_delete','Master\MasterSetupCon@territory_delete');
//Maung point set up starts

//Maung Route set up starts
Route::get('/newRoute','Master\MasterSetupCon@route_setup');
Route::post('/route_process','Master\MasterSetupCon@route_process');
//Route::post('/route_edit','Master\MasterSetupCon@route_setup');
Route::get('/route_delete','Master\MasterSetupCon@route_delete');
Route::get('/route_edit','Master\MasterSetupCon@route_setup');
//Maung Route set up ends


//Distributor Starts Maung
Route::get('/newDistributor','Master\MasterSetupCon@distributor_setup');
Route::post('/distributor_process','Master\MasterSetupCon@distributor_process');
Route::get('/distri_delete','Master\MasterSetupCon@distri_delete');
Route::get('/distri_edit','Master\MasterSetupCon@distributor_setup');

//Distributor Ends Maung

//Product Category starts Page
Route::get('/productCategory','Master\MasterSetupCon@procategory_setup');
Route::post('/proCategory_process','Master\MasterSetupCon@proCategory_process');
Route::get('/procategory_delete','Master\MasterSetupCon@procategory_delete');
Route::get('/proCategory_edit','Master\MasterSetupCon@procategory_setup');
Route::get('/productSetup','Master\MasterSetupCon@product_setup');
Route::post('/productProcess','Master\MasterSetupCon@productProcess');
Route::get('/productsMaster_delete','Master\MasterSetupCon@productsMaster_delete');
Route::get('/proCategory_edit','Master\MasterSetupCon@procategory_setup');

Route::get('/productsSetup_edit','Master\MasterSetupCon@product_setup');

//Product Category ends page

//Reject Reason Zubair

Route::get('/reject_reason_list','Master\MasterSetupCon@rejectreason_setup');
Route::post('/rejectreason_process','Master\MasterSetupCon@rejectreason_process');
Route::post('/rejectreason_edit','Master\MasterSetupCon@rejectreason_edit');
Route::post('/rejectreasonEditProcess','Master\MasterSetupCon@rejectreason_edit_process');
Route::get('/rejectreason_delete','Master\MasterSetupCon@rejectreason_delete');

//FO Setup Zubair
Route::get('/fo_list','Master\MasterSetupCon@fo_setup');
Route::post('/fo_process','Master\MasterSetupCon@fo_process');
Route::get('/fo_delete','Master\MasterSetupCon@fo_delete');
Route::get('/fo_edit','Master\MasterSetupCon@fo_setup');

//Division Setup Zubair
Route::get('/division_list','Master\MasterSetupCon@division_setup');
Route::post('/division_process','Master\MasterSetupCon@division_process');
Route::post('/division_edit','Master\MasterSetupCon@division_edit');
Route::post('/divisionEditProcess','Master\MasterSetupCon@division_edit_process');
Route::get('/division_delete','Master\MasterSetupCon@division_delete');

//Retailer Setup Zubair
Route::get('/retailer_list','Master\MasterSetupCon@retailer_setup');
Route::post('/retailer_process','Master\MasterSetupCon@retailer_process');
Route::get('/retailer_delete','Master\MasterSetupCon@retailer_delete');
Route::get('/retailer_edit','Master\MasterSetupCon@retailer_setup');

// Zubair Global Company Set up starts
Route::get('/globalCompany','Master\MasterSetupCon@global_company_setup');
Route::post('/globalCompany_process','Master\MasterSetupCon@global_company_process');
Route::get('/globalCompany_edit','Master\MasterSetupCon@global_company_edit');
Route::post('/globalCompanyEditProcess','Master\MasterSetupCon@global_company_edit_process');
Route::get('/globalCompany_delete','Master\MasterSetupCon@global_company_delete');


//Zubair Depot Paymnets
Route::get('/depot-payment-list','Depot\DepotManage@depot_payment_list');
Route::get('/newDepotPayment','Depot\DepotManage@depot_payments');
Route::post('/depot_paymnet_process','Depot\DepotManage@depot_paymnet_process');

Route::post('/depotPaymentEdit','Depot\DepotManage@depot_payment_edit');
Route::post('/depotPaymentEditProcess','Depot\DepotManage@depot_payment_edit_process');

Route::get('/paymentDelete','Depot\DepotManage@depot_payment_delete');

Route::get('/DepotTransHistory','Depot\DepotManage@depot_transaction_history');


// Zubiar Depot billing
Route::get('/depotPaymentList','depotBilling\depotBilling@depotPaymentList');
Route::post('/depotPaymentAcknowledge','depotBilling\depotBilling@ackDepotProcess');
Route::get('/depotAckList','depotBilling\depotBilling@ackDepotList');
Route::get('/depo-payment-undo/{transaction_id}','depotBilling\depotBilling@depo_payment_undo');

Route::get('/download_depotAckList','depotBilling\depotBilling@download_ackDepotList');

// Zubair Depot accounts

Route::get('/depotPaymentAckList','depotBilling\depotBilling@depotPaymentAcknowledgeList');
Route::post('/depotPaymentVerify','depotBilling\depotBilling@verifyDepotProcess');
Route::get('/depotVerifiedList','depotBilling\depotBilling@depotVerifiedList');


Route::get('/download_depotVerifiedList','depotBilling\depotBilling@download_depotVerifiedList');

//Zubair Depot Collection

Route::get('/DepotCollection','Depot\DepotManage@depot_collection');
Route::post('/DepotCollectionProcess','Depot\DepotManage@depot_collection_process');

Route::post('/depotCollectionEdit','Depot\DepotManage@depot_collection_edit');
Route::post('/collectionEditProcess','Depot\DepotManage@collection_edit_process');

Route::get('/collectionDelete','Depot\DepotManage@depot_collection_delete');

Route::get('/CollectionTransHistory','Depot\DepotManage@collection_transaction_history');


// Zubair Requisition Manage

Route::get('/req-manage', 'Depot\DepotRequisition@req_manage');
Route::get('/req-add', 'Depot\DepotRequisition@req_add');
Route::post('/req-process', 'Depot\DepotRequisition@req_process');
Route::get('/req-list_product/{reqId}', 'Depot\DepotRequisition@req_list_product');
Route::post('/req-category-products', 'Depot\DepotRequisition@req_category_products');
Route::post('/req-add-to-product', 'Depot\DepotRequisition@req_add_product');
Route::get('/req-bucket/{reqid}', 'Depot\DepotRequisition@req_bucket');
Route::get('/req-send/{reqid}', 'Depot\DepotRequisition@req_send');
Route::get('/req-send_list', 'Depot\DepotRequisition@req_send_list');
Route::get('/req-received_list', 'Depot\DepotRequisition@req_received_list');


Route::get('/reqAcknowledgeList', 'Depot\DepotRequisition@req_acknowledge_list');
Route::get('/reqApprovedList', 'Depot\DepotRequisition@req_approved_list');
Route::get('/reqCanceledList', 'Depot\DepotRequisition@req_canceled_list');
Route::get('/reqDeliveredList', 'Depot\DepotRequisition@req_delivered_list');

//Route::get('/reqReceive/{reqid}', 'Depot\DepotRequisition@req_receive');

Route::post('/reqReceive/', 'Depot\DepotRequisition@req_receive');

Route::get('/reqReceivedList', 'Depot\DepotRequisition@req_received_list');

Route::get('/reqDeliveryReceivedList/{reqid}', 'Depot\DepotRequisition@req_delivery_received_list');




// Zubair Admin Requisition Manage

Route::get('/reqPendingList', 'Depot\DepotRequisition@req_pending_list');
Route::post('/reqAcknowledge', 'Depot\DepotRequisition@req_acknowledge_new');

Route::get('/reqAllAnalysisList', 'Depot\DepotRequisition@req_analysis_list_new');
Route::get('/reqOrderAnalysis/{prodid}', 'Depot\DepotRequisition@req_order_analysis_new');

Route::post('/reqApproved', 'Depot\DepotRequisition@req_approved_new');
Route::get('/reqAllApprovedList', 'Depot\DepotRequisition@req_all_approved_list');

Route::get('/reqBilled/{reqid}', 'Depot\DepotRequisition@req_billed_process');
Route::get('/reqBilledList', 'Depot\DepotRequisition@req_billed_list');
Route::get('/reqAllBilledList', 'Depot\DepotRequisition@req_all_billed_list');
Route::get('/reqBillDetails/{reqid}', 'Depot\DepotRequisition@req_bill_details_list');

Route::get('/reqOpenOrderList/{reqid}', 'Depot\DepotRequisition@req_open_order_list');

/* Factory Delivery Begin */

Route::get('/reqReadyForDelivery', 'Depot\DepotRequisition@req_ready_for_delivery');
Route::get('/reqDeliveryItemList/{reqid}', 'Depot\DepotRequisition@req_delivery_item_list');

Route::post('/reqDeliver/', 'Depot\DepotRequisition@req_deliver_process');


/* Factory Delivery End */

//Route::get('/reqDelivere/{reqid}', 'Depot\DepotRequisition@req_deliver');
Route::get('/reqAllDeliveredList', 'Depot\DepotRequisition@req_all_delivered_list');

Route::get('/reqCanceled/{reqid}', 'Depot\DepotRequisition@req_canceled');
Route::get('/reqAllCanceledList', 'Depot\DepotRequisition@req_all_canceled_list');

Route::get('/reqAllActiveList', 'Depot\DepotRequisition@req_active_list');
Route::post('/reqInActiveProcess', 'Depot\DepotRequisition@req_inactive_process');

Route::get('/reqAllInActiveList', 'Depot\DepotRequisition@req_inactive_list');
Route::post('/reqActiveProcess', 'Depot\DepotRequisition@req_active_process');

Route::get('/custAllActiveList', 'Depot\DepotRequisition@cust_active_list');
Route::post('/custInActiveProcess', 'Depot\DepotRequisition@cust_inactive_process');

Route::get('/custAllInActiveList', 'Depot\DepotRequisition@cust_inactive_list');
Route::post('/custActiveProcess', 'Depot\DepotRequisition@cust_active_process');



Route::get('/reqAllReceivedList', 'Depot\DepotRequisition@req_all_received_list');

/* Common for depot and admin */

Route::get('/reqDetails/{reqid}', 'Depot\DepotRequisition@req_details_list');
Route::get('/reqApprovedDetails/{reqid}', 'Depot\DepotRequisition@req_approved_details_list');
Route::get('/reqDeliveredDetails/{reqid}', 'Depot\DepotRequisition@req_delivered_details_list');
Route::get('/reqReceivedDetails/{reqid}', 'Depot\DepotRequisition@req_received_details_list');

Route::get('/reqDeliveryChallan/{reqid}', 'Depot\DepotRequisition@req_delivery_challan');


// Zubair Delivery Pending 

Route::get('/PendingOrderSummary', 'Depot\DepotRequisition@pending_order_summary');

Route::get('/PendingOrderList', 'Depot\DepotRequisition@pending_order_list');
Route::post('/PendingOrderProccess', 'Depot\DepotRequisition@pending_order_process');


// Zubair Delivery Claim 

Route::get('/depot/DepotClaim', 'Depot\DepotClaim@claim_order_list');
Route::post('/depot/ClaimOrderProcess', 'Depot\DepotClaim@claim_order_process');

/* Zubair Depo Cash In Hand */

Route::get('/depoOpenCashInHand', 'Depot\DepotManage@depo_opening_cashinhand');
Route::post('/update_depo_cash_in_hand', 'Depot\DepotManage@depo_opening_cashinhand_update');

/* Zubair Reatiler Balance */

Route::get('/retBalanceList', 'Depot\DepotManage@route_wise_retailer_list');
Route::post('/route-retailer', 'Depot\DepotManage@getRetailerByRouteId');
Route::post('/update_retailer_balance', 'Depot\DepotManage@retailer_balance_update');
Route::get('/retailer/get_invoice', 'Depot\DepotManage@get_invoice');

Route::get('/collection/money_recipt/{collection_id}', 'Depot\DepotManage@money_recipt');

Route::get('/PartyLaser','Depot\DepotManage@retailer_laser_history');
Route::get('/RouteRetaierList', 'Depot\DepotManage@get_retaier_list');

/* Zubair Depo Stock Export */

Route::get('depot/stock_export', 'Depot\DepotRequisition@stock_export');

// Sharif depot Export && Import

Route::get('depot/sales_order_export', 'Depot\DepotRequisition@sales_order_export');

Route::get('depot/export', 'Depot\DepotRequisition@export');

Route::get('depot/export-sales-order', 'Depot\DepotRequisition@export_sale_order');

Route::get('depot/stock_list','Depot\DepotRequisition@upload_stock_list');
Route::post('depot/stock_products_upload','Depot\DepotRequisition@stockProductsUpload');

Route::get('depot/cust_balance_list','Depot\DepotRequisition@upload_customar_balance');
Route::post('depot/cust_balance_upload','Depot\DepotRequisition@custBalanceUploadProcess');

Route::get('depot/req-items-edit', 'Depot\DepotRequisition@depot_items_edit');
Route::post('depot/req-edit-submit', 'Depot\DepotRequisition@depot_items_edit_submit');
Route::get('depot/depot-req-items-delete', 'Depot\DepotRequisition@depot_req_items_delete');

// Zubair FO Return & Change Module

Route::get('/returnproduct', 'Sales\ReturnController@returnproduct');
Route::post('/return-retailer', 'Sales\ReturnController@return_retailer');
Route::get('/return-process/{retailderid}/{routeid}', 'Sales\ReturnController@return_process');
Route::post('/return-category-products', 'Sales\ReturnController@return_products');
Route::post('/return-add-to-cart', 'Sales\ReturnController@return_add_to_cart_products');
Route::get('/return-bucket/{pointid}/{routeid}/{retailderid}', 'Sales\ReturnController@return_bucket');
Route::post('/return-items-edit', 'Sales\ReturnController@return_items_edit');
Route::post('/return-edit-submit', 'Sales\ReturnController@return_items_edit_submit');
Route::get('/return-items-del', 'Sales\ReturnController@return_items_delete');
//Route::get('/confirm-return/{orderpid}/{orderid}/{retailderid}/{routeid}/{pointid}/{distributorID}', 'Sales\ReturnController@ssg_confirm_return');
// Route::post('/delete-return', 'Sales\ReturnController@ssg_delete_return');

Route::get('/delete-return/{retailderid}/{routeid}/{orderid}', 'Sales\ReturnController@ssg_delete_return');

Route::get('/return_change/get_product','Sales\ReturnController@get_product');
Route::get('/return_change/get_product_price','Sales\ReturnController@get_product_price');


// Zubair Depot/Distributor Return & Change Module

Route::get('/returnorder', 'Sales\ChangeController@ssg_return_order');
Route::post('/return-order-list', 'Sales\ChangeController@ssg_return_order_list');
Route::get('/change-category-products/{return_order_id}', 'Sales\ChangeController@return_change_products');
Route::post('/confirm-return-change', 'Sales\ChangeController@ssg_confirm_return_change');
Route::get('/return-invoice/{orderMainId}/{foMainId}', 'Sales\ChangeController@ssg_return_invoice_order');

// Zubair FO Return Only Module

Route::get('/return-only-product', 'Sales\ReturnOnlyController@returnproduct');
Route::post('/return-only-retailer', 'Sales\ReturnOnlyController@return_retailer');
Route::get('/return-only-process/{retailderid}/{routeid}', 'Sales\ReturnOnlyController@return_process');
Route::post('/return-only-category-products', 'Sales\ReturnOnlyController@return_products');
Route::post('/return-only-add-to-cart', 'Sales\ReturnOnlyController@return_add_to_cart_products');
Route::get('/return-only-bucket/{pointid}/{routeid}/{retailderid}', 'Sales\ReturnOnlyController@return_bucket');
Route::post('/return-only-items-edit', 'Sales\ReturnOnlyController@return_items_edit');
Route::post('/return-only-edit-submit', 'Sales\ReturnOnlyController@return_items_edit_submit');
Route::get('/return-only-items-del', 'Sales\ReturnOnlyController@return_items_delete');
Route::get('/confirm-only-return/{orderpid}/{orderid}/{retailderid}/{routeid}/{pointid}/{distributorID}', 'Sales\ReturnOnlyController@ssg_confirm_return');
Route::post('/delete-only-return', 'Sales\ReturnOnlyController@ssg_delete_return');

Route::get('/return_only_change/get_product','Sales\ReturnOnlyController@get_product');
Route::get('/return_only_change/get_product_price','Sales\ReturnOnlyController@get_product_price');


// Zubair Depot/Distributor Return Only Module

Route::get('/return-only-order', 'Sales\ChangeOnlyController@ssg_return_order');
Route::post('/return-only-order-list', 'Sales\ChangeOnlyController@ssg_return_order_list');
Route::get('/change-only-category-products/{return_order_id}', 'Sales\ChangeOnlyController@return_change_products');
Route::post('/confirm-only-return-change', 'Sales\ChangeOnlyController@ssg_confirm_return_change');
Route::get('/return-only-invoice/{orderMainId}/{foMainId}', 'Sales\ChangeOnlyController@ssg_return_invoice_order');


//Zubair Depot Cash-Book Module
Route::get('/newDepotCashBook','Depot\DepotCashbook@depot_cashbook');
Route::post('/depot_cashbook_process','Depot\DepotCashbook@depot_cashbook_process');

Route::post('/depotCashBookEdit','Depot\DepotCashbook@depot_cashbook_edit');
Route::post('/cashBookEditProcess','Depot\DepotCashbook@depot_cashbook_edit_process');

Route::get('/cashbookDelete','Depot\DepotCashbook@depot_cashbook_delete');

Route::get('/DepotCashBookHistory','Depot\DepotCashbook@depot_cashbook_history');

//Zubair Distributor Cash-Book Module

Route::get('/newDistCashBook','distExpense\DistCashbook@dist_cashbook');
Route::post('/dist_cashbook_process','distExpense\DistCashbook@dist_cashbook_process');

Route::post('/distCashBookEdit','distExpense\DistCashbook@dist_cashbook_edit');
Route::post('/distCashBookEditProcess','distExpense\DistCashbook@dist_cashbook_edit_process');

Route::get('/DistCashbookDelete','distExpense\DistCashbook@dist_cashbook_delete');
Route::get('/DistCashBookHistory','distExpense\DistCashbook@dist_cashbook_history');

Route::get('/DistExpenseSummary','distExpense\DistCashbook@dist_expense_summary');


//Zubair Depot Report Module

Route::get('/DepotLedger','Depot\DepotReport@depot_ledger_details');
Route::get('/DownloadDeopotLedger','Depot\DepotReport@download_depot_ledger_details');

Route::get('/RetailerCreditSummary','Depot\DepotReport@retailer_wise_credit_summary');
Route::get('/DownloadCreditSummary','Depot\DepotReport@download_credit_summary');

Route::get('/DepotExpenseSummary','Depot\DepotReport@depot_expense_summary');
Route::get('/DownloadExpenseSummary','Depot\DepotReport@download_expense_summary');

Route::get('/DepotCashbookSummary','Depot\DepotReport@depot_cashbook_summary');
Route::get('/DownloadDepotCashbookSummary','Depot\DepotReport@download_cashbook_summary');

Route::get('/DepotFOSalesSummary','Depot\DepotReport@depot_fo_wise_sales');
//Route::get('/DownloadDepotCashbookSummary','Depot\DepotReport@download_cashbook_summary');


//Zubair Point wise retailer ledger auto adjust
Route::get('/PointWiseRetailerLedgerAdjust','Depot\DepotManage@point_wise_retailer_all_ledger_adjust');


//Zubair Set Up Ends


//MAUNG USER MANAGEMENT STARTS//
Route::get('/userCreate','Master\MasterSetupUser@userCreate');
Route::post('/userSetup','Master\MasterSetupUser@userSetup');
Route::post('/userDetails','Master\MasterSetupUser@userDetails');
Route::post('/user_scope','Master\MasterSetupUser@user_scope');
Route::get('/userbasic_delete','Master\MasterSetupUser@userbasic_delete');
Route::get('/userbasic_edit','Master\MasterSetupUser@userCreate');
//Route::post('/get_points','Master\MasterSetupUser@get_points');//Today 280218 userDeatails_edit
Route::get('/userScope_edit','Master\MasterSetupUser@userCreate');
Route::get('/userdetails_edit','Master\MasterSetupUser@userCreate');
//MAUNG USER MANAGEMENT STARTS//

//Offer Product setup starts Page -- Sharif

// Regular offer product start
Route::get('/offer/regular_offer_products', 'Sales\Offer\OfferController@regular_offer_products');
Route::get('/offer/get_product','Sales\Offer\OfferController@get_product');
Route::post('/offer/regular_offer_product_save','Sales\Offer\OfferController@regular_offer_product_save');

Route::get('/sales/offer/regular_products_edit','Sales\Offer\OfferController@regular_products_edit');
Route::post('/offer/regular_product_edit_process','Sales\Offer\OfferController@regular_product_edit_process');
Route::get('/offer/deleteProduct','Sales\Offer\OfferController@deleteRegularProduct');

// Regular SKU offer product setup start

Route::get('/offer/regular_sku_products', 'Sales\Offer\OfferController@regular_sku_products');
Route::post('/offer/regular_sku_product_save','Sales\Offer\OfferController@regular_sku_product_save');
Route::get('/sales/offer/regular_sku_products_edit','Sales\Offer\OfferController@regular_sku_products_edit');
Route::post('/offer/regular_sku_product_edit_process','Sales\Offer\OfferController@regular_sku_product_edit_process');
Route::get('/offer/regular_sku_product_delete','Sales\Offer\OfferController@regular_sku_product_delete');
// Regular offer product end

// Special offer product start
Route::get('/offer/special_offer_products', 'Sales\Offer\OfferController@special_offer_products');
Route::post('/offer/special_offer_product_save','Sales\Offer\OfferController@special_offer_product_save');

Route::get('/sales/offer/special_products_edit','Sales\Offer\OfferController@special_products_edit');
Route::post('/offer/special_product_edit_process','Sales\Offer\OfferController@special_product_edit_process');
Route::get('/offer/specialProductDelete','Sales\Offer\OfferController@specialProductDelete');
// Special offer product end

// Special SKU offer product start
Route::get('/offer/special_sku_products', 'Sales\Offer\OfferController@special_sku_products');
Route::get('/offer/get_sku','Sales\Offer\OfferController@get_special_offer_sku');
Route::get('/offer/get_sku_and_products','Sales\Offer\OfferController@special_sku_and_products');
Route::post('/offer/special_sku_product_save','Sales\Offer\OfferController@special_sku_product_save');

Route::get('/sales/offer/special_sku_products_edit','Sales\Offer\OfferController@special_sku_products_edit');
Route::post('/offer/special_sku_product_edit_process','Sales\Offer\OfferController@special_sku_product_edit_process');
Route::get('/offer/special_sku_product_delete','Sales\Offer\OfferController@special_sku_product_delete');
// Special SKU offer product end

Route::get('/offer/regular_special_offer_setup', 'Sales\Offer\OfferController@regular_special_offer_setup');
Route::post('/offer/offer_setup_save','Sales\Offer\OfferController@offer_setup_save');
Route::get('/sales/offer/offer_setup_edit','Sales\Offer\OfferController@offer_setup_edit');
Route::post('/offer/offer_setup_edit_process','Sales\Offer\OfferController@offer_setup_edit_process');
Route::get('/offer/offerSetupDelete','Sales\Offer\OfferController@offerSetupDelete');
//Offer Product setup ends page -- Sharif

  // --- Sharif start target file upload -- //

Route::get('/fo_target_upload','Master\MasterUploadController@fo_target_list');
Route::post('/target_file_upload','Master\MasterUploadController@targetUpload');
Route::get('/fo_target_edit','Master\MasterUploadController@fo_target_edit');
Route::post('/fo_target_edit_process','Master\MasterUploadController@fo_target_edit_process');

Route::get('/targetDelete','Master\MasterUploadController@targetDelete');


 // --- Sharif start depot stock -- //
Route::get('/depot/depot_list', 'Master\MasterDepotStockController@depot_setup_list');
Route::post('/depot/depot_setup_save', 'Master\MasterDepotStockController@depot_setup_save');
Route::get('/depot/depot_list_edit','Master\MasterDepotStockController@depotListEdit');
Route::get('/depot/deleteDepotList','Master\MasterDepotStockController@deleteDepotList');
Route::post('/depot/depot_list_edit_process','Master\MasterDepotStockController@depot_list_edit_process');

Route::get('/depot', 'Master\MasterDepotStockController@ssg_depot');
Route::get('/depot_div_list', 'Master\MasterDepotStockController@ssg_depot_list');
Route::get('/stock-process/{depotID}/{inOut}', 'Master\MasterDepotStockController@ssg_stock_process');

Route::post('/category-products', 'Master\MasterDepotStockController@ssg_category_products');

Route::post('/inventory_file_upload','Master\MasterUploadController@stockInventoryUpload');

Route::post('/add_to_inventory', 'Master\MasterDepotStockController@products_add_to_inventory');

Route::get('/depot_distributor', 'Master\MasterDepotStockController@ssg_depot_distributor');

Route::get('/depot_stock_list', 'Master\MasterDepotStockController@ssg_depot_stock_list');

Route::post('/stock_products', 'Master\MasterDepotStockController@ssg_stock_products');

/*
|--------------------------------------------------------------------------
| Password Routes
|--------------------------------------------------------------------------
*/

Route::get('/password/change-password', 'Sales\PasswordController@ssg_change_password');
Route::get('/password/check-password', 'Sales\PasswordController@ssg_check_password');
Route::get('/password/change-password-submit', 'Sales\PasswordController@ssg_change_password_submit');


//Route::get('apps/api/login','Sales\DashboardController@ssg_apps_api_login');
Route::get('/bundle-gifts', 'Sales\VisitController@ssg_bucket_bundle_gifts');
Route::get('/bundle-gifts-added', 'Sales\VisitController@ssg_bundle_gifts_added');


// Admin

Route::get('/fo/new-retails', 'Sales\AdminController@ssg_new_retails');
Route::post('/fo/retailer-all', 'Sales\AdminController@ssg_retailer_all');

Route::get('/admin/{serialid}/{retailerid}/{routeid}', 'Sales\AdminController@ssg_fo_admin');
Route::post('/fo/admin-add', 'Sales\AdminController@ssg_fo_admin_add');
Route::get('/fo/admin-active', 'Sales\AdminController@ssg_fo_admin_active');
Route::get('/retailer-req-delete/{id}','Sales\AdminController@retailer_req_delete');










/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/fo-orders','Sales\DashboardController@ssg_fo_orders');
Route::get('/dashboard/fo-orders-excel','Sales\MasterExportController@ssg_fo_orders_excel');


/*
|--------------------------------------------------------------------------
| Dashboard for Distributor ---- Sharif
|--------------------------------------------------------------------------
*/

Route::get('/dashboard/distributor-orders','Sales\DashboardController@ssg_distributor_orders');

//MAUNG MGT DASHBOARD REPORT
Route::get('/report/fo/topten', 'Sales\FoReportController@fotopten');

//MAUNG MGT DASHBOARD REPORT


//----> Masud TSM --------------------------------------->///

Route::get('/report/tsm/folist', 'Sales\tsmReportController@folist');
Route::get('/report/tsm/distlist', 'Sales\tsmReportController@tsmDist');

Route::get('/report/tsm/fo-attendance', 'Sales\tsmReportController@foaAttendance');
Route::get('/report/tsm/fo-attendance-list', 'Sales\tsmReportController@foaAttendanceList');

Route::get('/report/tsm/retailer', 'Sales\tsmReportController@retailer');
Route::get('/report/tsm/retailer-list', 'Sales\tsmReportController@retailerList');

Route::get('/report/tsm/fo-wise-report', 'Sales\tsmReportController@foWiseReport');

Route::get('/report/tsm/folist', 'Sales\tsmReportController@folist');
Route::get('/report/tsm/distlist', 'Sales\tsmReportController@tsmDist');
Route::get('/report/tsm/foattendance', 'Sales\tsmReportController@foaAttendance');


Route::get('/report/tsm/db-wise-requisition', 'Sales\tsmReportController@db_wise_requisition');
Route::get('/report/tsm/db-wise-requisition-list', 'Sales\tsmReportController@db_wise_requisition_list');

Route::get('/report/tsm/retailer-ledger', 'Sales\tsmReportController@retailer_ledger');
Route::get('/report/tsm/retailer-ledger-list', 'Sales\tsmReportController@retailer_ledger_list');

Route::get('/report/tsm/daily-ims-report', 'Sales\tsmReportController@daily_ims_report');
Route::get('/report/tsm/daily-ims-report-list', 'Sales\tsmReportController@daily_ims_report_list');

Route::get('/report/tsm/monthly-ims-status', 'Sales\tsmReportController@monthly_ims_status');
Route::get('/report/tsm/monthly-ims-status-list', 'Sales\tsmReportController@monthly_ims_status_list');

Route::get('/report/tsm/pg-wise-report', 'Sales\tsmReportController@pg_wise_report');
Route::get('/report/tsm/pg-wise-report-list', 'Sales\tsmReportController@pg_wise_report_list');


Route::get('/report/tsm/monthly-fo-performance', 'Sales\tsmReportController@monthly_fo_performance');
Route::get('/report/tsm/monthly-fo-performance-list', 'Sales\tsmReportController@monthly_fo_performance_list');


// Division -> Point -> Fo
Route::get('/report/tsm/division-wise-points', 'Sales\tsmReportController@division_wise_points');
Route::get('/report/tsm/point-wise-fos','Sales\tsmReportController@point_wise_fos');

// Division -> Point -> Route
Route::get('/report/tsm/division-wise-points1', 'Sales\tsmReportController@division_wise_points1');
Route::get('/report/tsm/point-wise-route','Sales\tsmReportController@point_wise_routes');


Route::get('/report/tsm/fo-performance-daily-report', 'Sales\tsmReportController@ssg_fo_performance_report');


//----> Masud TSM --------------------------------------->///

/* ---------- Sharif TSM----------- */

Route::get('/report/tsm/depot_stock_list', 'Sales\tsmReportController@ssg_depot_stock_list');

Route::post('/report/tsm/stock_products', 'Sales\tsmReportController@ssg_stock_products');

/* ----------FO Performance report for TSM----------- */

Route::get('/report/tsm/fo-performance-report', 'Sales\tsmReportController@fo_performance_report');

Route::post('/report/tsm/fo-performance-list', 'Sales\tsmReportController@fo_performance_list');

///////////////////////////////// MASUD RANA /////////////////////////////////////


/*
|--------------------------------------------------------------------------
| Apps API Routes/ Masud Start
|--------------------------------------------------------------------------
*/

Route::get('apps/api/login','Sales\MasterAppsController@ssg_apps_api_login');
Route::get('apps/api/dashboard','Sales\MasterAppsController@ssg_apps_api_dashboard');
Route::get('apps/api/route','Sales\MasterAppsController@ssg_apps_api_route');
Route::get('apps/api/attendance','Sales\MasterAppsController@ssg_apps_api_attendance');
Route::get('apps/api/order-process','Sales\MasterAppsController@ssg_apps_api_order_process');

// ORDER VISIT & NONVISIT OPTION
Route::get('apps/api/visit','Sales\MasterAppsController@ssg_apps_api_visit');
Route::get('apps/api/visit-submit','Sales\MasterAppsController@ssg_apps_api_visit_submit');

// ORDER VISIT & NONVISIT OPTION
Route::get('apps/api/attendance-list','Sales\MasterAppsController@ssg_apps_api_attendance_list');

// ORDER MANAGEMENT
Route::post('apps/api/confirm-order','Sales\MasterAppsController@ssg_apps_api_confirm_order');

/*
|--------------------------------------------------------------------------
| Apps API Routes/ Masud End
|--------------------------------------------------------------------------
*/

//MANAGEMENT REPORT
Route::get('/report/management', 'Management\ManagementReportController@ssg_management');
Route::get('/management/filtering', 'Management\ManagementReportController@filtering');


//ACTIVATION 
Route::get('/activation', 'Sales\AdminController@ssg_fo_activation');
Route::post('/activation-submit', 'Sales\AdminController@ssg_fo_activation_submit');


// ADMIN PANEL ACTIVATION 

Route::get('/admin/retailer', 'Sales\AdminController@ssg_fo_admin_activation');
Route::post('/admin/retailer-submit', 'Sales\AdminController@ssg_fo_admin_activation_submit');

Route::get('/admin/activation', 'Sales\AdminController@ssg_fo_admin_activation');
Route::get('/admin/activation-done/{id}', 'Sales\AdminController@ssg_fo_admin_activation_done');
Route::post('/admin/activation-submit', 'Sales\AdminController@ssg_fo_admin_activation_submit');

Route::get('/admin/new-retailer', 'Sales\AdminController@ssg_fo_admin_new_retailer');
Route::get('/admin/retailer-done/{id}', 'Sales\AdminController@ssg_fo_admin_retailer_done');
Route::post('/admin/retailer-submit', 'Sales\AdminController@ssg_fo_admin_retailer_submit');
Route::get('/admin/new-retailer-delete', 'Sales\AdminController@ssg_fo_admin_retailer_delete');


// ADMIN PANEL COMMISSION SETUP OPTION 

Route::get('/admin/commission', 'Sales\CommissionController@ssg_admin_commission');
Route::get('/admin/commission-add', 'Sales\CommissionController@ssg_admin_commission_add');
Route::post('/admin/commission-submit', 'Sales\CommissionController@ssg_admin_commission_submit');
Route::get('/admin/commission-edit/{id}', 'Sales\CommissionController@ssg_admin_commission_edit');
Route::post('/admin/commission-update', 'Sales\CommissionController@ssg_admin_commission_update');

// ADMIN PANEL EXCEPT CATEGORY COMMISSION SETUP OPTION

Route::get('/admin/except-commission', 'Sales\CommissionController@ssg_admin_except_commission');
Route::get('/admin/except-commission-add', 'Sales\CommissionController@ssg_admin_except_commission_add');
Route::post('/admin/except-commission-submit', 'Sales\CommissionController@ssg_admin_except_commission_submit');
Route::get('/admin/except-commission-edit/{id}', 'Sales\CommissionController@ssg_admin_except_commission_edit');
Route::post('/admin/except-commission-update', 'Sales\CommissionController@ssg_admin_except_commission_update');

// SPECIAL OFFER OTHERS SETUP OPTION
Route::get('/offer/other-products', 'Sales\Offer\OfferController@ssg_others');
Route::post('/offer/other-save','Sales\Offer\OfferController@ssg_others_save');

Route::get('/offer/other-edit','Sales\Offer\OfferController@ssg_others_edit');
Route::post('/offer/other-edit-process','Sales\Offer\OfferController@ssg_others_edit_process');
Route::get('/offer/other-delete','Sales\Offer\OfferController@ssg_others_delete');

//SPECIAL OFFER FO PANEL
Route::get('/show-special-products', 'Sales\VisitController@ssg_show_special_products');
Route::post('/show-special-products-submit', 'Sales\VisitController@ssg_show_special_products_submit');

//REGULAR OFFER FO PANEL
Route::get('/show-regular-products', 'Sales\VisitController@ssg_show_regular_products');
Route::post('/show-regular-products-submit', 'Sales\VisitController@ssg_show_regular_products_submit');

// SPECIAL OFFER VALUE WISE PRODUCT PROCESS

Route::get('/order-process-valuewise/{retailderid}/{routeid}/{orderid}/{amount}/{catid}/{offerid}/{pagestatus}/{partialOrder}', 'Sales\VisitController@ssg_order_valuewise_process');
Route::post('/add-to-cart-value-wise', 'Sales\VisitController@ssg_add_to_cart_value_wise_products');




//FO PANEL RETURN OPTION ROUTES

Route::get('/fo/return-only-product', 'RequisitionProcess\ReturnOnlyController@returnproduct');
Route::post('/fo/return-only-retailer', 'RequisitionProcess\ReturnOnlyController@return_retailer');
Route::get('/fo/return-only-process/{retailderid}/{routeid}', 'RequisitionProcess\ReturnOnlyController@return_process');
Route::post('/fo/return-only-category-products', 'RequisitionProcess\ReturnOnlyController@return_products');
Route::post('/fo/return-only-add-to-cart', 'RequisitionProcess\ReturnOnlyController@return_add_to_cart_products');
Route::get('/fo/return-only-bucket/{pointid}/{routeid}/{retailderid}', 'RequisitionProcess\ReturnOnlyController@return_bucket');
Route::post('/fo/return-only-items-edit', 'RequisitionProcess\ReturnOnlyController@return_items_edit');
Route::post('/fo/return-only-edit-submit', 'RequisitionProcess\ReturnOnlyController@return_items_edit_submit');
Route::get('/fo/return-only-items-del', 'RequisitionProcess\ReturnOnlyController@return_items_delete');
Route::get('/fo/confirm-only-return/{orderpid}/{orderid}/{retailderid}/{routeid}/{pointid}/{distributorID}', 'RequisitionProcess\ReturnOnlyController@ssg_confirm_return');
Route::post('/fo/delete-only-return', 'RequisitionProcess\ReturnOnlyController@ssg_delete_return');


// DISTRIBUTOR PANEL RETURN OPTION ROUTES

Route::get('/fo/return-only-order', 'RequisitionProcess\ChangeOnlyController@ssg_return_order');
//Route::post('/fo/return-only-order-list', 'RequisitionProcess\ChangeOnlyController@ssg_return_order_list');
Route::get('/fo/change-only-category-products/{return_order_id}', 'RequisitionProcess\ChangeOnlyController@return_change_products');
Route::post('/fo/confirm-only-return-change', 'RequisitionProcess\ChangeOnlyController@ssg_confirm_return_change');
Route::get('/fo/return-only-invoice/{orderMainId}/{foMainId}', 'RequisitionProcess\ChangeOnlyController@ssg_return_invoice_order');

Route::post('/fo/return-order-list', 'RequisitionProcess\ChangeOnlyController@ssg_return_order_list');


// FO PANEL RETURN CHANGE OPTION ROUTES

Route::get('/fo/returnproduct', 'RequisitionProcess\ReturnController@returnproduct');
Route::post('/fo/return-retailer', 'RequisitionProcess\ReturnController@return_retailer');
Route::get('/fo/return-process/{retailderid}/{routeid}', 'RequisitionProcess\ReturnController@return_process');
Route::post('/fo/return-category-products', 'RequisitionProcess\ReturnController@return_products');
Route::post('/fo/return-add-to-cart', 'RequisitionProcess\ReturnController@return_add_to_cart_products');
Route::get('/fo/return-bucket/{pointid}/{routeid}/{retailderid}', 'RequisitionProcess\ReturnController@return_bucket');
Route::post('/fo/return-items-edit', 'RequisitionProcess\ReturnController@return_items_edit');
Route::post('/fo/return-edit-submit', 'RequisitionProcess\ReturnController@return_items_edit_submit');
Route::get('/fo/return-items-del', 'RequisitionProcess\ReturnController@return_items_delete');
Route::get('/fo/confirm-return/{orderpid}/{orderid}/{retailderid}/{routeid}/{pointid}/{distributorID}', 'RequisitionProcess\ReturnController@ssg_confirm_return');
Route::post('/fo/delete-return', 'RequisitionProcess\ReturnController@ssg_delete_return');

Route::get('/fo/return_change/get_product','RequisitionProcess\ReturnController@get_product');
Route::get('/fo/return_change/get_product_price','RequisitionProcess\ReturnController@get_product_price');

// DISTRIBUTOR PANEL RETURN & CHANGE OPTION ROUTES

Route::get('/fo/returnorder', 'RequisitionProcess\ChangeController@ssg_return_order');
Route::post('/fo/return-change-order-list', 'RequisitionProcess\ChangeController@ssg_return_order_list');
Route::get('/fo/change-category-products/{return_order_id}', 'RequisitionProcess\ChangeController@return_change_products');
Route::post('/fo/confirm-return-change', 'RequisitionProcess\ChangeController@ssg_confirm_return_change');
Route::get('/fo/return-invoice/{orderMainId}/{foMainId}', 'RequisitionProcess\ChangeController@ssg_return_invoice_order');


// DISTRIBUTOR PANEL INVENTORY MENU OPTION ROUTES

Route::get('/dist/stock', 'RequisitionProcess\DistributorStockController@ssg_depot_stock_list');
Route::post('/dist/stock_products', 'RequisitionProcess\DistributorStockController@ssg_stock_products');
Route::get('/dist/stock_export', 'RequisitionProcess\StockExport@stock_export');

Route::get('/dist/inventory', 'RequisitionProcess\DistributorStockController@ssg_depot_distributor');

Route::get('/dist/stock-process/{depotID}/{inOut}', 'RequisitionProcess\DistributorStockController@ssg_stock_process');
//Route::post('/dist/category-products', 'RequisitionProcess\DistributorStockController@ssg_category_products');
Route::post('/dist/inventory_file_upload','Master\MasterUploadController@stockPhysicalInventoryUpload');
Route::post('/dist/add_to_inventory', 'RequisitionProcess\DistributorStockController@products_add_to_inventory');


// DISTRIBUTOR PANEL REQUISITION OPTION ROUTES

// Route::get('/dist/req-manage', 'RequisitionProcess\DistributorRequisition@req_manage');
// Route::get('/dist/req-add', 'RequisitionProcess\DistributorRequisition@req_add');
// Route::post('/dist/req-process', 'RequisitionProcess\DistributorRequisition@req_process');

// Route::get('/dist/req-list_product/{reqId}', 'RequisitionProcess\DistributorRequisition@req_list_product');
// Route::get('/dist/req-category-products', 'RequisitionProcess\DistributorRequisition@req_category_products');
// Route::post('/dist/req-add-to-product', 'RequisitionProcess\DistributorRequisition@req_add_product');

// Route::get('/dist/req-bucket/{reqid}', 'RequisitionProcess\DistributorRequisition@req_bucket');

// Route::get('/dist/req-send/{reqid}', 'RequisitionProcess\DistributorRequisition@req_send');
// Route::get('/dist/req-send_list', 'RequisitionProcess\DistributorRequisition@req_send_list');
// Route::get('/dist/req-received_list', 'RequisitionProcess\DistributorRequisition@req_received_list');

// Route::get('/dist/req-items-edit', 'RequisitionProcess\DistributorRequisition@depot_items_edit');
// Route::post('/dist/req-edit-submit', 'RequisitionProcess\DistributorRequisition@depot_items_edit_submit');
// Route::get('/dist/depot-req-items-delete', 'RequisitionProcess\DistributorRequisition@depot_req_items_delete');

// Route::get('/dist/reqDetails/{reqid}', 'RequisitionProcess\DistributorRequisition@req_details_list');


// Route::get('/dist/reqAcknowledgeList', 'RequisitionProcess\DistributorRequisition@req_acknowledge_list');
// Route::get('/dist/reqApprovedList', 'RequisitionProcess\DistributorRequisition@req_approved_list');
// Route::get('/dist/reqCanceledList', 'RequisitionProcess\DistributorRequisition@req_canceled_list');
// Route::get('/dist/reqDeliveredList', 'RequisitionProcess\DistributorRequisition@req_delivered_list');

// Route::post('/dist/reqReceive/', 'RequisitionProcess\DistributorRequisition@req_receive');

// Route::get('/dist/reqReceivedList', 'RequisitionProcess\DistributorRequisition@req_received_list');

// Route::get('/dist/reqDeliveryReceivedList/{reqid}', 'RequisitionProcess\DistributorRequisition@req_delivery_received_list');


// DISTRIBUTOR PANEL REQUISITION OPTION ROUTES

Route::get('/dist/req-manage', 'RequisitionProcess\DistributorRequisition@req_manage');
Route::get('/dist/req-add', 'RequisitionProcess\DistributorRequisition@req_add');
Route::post('/dist/req-process', 'RequisitionProcess\DistributorRequisition@req_process');

Route::get('/dist/req-list_product/{reqId}', 'RequisitionProcess\DistributorRequisition@req_list_product');
Route::get('/dist/req-category-products', 'RequisitionProcess\DistributorRequisition@req_category_products');
Route::post('/dist/req-add-to-product', 'RequisitionProcess\DistributorRequisition@req_add_product');

Route::get('/dist/req-bucket/{reqid}', 'RequisitionProcess\DistributorRequisition@req_bucket');

Route::get('/dist/req-send/{reqid}', 'RequisitionProcess\DistributorRequisition@req_send');
Route::get('/dist/req-send_list', 'RequisitionProcess\DistributorRequisition@req_send_list');
Route::get('/dist/req-received_list', 'RequisitionProcess\DistributorRequisition@req_received_list');

Route::get('/dist/req-items-edit', 'RequisitionProcess\DistributorRequisition@depot_items_edit');
Route::post('/dist/req-edit-submit', 'RequisitionProcess\DistributorRequisition@depot_items_edit_submit');
Route::get('/dist/depot-req-items-delete', 'RequisitionProcess\DistributorRequisition@depot_req_items_delete');

Route::get('/dist/reqDetails/{reqid}', 'RequisitionProcess\DistributorRequisition@req_details_list');


Route::get('/dist/reqAcknowledgeList', 'RequisitionProcess\DistributorRequisition@req_acknowledge_list');
Route::get('/dist/reqApprovedList', 'RequisitionProcess\DistributorRequisition@req_approved_list');
Route::get('/dist/reqCanceledList', 'RequisitionProcess\DistributorRequisition@req_canceled_list');
Route::get('/dist/reqDeliveredList', 'RequisitionProcess\DistributorRequisition@req_delivered_list');

Route::post('/dist/reqReceive/', 'RequisitionProcess\DistributorRequisition@req_receive');

Route::get('/dist/reqReceivedList', 'RequisitionProcess\DistributorRequisition@req_received_list');

Route::get('/dist/reqDeliveryReceivedList/{reqid}', 'RequisitionProcess\DistributorRequisition@req_delivery_received_list');

//Route::get('/reqDetails/{reqid}', 'Depot\DepotRequisition@req_details_list');
Route::get('/dist/reqApprovedDetails/{reqid}', 'RequisitionProcess\DistributorRequisition@req_approved_details_list');
Route::get('/dist/reqDeliveredDetails/{reqid}', 'RequisitionProcess\DistributorRequisition@req_delivered_details_list');
Route::get('/dist/reqReceivedDetails/{reqid}', 'RequisitionProcess\DistributorRequisition@req_received_details_list');

Route::get('/dist/reqDeliveryChallan/{reqid}', 'RequisitionProcess\DistributorRequisition@req_delivery_challan');


// DISTRIBUTOR REQUISITION FOR BILLING

Route::get('/dist/reqPendingList', 'RequisitionProcess\DistributorRequisition@req_pending_list');

Route::post('/dist/reqAcknowledge', 'RequisitionProcess\DistributorRequisition@req_acknowledge');
Route::get('/dist/reqAllAnalysisList', 'RequisitionProcess\DistributorRequisition@req_analysis_list');

Route::get('/dist/reqOrderAnalysis/{reqid}', 'RequisitionProcess\DistributorRequisition@req_order_analysis');

//Route::get('/reqApproved/{reqid}', 'Depot\DepotRequisition@req_approved');
Route::post('/dist/reqApproved', 'RequisitionProcess\DistributorRequisition@req_approved');

Route::get('/dist/reqAllApprovedList', 'RequisitionProcess\DistributorRequisition@req_all_approved_list');
Route::get('/dist/reqOpenOrderList/{reqid}', 'RequisitionProcess\DistributorRequisition@req_open_order_list');

Route::get('/dist/reqDelivere/{reqid}', 'RequisitionProcess\DistributorRequisition@req_deliver');
Route::get('/dist/reqAllDeliveredList', 'RequisitionProcess\DistributorRequisition@req_all_delivered_list');

Route::get('/dist/reqCanceled/{reqid}', 'RequisitionProcess\DistributorRequisition@req_canceled');
Route::get('/dist/reqAllCanceledList', 'RequisitionProcess\DistributorRequisition@req_all_canceled_list');

Route::get('/dist/reqAllReceivedList', 'RequisitionProcess\DistributorRequisition@req_all_received_list');

Route::get('dist/export', 'RequisitionProcess\DistributorRequisition@export');

// DISTRIBUTOR PAYMENT ROUTES

Route::get('/newDistriPayment','distributorPayment\DistriPayment@distri_payments');
Route::post('/distri_payment_process','distributorPayment\DistriPayment@distri_payment_process');
Route::get('/paymentDistriDelete','distributorPayment\DistriPayment@distri_payment_delete');
Route::post('/distriPaymentEdit','distributorPayment\DistriPayment@distri_payment_edit');
Route::post('/paymentEditProcess','distributorPayment\DistriPayment@distri_payment_edit_process');

// for billing
Route::get('/distPaymentList','distBilling\distriBilling@paymentList');
Route::post('/paymentAcknowledge','distBilling\distriBilling@ackDistriProcess');
Route::get('/ackList','distBilling\distriBilling@ackList');

// for accounts
Route::get('/distPaymentAckList','distBilling\distriBilling@paymentAcknowledgeList');
Route::post('/paymentVerify','distBilling\distriBilling@verifyDistriProcess');
Route::get('/verifiedList','distBilling\distriBilling@verifiedList');


// IMS REPORT
Route::get('/ims/report','Sales\ImsReportController@ims_report');
Route::get('/report/ims/order-list','Sales\ImsReportController@ims_report_list');
Route::get('/ims/get-point-fo-list','Sales\ImsReportController@get_point_fo_list');

Route::get('/ims/report-delivery','Sales\ImsReportController@ims_delivery_report');
Route::get('/report/ims/order-list-delivery','Sales\ImsReportController@ims_delivery_report_list');

Route::get('/ims/dist-req','Sales\ImsReportController@ims_distributor_requisition');
Route::get('/ims/dist-req-list','Sales\ImsReportController@ims_distributor_requisition_list');

//depo req
Route::get('/ims/depo-req','Sales\ImsReportController@ims_depo_requisition');
Route::get('/ims/depo-req-list','Sales\ImsReportController@ims_depo_requisition_list');

// VALUE WISE COMMISSION NEW OPTIONS

Route::get('/offer/value/other-edit','Sales\Offer\OfferController@ssg_others_value_edit');

Route::post('/offer/value/other-edit-process','Sales\Offer\OfferController@ssg_others_value_edit_process');
Route::get('/offer/value/offer-pro-delete','Sales\Offer\OfferController@ssg_others_value_delete');

// Retailer Remaining Commission Report

Route::get('/report/remaining-commission', 'Sales\DistributorReportController@ssg_report_remaining_commission');
Route::post('/report/remaining-commission-list', 'Sales\DistributorReportController@ssg_report_remaining_commission_list');



///////////////////////////////// END MASUD RANA /////////////////////////////////////

///////////////////////////////// START SHARIFUR RAHMAN /////////////////////////////////////

// Wastage declaration start

Route::get('/dist/was-declaration-manage', 'RequisitionProcess\DistributorWastageRequisition@was_declaration_manage');
Route::get('/dist/was-declaration-add', 'RequisitionProcess\DistributorWastageRequisition@was_declaration_add');
Route::post('/dist/was-declaration-process', 'RequisitionProcess\DistributorWastageRequisition@was_declaration_process');
Route::get('/dist/was-declaration-category-products', 'RequisitionProcess\DistributorWastageRequisition@was_declaration_category_products');
Route::get('/dist/was-declaration-list-product/{reqId}', 'RequisitionProcess\DistributorWastageRequisition@was_declaration_list_product');
Route::get('/dist/was-declaration-bucket/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_declaration_bucket');

Route::post('/dist/was-declaration-add-to-product', 'RequisitionProcess\DistributorWastageRequisition@was_declaration_add_product');


// DISTRIBUTOR Wastage REQUISITION OPTION ROUTES


Route::get('/dist/was-req-manage', 'RequisitionProcess\DistributorWastageRequisition@was_req_manage');
Route::get('/dist/was-req-add', 'RequisitionProcess\DistributorWastageRequisition@was_req_add');
Route::post('/dist/was-req-process', 'RequisitionProcess\DistributorWastageRequisition@was_req_process');

Route::get('/dist/was-req-list-product/{reqId}', 'RequisitionProcess\DistributorWastageRequisition@was_req_list_product');
Route::get('/dist/was-req-category-products', 'RequisitionProcess\DistributorWastageRequisition@was_req_category_products');
Route::get('/dist/was-req-bucket/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_bucket');
Route::get('/dist/was-req-items-edit', 'RequisitionProcess\DistributorWastageRequisition@was_req_items_edit');
Route::get('/dist/was-req-send/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_send');
Route::get('/dist/was-req-list-product/{reqId}', 'RequisitionProcess\DistributorWastageRequisition@was_req_list_product');
Route::post('/dist/was-req-add-to-product', 'RequisitionProcess\DistributorWastageRequisition@was_req_add_product');
Route::get('/dist/was-req-send/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_send');
Route::get('/dist/was-req-send-list', 'RequisitionProcess\DistributorWastageRequisition@was_req_send_list');
Route::get('/dist/was-reqDetails/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_details_list');
Route::get('/dist/was-req-delivered-list', 'RequisitionProcess\DistributorWastageRequisition@was_req_delivered_list');
Route::get('/dist/was-req-delivered-details-list/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_delivered_details_list');
Route::get('/dist/was-req-delivery-received-list/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_delivery_received_list');
Route::post('/dist/was-req-receive/', 'RequisitionProcess\DistributorWastageRequisition@was_req_receive');
Route::get('/dist/was-req-delivery-challan/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_delivery_challan');
// Wastage Requisition for billing part

Route::get('/dist/was-req-pending-list', 'RequisitionProcess\DistributorWastageRequisition@was_req_pending_list');
Route::post('/dist/was-req-acknowledge', 'RequisitionProcess\DistributorWastageRequisition@was_req_acknowledge');
Route::get('/dist/was-req-analysis-list', 'RequisitionProcess\DistributorWastageRequisition@was_req_analysis_list');
Route::get('/dist/was-req-order-analysis/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_order_analysis');
Route::post('/dist/was-req-approved', 'RequisitionProcess\DistributorWastageRequisition@was_req_approved');
Route::get('/dist/was-req-details-list/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_details_list');

Route::get('/dist/was-req-all-approved-list', 'RequisitionProcess\DistributorWastageRequisition@was_req_all_approved_list');
Route::get('/dist/was-req-open-order-list/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_open_order_list');
Route::get('/dist/was-req-canceled/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_canceled');

Route::get('/dist/was-req-deliver/{reqid}', 'RequisitionProcess\DistributorWastageRequisition@was_req_deliver');



// DISTRIBUTOR FREE REQUISITION OPTION ROUTES


Route::get('/dist/free-req-manage', 'RequisitionProcess\DistributorFreeRequisition@free_req_manage');
Route::get('/dist/free-req-add', 'RequisitionProcess\DistributorFreeRequisition@free_req_add');
Route::post('/dist/free-req-process', 'RequisitionProcess\DistributorFreeRequisition@free_req_process');

Route::get('/dist/free-req-list-product/{reqId}', 'RequisitionProcess\DistributorFreeRequisition@free_req_list_product');
Route::get('/dist/free-req-category-products', 'RequisitionProcess\DistributorFreeRequisition@free_req_category_products');
Route::get('/dist/free-req-bucket/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_bucket');
Route::get('/dist/free-req-items-edit', 'RequisitionProcess\DistributorFreeRequisition@free_req_items_edit');
Route::get('/dist/free-req-send/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_send');
Route::get('/dist/free-req-list-product/{reqId}', 'RequisitionProcess\DistributorFreeRequisition@free_req_list_product');
Route::post('/dist/free-req-add-to-product', 'RequisitionProcess\DistributorFreeRequisition@free_req_add_product');
Route::get('/dist/free-req-send/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_send');
Route::get('/dist/free-req-send-list', 'RequisitionProcess\DistributorFreeRequisition@free_req_send_list');
Route::get('/dist/free-req-details/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_details');
Route::get('/dist/free-req-delivered-list', 'RequisitionProcess\DistributorFreeRequisition@free_req_delivered_list');
Route::get('/dist/free-req-delivered-details-list/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_delivered_details_list');
Route::get('/dist/free-req-delivery-received-list/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_delivery_received_list');
Route::post('/dist/free-req-receive/', 'RequisitionProcess\DistributorFreeRequisition@free_req_receive');
Route::get('/dist/free-req-delivery-challan/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_delivery_challan');
// Wastage Requisition for billing part

Route::get('/dist/free-req-pending-list', 'RequisitionProcess\DistributorFreeRequisition@free_req_pending_list');
Route::post('/dist/free-req-acknowledge', 'RequisitionProcess\DistributorFreeRequisition@free_req_acknowledge');
Route::get('/dist/free-req-analysis-list', 'RequisitionProcess\DistributorFreeRequisition@free_req_analysis_list');
Route::get('/dist/free-req-order-analysis/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_order_analysis');
Route::post('/dist/free-req-approved', 'RequisitionProcess\DistributorFreeRequisition@free_req_approved');
Route::get('/dist/free-req-details-list/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_details_list');

Route::get('/dist/free-req-all-approved-list', 'RequisitionProcess\DistributorFreeRequisition@free_req_all_approved_list');
Route::get('/dist/free-req-open-order-list/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_open_order_list');
Route::get('/dist/free-req-canceled/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_canceled');

Route::get('/dist/free-req-deliver/{reqid}', 'RequisitionProcess\DistributorFreeRequisition@free_req_deliver');

Route::get('/dist/wastage-req-delete', 'RequisitionProcess\DistributorWastageRequisition@depot_req_items_delete');


// New Masud

Route::get('/visit-category-products-free', 'Sales\VisitController@ssg_category_products_free');


// EPP Panel

Route::get('/epp/memo-wise-sales', 'Sales\eppReportController@memoWiseSalesReport');
Route::get('/epp/memo-wise-sales-list', 'Sales\eppReportController@memoWiseSalesReportList');



/* Sales Data for Commission */

Route::get('/PartySalesHistory','Sales\SalesAdminController@retailer_sales_history');
Route::post('/ApplySalesCommission','Sales\SalesAdminController@sales_commission_process');
Route::post('/PointWiseRouteList', 'Sales\SalesAdminController@get_route_list');
Route::get('/RouteWiseRetailerList', 'Sales\SalesAdminController@get_retaier_list');


// Visit Frequency Report
Route::get('/sa/visit-frequency-report', 'Sales\SalesAdminController@ssg_visit_frequency_report');
Route::get('/sa/visit-frequency-report-list', 'Sales\SalesAdminController@ssg_visit_frequency_report_list');


// System Admin

Route::get('/sys/all-invoice', 'Sales\SystemController@ssg_all_order_manage');
Route::get('/sys/invoice-details/{orderMainId}/{foMainId}', 'Sales\SystemController@ssg_all_invoice_details_order');
Route::post('/sys/invoice-delete', 'Sales\SystemController@ssg_all_delete_order');

Route::get('/print/invoice-print', 'Sales\DistributorController@ssg_invoice_print');



//API START

Route::get('/tabs/login/{pointid}/{routeid}/{retailderid}/{partialOrder}/{username}/{password}','Sales\MasterAppsController@ssg_tabs_login');

Route::get('/tabs/loginom/{orderId}/{retailderid}/{routeid}/{partial_order_id}/{username}/{password}','Sales\MasterAppsController@ssg_tabs_login_order_manage');

//API END



Route::get('/sa/depot-operation-report', 'Sales\SalesAdminController@ssg_depot_report');
Route::get('/sa/depot-operation-report-list', 'Sales\SalesAdminController@ssg_depot_report_list');




/////////////////////// EXCEPTION ROUTES MASUD START //////////////////////////

// For Visit Routes

Route::get('/visit-exception', 'Sales\VisitControllerException@ssg_visit');

Route::post('/retailer-exception', 'Sales\VisitControllerException@ssg_retailer');

Route::get('/order-process-exception/{retailderid}/{routeid}/{offerType}', 'Sales\VisitControllerException@ssg_order_process');


Route::post('/visit-order-category-products-exception', 'Sales\VisitControllerException@ssg_order_category_products');


Route::post('/add-to-cart-exception', 'Sales\VisitControllerException@ssg_add_to_cart_products');

Route::get('/bucket-exception/{pointid}/{routeid}/{retailderid}/{partial_id}/{offerType}', 'Sales\VisitControllerException@ssg_bucket');
Route::post('/confirm-order-exception',  'Sales\VisitControllerException@ssg_confirm_order');

Route::get('/close-order-exception/{order_id}/{partialOrder}/{offerType}', 'Sales\VisitControllerException@ssg_order_closed');

Route::get('/bucket-offer-exception/{pointid}/{routeid}/{retailderid}/{partialOrder}/{offerType}', 'Sales\VisitControllerException@ssg_bucket_offer');

Route::post('/items-edit-exception', 'Sales\VisitControllerException@ssg_items_edit');
Route::post('/edit-submit-exception', 'Sales\VisitControllerException@ssg_items_edit_submit');
Route::post('/items-delete-exception', 'Sales\VisitControllerException@ssg_items_items_delete');


// Invoice Edit
Route::get('/all-invoice-exception/{orderId}/{retailderid}/{routeid}/{offerType}', 'Sales\VisitControllerException@all_invoice_exception');

Route::get('/invoice-edit-exception/{orderId}/{retailderid}/{routeid}/{partial_order_id}/{offerType}', 'Sales\VisitControllerException@ssg_order_edit_process');

Route::post('/add-to-edit-cart-exception', 'Sales\VisitControllerException@ssg_add_to_edit_cart_products');
Route::get('/bucket-edit-exception/{orderid}/{pointid}/{routeid}/{retailderid}/{partial_id}/{offerType}', 'Sales\VisitControllerException@ssg_bucket_edit');

Route::get('/delete-order-exception/{orderID}/{retailderid}/{routeid}/{partial_id}', 'Sales\VisitControllerException@ssg_delete_order');


// For Order Manage Route

Route::get('/order-manage-exception', 'Sales\VisitControllerException@ssg_order_manage');
Route::get('/invoice-details-exception/{orderMainId}/{foMainId}', 'Sales\VisitControllerException@ssg_invoice_details_order');

Route::post('/visit-order-manage-category-products-exception', 'Sales\VisitControllerException@ssg_order_manage_category_products');

Route::get('/close-order-manage-exception/{order_id}/{partialOrder}/{offerType}', 'Sales\VisitControllerException@ssg_order_manage_closed');


/////////////////////// EXCEPTION ROUTES MASUD END //////////////////////////


//New Masud

Route::post('/confirm-return', 'Sales\ReturnController@ssg_confirm_return');

Route::get('/report/return-change-report', 'Sales\ChangeController@ssg_return_order_report');
Route::post('/report/return-change-report-list', 'Sales\ChangeController@ssg_return_order_report_list');
Route::get('/report/change-category-products/{return_order_id}', 'Sales\ChangeController@return_change_products_report');


// SPECIAL OFFER VALUE WISE PRODUCT PROCESS

Route::get('/order-process-valuewise-exception/{retailderid}/{routeid}/{orderid}/{amount}/{catid}/{offerid}/{pagestatus}/{partialOrder}/{offerType}', 'Sales\VisitControllerException@ssg_order_valuewise_process');

Route::post('/add-to-cart-value-wise-exception', 'Sales\VisitControllerException@ssg_add_to_cart_value_wise_products');


//New Bundle Offer

Route::get('/offers/bundle-offer-pro-edit-new/{id}', 'Sales\MasterOfferController@ssg_bundle_offer_pro_edit_new');

Route::get('/offers/bundle-offer-category-wise-pro-new', 'Sales\MasterOfferController@ssg_bundle_offer_category_wise_pro_new');

Route::post('/offers/bundle-offer-pro-update-new', 'Sales\MasterOfferController@ssg_bundle_offer_pro_update_new');

Route::get('/offers/bundle-items-delete/{id}', 'Sales\MasterOfferController@ssg_bundle_items_delete');
