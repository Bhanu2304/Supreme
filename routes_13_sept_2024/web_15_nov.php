<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::auth();
 

 
Route::get('/', function () {
 return view('auth/login');
});
 
Route::post('login', function () {
 return view('auth/login');
});

//Register Backend User From here show hashed password will genrated easily by using get
//Route::get('register', 'Auth\AuthController@getRegister');
//post data to save in backedn mysql table and save it for future logins.
Route::post('register', 'Auth\AuthController@getRegister');

//Verify Email Address For Login
Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
//For Opening The Url TO Change Passwords
Route::post('password/reset', 'Auth\PasswordController@postReset');
//For Saving The Password

//Route::post('register', 'Auth\RegisterController@create');
//show dashboard of transactions done by channel partner or merchant
Route::resource('dashboard', 'DashController');
Route::post('dashboard', 'DashController@get_data');

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

// Self User Registration
Route::get('/user-save-password', 'UserPasswordCreationController@saveUserPassword')->name('user-save-password');

// Web User Creation Starts From Here
Route::get('add-user', 'WebUserController@index')->name('add-user');
Route::post('save-user', 'WebUserController@save_user')->name('save-user');
Route::get('view-user', 'WebUserController@view_user')->name('view-user');
Route::get('edit-user', 'WebUserController@edit_user')->name('edit-user');
Route::post('update-user', 'WebUserController@update_user')->name('update-user');

// Service Center Creation Starts From Here
Route::get('add-centre', 'ServiceCenterController@add_center')->name('add-centre');
Route::post('save-centre', 'ServiceCenterController@save_centre')->name('save-centre'); 

Route::get('edit-centre', 'ServiceCenterController@edit_centre')->name('edit-centre');
Route::post('update-centre', 'ServiceCenterController@update_centre')->name('update-centre');
Route::get('map-pincode', 'ServiceCenterController@map_pincode')->name('map-pincode');
Route::post('save-map-pincode', 'ServiceCenterController@save_map_pincode')->name('save-map-pincode');
Route::post('get-sc-pincode', 'ServiceCenterController@get_map_pincode')->name('get-sc-pincode');
Route::post('remove-pincode', 'ServiceCenterController@remove_pincode')->name('remove-pincode');

Route::get('map-product-detail', 'ServiceCenterController@map_product')->name('map-product-detail');
Route::post('save-map-product', 'ServiceCenterController@save_map_product')->name('save-map-product');
Route::post('get-sc-product', 'ServiceCenterController@get_map_product')->name('get-sc-product');
Route::post('get-asc-code-by-asc-id', 'ServiceCenterController@get_asc_code')->name('get-asc-code-by-asc-id');

Route::post('get-ticket-date-by-ticket-no', 'TaggingController@get_ticket_date')->name('get-ticket-date-by-ticket-no');

// add se
Route::get('vendor-add-se', 'ServiceEngineerController@index')->name('vendor-add-se');
Route::post('vendor-save-se', 'ServiceEngineerController@save_se')->name('vendor-save-se');
Route::get('vendor-se-view', 'ServiceEngineerController@se_user')->name('vendor-se-view');
Route::get('vendor-se-edit', 'ServiceEngineerController@edit_se')->name('vendor-se-edit');
Route::post('vendor-se-update', 'ServiceEngineerController@update_se')->name('vendor-se-update');
Route::get('se-job-view', 'SeJobController@index')->name('se-job-view');
Route::post('se-job-save', 'SeJobController@save_shd')->name('se-job-save');
Route::get('se-job-ob', 'SeJobController@observation')->name('se-job-ob');
Route::post('se-save-ob', 'SeJobController@save_observation')->name('se-save-ob');

// add coord
Route::get('add-coord', 'CoordinatorController@index')->name('add-coord');
Route::post('save-coord', 'CoordinatorController@save_coord')->name('save-coord');
Route::get('coord-view', 'CoordinatorController@coord_user')->name('coord-view');
Route::get('coord-edit', 'CoordinatorController@edit_coord')->name('coord-edit');
Route::post('coord-update', 'CoordinatorController@update_coord')->name('coord-update');


// add manager
Route::get('add-man', 'ManagerController@index')->name('add-man');
Route::post('save-man', 'ManagerController@save_man')->name('save-man');
Route::get('man-view', 'ManagerController@man_user')->name('man-view');
Route::get('man-edit', 'ManagerController@edit_man')->name('man-edit');
Route::post('man-update', 'ManagerController@update_man')->name('man-update');

//add regional manager
Route::get('add-reg-man',   'ManagerController@add_region_manager')->name('add-reg-man');
Route::post('save-reg-man', 'ManagerController@save_region_man')->name('save-reg-man');
Route::get('edit-reg-man',   'ManagerController@edit_region_man')->name('edit-reg-man');
Route::post('update-reg-man',   'ManagerController@update_region_man')->name('update-reg-man');
Route::get('map-region',    'ManagerController@map_region')->name('map-region');
Route::post('save-map-area',    'ManagerController@save_map_area')->name('save-map-area');
Route::post('get-rm-area', 'ManagerController@get_map_area')->name('get-rm-area');
Route::post('remove-rm-area', 'ManagerController@remove_map_area')->name('remove-rm-area');

// sc service center management
Route::get('ho-alloc-view', 'AllocationManagementController@index')->name('ho-alloc-view');
Route::get('ho-tag-view', 'AllocationManagementController@view_ob')->name('ho-tag-view');
Route::get('ho-alloc-se-view', 'AllocationManagementController@se_view')->name('ho-alloc-se-view');
Route::post('ho-allocate-se', 'AllocationManagementController@allocate_se')->name('ho-allocate-se');

Route::get('vendor-tag-view', 'VendorManagementController@index')->name('vendor-tag-view');
Route::post('accept-job', 'JobAcceptController@index')->name('accept-job');
Route::post('reject-job', 'JobAcceptController@reject')->name('reject-job');
Route::get('allocate-se', 'VendorManagementController@allocate_case')->name('allocate-se');
Route::get('vendor-audit-view', 'VendorManagementController@view_audit')->name('vendor-audit-view');
Route::post('vendor-get-pin', 'PincodeController@get_pincode_by_state_id')->name('vendor-get-pin');
Route::get('vendor-observation', 'VendorManagementController@observation')->name('vendor-observation');
Route::post('vendor-save-observation', 'VendorManagementController@save_observation')->name('vendor-save-observation');
Route::post('se-allocate', 'VendorManagementController@allocate_se')->name('se-allocate'); 
Route::get('view-se-allocate', 'VendorManagementController@view_allocate_case')->name('view-se-allocate'); 
Route::post('se-reallocate', 'VendorManagementController@reallocate_se')->name('se-reallocate');
Route::get('close-case-view', 'InvoiceController@view_close_case')->name('close-case-view'); 
Route::post('close-case-view', 'InvoiceController@view_close_case')->name('close-case-view'); 
Route::get('view_img', 'VendorManagementController@view_img')->name('view_img');
Route::get('view-pdf', 'VendorManagementController@search_pen_case')->name('view-pdf');

Route::get('center-alloc-view', 'VendorManagementController@view_ce_alloc')->name('center-alloc-view');
Route::post('ce-allocate', 'VendorManagementController@allocate_ce')->name('ce-allocate'); 

Route::get('center-realloc-view', 'VendorManagementController@view_ce_realloc')->name('center-realloc-view');
Route::post('ce-reallocate', 'VendorManagementController@reallocate_ce')->name('ce-reallocate'); 

Route::get('vendor-pen-view', 'VendorManagementController@search_pen_case')->name('vendor-pen-view');
Route::get('vendor-pen-view-case', 'VendorManagementController@view_pen_case')->name('vendor-pen-view-case');
Route::post('get-add-part', 'VendorManagementController@add_part')->name('get-add-part');

//partial close window
Route::get('view-partial-close-case', 'CallTaggingController@search_audit_case')->name('view-partial-close-case');
Route::get('partial-close-observation', 'CallTaggingController@view_audit_case')->name('partial-close-observation');

//rma approval
Route::get('rma-tag-view', 'RMAApprovalController@index')->name('rma-tag-view');
Route::post('rma-approve', 'RMAApprovalController@save_multi_rma')->name('rma-approve'); 
Route::get('rma-tag-case-view', 'RMAApprovalController@view_tag_case')->name('rma-tag-case-view');  
Route::post('rma-case-approval', 'RMAApprovalController@save_rma')->name('rma-case-approval');

//part approval
Route::get('part-tag-view', 'PartApprovalController@index')->name('part-tag-view');
Route::post('part-approve', 'PartApprovalController@save_multi_rma')->name('part-approve'); 
Route::get('part-tag-case-view', 'PartApprovalController@view_tag_case')->name('part-tag-case-view');  
Route::post('part-case-approval', 'PartApprovalController@save_rma')->name('part-case-approval');


//Route::post('vendor-add', 'VendorManagementController@save_vendor')->name('vendor-save');
//Route::get('vendor-view', 'VendorManagementController@view_vendor')->name('vendor-view');
//Route::get('vendor-edit', 'VendorManagementController@edit_vendor')->name('vendor-edit');
//Route::post('vendor-update', 'VendorManagementController@update_vendor')->name('vendor-update');
//Route::get('vendor-export', 'VendorManagementController@export_vendors')->name('vendor-export');

//state creation
Route::get('/add-state', 'StateController@index')->name('add-state');
Route::post('/save-state', 'StateController@save_state')->name('save-state');
Route::post('/state-exist', 'StateController@state-exist')->name('state-exist');
Route::get('/view-state', 'StateController@view_state')->name('view-state');
Route::post('/get-state', 'StateController@get_state')->name('get-state');
Route::get('/edit-state', 'StateController@edit_state')->name('edit-state');
Route::post('/state-exist-update', 'StateController@state_exist')->name('state-exist-update');
Route::post('/update-state', 'StateController@update_state')->name('update-state');
Route::post('/state-exist', 'StateController@state_exist')->name('state-exist');
Route::post('/state-exist-update', 'StateController@state_exist_update')->name('state-exist-update');

Route::get('/add-district', 'StateController@add_district')->name('add-district');
Route::get('/delete-district', 'StateController@delete_district')->name('delete-district');
Route::post('/save-district', 'StateController@save_district')->name('save-district');
Route::post('/get-district', 'StateController@get_district')->name('get-district');
Route::get('/edit-district', 'StateController@edit_district')->name('edit-district');
Route::post('/update-district', 'StateController@update_district')->name('update-district');
Route::post('get-state-by-region-id', 'StateController@get_state_by_region_id')->name('get-state-by-region-id');
Route::post('get-division-by-state-id', 'StateController@get_division_by_state_id')->name('get-division-by-state-id');
Route::post('get-district-by-div-name', 'StateController@get_district_by_div_name')->name('get-district-by-div-name');
Route::post('get-district-by-state-id', 'StateController@get_district_by_state_id')->name('get-district-by-state-id');
Route::post('get-district-by-state-id-map', 'StateController@get_district_by_state_id_map')->name('get-district-by-state-id-map');

Route::get('add-pincode', 'PincodeController@index')->name('add-pincode');
Route::post('/save-pincode', 'PincodeController@save_pincode')->name('save-pincode');
Route::post('/pincode-exist', 'PincodeController@pincode-exist')->name('pincode-exist');
Route::get('/view-pincode', 'PincodeController@view_pincode')->name('view-pincode');
Route::post('/get-states', 'PincodeController@get_states')->name('get-states');
Route::post('/get-pincode', 'PincodeController@get_pincode')->name('get-pincode');
Route::get('/edit-pincode', 'PincodeController@edit_pincode')->name('edit-pincode');
Route::post('/pincode-exist-update', 'PincodeController@pincode_exist')->name('pincode-exist-update');
Route::post('/update-pincode', 'PincodeController@update_pincode')->name('update-pincode');
Route::post('/pincode-exist', 'PincodeController@pincode_exist')->name('state-exist');
Route::post('/pincode-exist-update', 'PincodeController@pincode_exist_update')->name('pincode-exist-update');
Route::post('get-pincode-by-state', 'PincodeController@get_pincode_by_state')->name('get-pincode-by-state');
Route::post('get-pincode-by-dist-id', 'PincodeController@get_pincode_by_dist_id')->name('get-pincode-by-dist-id');
Route::post('get-pincode-by-mdist-id', 'PincodeController@get_pincode_by_mdist_id')->name('get-pincode-by-mdist-id');
Route::post('get-pincode-by-state-name', 'PincodeController@get_pincode_by_state_name')->name('get-pincode-by-state-name');
Route::post('get-area-by-pincode', 'PincodeController@get_area_by_pincode')->name('get-area-by-pincode');

// Brand Creation Starts From Here
Route::get('add-brand', 'ProductController@add_brand')->name('add-brand');
Route::post('save-brand', 'ProductController@save_brand')->name('save-brand');
Route::get('view-brand', 'ProductController@view_brand')->name('view-brand');
Route::get('edit-brand', 'ProductController@edit_brand')->name('edit-brand');
Route::post('update-brand', 'ProductController@update_brand')->name('update-brand');

// Product Category Creation Starts From Here
Route::get('add-product-category', 'ProductCategoryController@index')->name('add-product-category');
Route::post('save-product-category', 'ProductCategoryController@save_product_category')->name('save-product-category');
Route::get('edit-product-category', 'ProductCategoryController@edit_product_category')->name('edit-product-category');
Route::post('update-product-category', 'ProductCategoryController@update_product_category')->name('update-product-category');
Route::post('get-product-category-by-brand-name', 'ProductCategoryController@get_product_category_by_brand_name')->name('get-product-category-by-brand-name');
Route::post('get-product-category-by-brand-id',   'ProductCategoryController@get_product_category_by_brand_id')->name('get-product-category-by-brand-id');

// Product Creation Starts From Here
Route::get('add-product', 'ProductController@index')->name('add-product');
Route::post('save-product', 'ProductController@save_product')->name('save-product');
Route::get('view-product', 'ProductController@view_product')->name('view-product');
Route::get('edit-product', 'ProductController@edit_product')->name('edit-product');
Route::post('update-product', 'ProductController@update_product')->name('update-product');
Route::post('search-product', 'ProductController@search_product')->name('search-product');
Route::post('get-product-by-brand-name', 'ProductController@get_product_by_brand_name')->name('get-product-by-brand-name');
Route::post('get-product-by-brand-id', 'ProductController@get_product_by_brand_id')->name('get-product-by-brand-id');

// Model Creation Starts From Here
Route::get('add-model', 'ProductController@add_model')->name('add-model');
Route::post('save-model', 'ProductController@save_model')->name('save-model');
Route::get('view-model', 'ProductController@view_model')->name('view-model');
Route::get('edit-model', 'ProductController@edit_model')->name('edit-model');
Route::post('update-model', 'ProductController@update_model')->name('update-model');
Route::post('get-model-by-product-name', 'ProductController@get_model_by_product_name')->name('get-model-by-product-name');
Route::post('get-model-by-product-id', 'ProductController@get_model_by_product_id')->name('get-model-by-product-id');
Route::post('search-model', 'ProductController@search_model')->name('search-model');



// Region Creation Starts From Here
Route::get('add-region', 'ProductController@add_region')->name('add-region');
Route::post('save-region', 'ProductController@save_region')->name('save-region');
Route::get('view-region', 'ProductController@view_region')->name('view-region');
Route::get('edit-region', 'ProductController@edit_region')->name('edit-region');
Route::post('update-region', 'ProductController@update_region')->name('update-region');


// Accessories Creation Starts From Here
Route::get('add-acc', 'AccessoriesController@add_acc')->name('add-acc');
Route::post('save-acc', 'AccessoriesController@save_acc')->name('save-acc');
Route::get('view-acc', 'AccessoriesController@view_acc')->name('view-acc');
Route::get('edit-acc', 'AccessoriesController@edit_acc')->name('edit-acc');
Route::post('update-acc', 'AccessoriesController@update_acc')->name('update-acc');
Route::post('search-acc', 'AccessoriesController@search_acc')->name('search-acc');
Route::post('get-acc-dt', 'AccessoriesController@get_acc')->name('get-acc-dt');


// Set Condition Creation Starts From Here
Route::get('add-cndn', 'ConditionController@add_cndn')->name('add-cndn');
Route::post('save-cndn', 'ConditionController@save_cndn')->name('save-cndn');
Route::post('update-cndn', 'ConditionController@update_cndn')->name('update-cndn');
Route::post('delete-cndn', 'ConditionController@delete_cndn')->name('delete-cndn');
Route::post('search-cndn', 'ConditionController@search_cndn')->name('search-cndn');
Route::post('get-cndn-dt', 'ConditionController@get_cndn')->name('get-cndn-dt');
//Route::get('view-cond', 'ProductController@view_cond')->name('view-cond');
//Route::get('edit-cond', 'ProductController@edit_cond')->name('edit-cond');


// Spare Parts Creation Starts From Here
Route::get('add-part', 'SparePartController@index')->name('add-part');
Route::post('save-part', 'SparePartController@save_part')->name('save-part');
Route::get('edit-part', 'SparePartController@edit_part')->name('edit-part');
Route::post('update-part', 'SparePartController@update_part')->name('update-part');
Route::post('search-part', 'SparePartController@search_part')->name('search-part');

// Spare Parts Creation Starts From Here
Route::get('add-inv', 'InventoryController@index')->name('add-inv');
Route::post('save-inv', 'InventoryController@save_inv')->name('save-inv');
Route::get('inv-details', 'InventoryController@inv_details')->name('inv-details');
Route::get('edit-inv', 'InventoryController@edit_inv')->name('edit-inv');
Route::post('update-inv', 'InventoryController@update_inv')->name('update-inv');
Route::get('allocate-inv', 'InventoryCenterController@allocate_inv')->name('allocate-inv');
Route::post('save-allocate-inv', 'InventoryCenterController@save_allocate_inv')->name('save-allocate-inv');
Route::get('center-inv-details', 'InventoryCenterController@center_inv_details')->name('center-inv-details');
Route::get('edit-allocate-inv', 'InventoryCenterController@edit_allocate')->name('edit-allocate-inv');
Route::post('update-allocate-inv', 'InventoryCenterController@update_allocate_inv')->name('update-allocate-inv');
Route::post('get-stock', 'InventoryCenterController@get_stock')->name('get-stock');
Route::post('get-part-name', 'SparePartController@get_part_name_by_model')->name('get-part-name');
Route::post('get-part-no', 'SparePartController@get_partno_by_part_name')->name('get-part-no');
Route::post('get-hsn-code', 'InventoryController@get_hsnno_by_part_no')->name('get-hsn-code');
//Route::get('edit-inv', 'InventoryController@edit_inv')->name('edit-inv');
//Route::post('update-inv', 'InventoryController@update_inv')->name('update-inv');

//Center Inventory Management Master
Route::get('view-part-pending', 'InventoryManagementController@view_part_pending')->name('view-part-pending');
Route::post('save-part-approval', 'InventoryManagementController@approve_part_pending')->name('save-part-approval');
Route::post('save-part-approval-multiple', 'InventoryManagementController@approve_part_pending_multiple')->name('save-part-approval-multiple');


//HO Inventory Management Master
Route::get('view-part-pending-ho', 'HOInventoryManagementController@view_part_pending')->name('view-part-pending-ho');
Route::get('part-pending-job-view', 'HOInventoryManagementController@part_pending_view')->name('part-pending-job-view');
Route::post('save-part-approval-ho', 'HOInventoryManagementController@approve_part_pending')->name('save-part-approval-ho');
Route::post('save-part-approval-multiple-ho', 'HOInventoryManagementController@approve_part_pending_multiple')->name('save-part-approval-multiple-ho');



// Inventory Request Starts From Here
Route::get('req-inv-entry', 'RequestInventoryController@index')->name('req-inv-entry');
Route::post('get-add-req-part', 'RequestInventoryController@add_req_part')->name('get-add-req-part');
Route::post('get-part-rate', 'RequestInventoryController@get_rate_by_spare_part')->name('get-part-rate');
Route::post('save-request-entry', 'RequestInventoryController@save_req_inv')->name('save-request-entry');
Route::get('view-req-inv', 'RequestInventoryController@view')->name('view-req-inv');
Route::get('edit-req-inv-entry', 'RequestInventoryController@edit')->name('edit-req-inv-entry');
Route::post('upd-req-inv-entry', 'RequestInventoryController@update')->name('upd-req-inv-entry');

// HO PO Starts From Here
Route::get('req-inv-entry-ho', 'ApproveRequestInventoryController@view')->name('req-inv-entry-ho');
Route::get('approve-req-inv', 'ApproveRequestInventoryController@view_case')->name('approve-req-inv');
Route::post('save-approve-req-inv', 'ApproveRequestInventoryController@approve')->name('save-approve-req-inv');
Route::get('search-challan', 'ApproveRequestInventoryController@search_challan')->name('search-challan');

// Symptom code Creation Starts From Here
Route::get('add-symptom', 'SymptomController@add_symptom')->name('add-symptom');
Route::post('save-symptom', 'SymptomController@save_symptom')->name('save-symptom');
Route::get('edit-symptom', 'SymptomController@edit_symptom')->name('edit-symptom');
Route::post('update-symptom', 'SymptomController@update_symptom')->name('update-symptom');


// Warranty code Creation Starts From Here
Route::get('add-warranty', 'WarrantyController@add_warranty')->name('add-warranty');
Route::post('save-warranty', 'WarrantyController@save_warranty')->name('save-warranty');
Route::get('edit-warranty', 'WarrantyController@edit_warranty')->name('edit-warranty');
Route::post('update-warranty', 'WarrantyController@update_warranty')->name('update-warranty');

// Reports Creation
Route::get('open-calls-report', 'ReportController@index')->name('open-calls-report');
Route::post('export-report-job', 'ReportController@export_job_report')->name('export-report-job');
Route::get('report-pending', 'ReportController@report_pending')->name('report-pending');
Route::post('export-report-pending', 'ReportController@export_pending_report')->name('export-report-pending');
Route::get('report-case-view', 'ReportController@view_case')->name('report-case-view'); 

//Menu Access
Route::get('manage-access', 'ManageController@index')->name('manage-access');
Route::get('manage-page-access', 'ManageController@manage_page_access')->name('manage-page-access');
Route::post('save-page-access', 'ManageController@save_page_access')->name('save-page-access');
Route::get('get-access', 'ManageController@get_access')->name('get-access');
Route::post('get-pages', 'ManageController@get_pages')->name('get-pages');
Route::get('save-access', 'ManageController@save_access')->name('save-access');
Route::get('manage-user-type', 'ManageController@manage_user_type')->name('manage_user_type');




//tagging Get
Route::get('tagging-master', 'TaggingController@index')->name('tagging-master');
Route::get('walking-master', 'TaggingController@walking')->name('walking-master');
Route::get('tagging-data', 'TaggingController@tagging_data')->name('tagging-data');  
Route::post('save-tagging', 'TaggingController@save_tagging')->name('save-tagging');  
Route::get('edit-tagging-data', 'TaggingController@edit_details')->name('edit-tagging-data'); 
Route::post('update-tagging-data', 'TaggingController@update_details')->name('update-tagging-data');  
Route::get('edit-tagging-product', 'TaggingController@edit_product')->name('edit-tagging-product'); 
Route::post('update-tagging-product', 'TaggingController@update_product')->name('update-tagging-product');  

//Scenario Tree and Import
Route::get('support', 'HomeController@support')->name('support');


Route::get('tax-invoice', 'InvoiceController@index')->name('tax-invoice');
Route::post('save-invoice', 'InvoiceController@save_invoice')->name('save-invoice');


//View Payment Status
Route::get('view-invoice', 'CollectionController@view_invoice')->name('view-invoice');
Route::post('view-invoice', 'CollectionController@view_invoice')->name('view-invoice');
Route::get('update-payment', 'CollectionController@index')->name('update-payment');
Route::post('save-payment', 'CollectionController@save_payment')->name('save-payment');

Route::get('view-payment', 'CollectionController@view_payment')->name('view-payment');
Route::get('update-payment-symptom', 'CollectionController@add_symptom')->name('update-payment-symptom');
Route::post('save-payment-symptom', 'CollectionController@save_symptom_payment')->name('save-payment-symptom');






///*PDF Create*/
Route::get('view-generate-pdf','PdfController@view_pdf');
Route::get('generate-pdf','PdfController@generatePDF');
Route::get('generate-TXInvoice','PdfController@generateTXInvoice');
Route::get('generate-Challan','PdfController@generateChallan');



