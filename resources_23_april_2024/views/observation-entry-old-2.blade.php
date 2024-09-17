@extends('layouts.app')

@section('content') 


<script type="text/javascript" src="./js/job_case.js"></script>
<style>


/* Style the tab */
.tabForm {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tabForm button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 10px 11px;
  transition: 0.3s;
  font-size: 14px;
}

/* Change background color of buttons on hover */
.tabForm button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tabForm button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabFormcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}


.float-container {
    border: 3px solid #fff;
    
    
}

.float-child {
    width: 50%;
    float: left;       
}  

.clear {
    clear: both;
}
</style>
<script type="text/javascript">
    var k = jQuery.noConflict();
    k(function(){
        k("#sch_date").datepicker({ altField: "#job_date",changeMonth: true,changeYear: true,dateFormat: "dd-mm-yy" });
   });  
   //bhanu image work starts from here.
   function download_img(img)
{
    var imgs  = document.getElementById(''+img+'_img').src;
	var image = document.createElement("a");
	image.setAttribute("href", imgs);
	image.setAttribute("download", "img.jpg");
	document.body.appendChild(image);
 	image.click();
	image.remove();
      
}

function get_partno(div_id,part_name)
 {

   var brand_id = $('#Brand').val();
    var product_category_id = $('#Product_Detail').val();
    var product_id = $('#Product').val();
    var model_id = $('#Model').val();
    var labourcharge = part_name.includes("lc-");

	$.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });

	if(!labourcharge)
	{
		
    	jQuery.ajax({
              url: 'get-part-code-by-part-name-select',
              method: 'post',
              data: {
		 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id,
                 part_name: part_name 
              },
              success: function(result){
                  $('#part_no'+div_id).html(result);
              }});
	}
	else
	{
		jQuery.ajax({
              url: 'lc-symptom-name',
              method: 'post',
              data: {
                 symptom_type: part_name 
              },
              success: function(result){
                  $('#part_no'+div_id).html(result);
              }});
	
	}

     
 }


   function file_move(img)
    {
    var img_demo = $('#img_demo');
    //console.log(img_demo.attr('src'));
    if(img_demo.attr('src')==='' || img_demo.attr('src')==='#')
    {
        return 0;
    }
    var clone_img = img_demo.clone();
    clone_img.attr("id",img+'_img');
    $('#'+img+'_img'+'_disp').html(clone_img);
    var save_btn = '<button type="button"   name="submit" class="btn btn-primary" onclick="save_image('+"'"+img+"'"+')" style="float:right;margin-right: 70px;">save</button>';
    $('#'+img+'_img'+'_disp').append(save_btn);
    
    $('#'+img+'_cntr').show();
    img_demo.attr('src', '#');
    img_demo.hide();
    var file_input = $('#file-input-type').val();
    var file_demo = $('#'+file_input);
    var clone_file = file_demo.clone();
    clone_file.attr("id",img);
    clone_file.attr("name",img);
        $('#form'+img).remove();
        $('#'+img+'_img'+'_disp').append('<form id="form'+img+'" name="form'+img+'"></form>');
        $('#'+"form"+img).append(clone_file);
        var tagid = $('#TagId').val();
        $('#'+"form"+img).append('<input type="hidden" name="TagId" value="'+tagid+'">'); 
    
    
    //console.log(clone);
            
    }


function save_image(id)
 {

 $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#form"+id);
    var formData = new FormData(form[0]);
    
    var url = form.attr('action');
    
    $.ajax({
           type: "POST",
           url: 'save-image',
           data: formData,            
           enctype: 'multipart/form-data',
           cache:false,
           processData: false,
           contentType: false,
           success: function(data)
           {
            //    alert(data); // show response from the php script.
            if(data==='1')
               {
                   $('#remove_'+id).remove();
                   alert('Image saved Successfully.');
                   
               }
               else
               {
                   alert('Image already Saved');
                   
               }
               
           }
         });    

    return false;
 }


</script>

<div class="app-main"> 
 <div class="app-main__outer">
                    <div class="app-main__inner">
<div class="dashboard_header mb_50">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="dashboard_header_title">
                                        
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="dashboard_breadcam text-right">
                                        <div>                                
                                            <button style="background: yellow;font-weight: 600;float: right;border: yellow;font-size: 15px;padding: 8px;"><b>Job Number -</b> {{$data['ticket_no']}}</button>
                                        </div>
                                    </div>
                                    </div>
                            </div>
                          </div>
                         <div class="tab-content">
                            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                                <div class="main-card mb-3 card">
                                    <!--<form method="post" id="observation_save" name="observation_save" action="vendor-save-observation" enctype="multipart/form-data"></form> -->
                                    <div class="card-body">
                                        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                        @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif

                                        <div class="tabForm">
                                        <button type="button" id="Customer_Details1" class="tabFormlinks active" onclick="openTab(event, 'Customer_Details')" >Customer Details</button>
                                            <button id="Product_Details1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Product_Details')">Product Details</button>
                                            
                                                <button id="Reschedule1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Reschedule')">Reschedule Date & Calling Remarks</button>
                                                <button id="file_upload1" type="button"  class="tabFormlinks" onclick="openTab(event, 'file_upload')">Image Upload</button>
                                                <button id="Estimated_Cost1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Estimated_Cost')">Create Estimation</button>

                                                <button id="Part_Required1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Part_Required');get_po_parts();">Order Part</button>
                                                {{-- <button id="PO_Raise1" type="button"  class="tabFormlinks" onclick="get_po_parts();openTab(event, 'PO_Raise')">PO Raise</button> --}}
                                                <button id="sp1" type="button"  class="tabFormlinks" onclick="openTab(event, 'sp')">Special Approvals</button>

                                                <button id="CC" type="button"  class="tabFormlinks" onclick="openTab(event, 'CCs')">Closure Codes</button>

                                                <button id="DSS1" type="button"  class="tabFormlinks" onclick="openTab(event, 'DSS')">Delivery Status</button>
                                            

                                          </div>
                                            <div id="Customer_Details" class="tabFormcontent" style="display:block;">
                                            <h5 class="card-title">Customer Details</h5>
                                                <form method="post" id="observation_customer_details_save" name="observation_customer_details_save" action="vendor-customer-details-save-observation" enctype="multipart/form-data">
                                            
                                            <div class="form-row">

                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Group<span style="color: #f00;">*</span></label>
                                                        <select  name="Customer_Group" id="Customer_Group" class="form-control" required>
                                                            <option value="">Customer Group</option>
                                                            <option value="Dealer" <?php if($data['Customer_Group']=='Dealer') { echo "selected";} ?>>Dealer</option>
                                                            <option value="Normal Customer" <?php if($data['Customer_Group']=='Normal Customer') { echo "selected";} ?>>Normal Customer</option>
                                                            <option value="Internal Customer" <?php if($data['Customer_Group']=='Internal Customer') { echo "selected";} ?>>Internal Customer</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Customer Name <span style="color: #f00;">*</span></label>
                                                        <input  name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" value="<?php echo $data['Customer_Name']; ?>" class="form-control" required>
                                                    </div>

                                                </div>
                                                   
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Communication Address<span style="color: #f00;">*</span></label>
                                                        <input  name="Customer_Address" id="Customer_Address" placeholder="Communication Address" type="text" value="<?php echo $data['Customer_Address']; ?>" class="form-control" required>
                                                    </div>
                                                </div>

                                                   <div class="col-md-4">
                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Near By Landmark</label>
                                                            <input  name="Landmark" id="Landmark" value="<?php echo $data['Landmark']; ?>" placeholder="Landmark" type="text" class="form-control" >
                                                        </div>	
						    </div>
                                                    
                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Call received from<span style="color: #f00;">*</span></label>
                                                        <input  name="call_rcv_frm" id="call_rcv_frm"  value="<?php echo $data['call_rcv_frm']; ?>" placeholder="Call received from" type="text" class="form-control" required>
                                                    </div>	
                                                    </div>
                                                     <div class="col-md-4">
                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Customer No.<span style="color: #f00;">*</span></label>
                                                            <input  name="Contact_No" id="Contact_No" placeholder="Contact no." type="text" class="form-control" value="<?php echo $data['Contact_No']; ?>" onkeypress="return checkNumber(this.value,event)" required="" maxlength="10" ></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <label for="examplePassword11" class="">Alternate No.</label>
                                                            <input  name="Alt_No" id="Alt_No" value="<?php echo $data['Alt_No']; ?>" placeholder="Alternate No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)"  maxlength="12" >
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">State <span style="color: #f00;">*</span></label>

                                                        <select  name="state" id="state" data-placeholder="" class="form-control" onclick="get_pincode(this.value)"  required="">
                                                            <option value="">Select</option>
                                                            <?php   foreach($state_master as $state_id=>$state)
                                                                    {
                                                                        echo '<option value="'.$state.'" ';
                                                                        if($data['State']==$state) { echo "selected";} 
                                                                        echo '>'.$state.'</option>';
                                                                    }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Pincode <span style="color: #f00;">*</span></label>

                                                        <select  onchange="get_area(this.value)" name="pincode" id="pincode" data-placeholder="" class="form-control chzn-select chzn-rtl multiselect" tabindex="9" required="">

                                                            <option value="<?php echo $data['Pincode']; ?>"><?php echo $data['Pincode']; ?></option>
                                                            <?php   foreach($pin_master as $pin_id=>$pincode)
                                                                    {
                                                                        echo '<option value="'.$pincode.'" ';
                                                                        echo '>'.$pincode.'</option>';
                                                                    }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Place </label>

                                                        <select  name="place" id="place" class="form-control" >
                                                            <option value="">Select</option>
                                                            <?php   foreach($area_master as $pin_id=>$place)
                                                                    {
                                                                        echo '<option value="'.$pin_id.'" ';
                                                                        if($pin_id==$data['place']) { echo 'selected';}
                                                                        echo '>'.$place.'</option>';
                                                                    }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                    
                                                    
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Email</label>
                                                            <input  name="email" id="email" value="<?php echo $data['email']; ?>" placeholder="Email" type="text" class="form-control"   >
                                                        </div>
                                                    </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer GST No.</label>
                                                        <input  name="Gst_No" id="Gst_No" value="<?php echo $data['Gst_No']; ?>" placeholder="Customer GST No." type="text" class="form-control"   ></div>
                                                </div>


                                                    <div class="col-md-8">

                                                        <div class="position-relative form-group">
                                                            <br/>
                                                            <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                                            <button type="button" onclick="save_customer_details_return()"  class="mt-2 btn btn-primary">Save</button>


                                                        </div>
                                                    </div>

                                                    
                                                    <div class="col-md-4">

                                                        <div class="position-relative form-group">
                                                            <br/>
                                                            <button type="button" onclick="openTab1('Customer_Details', 'Product_Details');" class="mt-2 btn btn-success" style="float: right;">Next</button>

                                                        </div>
                                                    </div>

                                                
                                            </div>
                                                    </form>
						</div>
                                            <div id="Product_Details" class="tabFormcontent">
                                                <form method="post" id="observation_product_details_save" name="observation_product_details_save" action="vendor-product-details-save-observation">

                                                                <div class="form-row">
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type <span style="color: #f00;">*</span></label>
                                                                            <select id="service_type" name="service_type" class="form-control"  required="">
                                                                                <option value="">Select</option>
                                                                                <option value="Home Service" <?php if($data['service_type']=='Home Service') { echo "selected";} ?>>Home Service</option>
                                                                                <option value="Refurbrish" <?php if($data['service_type']=='Refurbrish') { echo "selected";} ?>>Refurbrish</option>
                                                                                <option value="Demo & Instalation" <?php if($data['service_type']=='Demo & Instalation') { echo "selected";} ?>>Demo & Instalation</option>
                                                                                <option value="Dealer Service" <?php if($data['service_type']=='Dealer Service') { echo "selected";} ?>>Dealer Service</option>
                                                                                <option value="Walk in Service" <?php if($data['service_type']=='Walk in Service') { echo "selected";} ?>>Walk in Service</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Type <span style="color: #f00;">*</span></label>
                                                                            <select id="warranty_type" name="warranty_type" class="form-control" required="">
                                                                                <option value="">Select</option>
                                                                                <option value="Standard Warranty" <?php if($data['warranty_type']=='Standard Warranty') { echo "selected";} ?>>Standard Warranty</option>
                                                                                <option value="Out Warranty" <?php if($data['warranty_type']=='Out Warranty') { echo "selected";} ?>>Out Warranty</option>
                                                                                <option value="Extended Warranty" <?php if($data['warranty_type']=='Extended Warranty') { echo "selected";} ?>>Extended Warranty</option>
                                                                                <option value="International Warranty" <?php if($data['warranty_type']=='International Warranty') { echo "selected";} ?>>International Warranty</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Brand<span style="color: #f00;">*</span></label>
                                                                            <select name="Brand" id="Brand" class="form-control" onchange="get_product_category('',this.value)" required>
                                                                                <option value="">Select</option>
                                                                            <?php   foreach($brand_master as $brand_id=>$brand_name)
                                                                                    {
                                                                                        echo '<option value="'.$brand_id.'" ';
                                                                                            if($data['Brand']==$brand_name) { echo "selected";}
                                                                                                echo '>'.$brand_name.'</option>';
                                                                                    }
                                                                            ?>
                                                                        </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-md-4">
                                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Product Category<span style="color: #f00;">*</span></label>
                                                                                <select name="Product_Detail" id="Product_Detail" class="form-control" onchange="get_product('',this.value)" required>
                                                                                    <option value="">Select</option>
                                                                                    <?php   foreach($ProductDetailMaster as $prod)
                                                                                            {
                                                                                                echo '<option value="'.$prod['product_category_id'].'" ';
                                                                                                    if($data['Product_Detail']==$prod['category_name']) { echo "selected";}
                                                                                                        echo '>'.$prod['category_name'].'</option>';
                                                                                            }
                                                                                    ?>
                                                                                </select>
                                                                            </div>
                                                                    </div>
                                                                    
                                                                    
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Model No.<span style="color: #f00;">*</span></label>
                                                                            <select name="Product" id="Product" class="form-control" onchange="get_model('',this.value)" required>
                                                                                                        <option value="">Select</option>
                                                                                                        <?php   foreach($ProductMaster as $prod)
                                                                                                                {
                                                                                                                    echo '<option value="'.$prod['product_id'].'" ';
                                                                                                                        if($data['Product']==$prod['product_name']) { echo "selected";}
                                                                                                                            echo '>'.$prod['product_name'].'</option>';
                                                                                                                }
                                                                                                        ?>
                                                                                                    </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                
                                                                                                <div class="col-md-4">
                                                                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Model Name<span style="color: #f00;">*</span></label>
                                                                                                        <select name="Model" id="Model" class="form-control" onchange="get_spare_part('',this.value)" required>
                                                                                                        <option value="">Select</option>
                                                                                                        <?php   foreach($model_master as $model_id=>$model)
                                                                                                                {
                                                                                                                    echo '<option value="'.$model_id.'" ';
                                                                                                                        if($data['Model']==$model) { echo "selected";}
                                                                                                                            echo '>'.$model.'</option>';
                                                                                                                }
                                                                                                        ?>
                                                                                                    </select>
                                                                                                    </div>	 
                                                                        </div>
                                            
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Serial Number<span style="color: #f00;">*</span></label>
                                                                            <input name="Serial_No" id="Serial_No" placeholder="Serial No" type="text" class="form-control" value="<?php echo $data['Serial_No']; ?>" required></div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Manufacturer serial number</label>
                                                                        <input name="man_ser_no" id="man_ser_no" value="<?php echo $data['man_ser_no']; ?>" placeholder="Man. Serial No" type="text" class="form-control" ></div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty card Availability<span style="color: #f00;">*</span></label>
                                                                            <select id="warranty_card" name="warranty_card" class="form-control" onchange="get_estmt_charge();" required="">
                                                                                <option value="">Select</option>
                                                                                <option value="Yes" <?php if($data['warranty_card']=='Yes') {echo 'selected';} ?>>Yes</option>
                                                                                <option value="No" <?php if($data['warranty_card']=='No') {echo 'selected';} ?>>No</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Purchase Invoice Availability <span style="color: #f00;">*</span></label>
                                                                            <select id="invoice" name="invoice" class="form-control" required="" >
                                                                                <option value="">Select</option>
                                                                                <option value="Yes" <?php if($data['invoice']=='Yes') {echo 'selected';} ?>>Yes</option>
                                                                                <option value="No" <?php if($data['invoice']=='No') {echo 'selected';} ?>>No</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="col-md-4"> 
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Purchase Date</label>
                                                                        <input name="Bill_Purchase_Date" id="Bill_Purchase_Date" placeholder="Bill Purchase Date" type="text" class="form-control datepicker" value="<?php echo $data['Bill_Purchase_Date']; ?>" ></div>
                                                                    </div>
                                                                    <div class="col-md-4"> 
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer Name</label>
                                                                            <input name="dealer_name" id="dealer_name" value="<?php echo $data['dealer_name']; ?>" placeholder="Dealer Name" type="text" class="form-control"  ></div>
                                                                    </div>
                                                                
                                                                    <div class="col-md-4">
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice No.</label>
                                                                            <input name="invoice_no" id="invoice_no" value="<?php echo $data['invoice_no']; ?>" placeholder="Invoice No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" maxlength="10" ></div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Complaint <span style="color: #f00;">*</span></label>
                                                                                <input name="ccsc" id="ccsc" placeholder="Customer Complaint" type="text" value="<?php echo $data['ccsc']; ?>" class="form-control" required="" ></div>
                                                                        </div>
                                                                    <div class="col-md-4"> 
                                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Special Comments</label>
                                                                                <input name="report_fault" id="report_fault" placeholder="Special Comments" type="text" value="<?php echo $data['report_fault']; ?>" class="form-control" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-row">

                                                                    <div class="col-md-6">
                                                                        <div class="position-relative form-group">
                                                                        <button type="button" onclick="openTab(event, 'Customer_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                                                        <button type="button" onclick="save_product_details_return()"  class="mt-2 btn btn-primary">Save</button>

                                                                    </div>

                                                                    <div class="col-md-3">
                                                                        <div class="position-relative form-group">
                                                                        <button type="button" onclick="openTab1('Product_Details', 'Reschedule');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                        </form>
                                            </div>    
                                        
                                            <div id="Reschedule" class="tabFormcontent">
                                                <table border="1" style="width:100%;background: #ffefd5;">
                                                    <tr>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Reschedule Date & Time</b></td>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Reschedule Remarks</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="height:200px;width: 500px;" align="center">
                                                            <div id="sch_date"></div>
                                                            <br><br><br>
                                                            <div>
                                                                <table border="1" style="font-size:18px;width:300px;">
                                                                    <tr>
                                                                        <th style="background:#ffa500;text-align: center;color: black;" >Time</th>
                                                                        <td>
                                                                            <select  id="job_hour" style="background:null;width:100%;height:100%;">
                                                                                <?php for($tt=0;$tt<=23;$tt++) { ?>
                                                                                <option value="<?php echo str_pad($tt, 2, '0', STR_PAD_LEFT);;?>"><?php echo str_pad($tt, 2, '0', STR_PAD_LEFT);;?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select  id="job_minute" style="background:null;width:100%;height:100%;">
                                                                                <?php for($tt=0;$tt<=59;$tt++) { ?>
                                                                                <option value="<?php echo str_pad($tt, 2, '0', STR_PAD_LEFT);?>"><?php echo str_pad($tt, 2, '0', STR_PAD_LEFT);?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            
                                                            
                                                            <div>
                                                                <table border="1" style="font-size:16px;width:400px;margin-top: 10px;">
                                                                    <tr>
                                                                        <th style="background:#ffa500;text-align: center;color: black;" >Reason of Reschedule</th>
                                                                        <td>
                                                                            <input type="text"  id="job_remarks" style="background:null;width:100%;height:100%;">
                                                                            <input type="hidden" id="job_date" value="">
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            
                                                            <div style="text-align:right;">
                                                                <button type="button" style="width: 200px;font-size:16px;margin-top: 10px;font-weight: bold;" class="btn btn-secondary" onclick="save_shd_time()" >Apply</button>
                                                            </div>
                                                        </td>
                                                        <td style="vertical-align:top">
                                                            <br>
                                                            <?php $history_json = $data['se_sdl_history'];?>
                                                            <table style="font-size:14px;width:100%" id="rsch">
                                                            <?php
                                                            $history_arr = json_decode($history_json,true);
                                                            $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
                                                            $index_bg = 0;
                                                            foreach($history_arr as $his)
                                                            {
                                                                    $index_bg++;
                                                                    $index_cl = $bg_color[$index_bg%2];
                                                                     $entry_date = strtotime($his['se_sdl_date']);
                                                                     $entry_date_str = date('d/m/Y',$entry_date);
                                                                     $entry_time_str = date('h:i A',$entry_date);

                                                                     $job_date = strtotime($his['job_date']);
                                                                     $job_date_str = date('d/m/Y',$job_date);
                                                                     $job_time_str = date('h:i A',$job_date);


                                                                     $user = $his['user'];
                                                                echo "<tr style=\"background:$index_cl;\"><td>";     
                                                                     echo "<b>$entry_date_str at $entry_time_str by $user --</b> Customer appointment";
                                                                echo '</td></tr>';
                                                                echo "<tr style=\"background:$index_cl;\"><td>";
                                                                    echo "reschedule for $job_date_str at $job_time_str";
                                                                echo '</td></tr>';
                                                                echo "<tr style=\"background:$index_cl;\"><td>";
                                                                     $reason = $his['se_sdl_remarks'];
                                                                     echo "<b>Reason of Reschedule -- </b>$reason";
                                                                echo '</td></tr>';
                                                            }?>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Additional Calling Remarks (Follow Remarks)</b></td>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Calling or Followup Remarks</b></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td style="height:200px;" >
                                                            <div class="card-body">
                                                                <div class="position-relative row form-group"><label for="exampleEmail" class="col-sm-2 col-form-label">Subject:</label>
                                                                    <div class="col-sm-10">
                                                                        <select  id="se_followup_sub" class="form-control" >
                                                                            <option value="General Inquiry" <?php if($data['Customer_Group']=='General Inquiry') { echo "selected";} ?>>General Inquiry</option>
                                                                            <option value="Estimation Inquiry" <?php if($data['Customer_Group']=='Estimation Inquiry') { echo "selected";} ?>>Estimation Inquiry</option>
                                                                            <option value="Repair related Inquiry" <?php if($data['Customer_Group']=='Repair related Inquiry') { echo "selected";} ?>>Repair related Inquiry</option>
                                                                            <option value="Part Related Inquiry" <?php if($data['Customer_Group']=='Part Related Inquiry') { echo "selected";} ?>>Part Related Inquiry</option>
                                                                            <option value="Delivery related Inquiry" <?php if($data['Customer_Group']=='Delivery related Inquiry') { echo "selected";} ?>>Delivery related Inquiry</option>
                                                                            <option value="Other Inquiry" <?php if($data['Customer_Group']=='Other Inquiry') { echo "selected";} ?>>Other Inquiry</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="position-relative row form-group"><label for="exampleEmail" class="col-sm-2 col-form-label">Remark:</label>
                                                                    <div class="col-sm-10">
                                                                        <textarea id="se_followup_remarks" placeholder="Message in Reservation (APP)" class="form-control" ></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="position-relative row form-check">
                                                                    <div class="col-sm-10 offset-sm-2">
                                                                        <button type="button" onclick="save_follow_up()" class="btn btn-secondary">Apply</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                        </td>
                                                        <td style="vertical-align:top">
                                                            <br>
                                                            <?php $followup_history_json = $data['se_followup_history'];
                                                                    
                                                            ?>
                                                            <table style="font-size:14px;width:100%" id="tbl_follow">
                                                            <?php
                                                            $followup_history_arr = json_decode($followup_history_json,true);
                                                            $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
                                                            $index_bg = 0;
                                                            foreach($followup_history_arr as $his)
                                                            {
                                                                    $index_bg++;
                                                                    $index_cl = $bg_color[$index_bg%2];
                                                                     $entry_date = strtotime($his['se_follow_date']);
                                                                     $entry_date_str = date('d/m/Y',$entry_date);
                                                                     $entry_time_str = date('h:i A',$entry_date);
                                                                     $user = $his['user'];
                                                                     $subject = $his['se_followup_sub'];
                                                                     $remark = $his['se_followup_remarks'];
                                                                     
                                                                echo "<tr style=\"background:$index_cl;\"><td>";     
                                                                     echo "<b>$subject";
                                                                echo '</td></tr>';
                                                                echo "<tr style=\"background:$index_cl;\"><td>";
                                                                    echo "<b>$entry_date_str at $entry_time_str by $user --</b> Updated to customer";
                                                                echo '</td></tr>';
                                                                echo "<tr style=\"background:$index_cl;\"><td>";
                                                                     echo "$remark";
                                                                echo '</td></tr>';
                                                            }?>
                                                            </table>
                                                               
                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                <div class="form-row">
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab(event, 'Product_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab1('Reschedule', 'file_upload');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
						    
                                            </div>
                                                    
                                            <!-- Basant Code Start From Here                                       -->
                                          
                                       

                        <div id="file_upload" class="tabFormcontent">

                            <div class="form-row">

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="float-child1">
                                                <div class="form-row">
                                                    <div class="col-md-12">
                                                        <div class="position-relative form-group">
                                                            <button type="button" id="camera" onclick="open_camera()"
                                                                style="width:100%" name="camera"
                                                                class="mt-2 btn btn-primary">Take Photo</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-12">
                                                        <div class="position-relative form-group">
                                                            <button type="button" style="width:100%" id="gallery"
                                                                name="gallery" onclick="open_gallery()"
                                                                class="mt-2 btn btn-primary">Open Gallery</button>
                                                            <input id="file-input-type" value="" type="hidden"
                                                                style="display: none;" />
                                                            <input id="file-input" type="file" onchange="readURL(this);"
                                                                name="file_input" style="display: none;" />
                                                            <input id="file-input-camera" type="file"
                                                                onchange="readURL(this);" name="file_input_camera"
                                                                accept="image/*;capture=camera"
                                                                style="display: none;" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-12">
                                                        <div class="position-relative form-group"><label for="file_type"
                                                                class="">Image Type</label>
                                                            <select id="file_type" name="file_type"
                                                                class="form-control">
                                                                <option value="Warranty card">Warranty card</option>
                                                                <option value="Purchase Invoice">Purchase Invoice
                                                                </option>
                                                                <option value="Serial No. Image">Model & Serial No.
                                                                    Image</option>
                                                                <option value="Symptom Image 1">Symptom Image</option>
                                                                <option value="Symptom Image 2">Any special Approval
                                                                </option>
    
                                                            </select>
    
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-md-2">
                                                        <div class="position-relative form-group">
                                                            <button type="button" id="upload" name="upload"
                                                                onclick="file_upload();"
                                                                class="mt-2 btn btn-primary">Upload</button>
    
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">

                                            <img id="img_demo" src="#" alt="File" style="display:none;" />

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                 <div class="row">

                                    
                                    <div class="col-md-12">
                                    
                                        <div id="wrrn_cntr" style="<?php if(empty($data['warranty_card_copy'])) { ?>display:none <?php } ?>"
                                            class="float-container">
                                            <div class="float-child1">
                                                <div class="float-container">
                                                    <div class="float-child">

                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group">
                                                                    <label for="examplePassword11" class="">
                                                                        <!-- <a href="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['warranty_card_copy']; ?>" target="_blank" >Download Image</a>-->
                                                                        <br><b>Image Type: Warranty card</b></label>
                                                                    <button type="button" style="width:100%" id="remove_wrrn"
                                                                        name="remove_wrrn" onclick="remove_img('wrrn')"
                                                                        class="mt-2 btn btn-primary">Remove</button>
                                                                </div>
                                                                <div class="position-relative form-group">

                                                                    <button type="button" style="width:100%"
                                                                        onclick="download_img('wrrn')"
                                                                        class="btn btn-primary">Download</button>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="wrrn_img_disp">
                                                        <img id="wrrn_img"
                                                            src="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$data['warranty_card_copy']; ?>"
                                                            style="width:150px" />
                                                        <button type="button" class="btn btn-primary" id="warraty_card_save" name="submit"
                                                            onclick="save_image('wrrn')" style="float:right;margin-right: 70px;">save</button>
                                                    </div>


                                                </div>

                                            </div>

                                        </div>

                                        <div id="prcs_cntr" style="<?php if(empty($data['purchase_copy'])) { ?>display:none <?php } ?>" class="float-container">

                                            <div class="float-child1">
                                                <div class="float-container">
                                                    <div class="float-child">

                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label
                                                                        for="examplePassword11" class="">
                                                                        <!--                                                                        <a  href="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['purchase_copy']; ?>" target="_blank" >Download Image</a>-->
                                                                        <br><b>File Type: Purchase Invoice</b></label>
                                                                    <button type="button" style="width:100%" id="remove_prcs"
                                                                        name="remove_prcs" onclick="remove_img('prcs')"
                                                                        class="mt-2 btn btn-primary">Remove</button>
                                                                </div>
                                                                <div class="position-relative form-group">

                                                                    <button type="button" style="width:100%"
                                                                        onclick="download_img('prcs')"
                                                                        class="btn btn-primary">Download</button>

                                                                </div>
                                                            </div>

                                                        </div>



                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="prcs_img_disp">
                                                        <img id="prcs_img"
                                                            src="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$data['purchase_copy']; ?>"
                                                            style="width:150px" />
                                                        <button type="button" name="submit" class="btn btn-primary"
                                                            onclick="save_image('prcs')"
                                                            style="float:right;margin-right: 70px;">save</button>

                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <div id="mdl_cntr" style="<?php if(empty($data['model_no_copy'])) { ?>display:none <?php } ?>" class="float-container">

                                            <div class="float-child1">
                                                <div class="float-container">
                                                    <div class="float-child">

                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label
                                                                        for="examplePassword11" class="">
                                                                        <!--        <a href="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['model_no_copy']; ?>" target="_blank" >Download Image</a>-->
                                                                        <br><b>File Type: Model No. Image</b></label>
                                                                    <button type="button" style="width:100%" id="remove_mdl"
                                                                        name="remove_mdl" onclick="remove_img('mdl')"
                                                                        class="mt-2 btn btn-primary">Remove</button>
                                                                </div>
                                                                <div class="position-relative form-group">

                                                                    <button type="button" style="width:100%"
                                                                        onclick="download_img('mdl')"
                                                                        class="btn btn-primary">Download</button>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="mdl_img_disp">
                                                        <img id="mdl_img"
                                                            src="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$data['model_no_copy']; ?>"
                                                            style="width:150px" />
                                                        <button type="button" name="submit" class="btn btn-primary"
                                                            onclick="save_image('mdl')"
                                                            style="float:right;margin-right: 70px;">save</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div id="srl_cntr" style="<?php if(empty($data['serial_no_copy'])) { ?>display:none <?php } ?>" class="float-container">
                                            <div class="float-child1" style="">
                                                <div class="float-container">
                                                    <div class="float-child">


                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label
                                                                        for="examplePassword11" class="">
                                                                        <!--                                                                        <a href="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['serial_no_copy']; ?>" target="_blank" >Download Image</a>-->
                                                                        <br><b>File Type: Model & Serial No. Image</b></label>
                                                                    <button type="button" style="width:100%" id="remove_srl"
                                                                        name="remove_srl" onclick="remove_img('srl')"
                                                                        class="mt-2 btn btn-primary">Remove</button>
                                                                </div>
                                                                <div class="position-relative form-group">

                                                                    <button type="button" style="width:100%"
                                                                        onclick="download_img('srl')"
                                                                        class="btn btn-primary">Download</button>

                                                                </div>
                                                            </div>
                                                        </div>



                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="srl_img_disp">
                                                        <img id="srl_img"
                                                            src="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$data['serial_no_copy']; ?>"
                                                            style="width:150px" />
                                                        <button type="button" name="submit" class="btn btn-primary"
                                                            onclick="save_image('srl')"
                                                            style="float:right;margin-right: 70px;">save</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        
                                        <div id="smtm1_cntr"  style="<?php if(empty($data['symptom_photo1'])) { ?>display:none <?php } ?>" class="float-container">
                                            <div class="float-child1" style="float:right;">
                                                <div class="float-container">
                                                    <div class="float-child">

                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label
                                                                        for="examplePassword11" class="">
                                                                        <!--   <a href="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['symptom_photo1']; ?>" target="_blank" >Download Image</a>-->
                                                                        <br><b>File Type: Symptom Image</b></label>
                                                                    <button type="button" style="width:100%" id="remove_smtm1"
                                                                        name="remove_smtm1" onclick="remove_img('smtm1')"
                                                                        class="mt-2 btn btn-primary">Remove</button>
                                                                </div>
                                                                <div class="position-relative form-group">

                                                                    <button type="button" style="width:100%"
                                                                        onclick="download_img('smtm1')"
                                                                        class="btn btn-primary">Download</button>

                                                                </div>
                                                            </div>
                                                        </div>



                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="smtm1_img_disp">
                                                        <img id="smtm1_img"
                                                            src="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$data['symptom_photo1']; ?>"
                                                            style="width:150px" />
                                                        <button type="button" name="submit" class="btn btn-primary"
                                                            onclick="save_image('smtm1')"
                                                            style="float:right;margin-right: 70px;">save</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div id="smtm2_cntr" style="<?php if(empty($data['symptom_photo2'])) { ?>display:none <?php } ?>" class="float-container">
                                            <div class="float-child1" style="float:right;">
                                                <div class="float-container">
                                                    <div class="float-child">

                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label
                                                                        for="examplePassword11" class="">
                                                                        <!--   <a href="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['symptom_photo2']; ?>" target="_blank" >Download Image</a>-->
                                                                        <br><b>File Type: Any special Approval</b></label>
                                                                    <button type="button" style="width:100%" id="remove_smtm2"
                                                                        name="remove_smtm2" onclick="remove_img('smtm2')"
                                                                        class="mt-2 btn btn-primary">Remove</button>
                                                                </div>
                                                                <div class="position-relative form-group">

                                                                    <button type="button" style="width:100%"
                                                                        onclick="download_img('smtm2')"
                                                                        class="btn btn-primary">Download</button>
                                                                </div>
                                                            </div>
                                                        </div>



                                                    </div>

                                                    <div class="float-child" style="height:280px;" id="smtm2_img_disp">
                                                        <img id="smtm2_img"
                                                            src="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$data['symptom_photo2']; ?>"
                                                            style="width:150px" />

                                                        <button type="button" name="submit" class="btn btn-primary"
                                                            onclick="save_image('smtm2')"
                                                            style="float:right;margin-right: 70px;">save</button>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                        <div id="smtm3_cntr" style="<?php if(empty($data['symptom_photo3'])) { ?>display:none <?php } ?>" class="float-container">
                                            <div class="float-child">
                                                <div class="float-container">
                                                    <div class="float-child">

                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label
                                                                        for="examplePassword11" class="">
                                                                        <!--   <a href="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['symptom_photo3']; ?>" target="_blank" >Download Image</a>-->
                                                                        <br><b>File Type: Symptom Image 3</b></label>
                                                                    <button type="button" style="width:100%" id="remove_smtm3"
                                                                        name="remove_smtm3" onclick="remove_img('smtm3')"
                                                                        class="mt-2 btn btn-primary">Remove</button>
                                                                </div>
                                                                <div class="position-relative form-group">

                                                                    <button type="button" style="width:100%"
                                                                        onclick="download_img('smtm3')"
                                                                        class="btn btn-primary">Download</button>

                                                                </div>
                                                            </div>
                                                        </div>



                                                    </div>
                                                    <div class="float-child" style="height:280px;" id="smtm3_img_disp">
                                                        <img id="smtm3_img"
                                                            src="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$data['symptom_photo3']; ?>"
                                                            style="width:70%" />

                                                        <button type="button" name="submit" class="btn btn-primary"
                                                            onclick="save_image('smtm3')"
                                                            style="float:right;margin-right: 70px;">save</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                 </div>



                                    <?php 
    
                                        foreach ($imagedata as $item)
                                        { 
                                            
                                            ?>       
                                       
                                        
                                      
                                    <div id="wrrn_cntr256" class="float-container mb-4">
                                    
                                                        <div class="form-row">
                                                                <div class="col-md-6">
                                                                    <div class="position-relative form-group">
                                                                        <label for="examplePassword11" class="">
                                                                            <b>Image Type: <?php echo $item['image_type'];?></b></label>
                                                                        
                                                                            <!-- <a href="<?php  echo "{$str_server}/supreme/storage/app/supreme/".$data['TagId']."/".$item['img_url']; ?>" download>Download Image</a> -->
                                                                            
                                                                        {{-- <button type="button" style="width:100%" id="remove_wrrn"
                                                                            name="remove_wrrn" onclick="remove_img('wrrn')"
                                                                            class="mt-2 btn btn-primary">Remove</button> --}}

                                                                           <!-- <button onclick="deleteRecord('<?php echo $item['ImagId'];?>')" data-id="<?php echo $item['ImagId'];?>" >Delete Image</button>  --}} --->
                                                                    </div>
                                                                    <div class="position-relative form-group">
                                                
                                                                        <button type="button" style="width:100%"
                                                                            onclick="download_img('wrrn<?php echo $item['ImagId'];?>')"
                                                                            class="btn btn-primary">Download</button>
                                                
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">

                                                                    <div>
                                                                        <img id="wrrn<?php echo $item['ImagId'];?>_img" src="<?php  echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$item['img_url']; ?>"
                                                                            class="img-fluid" />
                                                                        
                                                                    </div>

                                                                </div>
                                                        </div>                                                   
                                    </div>
                                    
                                    
                                    <?php } ?>
                                    
                                    <?php // } ?>
                                </div>

                            </div>
                            


                            <div class="form-row">

                                <div class="col-md-6"></div>
                               

                            </div>
 

                            <div class="clear"></div>
                            <div class="form-row">

                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <button type="button" onclick="openTab(event, 'Reschedule');"
                                            class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <button type="button" onclick="openTab1('file_upload', 'Estimated_Cost');"
                                            class="mt-2 btn btn-success" style="float:right;">Next</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                                          

             <!--- Basant code End Here --->

                                            <div id="Estimated_Cost" class="tabFormcontent">
                                                <a href="se-job-view-contact?contact_no=<?php echo $data['Contact_No'];?>&brand_id=<?php echo $data['brand_id'];?>&product_category_id=<?php echo $data['product_category_id'];?>&product_id=<?php echo $data['product_id'];?>&model_id=<?php echo $data['model_id'];?>" class="mt-2 btn btn-primary" style="float:right;">Check Repair History</a>
                                                <br>
                                                <br>
                                                
                                                <form method="post" name="request_to_npc" id="request_to_npc" action="request-to-npc">
                                                    <div style="overflow-x:auto;">
                                                    <table border="1" id="tbl_part" style="width: 100%;">
                                                        <thead id="thead">
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Part Name</th>
                                                            <th>Part Number</th>
                                                            <th>Quantity</th>
                                                            <th>Color</th>
                                                            <th>Part Type</th>
                                                            <th>Customer Price Per Unit</th>
                                                            <th>GST %</th>
                                                            <th>Total Amount</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        <tr id="tr130">
    <td id="rowno">{{$i++}}</td>
    <td>
        
        <select id="spare_id0" name="SparePart[0][spare_id]" onchange="get_partno('0',this.value)" class="form-control" required="">
        <option value="">Select</option>
               <?php
                    foreach($part_arr as $part)
                    {
                        ?><option value="<?php echo $part->spare_id; ?>" <?php if($part->spare_id==$tpart->spare_id) { echo 'selected';} ?>><?php echo $part->part_name; ?></option>  
            <?php   }
                    foreach($lbr_charge_det as $lcd)
                    {
            ?>        <option value="lc-<?php echo $lcd->symptom_type; ?>" <?php if($lcd->symptom_type==$lpart->symptom_type) { echo 'selected';} ?>><?php echo $lcd->symptom_type; ?></option>    
            <?php   }
            ?>
        </select>
    </td>
    <td>
        <select id="part_no0" name="SparePart[0][part_no]" class="form-control"  required="">
            <option value="">Select</option>
        </select>
    </td>
    <td> 
        <input maxlength="5" required="" onKeyPress="return checkNumber(this.value,event);" id="no_pen_part" name="SparePart[0][no_pen_part]" class="form-control"  type="text"  value="" >
    </td>
    <td>
        <input id="color0" name="SparePart[0][color]" class="form-control"  type="text"  value="" >
    </td>
    <td>
        <select id="charge_type130" name="SparePart[0][charge_type]" class="form-control"  >
            <option value="Chargeable">Chargeable</option>
            <option value="Non Chargeable">Non Chargeable</option>
        </select>
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td>
        <button type="button" class="mt-2 btn btn-danger remove_npc_part" onclick="del_part_temp('130');" >Remove</button>
    </td>
</tr>
                                                        
                                                            <?php $i = 2; foreach($tagg_part as $tpart) { ?>
                                                                <tr id="tr<?php echo $tpart->part_id; ?>">
                                                                    <td>{{$i++}}</td>
                                                                    <td>
                                                                    <select id="part_name<?php echo $tpart->part_id; ?>"   class="form-control" onchange="get_partno('<?php echo $tpart->part_id; ?>',this.value)" >
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                                foreach($part_arr as $part)
                                                                                {
                                                                                    ?><option value="<?php echo $part->spare_id; ?>" <?php if($part->spare_id==$tpart->spare_id) { echo 'selected';} ?>><?php echo $part->part_name; ?></option>     
                                                                        <?php   }
                                                                        ?>
                                                                    </select>
                                                                    </td>
                                                                    <td>
                                                                        <select id="part_no<?php echo $tpart->part_id; ?>"  class="form-control"  >
                                                                            <option value="<?php echo $tpart->part_no; ?>"><?php echo $tpart->part_no; ?></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input maxlength="5" id="no_pen_part<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->pending_parts;?>" >
                                                                    </td>
                                                                    <td>
                                                                        <input id="color<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->color;?>" >
                                                                    </td>
                                                                    <td>
                                                                        <select id="charge_type<?php echo $tpart->part_id; ?>"  class="form-control"  >
                                                                            <option value="Chargeable" <?php if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option>
                                                                            <option value="Non Chargeable" <?php if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option>
                                                                        </select>
                                                                    </td>

                                                                    <td>
                                                                        <?php echo $tpart->customer_price;?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $tpart->gst;?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $tpart->total;?>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="mt-2 btn btn-danger remove_npc_part" onclick="del_part('<?php echo $tpart->part_id; ?>');" >Remove</button>
                                                                    </td>
                                                                </tr>

                                                            <?php } ?>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td colspan="5" style="marging-left:20px;"><button type="button" class="mt-2 btn btn-primary" onclick="add_part('1');" >Add Row</button> </td>
                                                            <td colspan="5" align="right"><button id="npc_approve"   type="button" class="mt-2 btn btn-primary" onclick="return npc_approve_save();" >Request to NPC for part price</button> </td>
                                                        </tr>
                                                        </tbody>
                                                    </table> 
                                                </div> 
                                                    <input  type="hidden" id="tag_id" name="tag_id" value="<?php echo $data['TagId'];?>" />
                                                </form>
                                                
                                                <?php if(!empty($tagg_part_npc[0]) || !empty($labr_part_npc[0])) { ?>
                                                <br><br>
                                                <div id="estmt_approval">
                                                <form method="post" name="estmt_approve_save" id="estmt_approve_save" action="estmt-approve-save"></form>
                                                    <table border="1" id="tbl_part" style="width: 100%;">
                                                        <thead id="thead">
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Part Name</th>
                                                            <th>Part Number</th>
                                                            <th>Quantity</th>
                                                            <th>Color</th>
                                                            <th>Part Type</th>
                                                            <th>Customer Price Per Unit</th>
                                                            <th>GST %</th>
                                                            <th>Total Amount</th>
                                                            
                                                        </tr>  
                                                            <?php $i = 1; foreach($tagg_part_npc as $tpart) { ?>
                                                                <tr id="tr<?php echo $tpart->part_id; ?>">
                                                                    <td>{{$i++}}</td>
                                                                    <td>
                                                                    <select form="estmt_approve_save" name="SparePart[<?php echo $tpart->part_id;?>][spare_id]" id="part_name<?php echo $tpart->part_id; ?>"   class="form-control" onchange="get_partno('<?php echo $tpart->part_id; ?>',this.value)" >
                                                                        <option value="">Select</option>
                                                                        <?php
                                                                                foreach($part_arr as $part)
                                                                                {
                                                                                    ?><option value="<?php echo $part->spare_id; ?>" <?php if($part->spare_id==$tpart->spare_id) { echo 'selected';} ?>><?php echo $part->part_name; ?></option>     
                                                                        <?php   }
                                                                        ?>
                                                                    </select>
                                                                    </td>
                                                                    <td>
                                                                        <select form="estmt_approve_save" id="part_no<?php echo $tpart->part_id; ?>"  class="form-control"  >
                                                                            <option value="<?php echo $tpart->part_no; ?>"><?php echo $tpart->part_no; ?></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input form="estmt_approve_save" maxlength="5" id="no_pen_part<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->pending_parts;?>" >
                                                                    </td>
                                                                    <td>
                                                                        <input form="estmt_approve_save" id="color<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->color;?>" >
                                                                    </td>
                                                                    <td>
                                                                        <select form="estmt_approve_save" id="charge_type<?php echo $tpart->part_id; ?>"  class="form-control"  >
                                                                            <option value="Chargeable" <?php if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option>
                                                                            <option value="Non Chargeable" <?php if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option>
                                                                        </select>
                                                                    </td>

                                                                    <td style="text-align:right;">
                                                                        <?php echo number_format((float)$tpart->customer_price,2,'.','');?>
                                                                    </td>
                                                                    <td style="text-align:right;">
                                                                        <?php echo number_format((float)$tpart->gst,2,'.','');?>
                                                                    </td>
                                                                    <td style="text-align:right;">
                                                                        <?php echo number_format((float)$tpart->total,2,'.','');?>
                                                                    </td>
                                                                    
                                                                </tr>

                                                            <?php } ?>
                                                                <?php $i = 1; foreach($labr_part_npc as $tpart) { $tpart->part_id = $tpart->tlp_id; ?>
                                                                <tr id="tr<?php echo $tpart->part_id; ?>">
                                                                    <td>{{$i++}}</td>
                                                                    <td>
                                                                    <select form="estmt_approve_save" name="LabPart[<?php echo $tpart->part_id;?>][spare_id]" id="part_name<?php echo $tpart->part_id; ?>"   class="form-control" onchange="get_partno('<?php echo $tpart->part_id; ?>',this.value)" >
                                                                        <option value="">Select</option>
                                                                       <?php foreach($lbr_charge_det as $lcd)
                                                                        {
                                                                ?>        <option value="lc-<?php echo $lcd->symptom_type; ?>" <?php if($lcd->symptom_type==$tpart->symptom_type) { echo 'selected';} ?>><?php echo $lcd->symptom_type; ?></option>    
                                                                <?php   }?>
                                                                    </select>
                                                                    </td>
                                                                    <td>
                                                                        <select form="estmt_approve_save" id="part_no<?php echo $tpart->part_id; ?>"  class="form-control"  >
                                                                            <option value="<?php echo $tpart->lab_id; ?>"><?php echo $tpart->symptom_name; ?></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input form="estmt_approve_save" maxlength="5" id="no_pen_part<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->pending_parts;?>" >
                                                                    </td>
                                                                    <td>
                                                                        <input form="estmt_approve_save" id="color<?php echo $tpart->part_id; ?>" class="form-control" readonly="" type="text"  value="<?php echo $tpart->color;?>" >
                                                                    </td>
                                                                    <td>
                                                                        <select form="estmt_approve_save" id="charge_type<?php echo $tpart->part_id; ?>"  class="form-control"  >
                                                                            <option value="Chargeable" <?php if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option>
                                                                            <option value="Non Chargeable" <?php if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option>
                                                                        </select>
                                                                    </td>

                                                                    <td style="text-align:right;">
                                                                        <?php echo number_format((float)$tpart->customer_price,2,'.','');?>
                                                                    </td>
                                                                    <td style="text-align:right;">
                                                                        <?php echo number_format((float)$tpart->gst,2,'.','');?>
                                                                    </td>
                                                                    <td style="text-align:right;">
                                                                        <?php echo number_format((float)$tpart->total,2,'.','');?>
                                                                    </td>
                                                                    
                                                                </tr>

                                                            <?php } ?>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="6"></td>
                                                                <td colspan="2" style="text-align:right;">Total Estimation </td>
                                                                <td  align="right">
                                                                    <?php echo number_format((float)$data['estmt_charge'],2,'.',''); ?>
                                                                </td>
                                                            
                                                            </tr>
                                                            <tr>
                                                                <td colspan="9" align="right">
                                                                <button id="estmt_cancel"   type="button" class="mt-2 btn btn-primary npc_cancel_class" onclick="return estmt_cancelled();" >Cancel</button> 
                                                                <button id="estmt_not_approve"  type="button" class="mt-2 btn btn-primary npc_notapprove_class" onclick="return estmt_not_approved();" >Not Approved</button> 
                                                                <button form="estmt_approve_save" id="estmt_approve" name="submit" value="approve" type="button" onclick="return estmt_approved();" class="mt-2 btn btn-primary"  >Approved</button> 
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>  
                                                    <input form="estmt_approve_save" type="hidden" id="tag_id2" name="tag_id" value="<?php echo $data['TagId'];?>" />
                                                
                                                </div>
                                                <?php } ?>
                                                <form id="save_add_comment" >
                                                <div class="form-row">
                                                    
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Symptom Confirmed by Technician</label>
                                                            <textarea  name="add_cmnt" id="add_cmnt" placeholder="Additional Comment" type="text" class="form-control" ><?php echo $data['add_cmnt']; ?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1" style="top: 35px;">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="save_symptom()"  class="mt-2 btn btn-info">Save</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p  id="user_message" style="display:none;background: green;padding: 5px 10px;margin-top: 45px;color: white;border-radius: 5px;"></p>
                                                    </div>
                                                      <input  type="hidden" id="tag_id_add_comment" name="TagId" value="<?php echo $data['TagId'];?>" />  
                                                    
                                                </div>    
                                                    </form>
                                                <div class="form-row">
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab(event, 'file_upload');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="get_po_parts();openTab1('Estimated_Cost', 'Part_Required');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>  

                                            <div id="Part_Required" class="tabFormcontent">
                                                <span id="scc"></span>
                                                 <div id="po_part_arr"></div>    

                                                <div class="form-row">
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab(event, 'Estimated_Cost');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
								<button type="button" onclick="openTab1('PO_Raise', 'sp');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="PO_Raise" class="tabFormcontent">
                                                
                                                
                                                    
                                                
                                                <div class="form-row">

                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Part_Required');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('PO_Raise', 'sp');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                            </div>
                                            <div id="sp" class="tabFormcontent">
                                            
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                           
                                                        </div>    
                                                    </div>                                       
                                                    
                                               <div class="form-row">     
                                            <div class="col-md-6">
                                                <div class="position-relative form-group">
                                                    <button type="button" onclick="openTab1('', 'Part_Required');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-6">
                                                <div class="position-relative form-group">

                                                    <button form="observation_save" type="submit"  class="mt-2 btn btn-primary" style="float:right;">Save</button>
                                                </div>
                                            </div> --}}


                                            <div class="col-md-6">
                                                <div class="position-relative form-group">
                                                    <button type="button" onclick="openTab1('CC', 'CCs');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                </div>
                                            </div>

                                        </div>
                                            </div>
					
 {{-- start closure codes --}}
<style>
    #closure_codes > option{   
        overflow: hidden;
        white-space: normal;
        }
    </style>
    <div id="CCs" class="tabFormcontent">
        <form method="post" id="observation_closure_code_save" name="observation_closure_code_save" action="vendor-closure-code-save-observation">                                         
            <div class="col-md-6">
                <div class="position-relative form-group">
                    <label for="examplePassword11" class="">Closure Codes<span style="color: #f00;">*</span></label>
                   
                    
                   
                        <select id="closure_codes" name="closure_codes" class="form-control"  required="" size="5">
                            <option value="">Select</option>
                            <option value="01 - IW Repairable Product but Complete unit replaced" <?php if($data['closure_codes']=='01 - IW Repairable Product but Complete unit replaced') { echo 'selected';} ?>>01 - IW Repairable Product but Complete unit replaced</option>
                            <option value="02 - IW Non Repairable (Complete product replaced) - Product fall in replacement category, Ex. Speaker  (Not accessories)" <?php if($data['closure_codes']=='02 - IW Non Repairable (Complete product replaced) - Product fall in replacement category, Ex. Speaker  (Not accessories)') { echo 'selected';} ?>>02 - IW Non Repairable (Complete product replaced) - Product fall in replacement category, Ex. Speaker  (Not accessories)</option>
                            <option value="03 - IW part replaced (Main board, LCD Panel )" <?php if($data['closure_codes']=='03 - IW part replaced (Main board, LCD Panel )') { echo 'selected';} ?>>03 - IW part replaced (Main board, LCD Panel )</option>
                            <option value="04 - IW Component Level repair - Part Changed (Transistor, Capacitor, Transformer)" <?php if($data['closure_codes']=='04 - IW Component Level repair - Part Changed (Transistor, Capacitor, Transformer)') { echo 'selected';} ?>>04 - IW Component Level repair - Part Changed (Transistor, Capacitor, Transformer)</option>
                            <option value="05 - IW Accessories Replacement" <?php if($data['closure_codes']=='05 - IW Accessories Replacement') { echo 'selected';} ?>>05 - IW Accessories Replacement</option>
                            <option value="06 - OW Product job cancelled - Estimation not approved (Normal electrical failure) ex. Line on panel , Dead" <?php if($data['closure_codes']=='06 - OW Product job cancelled - Estimation not approved (Normal electrical failure) ex. Line on panel , Dead') { echo 'selected';} ?>>06 - OW Product job cancelled - Estimation not approved (Normal electrical failure) ex. Line on panel , Dead</option>
                            <option value="07 - OW Product job cancelled - Estimation not approved (Customer indused defect) ex-Broken, water ingress" <?php if($data['closure_codes']=='07 - OW Product job cancelled - Estimation not approved (Customer indused defect) ex-Broken, water ingress') { echo 'selected';} ?>>07 - OW Product job cancelled - Estimation not approved (Customer indused defect) ex-Broken, water ingress</option>
                            <option value="08 - OW Product - Unit Not repairable (In service period) - Some offer given to customer" <?php if($data['closure_codes']=='08 - OW Product - Unit Not repairable (In service period) - Some offer given to customer') { echo 'selected';} ?>>08 - OW Product - Unit Not repairable (In service period) - Some offer given to customer</option>
                            <option value="09 - OW product - Due to part shortage offer equivalent model at Rs.xxxxx" <?php if($data['closure_codes']=='09 - OW product - Due to part shortage offer equivalent model at Rs.xxxxx') { echo 'selected';} ?>>09 - OW product - Due to part shortage offer equivalent model at Rs.xxxxx</option>
                            <option value="10 - OW Part replaced (Main board, LCD Panel )" <?php if($data['closure_codes']=='10 - OW Part replaced (Main board, LCD Panel )') { echo 'selected';} ?>>10 - OW Part replaced (Main board, LCD Panel )</option>
                            <option value="11 - OW Component Level repair - Part Changed (Transistor, Capacitor, Transformer)" <?php if($data['closure_codes']=='11 - OW Component Level repair - Part Changed (Transistor, Capacitor, Transformer)') { echo 'selected';} ?>>11 - OW Component Level repair - Part Changed (Transistor, Capacitor, Transformer)</option>
                            <option value="12 - Installation - Wall Mount done" <?php if($data['closure_codes']=='12 - Installation - Wall Mount done') { echo 'selected';} ?>>12 - Installation - Wall Mount done</option>
                            <option value="13 - Installation - Table Top done" <?php if($data['closure_codes']=='13 - Installation - Table Top done') { echo 'selected';} ?>>13 - Installation - Table Top done</option>
                            <option value="14 - Inspection/ Refurbrish done" <?php if($data['closure_codes']=='14 - Inspection/ Refurbrish done') { echo 'selected';} ?>>14 - Inspection/ Refurbrish done</option>
                            <option value="15 - EOL Model - Offer 25% discount on New purchase in same category" <?php if($data['closure_codes']=='15 - EOL Model - Offer 25% discount on New purchase in same category') { echo 'selected';} ?>>15 - EOL Model - Offer 25% discount on New purchase in same category</option>
                            <option value="16 - IW Refund given to Customer" <?php if($data['closure_codes']=='16 - IW Refund given to Customer') { echo 'selected';} ?>>16 - IW Refund given to Customer</option>
                            <option value="17 - OW Refund given to Customer" <?php if($data['closure_codes']=='17 - OW Refund given to Customer') { echo 'selected';} ?>>17 - OW Refund given to Customer</option>
                            <option value="18 - Issue resolved through Telephonically" <?php if($data['closure_codes']=='18 - Issue resolved through Telephonically') { echo 'selected';} ?>>18 - Issue resolved through Telephonically</option>
                            <option value="19 - Dealer (Normal electrical Failure) - FOC approval received" <?php if($data['closure_codes']=='19 - Dealer (Normal electrical Failure) - FOC approval received') { echo 'selected';} ?>>19 - Dealer (Normal electrical Failure) - FOC approval received</option>
                            <option value="20 - Dealer (Normal electrical Failure) - FOC approval Not received" <?php if($data['closure_codes']=='20 - Dealer (Normal electrical Failure) - FOC approval Not received') { echo 'selected';} ?>>20 - Dealer (Normal electrical Failure) - FOC approval Not received</option>
                            <option value="21 - Dealer (Broken & indused defect) - FOC approval received" <?php if($data['closure_codes']=='21 - Dealer (Broken & indused defect) - FOC approval received') { echo 'selected';} ?>>21 - Dealer (Broken & indused defect) - FOC approval received</option>
                            <option value="22 - Dealer (Broken & indused defect) - FOC approval Not received" <?php if($data['closure_codes']=='22 - Dealer (Broken & indused defect) - FOC approval Not received') { echo 'selected';} ?>>22 - Dealer (Broken & indused defect) - FOC approval Not received</option>
                            <option value="23 - Dealer (Broken & indused defect) - Dealer agreed to pay for repair" <?php if($data['closure_codes']=='23 - Dealer (Broken & indused defect) - Dealer agreed to pay for repair') { echo 'selected';} ?>>23 - Dealer (Broken & indused defect) - Dealer agreed to pay for repair</option>
                            <option value="24 - Dealer (Broken & indused defect) - Cost sharing approval received from Management side" <?php if($data['closure_codes']=='24 - Dealer (Broken & indused defect) - Cost sharing approval received from Management side') { echo 'selected';} ?>>24 - Dealer (Broken & indused defect) - Cost sharing approval received from Management side</option>
    
                        </select> 
    
                    
                </div>    
            </div>                                       
            
            <div class="form-row">     
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <button type="button" onclick="openTab1('', 'sp');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                        </div>
                    </div>
    
                    <div class="col-md-2">
                        <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                        <button type="button" onclick="save_closure_code_return()"  class="mt-2 btn btn-primary" >Save</button>
                    </div>
    
    
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                        <button type="button" onclick="openTab1('DSS', 'DSS');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                        </div>
                    </div>
    
            </div>
        </form>
    </div>
    
    {{-- end closure codes --}}
    
    {{-- start Delivery status --}}
    
    <div id="DSS" class="tabFormcontent">
        <form method="post" id="observation_delivery_status_save" name="observation_delivery_status_save" action="vendor-closure-code-save-observation1">                                         
            <div class="col-md-6">
                <div class="position-relative form-group">
                   
                </div>    
            </div>                                       
            
            <div class="form-row">     
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <button type="button" onclick="openTab1('CC', 'CCs');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                        </div>
                    </div>
    
                    <div class="col-md-6">
                        <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                        <button type="button" onclick=""  class="mt-2 btn btn-primary" style="float: right;">Save</button>
                    </div>
    
            </div>
        </form>
    </div>
    
    {{-- end Delivery Status --}}
                                    </div>                                       
                                        <input form="observation_save" type="hidden" name="tag_type" value="<?php echo $tag_type;?>" />
                                        <input form="observation_save" type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                        <input  type="hidden" id="td_<?php echo $data['TagId'];?>" value="<?php echo $last_date;?>" />
                                      
                                    <div class="app-wrapper-footer">
                                        <div class="app-footer"></div>   
                                    </div>
                                    
                            </div>
                         </div>
                    </div>
 </div>
 </div>  
</div> 



<style>
    /* Popup box BEGIN */
    .npc_notapprove_popup{
    background:rgba(0,0,0,.4);
    cursor:pointer;
    display:none;
    height:100%;
    position:fixed;
    text-align:center;
    top:100px;
    width:100%;
    z-index:10000;
}
.npc_notapprove_popup > div {
    background-color: #fff;
    box-shadow: 10px 10px 60px #555;
    display: inline-block;
    height: auto;
    max-width: 400px;
    min-height: 80px;
    vertical-align: middle;
    width: 80%;
    position: relative;
    border-radius: 8px;
    padding: 15px 1%;
}
.npc_cancel_popup{
    background:rgba(0,0,0,.4);
    cursor:pointer;
    display:none;
    height:100%;
    position:fixed;
    text-align:center;
    top:100px;
    width:100%;
    z-index:10000;
}
.srn_return_popup{
    background:rgba(0,0,0,.4);
    cursor:pointer;
    display:none;
    height:100%;
    position:fixed;
    text-align:center;
    top:100px;
    width:100%;
    z-index:10000;
}
.hover_bkgr_fricc .helper{
    display:inline-block;
    height:40%;
    vertical-align:middle;
}
.npc_cancel_popup > div {
    background-color: #fff;
    box-shadow: 10px 10px 60px #555;
    display: inline-block;
    height: auto;
    max-width: 400px;
    min-height: 80px;
    vertical-align: middle;
    width: 80%;
    position: relative;
    border-radius: 8px;
    padding: 15px 1%;
}
.srn_return_popup > div {
    background-color: #fff;
    box-shadow: 10px 10px 60px #555;
    display: inline-block;
    height: auto;
    max-width: 800px;
    min-height: 120px;
    vertical-align: middle;
    width: 80%;
    position: relative;
    border-radius: 8px;
    padding: 15px 1%;
}
.popupCloseButton1 {
    background-color: #fff;
    border: 3px solid #999;
    border-radius: 50px;
    cursor: pointer;
    display: inline-block;
    font-family: arial;
    font-weight: bold;
    position: absolute;
    top: -20px;
    right: -20px;
    font-size: 25px;
    line-height: 30px;
    width: 30px;
    height: 30px;
    text-align: center;
}
.popupCloseButton2:hover {
    background-color: #ccc;
}
.popupCloseButton2 {
    background-color: #fff;
    border: 3px solid #999;
    border-radius: 50px;
    cursor: pointer;
    display: inline-block;
    font-family: arial;
    font-weight: bold;
    position: absolute;
    top: -20px;
    right: -20px;
    font-size: 25px;
    line-height: 30px;
    width: 30px;
    height: 30px;
    text-align: center;
}
.popupCloseButton1:hover {
    background-color: #ccc;
}
.npc_cancel_class {
    cursor: pointer;
    font-size: 13px;
    margin: 10px;
    display: inline-block;
    font-weight: bold;
}

.popupCloseButton5:hover {
    background-color: #ccc;
}
.popupCloseButton6 {
    background-color: #fff;
    border: 3px solid #999;
    border-radius: 50px;
    cursor: pointer;
    display: inline-block;
    font-family: arial;
    font-weight: bold;
    position: absolute;
    top: -20px;
    right: -20px;
    font-size: 25px;
    line-height: 30px;
    width: 30px;
    height: 30px;
    text-align: center;
}


.npc_notapprove_class {
    cursor: pointer;
    font-size: 13px;
    margin: 10px;
    display: inline-block;
    font-weight: bold;
}

.srn_return_class {
    cursor: pointer;
    font-size: 13px;
    margin: 10px;
    display: inline-block;
    font-weight: bold;
}

.donate-now {
  list-style-type: none;
  margin: 25px 0 0 0;
  padding: 0;
}

.donate-now li {
  float: left;
  margin: 0 5px 0 0;
  width: 200px;
  height: 40px;
  position: relative;
}

.donate-now label,
.donate-now input {
  display: block;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.donate-now input[type="radio"] {
  opacity: 0.01;
  z-index: 100;
}

.donate-now input[type="radio"]:checked+label,
.Checked+label {
  background: yellow;
}

.donate-now label {
  padding: 5px;
  border: 1px solid #CCC;
  cursor: pointer;
  z-index: 90;
}

.donate-now label:hover {
  background: #DDD;
}
</style>



<div class="npc_cancel_popup" style="display:none;">
    <span class="helper"></span>
    <div>
        <div class="popupCloseButton1">&times;</div>
            <div id="cancell_reasion">
                <h5>Pls. Mention the reason of Cancellation</h5>
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <textarea form="estmt_approve_save" name="npc_cancel_remarks" id="npc_cancel_remarks" class="form-control" placeholder="Pls. mention the reason of cancellation"></textarea> 
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <button form="estmt_approve_save" type="button" class="btn btn-success popupCloseButton3">Remarks Cancel</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <button form="estmt_approve_save" name="submit" value="cancel" type="submit" class="btn btn-success">Remarks Save</button>
                        </div>
                    </div>
                </div>
                
            </div>
    </div>
</div>

<div class="npc_notapprove_popup" style="display:none;">
    <span class="helper"></span>
    <div>
        <div class="popupCloseButton2">&times;</div>
            <div id="not_approve_reasion">
                <h5>Pls. Mention the reason of Not Approved</h5>
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <textarea form="estmt_approve_save" name="npc_not_approve_remarks" id="npc_not_approve_remarks" class="form-control" placeholder="Pls. mention the reason of Not Approved"></textarea> 
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <button form="estmt_approve_save" type="button" class="btn btn-success popupCloseButton4">Remarks Cancel</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <button form="estmt_approve_save" name="submit" value="not approve" type="submit" class="btn btn-success">Remarks Save</button>
                        </div>
                    </div>
                </div>
                
            </div>
    </div>
</div>

<div class="srn_return_popup" style="display:none;">
    <span class="helper"></span>
    <div>
        <div class="popupCloseButton6">&times;</div>
            
                <h5>Select Reason of Part Return</h5>
                <form id="form_srn_return" name="form_srn_return" action="return-srn-po" method="post">
                <div class="form-row">
                    <ul class="donate-now">
                        
                        <li>
                            <input type="radio" id="return_type1" name="srn_type" value="Defective" />
                          <label for="return_type1">Defective Part Received</label>
                        </li>
                        <li>
                          <input type="radio" id="return_type2" name="srn_type" value="Mismatched" />
                          <label for="return_type2">Wrong Part Ordered</label>
                        </li>
                        <li>
                          <input type="radio" id="return_type3" name="srn_type" value="Mismatched" />
                          <label for="return_type3">Wrong Part Received</label>
                        </li>  
                    </ul>
                    
                </div>
                    <br>
                    <div class="form-row">
                        <div class="col-md-12">
                            Please Mention Remarks Here
                            <textarea name="remarks_return" id="remarks_return" class="form-control" placeholder="" required=""></textarea> 
                        </div>
                    </div>
                    <br>
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <input type="file" id="attch_image" name="attch_image" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative form-group">
                            <button  name="submit" value="send_for_approval" type="button" onclick="save_srn_return()"  class="btn btn-success">Send for Approval</button>
                        </div>
                    </div>
                </div>
                    <input type="hidden" id="srn_part_id" name="srn_part_id" value="" />
                </form>
    </div>
</div>

<script>
     k(window).load(function () {
    k('.popupCloseButton1').click(function(){
        k('.npc_cancel_popup').hide();
    });
    k('.popupCloseButton2').click(function(){
        k('.npc_notapprove_popup').hide();
    });
    k('.popupCloseButton3').click(function(){
        k('.npc_cancel_popup').hide();
    });
    k('.popupCloseButton4').click(function(){
        k('.npc_notapprove_popup').hide();
    });
    
    k('.popupCloseButton5').click(function(){
        k('.srn_return_popup').hide();
    });
    k('.popupCloseButton6').click(function(){
        k('.srn_return_popup').hide();
    });
});

function return_srn_fun(part_id)
{
k('.srn_return_popup').show();
k('#srn_part_id').val(part_id);
}

function save_srn_return()
 {
     
    //e.preventDefault(); // avoid to execute the actual submit of the form.
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#form_srn_return");
    var formData = new FormData(form[0]);
    
    var url = form.attr('action');
    
    $.ajax({
           type: "POST",
           url: url,
           data: formData,            
           enctype: 'multipart/form-data',
           cache:false,
           processData: false,
           contentType: false,
           success: function(result)
           {
               if(result==='1')
               {
                   alert('SRN Return Generated Successfully.');
                   get_po_parts();
                   $('.srn_return_popup').hide();
                   
               }
               else
               {
                   alert('SRN Return Request Failed.');
                   
               }
           }
         });    

    return false;
 }
 
 function estmt_approved()
{
    var form = k("#estmt_approve_save");
    var formData = new FormData(form[0]);
    
    var url = form.attr('action');

	k.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
	jQuery.ajax({
           type: "POST",
           url: url,
           data: formData,
           processData: false,
            contentType: false,
              success: function(result){
                  const obj = JSON.parse(result);
                  if(obj.status===true)
                  {
                      $('#estmt_approval').html(obj.html);
                  }
                  $('#user_message').show();
                  $('#user_message').html(obj.msg);
              }}); 
	
}

function save_symptom()
{
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
    var form = $("#save_add_comment");
    var formData = new FormData(form[0]);   
    
    $.ajax({
        type: "POST",
        url: 'save_add_comment',
        data: formData,            
        cache:false,
        processData: false,
        contentType: false,
        success: function(result)
        {
            if(result==='1')
            {
                alert('Additional Comments Saved Successfully.');    
            }
            else
            {
                alert('Additional Comment Failed.');
            }
        }
    }); 
    
}


// customer details

function save_customer_details_return()
 {
    
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#observation_customer_details_save");
    var formData = new FormData(form[0]);
    var a = 1;

     $('.removeid').remove();

          if($('#Customer_Group').val()=='')
          {
            a = 0;
            $('#Customer_Group').after('<span style="color:red" class="removeid">Please Select Customer Group</span>');
              
          }

          if($('#Customer_Name').val()=='')
          {
            a = 0;
            $('#Customer_Name').after('<span style="color:red" class="removeid">Please Fill Customer Name</span>');
              
          }

          if($('#Customer_Address').val()=='')
          {
            a = 0;
            $('#Customer_Address').after('<span style="color:red" class="removeid">Please Fill Communication Address</span>');
              
          }
          
          if($('#call_rcv_frm').val()=='')
          {
            a = 0;
            $('#call_rcv_frm').after('<span style="color:red" class="removeid">Please Fill Call Receive</span>');
              
          }

          
          if($('#Contact_No').val()=='')
          {
            a = 0;
            $('#Contact_No').after('<span style="color:red" class="removeid">Please Fill Contact No. </span>');
              
          }

          
          if($('#state').val()=='')
          {
            a = 0;
            $('#state').after('<span style="color:red" class="removeid">Please Select State</span>');
              
          }


          
          if($('#pincode').val()=='')
          {
            a = 0;
            $('#pincode').after('<span style="color:red" class="removeid">Please Select Pincode</span>');
              
          }

   

        if(a=='1')
        {
            var url = form.attr('action');
            
            $.ajax({
                type: "POST",
                url: url,
                data: formData,            
                cache:false,
                processData: false,
                contentType: false,
                success: function(result)
                {
                    if(result==='1')
                    {
                        alert('Customer Details Updated Successfully.');
                    
                        
                    }
                    else
                    {
                        alert('Customer Details Updatation Failed.');
                        
                    }
                }
                });    
          }
            return false;
        }

// end customer details

// product details

function save_product_details_return()
 {
    
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#observation_product_details_save");
    var formData = new FormData(form[0]);
    
    var a = 1;

    $('.removeid').remove();

        if($('#service_type').val()=='')
        {
        a = 0;
        $('#service_type').after('<span style="color:red" class="removeid">Please Select Service Type</span>');
            
        }

        if($('#warranty_type').val()=='')
        {
        a = 0;
        $('#warranty_type').after('<span style="color:red" class="removeid">Please Fill Warranty Type</span>');
            
        }

        if($('#Brand').val()=='')
        {
        a = 0;
        $('#Brand').after('<span style="color:red" class="removeid">Please Select Brand</span>');
            
        }

        if($('#Product_Detail').val()=='')
        {
        a = 0;
        $('#Product_Detail').after('<span style="color:red" class="removeid">Please Select Product Category</span>');
            
        }


        if($('#Product').val()=='')
        {
        a = 0;
        $('#Product').after('<span style="color:red" class="removeid">Please Select Model No. </span>');
            
        }


        if($('#Model').val()=='')
        {
        a = 0;
        $('#Model').after('<span style="color:red" class="removeid">Please Select Model Name</span>');
            
        }



        if($('#Serial_No').val()=='')
        {
        a = 0;
        $('#Serial_No').after('<span style="color:red" class="removeid">Please Select Serial Number</span>');
            
        }


        if($('#warranty_card').val()=='')
        {
        a = 0;
        $('#warranty_card').after('<span style="color:red" class="removeid">Please Select Warranty card Availability</span>');
            
        }

        

        if($('#invoice').val()=='')
        {
        a = 0;
        $('#invoice').after('<span style="color:red" class="removeid">Please Select Purchase Invoice Availability</span>');
            
        }

        

        if($('#ccsc').val()=='')
        {
        a = 0;
        $('#ccsc').after('<span style="color:red" class="removeid">Please Fill Customer Complaint.</span>');
            
        }



        if(a=='1')
        {
            var url = form.attr('action');
            
            $.ajax({
                type: "POST",
                url: url,
                data: formData,            
                cache:false,
                processData: false,
                contentType: false,
                success: function(result)
                {
                    if(result==='1')
                    {
                        alert('Product Details Updated Successfully.');
                    
                        
                    }
                    else
                    {
                        alert('Product Details Updatation Failed.');
                        
                    }
                }
                });    

            return false;
        }

}

// end product details


// closure code

function save_closure_code_return(){

    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#observation_closure_code_save");
    var formData = new FormData(form[0]);
    
    var a = 1;
    $('.removeid').remove();

if($('#closure_codes').val()=='')
{
  a = 0;
  $('#closure_codes').after('<span style="color:red" class="removeid">Please Select Closure Code.</span>');
    
}




if(a=='1')
{

    var url = form.attr('action');
    
    $.ajax({
           type: "POST",
           url: url,
           data: formData,            
           cache:false,
           processData: false,
           contentType: false,
           success: function(result)
           {
               if(result==='1')
               {
                   alert('Closure Code Updated Successfully.');
             
                   
               }
               else
               {
                   alert('Closure Code Updatation Failed.');
                   
               }
           }
         });    

    return false;

}


}

//  end closure code

// image upload 

function download_img(img)
    {
        
        var imgs  = document.getElementById(''+img+'_img').src;
        var image = document.createElement("a");
        image.setAttribute("href", imgs);
        image.setAttribute("download", "img.jpg");
        document.body.appendChild(image);
         image.click();
        image.remove();
          
    }
    
    function file_move(img)
    {
        var img_demo = $('#img_demo');
        //console.log(img_demo.attr('src'));
        if(img_demo.attr('src')==='' || img_demo.attr('src')==='#')
        {
            return 0;
        }
        var clone_img = img_demo.clone();
        clone_img.attr("id",img+'_img');
        $('#'+img+'_img'+'_disp').html(clone_img);
        var save_btn = '<button type="button" name="submit" class="btn btn-primary" id="warraty_card_save" onclick="save_image('+"'"+img+"'"+')" style="float:right;margin-right: 70px;">save</button>';
        $('#'+img+'_img'+'_disp').append(save_btn);
        
        $('#'+img+'_cntr').show();
        img_demo.attr('src', '#');
        img_demo.hide();
        var file_input = $('#file-input-type').val();
        var file_demo = $('#'+file_input);
        var clone_file = file_demo.clone();
        clone_file.attr("id",img);
        clone_file.attr("name",img);
            $('#form'+img).remove();
            $('#'+img+'_img'+'_disp').append('<form id="form'+img+'" name="form'+img+'"></form>');
            $('#'+"form"+img).append(clone_file);
            var tagid = $('#TagId').val();
            var file_type = $('#file_type').val();
            $('#'+"form"+img).append('<input type="hidden" name="TagId" value="'+tagid+'"> <input type="hidden" name="file_type" value="'+file_type+'">'); 
        
        
        //console.log(clone);
                
    }
    
    function remove_img(img)
    {
        var img_prev = $('#'+img+'_img').remove();
        $('#'+img+'_cntr').hide();
        $('#'+img).remove();
    }


    function save_image(id)
    {
        // if($('#file-input').val()=='' || $('#file-input-camera').val()=='')
        // {
        // a = 0;
        // $('#file-input').after('<span style="color:red" class="removeid">Please Select Image.</span>');
            
        // }




        // if(a=='1')
        // {
        
    $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
        var form = $("#form"+id);
        var formData = new FormData(form[0]);
        
        var url = form.attr('action');
        
        $.ajax({
            type: "POST",
            url: 'save-image',
            data: formData,            
            enctype: 'multipart/form-data',
            cache:false,
            processData: false,
            contentType: false,
            success: function(data)
            {
                //    alert(data); // show response from the php script.
                if(data==='1')
                {
                    alert('Image saved Successfully.');
                    $('#remove_prcs').remove();
                    $('#'+"remove_"+id).remove();
                    $('#warraty_card_save').remove();
                    
                }
                else
                {
                    alert('Image already Saved');
                    $('#remove_wrrn').hide();
                    
                }
                
            }
            });    

        return false;
    //}

}

// delete image 

 function deleteRecord(id)
 {
        var token = $("meta[name='csrf-token']").attr("content");
    
        $.ajax(
        {
            url: "delete-upload-image/"+id,
            type: 'DELETE',
            data: {
                "id": id,
                "_token": token,
            },
            success: function (){
                alert("it Works");
            }
        });
    
  }

 // end image upload 


</script>
 
 
 


@endsection
