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
use App\Http\Controllers\ManageController;
use App\Http\Controllers\CallRegistrationController;
use App\Http\Controllers\StockController;

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
Route::get('se-dash', 'DashController@se_dash')->name('se-dash');
Route::get('vendor-add-se', 'ServiceEngineerController@index')->name('vendor-add-se');
Route::post('vendor-save-se', 'ServiceEngineerController@save_se')->name('vendor-save-se');
Route::get('vendor-se-view', 'ServiceEngineerController@se_user')->name('vendor-se-view');
Route::get('vendor-se-edit', 'ServiceEngineerController@edit_se')->name('vendor-se-edit');
Route::post('vendor-se-update', 'ServiceEngineerController@update_se')->name('vendor-se-update');
Route::get('se-job-view', 'SeJobController@index')->name('se-job-view');
Route::post('se-job-save', 'SeJobController@save_shd')->name('se-job-save');
Route::post('se-delivery-save', 'SeJobController@save_del')->name('se-delivery-save');
Route::post('se-follow-save', 'SeJobController@save_followup')->name('se-follow-save');
Route::get('se-job-detail', 'SeJobController@job_view')->name('se-job-detail');
Route::get('se-job-ob', 'SeJobController@observation')->name('se-job-ob');
Route::post('se-save-ob', 'SeJobController@save_observation')->name('se-save-ob');
Route::get('se-raise-po', 'SeJobController@se_view_part_pending')->name('se-raise-po');  
Route::post('se-raise-po', 'SeJobController@raise_part_po')->name('se-raise-po');

Route::post('se-get-raise-po', 'SeJobController@get_raise_po')->name('se-get-raise-po');
Route::post('return-srn-po', 'SeJobController@return_part_srn')->name('return-srn-po');
Route::get('se-job-view-contact', 'JobViewController@index')->name('se-job-view-contact');

Route::get('view-return-sc-ob', 'SCReceiveInventoryController@view_return_srn_ob')->name('view-return-sc-ob');
Route::post('save-return-sc-ob', 'SCReceiveInventoryController@return_srn_part_sc')->name('save-return-sc-ob');

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
Route::get('ho-tag-view-cl', 'AllocationManagementController@view_ob_cl')->name('ho-tag-view-cl');
Route::get('ho-alloc-se-view', 'AllocationManagementController@se_view')->name('ho-alloc-se-view');
Route::post('ho-allocate-se', 'AllocationManagementController@allocate_se')->name('ho-allocate-se');

Route::get('vendor-tag-view', 'VendorManagementController@index')->name('vendor-tag-view');
Route::post('accept-job', 'JobAcceptController@index')->name('accept-job');
Route::post('reject-job', 'JobAcceptController@reject')->name('reject-job');
Route::get('allocate-se', 'VendorManagementController@allocate_case')->name('allocate-se');
Route::get('vendor-audit-view', 'VendorManagementController@view_audit')->name('vendor-audit-view');
Route::post('vendor-get-pin', 'PincodeController@get_pincode_by_state_id')->name('vendor-get-pin');
Route::get('vendor-view-complaint', 'VendorManagementController@view_complaint')->name('vendor-view-complaint');
Route::get('vendor-view-complete-job', 'VendorManagementController@view_complete_job')->name('vendor-view-complete-job');
Route::get('vendor-delivery-job', 'VendorManagementController@view_delivery_job')->name('vendor-delivery-job');

Route::get('vendor-observation', 'VendorManagementController@observation')->name('vendor-observation');
Route::get('vendor-observation-cl', 'VendorManagementController@observation_cl')->name('vendor-observation-cl');
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
Route::post('job-del-part-po', 'VendorManagementController@del_part_po')->name('job-del-part-po');
Route::post('get-added-part', 'VendorManagementController@get_tag_parts')->name('get-added-part');

Route::post('request-to-npc', 'VendorManagementController@save_request_npc')->name('request-to-npc');

Route::post('request-to-reestimate', 'VendorManagementController@reestimate_request_npc')->name('request-to-reestimate');

Route::post('estmt-approve-save', 'VendorManagementController@estmt_approve')->name('estmt-approve-save');

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
Route::post('get-state-id-by-region-id', 'StateController@get_state_id_by_region_id')->name('get-state-id-by-region-id');
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
Route::post('get-model-by-brand-id', 'ProductController@get_model_by_brand_id')->name('get-model-by-brand-id');
Route::post('get-model-by-product-name', 'ProductController@get_model_by_product_name')->name('get-model-by-product-name');
Route::post('get-cl-model-by-product-id', 'ProductController@get_cl_model_by_product_id')->name('get-cl-model-by-product-id');
Route::post('get-model-by-product-id', 'ProductController@get_cl_model_by_product_id')->name('get-model-by-product-id');
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
Route::post('get-part-name-by-part-code', 'SparePartController@get_part_name_by_part_code')->name('get-part-name-by-part-code');
Route::post('get-part-name-by-part-code-select', 'SparePartController@get_part_name_by_part_code_select')->name('get-part-name-by-part-code-select');
Route::post('get-part-code-by-part-name-select', 'SparePartController@get_part_code_by_part_name_select')->name('get-part-code-by-part-name-select');
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



// Inventory PO Request HO Starts From Here
Route::get('req-inv-entry', 'RequestInventoryController@index')->name('req-inv-entry');
Route::post('get-add-req-part', 'RequestInventoryController@add_req_part')->name('get-add-req-part');
Route::post('get-part-rate', 'RequestInventoryController@get_rate_by_spare_part')->name('get-part-rate');
Route::post('save-request-entry', 'RequestInventoryController@save_req_inv')->name('save-request-entry');
Route::get('view-req-inv-entry', 'RequestInventoryController@view')->name('view-req-inv-entry');
Route::get('edit-req-inv-entry', 'RequestInventoryController@edit')->name('edit-req-inv-entry');
Route::post('upd-req-inv-entry', 'RequestInventoryController@update')->name('upd-req-inv-entry');

// Inventory Inward Starts From Here
Route::get('inward-inv-entry', 'InwardInventoryController@index')->name('inward-inv-entry');
Route::post('get-add-inw-part', 'InwardInventoryController@add_inw_part')->name('get-add-inw-part');
Route::post('save-inward-entry', 'InwardInventoryController@save_req_inv')->name('save-inward-entry');
Route::get('view-inw-inv-entry', 'InwardInventoryController@view')->name('view-inw-inv-entry');
Route::get('edit-inw-inv-entry', 'InwardInventoryController@edit')->name('edit-inw-inv-entry');
Route::post('update-inward-entry', 'InwardInventoryController@update')->name('update-inward-entry');

// Inventory Outward Starts From Here
Route::get('view-npc-job-request', 'NpcEstimateApprovalController@index')->name('view-npc-job-request');
Route::get('npc-job-estimate-approval', 'NpcEstimateApprovalController@approve_estimate_request')->name('npc-job-estimate-approval');
Route::post('save-npc-estimate-approval', 'NpcEstimateApprovalController@save')->name('save-npc-estimate-approval');
Route::get('outward-view-po', 'OutwardInventoryController@index')->name('outward-view-po');
Route::get('outward-job-part-po', 'OutwardInventoryController@part_po_order_view')->name('outward-job-part-po');
Route::post('approve-part-po', 'OutwardInventoryController@approve_part_po')->name('approve-part-po');
Route::post('cancel-part-po', 'OutwardInventoryController@cancel_part_po')->name('cancel-part-po');
Route::get('outward-sc-part-po', 'OutwardInventoryController@sc_po_order_view')->name('outward-sc-part-po');
Route::post('approve-part-po-sc', 'OutwardInventoryController@approve_part_po_sc')->name('approve-part-po-sc');
Route::post('cancel-part-po-sc', 'OutwardInventoryController@cancel_part_po_sc')->name('cancel-part-po-sc');
//Route::post('save-inward-entry', 'InwardInventoryController@save_req_inv')->name('save-inward-entry');
//Route::get('view-inw-inv-entry', 'InwardInventoryController@view')->name('view-inw-inv-entry');
//Route::get('edit-inw-inv-entry', 'InwardInventoryController@edit')->name('edit-inw-inv-entry');
//Route::post('update-inward-entry', 'InwardInventoryController@update')->name('update-inward-entry');


//HO Outward Invoice Generation
Route::get('ho-outward-invoice', 'HOInvoiceInventoryController@index')->name('ho-outward-invoice');
Route::post('ho-create-invoice', 'HOInvoiceInventoryController@create_invoice')->name('ho-create-invoice');
Route::post('ho-create-invoice-multiple', 'HOInvoiceInventoryController@create_invoice_multiple')->name('ho-create-invoice-multiple');
Route::get('ho-invoice-pdf', 'PdfController@invoice_pdf')->name('ho-invoice-pdf');
//Route::get('ho-outward-invoice', 'HOInvoiceInventoryController@index')->name('ho-outward-invoice');

//HO Dispatch PO
Route::get('ho-dispatch-po', 'HODispatchInventoryController@index')->name('ho-dispatch-po');
Route::post('save-dispatch', 'HODispatchInventoryController@save_dispatch')->name('save-dispatch');
Route::get('view-dispatch', 'HODispatchInventoryController@view_dispatch')->name('view-dispatch');
Route::get('edit-dispatch', 'HODispatchInventoryController@edit_dispatch')->name('edit-dispatch');
Route::get('update-dispatch', 'HODispatchInventoryController@update_dispatch')->name('update-dispatch');

//HO Approval Pending Cases
Route::get('ho-return-approval-pending', 'HOReturnApprovalPendingController@index')->name('ho-return-approval-pending');
Route::post('ho-return-approve-short', 'HOReturnApprovalPendingController@approve_short')->name('ho-return-approve-short');
Route::post('ho-return-cancel-short', 'HOReturnApprovalPendingController@cancel_short')->name('ho-return-cancel-short');
Route::post('ho-return-approve-fault', 'HOReturnApprovalPendingController@approve_fault')->name('ho-return-approve-fault');
Route::post('ho-return-cancel-fault', 'HOReturnApprovalPendingController@cancel_fault')->name('ho-return-cancel-fault');

//HO SRN Approval Pending Cases
Route::get('ho-srn-dispatch-cases', 'HOReturnApprovalPendingController@srn_dispatch')->name('ho-srn-dispatch-cases');
Route::post('ho-return-approve-srn', 'HOReturnApprovalPendingController@approve_srn')->name('ho-return-approve-srn');
Route::post('ho-return-cancel-srn', 'HOReturnApprovalPendingController@cancel_srn')->name('ho-return-cancel-srn');
/////////////////////////////////////// Service Center ////////////////////////////////////////////////

// SC (Service Center) NPC Starts From Here
Route::get('req-inv-entry-sc', 'RequestInventoryCenterController@index')->name('req-inv-entry-sc');
Route::get('req-inv-pdf', 'PdfController@req_inv_pdf')->name('req-inv-pdf');

Route::post('get-add-req-part-sc', 'RequestInventoryCenterController@add_req_part')->name('get-add-req-part-sc');
Route::post('get-part-rate-sc', 'RequestInventoryCenterController@get_rate_by_spare_part')->name('get-part-rate-sc');
Route::post('save-request-entry-sc', 'RequestInventoryCenterController@save_req_inv')->name('save-request-entry-sc');
Route::get('view-req-inv-entry-sc', 'RequestInventoryCenterController@view')->name('view-req-inv-entry-sc');
Route::get('edit-req-inv-entry-sc', 'RequestInventoryCenterController@edit')->name('edit-req-inv-entry-sc');
Route::post('upd-req-inv-entry-sc', 'RequestInventoryCenterController@update')->name('upd-req-inv-entry-sc');
Route::get('sc-view-dispatch', 'SCReceiveInventoryController@index')->name('sc-view-dispatch');
Route::get('view-dispatch-sc', 'SCReceiveInventoryController@view_dispatch')->name('view-dispatch-sc');
Route::post('sc-save-dispatch', 'SCReceiveInventoryController@save_dispatch')->name('sc-save-dispatch');


//SC SRN  Cases
Route::post('get-sc-dispatch-det', 'SCReceiveInventoryController@get_dispatch_parts')->name('get-sc-dispatch-det');
Route::post('return-dispatch-sc', 'SCReceiveInventoryController@return_dispatch_sc')->name('return-dispatch-sc');
Route::post('get-sc-srn-det', 'SCReceiveInventoryController@get_srn_parts')->name('get-sc-srn-det');
Route::post('return-srn-sc', 'SCReceiveInventoryController@return_srn_sc')->name('return-srn-sc');

Route::post('get-sc-cancel-dispatch-det', 'SCReceiveInventoryController@get_cancel_det')->name('get-sc-cancel-dispatch-det');
Route::get('get-sc-cancel-dispatch-det', 'SCReceiveInventoryController@get_cancel_det')->name('get-sc-cancel-dispatch-det');
//Route::get('recv-inv-inward-sc', 'RequestInventoryCenterController@index')->name('recv-inv-inward-sc');
//Route::post('get-add-req-part-sc', 'RequestInventoryCenterController@add_req_part')->name('get-add-req-part-sc');


// SC (Service Center) PO Starts From Here
Route::get('req-inv-entry-ho', 'ApproveRequestInventoryController@view')->name('req-inv-entry-ho');
Route::get('approve-req-inv', 'ApproveRequestInventoryController@view_case')->name('approve-req-inv');
Route::post('save-approve-req-inv', 'ApproveRequestInventoryController@approve')->name('save-approve-req-inv');
Route::get('search-challan', 'ApproveRequestInventoryController@search_challan')->name('search-challan');

// SC (Service Center) Jobsheets Starts From Here
Route::get('job-sheet-apply-sc', 'JobsheetController@index')->name('job-sheet-apply-sc');
Route::post('job-sheet-apply-save', 'JobsheetController@apply_job')->name('job-sheet-apply-save');


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

//PO Report
Route::get('po-report', 'ReportController@po_report')->name('po-report');
Route::post('export-po-report', 'ReportController@export_po_report')->name('export-po-report');

//MIS Report
Route::get('final-report', 'ReportController@mis_report')->name('final-report');
Route::post('export-mis-report', 'ReportController@export_mis_report')->name('export-mis-report');


//Clarion Report
Route::get('clarion-report', 'ReportController@clarion_report')->name('clarion-report');
Route::post('export-brand-report', 'ReportController@export_brand_report')->name('export-brand-report');


Route::get('pending-reservation-report', 'ReportController@pending_reservation_report')->name('pending-reservation-report');
Route::post('export-pending-reservation', 'ReportController@export_pending_reservation')->name('export-pending-reservation');

Route::get('open-pending-call-report', 'ReportController@open_pending_call_report')->name('open-pending-call-report');
Route::post('export-open-pending-call', 'ReportController@export_open_pending_call')->name('export-open-pending-call');


Route::get('pending-delivery-report', 'ReportController@pending_delivery_report')->name('pending-delivery-report');
Route::get('pending-delivery-export', 'ReportController@open_pending_call_export')->name('pending-delivery-export');

Route::get('delivery-set-report', 'ReportController@delivery_set_report')->name('delivery-set-report');
Route::get('delivery-set-export', 'ReportController@delivery_set_export')->name('delivery-set-export');




//Menu Access
Route::get('manage-access', 'ManageController@index')->name('manage-access');
Route::get('manage-page-access', 'ManageController@manage_page_access')->name('manage-page-access');
Route::post('save-page-access', 'ManageController@save_page_access')->name('save-page-access');
Route::get('get-access', 'ManageController@get_access')->name('get-access');
Route::post('get-pages', 'ManageController@get_pages')->name('get-pages');
Route::get('save-access', 'ManageController@save_access')->name('save-access');
Route::get('manage-user-type', 'ManageController@manage_user_type')->name('manage_user_type');

//new
Route::get('manage-center-access', 'ManageController@manage_center_access')->name('manage-center-access');




//tagging Get
Route::get('tagging-master', 'TaggingController@index')->name('tagging-master');
Route::get('walking-master', 'TaggingController@walking')->name('walking-master');
Route::get('tagging-data', 'TaggingController@tagging_data')->name('tagging-data');  
Route::post('save-tagging', 'TaggingController@save_tagging')->name('save-tagging');  
Route::get('edit-tagging-data', 'TaggingController@edit_details')->name('edit-tagging-data'); 
Route::post('update-tagging-data', 'TaggingController@update_details')->name('update-tagging-data');  
Route::get('edit-tagging-product', 'TaggingController@edit_product')->name('edit-tagging-product'); 
Route::post('update-tagging-product', 'TaggingController@update_product')->name('update-tagging-product');  

//HO View Compalint
Route::get('ho-view-complaint', 'HOTicketController@view_complaint')->name('ho-view-complaint');

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

Route::post('lc-symptom-name', 'VendorManagementController@lc_symptom_name')->name('lc-symptom-name');  




///*PDF Create*/
Route::get('view-generate-pdf','PdfController@view_pdf');
Route::get('generate-pdf','PdfController@generatePDF');
Route::get('generate-TXInvoice','PdfController@generateTXInvoice');
Route::get('generate-Challan','PdfController@generateChallan');

// customer details
Route::post('vendor-customer-details-save-observation', 'VendorManagementController@save_customer_details_observation')->name('vendor-customer-details-save-observation
');


Route::post('dealer-save-observation', 'VendorManagementController@dealer_save_observation')->name('dealer-save-observation');
Route::post('vehicle-save-observation', 'VendorManagementController@vehicle_save_observation')->name('vehicle-save-observation');
Route::post('complaint-save-observation', 'VendorManagementController@complaint_save_observation')->name('complaint-save-observation');



// product details

Route::post('vendor-product-details-save-observation', 'VendorManagementController@save_product_details_observation')->name('vendor-product-details-save-observation
');
// closure codes

Route::post('vendor-closure-code-save-observation', 'VendorManagementController@save_closure_code_observation')->name('vendor-closure-code-save-observation
');


///bhanu works starts from here
Route::post('save-image', 'VendorManagementController@save_image')->name('save-image');

Route::post('save-video', 'VendorManagementController@save_video')->name('save-video');

//
Route::post('save_add_comment', 'VendorManagementController@save_add_comment')->name('save_add_comment');

Route::post('get-image', 'TaggingController@get_image_field')->name('get-image');
Route::post('save-image-first', 'TaggingController@save_image')->name('save-image-first');


Route::post('get-video', 'TaggingController@get_video_field')->name('get-video');
Route::post('save-video-first', 'TaggingController@save_video')->name('save-video-first');

// Basant Code Start from Here
// Route::post('save-image', 'VendorManagementController@save_image')->name('save-image');  
Route::delete('delete-upload-image/{id}', 'VendorManagementController@destroy_image_data')->name('delete-upload-image');  
// Basant Code End Here


Route::post('accept-order', 'SeJobController@accept_order')->name('accept-order');


// new work start here
Route::get('brand-dashboard', 'DashController@brand_dashboard')->name('brand-dashboard');


#Route::get('closure-code', 'ManageController@closure_code')->name('closure-code');
#Route::post('save-closure-code', 'ManageController@save_closure')->name('save-closure-code');
Route::match(['get', 'post'], 'closure-code', 'ManageController@closure_code')->name('closure-code');
Route::match(['get', 'post'],'edit-closure', 'ManageController@edit_closure')->name('edit-closure');


Route::match(['get', 'post'],'call-registration-form', 'CallRegistrationController@index')->name('call-registration-form');
#Route::post('save-tagging', 'TaggingController@save_tagging')->name('save-tagging');  

//add Store
Route::get('add-store', 'StoreController@index')->name('add-store');
Route::post('save-store', 'StoreController@save_se')->name('save-store');
Route::get('store-view', 'StoreController@se_user')->name('store-view');
Route::get('store-edit', 'StoreController@edit_se')->name('store-edit');
Route::post('store-update', 'StoreController@update_se')->name('store-update');


#stock management
Route::match(['get', 'post'],'fresh-stock', 'StockController@index')->name('fresh-stock');
Route::match(['get', 'post'],'part-defective', 'StockController@part_defective')->name('part-defective');
Route::match(['get', 'post'],'fresh-stock-download', 'StockController@fresh_stock_download')->name('fresh-stock-download');


#fresh defective stock management
Route::match(['get', 'post'],'fresh-defective-stock', 'StockController@fresh_defective_stock')->name('fresh-defective-stock');
Route::match(['get', 'post'],'part-defective', 'StockController@part_defective')->name('part-defective');
Route::match(['get', 'post'],'fresh-stock-download', 'StockController@fresh_stock_download')->name('fresh-stock-download');

Route::match(['get', 'post'],'get-canbalize', 'StockController@canbalize')->name('get-canbalize');
Route::match(['get', 'post'],'get-scrap', 'StockController@scrap')->name('get-scrap');
Route::match(['get', 'post'],'get-def-return', 'StockController@get_def_return')->name('get-def-return');

Route::match(['get', 'post'],'canbalize-save', 'StockController@canbalize_save')->name('canbalize-save');
Route::match(['get', 'post'],'scrap-save', 'StockController@scrap_save')->name('scrap-save');
Route::match(['get', 'post'],'defective-save', 'StockController@defective_save')->name('defective-save');



