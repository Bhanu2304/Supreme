@extends('layouts.app')

@section('content') 

<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<script>
 function checkNumber(val,evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
	
	if (charCode> 31 && (charCode < 48 || charCode > 57) )
        {            
            return false;
        }
        if(val.length>10)
        {
            return false;
        }
	return true;
}

function openTab(evt, tabName) {
    
    var flag_valid = false;
    if(tabName==='Customer_Details')
    {
         flag_valid = true;
    }
    else
    {
        flag_valid = validate_customer_details();
        //alert(flag_valid);
        if(flag_valid===true && tabName!=='Product_Details')
        {
            flag_valid = validate_product_details();
        }
    }
    
    
    
    if(flag_valid===true)
    {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabFormcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tabFormlinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    }
}


function validate_customer_details()
{
    if(document.getElementById('Customer_Group').value==='')
    {
        alert('Please Fill Customer Group');
        return false;
    }
    else if(document.getElementById('Customer_Name').value==='')
    {
        alert('Please Fill Customer Name');
        return false;
    }
    else if(document.getElementById('Contact_No').value==='')
    {
        alert('Please Fill Contact No.');
        return false;
    }
    else if(document.getElementById('Contact_No').value.length!==10)
    {
        alert('Please Fill Right Mobile No.');
        return false;
    }
    else if(document.getElementById('Customer_Address').value==='')
    {
        alert('Please Fill Customer Address');
        return false;
    }
    else if(document.getElementById('call_rcv_frm').value==='')
    {
        alert('Please Fill Call Receive From');
        return false;
    }
    
    else if(document.getElementById('state').value==='')
    {
        alert('Please Select State');
        return false;
    }
    else if(document.getElementById('pincode').value==='')
    {
        alert('Please Select Pincode');
        return false;
    }
    return true;
}

function validate_product_details()
{
    if(document.getElementById('service_type').value==='')
    {
        alert('Please Select Service Type');
        return false;
    }
    if(document.getElementById('warranty_type').value==='')
    {
        alert('Please Select Warranty Type');
        return false;
    }
    else if(document.getElementById('warranty_category').value==='')
    {
        alert('Please Select Warranty Category');
        return false;
    }
    else if(document.getElementById('Brand').value==='')
    {
        alert('Please Select Brand');
        return false;
    }
    else if(document.getElementById('Product_Detail').value==='')
    {
        alert('Please Select Product Detail');
        return false;
    }
    else if(document.getElementById('Product').value==='')
    {
        alert('Please Select Product');
        return false;
    }
    else if(document.getElementById('Model').value==='')
    {
        alert('Please Select Model');
        return false;
    }
    else if(document.getElementById('Serial_No').value==='')
    {
        alert('Please Fill Serial No.');
        return false;
    }
    
    
    
    else if(document.getElementById('warranty_card').value==='')
    {
        alert('Please Select Warranty Card');
        return false;
    }
    
    
    return true;
}


function openTab1(div_name, tabName) {
    var flag_valid = false;
    if(div_name==='Customer_Details')
    {
         flag_valid = validate_customer_details();
    }
    if(div_name==='Product_Details')
    {
         flag_valid = validate_product_details();
    }
    if(flag_valid===true)
    {
        var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabFormcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tabFormlinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  document.getElementById(tabName+'1').className += " active";
    }
  
}
</script>

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
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
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
</style>


<div class="app-main"> 
 <div class="app-main__outer">
                    <div class="app-main__inner">
                         <div class="tab-content">
                            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                                <div class="main-card mb-3 card">
                                    <form action="save-tagging" method="post" autocomplete="off" enctype="multipart/form-data">
                                    <div class="card-body">
                                        
                                        <div class="tabForm">
                                            <button type="button" id="Customer_Details1" class="tabFormlinks" onclick="openTab(event, 'Customer_Details')" >Customer Details</button>
                                            <button type="button" id="Product_Details1" class="tabFormlinks" onclick="openTab(event, 'Product_Details')">Product Details</button>
                                            <!--<button type="button" id="Set_Conditions1" class="tabFormlinks" onclick="openTab(event, 'Set_Conditions')">Set Conditions</button>-->
                                            <button type="button" id="Estimated_Cost1" class="tabFormlinks" onclick="openTab(event, 'Estimated_Cost')">Upload Documents</button>
                                        </div>
                                        <div id="Customer_Details" class="tabFormcontent" style="display:block;">
                                        <h5 class="card-title">Customer Details</h5>
                                        
                                            <div class="form-row">
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Group<span style="color: #f00;">*</span></label>
						    <select name="Customer_Group" id="Customer_Group" class="form-control" required>
                                                        <option value="">Customer Group</option>
                                                        <option value="Dealer">Dealer</option>
                                                        <option value="Normal Customer">Normal Customer</option>
                                                        <option value="Internal Customer">Internal Customer</option>
                                                    </select>
                                                    </div>
                                                </div>
                                                
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Name<span style="color: #f00;">*</span></label>
						    <input name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" class="form-control" required></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Communication Address<span style="color: #f00;">*</span></label>
						    <input name="Customer_Address" id="Customer_Address" placeholder="Communication Address" type="text" class="form-control" required></div>	
						</div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Near By Landmark</label>
						    <input name="Landmark" id="Landmark" placeholder="Landmark" type="text" class="form-control" ></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Call received from<span style="color: #f00;">*</span></label>
                                                        <input name="call_rcv_frm" id="call_rcv_frm" value="Automatic" placeholder="Call received from" type="text" class="form-control" required></div>	
						</div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer No.<span style="color: #f00;">*</span></label>
                                                        <input name="Contact_No" id="Contact_No" placeholder="Customer No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required="" maxlength="10" ></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Alt No.</label>
                                                        <input name="Alt_No" id="Alt_No" placeholder="Alternate No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)"  maxlength="12" ></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">State <span style="color: #f00;">*</span></label>

                                                        <select name="state" id="state" data-placeholder="" class="form-control" onchange="get_pincode(this.value)"  required="">
                                                            <option value="">Select</option>
                                                            <?php   foreach($state_master as $state_id=>$state)
                                                                    {
                                                                        echo '<option value="'.$state.'">'.$state.'</option>';
                                                                    }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Pincode <span style="color: #f00;">*</span></label>

                                                        <select onchange="get_area(this.value)" name="pincode" id="pincode" data-placeholder="" class="form-control" tabindex="9" required="">
                                                            <option value="">Select</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Place </label>

                                                        <select name="place" id="place" class="form-control" >
                                                            <option value="">Select</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Email</label>
                                                        <input type="text" name="email" id="email" placeholder="Email"  class="form-control"   ></div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Gst No.</label>
                                                        <input name="Gst_No" id="Gst_No" placeholder="GST No." type="text" class="form-control"   ></div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <br/>
                                                        <button type="button" onclick="openTab1('Customer_Details', 'Product_Details');" class="mt-2 btn btn-success">Next</button>
                                                    </div>
                                                </div>
                                                
                                                
                                            </div> 
                                        </div>
                                        <div id="Product_Details" class="tabFormcontent">
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type <span style="color: #f00;">*</span></label>
                                                        <select id="service_type" name="service_type" class="form-control"  required="">
                                                            <option value="">Select</option>
                                                            <option value="Home Service">Home Service</option>
                                                            <option value="Walk in Service">Walk in Service</option>
                                                            <option value="Refurbrish">Refurbrish</option>
                                                            <option value="D&I">D&I</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Type <span style="color: #f00;">*</span></label>
                                                        <select id="warranty_type" name="warranty_type" class="form-control" required="">
                                                            <option value="">Select</option>
                                                            <option value="Standard Warranty">Standard Warranty</option>
                                                            <option value="Out of warranty">Out of warranty</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Category <span style="color: #f00;">*</span></label>
                                                        <select id="warranty_category" name="warranty_category" class="form-control" required="">
                                                            <option value="">Select</option>
                                                            <option value="Standard warranty">Standard Warranty</option>
                                                            <option value="Out Warranty">Out Warranty</option>
                                                            <option value="Extended">Extended</option>
                                                            <option value="International">International</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Brand<span style="color: #f00;">*</span></label>
                                                        <select name="Brand" id="Brand" class="form-control" onchange="get_product_category('',this.value)" required>
                                                            <option value="">Select</option>
                                                        <?php   foreach($brand_master as $brand_id=>$brand_name)
                                                                {
                                                                    echo '<option value="'.$brand_id.'">'.$brand_name.'</option>';
                                                                }
                                                        ?>
                                                    </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Product Category<span style="color: #f00;">*</span></label>
						    <select name="Product_Detail" id="Product_Detail" class="form-control" onclick="get_product('',this.value)" required>
                                                        <option value="">Select</option>
                                                        
                                                    </select>
                                                    </div>
                                            </div>
                                                
                                            <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Model No.<span style="color: #f00;">*</span></label>
						    <select name="Product" id="Product" class="form-control" onchange="get_model('',this.value)" required>
                                                        <option value="">Select</option>
                                                        
                                                    </select>
                                                    </div>
                                            </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Model Name<span style="color: #f00;">*</span></label>
                                                        <select name="Model" id="Model" onchange="get_model_dependents('',this.value)" class="form-control" required>
                                                        <option value="">Select</option>
                                                    </select>
                                                    </div>	 
						</div>
						
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Serial Number<span style="color: #f00;">*</span></label>
						    <input name="Serial_No" id="Serial_No" placeholder="Serial No" type="text" class="form-control" required></div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Manufacturer serial number</label>
						    <input name="man_ser_no" id="man_ser_no" placeholder="Man. Serial No" type="text" class="form-control" ></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty card <span style="color: #f00;">*</span></label>
                                                        <select id="warranty_card" name="warranty_card" class="form-control"  required="">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice </label>
                                                        <select id="invoice" name="invoice" class="form-control"   >
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Purchase Date</label>
                                                        <input name="Bill_Purchase_Date" id="Bill_Purchase_Date" placeholder="Bill Purchase Date" type="text" class="form-control datepicker" readonly="" ></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer Name</label>
                                                        <input name="dealer_name" id="dealer_name" placeholder="Dealer Name" type="text" class="form-control"  ></div>
                                                </div>
                                                
                                                
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice No.</label>
                                                        <input name="invoice_no" id="invoice_no" placeholder="Invoice No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" maxlength="10" ></div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Complaint & Special Comments</label>
                                                        <input name="ccsc" id="ccsc" placeholder="Comments..." type="text" class="form-control"  ></div>
                                                </div>
						
                                                
                                                
                                                
                                                
                                                 
                                                
						
						
						
                                                
                                                                                               
                                            </div>
                                            <div class="form-row">
                                                
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" style="float:left;" onclick="openTab(event, 'Customer_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                            <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        
                                                        <button type="button" style="float:right;" onclick="openTab1( 'Product_Details','Estimated_Cost');" class="mt-2 btn btn-success" >Next</button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>    
                                        
                                       <!-- <div id="Set_Conditions" class="tabFormcontent">
                                            <div class="form-row" id="cndn_det">
                                            <?php foreach($set_con_master as $field_name=>$sub_field_name) { ?>
                                            
                                            
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><?php echo $field_name; ?></label>
                                                        <select id="<?php echo $field_name; ?>" name="set_conditions[<?php echo $field_name; ?>]" class="form-control" >
                                                            <option value="">Select</option>
                                                            <?php 
                                                                foreach($sub_field_name as $sub)
                                                                {
                                                                    echo '<option value="'.$sub.'">'.$sub.'</option>';
                                                                }
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            
                                            <?php } ?>
                                            </div>
                                            <h5 class="card-title">Accessory List</h5>
                                            <div class="form-row" id="acc_det">
                                            <?php foreach($acc_master as $field_name=>$sub_field_name) { ?>
                                            
                                            
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><?php echo $sub_field_name; ?></label>
                                                        <select id="<?php echo $sub_field_name; ?>" name="accesories_list[<?php echo $sub_field_name; ?>]" class="form-control" >
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            
                                            <?php } ?>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Product_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Estimated_Cost');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                        </div>-->
                                        <div id="Estimated_Cost" class="tabFormcontent">
                                            <div class="form-row">
                                                <?php if($entry_type=='walking') { ?>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""> Warranty Card <span style="color: #f00;">*</span></label>
                                                        <input name="warranty_card_copy" id="warranty_card_copy" type="file" class="form-control" required=""></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""> Purchase Invoice. <span style="color: #f00;">*</span></label>
                                                        <input name="invoice_copy" id="invoice_copy" type="file" class="form-control" required=""></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""> Model No. Image <span style="color: #f00;">*</span></label>
                                                        <input name="model_no_copy" id="model_no_copy" type="file" class="form-control" required=""></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Serial No. Image <span style="color: #f00;">*</span></label>
                                                        <input name="serial_no_copy" id="serial_no_copy" type="file" class="form-control" required=""></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Symptom Image 1 <span style="color: #f00;">*</span></label>
                                                        <input name="symptom_photo1" id="symptom_photo1" type="file" class="form-control" required=""></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Symptom Image 2 <span style="color: #f00;">*</span></label>
                                                        <input name="symptom_photo2" id="symptom_photo2" type="file" class="form-control" required=""></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Symptom Image 3 <span style="color: #f00;">*</span></label>
                                                        <input name="symptom_photo3" id="symptom_photo3" type="file" class="form-control" required=""></div>
                                                </div>
                                                
                                                    <?php } ?>
                                                
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <br/>
                                                        <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Product_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                            
                                                
                                            </div>
                                            
                                            
                                        </div>    
                                        </div>                                      
                                        <input type="hidden" name="tag_type" value="<?php echo $tag_type;?>" />
                                        <input type="hidden" name="entry_type" value="<?php echo $entry_type;?>" />
                                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            
                        </div>   
                    </div>
                                         </form>
 </div>    
</div>
                         </div>
                    </div>
 </div>
</div>

<script>
function get_pincode(state_name)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-pincode-by-state-name',
              method: 'post',
              data: {
                 state_name: state_name 
              },
              success: function(result){
                  $('#pincode').html(result)
              }});
 }

function get_product_category(div_id,brand_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-product-category-by-brand-id',
              method: 'post',
              data: {
                 brand_id: brand_id 
              },
              success: function(result){
                  $('#Product_Detail'+div_id).html(result);
              }});
 }
 
 function get_product(div_id,product_category_id)
 {
     var brand_id = $('#Brand'+div_id).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-product-by-brand-id',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id
              },
              success: function(result){
                  $('#Product'+div_id).html(result);
              }});
 }
 
 function get_model(div_id,product_id)
 {
    var brand_id = $('#Brand'+div_id).val();
    var product_category_id = $('#Product_Detail'+div_id).val();
    
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-model-by-product-id',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id
              },
              success: function(result){
                  $('#Model'+div_id).html(result);
              }});
 }   
 
 function get_model_dependents(div_id,model_id)
 {
    var brand_id = $('Brand'+div_id).val();
    var product_category_id = $('#Product_Detail'+div_id).val();
    var product_id = $('#Product_Id'+div_id).val();
    
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-acc-dt',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id
              },
              success: function(result){
                  $('#acc_det'+div_id).html(result);
              }});
          
          jQuery.ajax({
              url: 'get-cndn-dt',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id
              },
              success: function(result){
                  $('#cndn_det'+div_id).html(result);
              }});
          
          
 }
 
 
 function get_area(pincode)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-area-by-pincode',
              method: 'post',
              data: {
                 pincode: pincode
                 
              },
              success: function(area){
                  $('#place').html(area);
              }});
 }
 
 
</script>


@endsection
