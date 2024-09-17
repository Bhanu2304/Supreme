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
    if(tabName=='Customer_Details')
    {
         flag_valid = true;
    }
    else
    {
        flag_valid = validate_customer_details();
        //alert(flag_valid);
        if(flag_valid==true && tabName!='Product_Details')
        {
            flag_valid = validate_product_details();
        }
    }
    
    if(flag_valid==true)
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

function validate_set_conditions()
{
    var flag_valid = true;
    $('select[name^=set_conditions]').each(function(index,val) 
    {
        var id = val.id;
        //console.log(val.name);
        
        var set_conditions = 'set_conditions[';
        var setlen = set_conditions.length;
        var key_name = val.name;
        var key = key_name.substring(setlen, key_name.length-1);
        
        if(document.getElementById(id).value==='')
        {
            if(flag_valid==true)
            {
                alert("Please Select "+key);
                document.getElementById(id).focus();
                flag_valid = false;
            }
        }
    });
    
   if(flag_valid==true)
   {
       $('select[name^=accesories_list]').each(function(index,val) 
    {
        var id = val.id;
        //console.log(val.name);
        
        var set_conditions = 'accesories_list[';
        var setlen = set_conditions.length;
        var key_name = val.name;
        var key = key_name.substring(setlen, key_name.length-1);
        
        if(document.getElementById(id).value==='')
        {
            if(flag_valid==true)
            {
                alert("Please Select "+key);
                document.getElementById(id).focus();
                flag_valid = false;
            }
        }
    });
   }
    
    
    return flag_valid;
    
    
}

function openTab1(div_name, tabName) {
    
    var flag_valid = false;
    if(div_name=='Customer_Details')
    {
         flag_valid = validate_customer_details();
    }
    else if(div_name=='Product_Details')
    {
         flag_valid = validate_product_details();
    }
    else if(div_name=='Set_Conditions')
    {
         flag_valid = validate_set_conditions();
    }
    else
    {
        flag_valid = true;
    }
    
    if(flag_valid==true)
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
                                    <form method="post" action="vendor-save-observation" enctype="multipart/form-data">
                                        <div class="card-body">
                                        
                                        <div class="tabForm">
                                            <button type="button" id="Customer_Details1" class="tabFormlinks" onclick="openTab(event, 'Customer_Details')" >Customer Details</button>
                                            <button id="Product_Details1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Product_Details')">Product Details</button>
                                            <button id="Set_Conditions1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Set_Conditions')">Set Conditions</button>
                                            <button id="Part_Required1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Part_Required')">Part Required</button>
                                            <button id="Estimated_Cost1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Estimated_Cost')">Estimated Cost</button>
                                          </div>
                                            <div id="Customer_Details" class="tabFormcontent" style="display:block;">
                                            <h5 class="card-title">Customer Details</h5>

                                                <div class="form-row">

                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Group<span style="color: #f00;">*</span></label>
						    <select name="Customer_Group" id="Customer_Group" class="form-control" required>
                                                        <option value="">Customer Group</option>
                                                        <option value="Dealer" <?php if($data['Customer_Group']=='Dealer') { echo "selected";} ?>>Dealer</option>
                                                        <option value="Normal Customer" <?php if($data['Customer_Group']=='Normal Customer') { echo "selected";} ?>>Normal Customer</option>
                                                        <option value="Internal Customer" <?php if($data['Customer_Group']=='Internal Customer') { echo "selected";} ?>>Internal Customer</option>
                                                    </select>
                                                    </div>
                                                </div>

                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Name<span style="color: #f00;">*</span></label>
                                                            <input name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" value="<?php echo $data['Customer_Name']; ?>" class="form-control" required></div>
                                                    </div>
                                                   
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Communication Address<span style="color: #f00;">*</span></label>
                                                        <input name="Customer_Address" id="Customer_Address" placeholder="Communication Address" type="text" value="<?php echo $data['Customer_Address']; ?>" class="form-control" required></div>	
                                                    </div>

                                                   <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Near By Landmark</label>
						    <input name="Landmark" id="Landmark" value="<?php echo $data['Landmark']; ?>" placeholder="Landmark" type="text" class="form-control" ></div>	
						</div>
                                                    
                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Call received from<span style="color: #f00;">*</span></label>
                                                        <input name="call_rcv_frm" id="call_rcv_frm"  value="<?php echo $data['call_rcv_frm']; ?>" placeholder="Call received from" type="text" class="form-control" required></div>	
						</div>
                                                     <div class="col-md-4">
                                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Customer No.<span style="color: #f00;">*</span></label>
                                                            <input name="Contact_No" id="Contact_No" placeholder="Contact no." type="text" class="form-control" value="<?php echo $data['Contact_No']; ?>" onkeypress="return checkNumber(this.value,event)" required="" maxlength="10" ></div>
                                                    </div>
                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Alt No.</label>
                                                        <input name="Alt_No" id="Alt_No" value="<?php echo $data['Alt_No']; ?>" placeholder="Alternate No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)"  maxlength="12" ></div>
                                                </div>
                                                    
                                                    
                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">State <span style="color: #f00;">*</span></label>

                                                        <select name="state" id="state" data-placeholder="" class="form-control" onclick="get_pincode(this.value)"  required="">
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

                                                        <select onchange="get_area(this.value)" name="pincode" id="pincode" data-placeholder="" class="form-control chzn-select chzn-rtl multiselect" tabindex="9" required="">

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

                                                        <select name="place" id="place" class="form-control" >
                                                            <option value="">Select</option>
                                                            <?php   foreach($area_master as $pin_id=>$place)
                                                                    {
                                                                        echo '<option value="'.$pin_id.'" ';
                                                                        if($place==$data['place']) { echo 'selected';}
                                                                        echo '>'.$place.'</option>';
                                                                    }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                    
                                                    
                                                    <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Email</label>
                                                        <input name="email" id="email" value="<?php echo $data['email']; ?>" placeholder="Email" type="text" class="form-control"   ></div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Gst No.</label>
                                                        <input name="Gst_No" id="Gst_No" value="<?php echo $data['Gst_No']; ?>" placeholder="GST No." type="text" class="form-control"   ></div>
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
                                                            <option value="Home Service" <?php if($data['service_type']=='Home Service') { echo "selected";} ?>>Home Service</option>
                                                            <option value="Walk in Service" <?php if($data['service_type']=='Walk in Service') { echo "selected";} ?>>Walk in Service</option>
                                                            <option value="Refurbrish" <?php if($data['service_type']=='Refurbrish') { echo "selected";} ?>>Refurbrish</option>
                                                            <option value="D&I" <?php if($data['service_type']=='D&I') { echo "selected";} ?>>D&I</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Type <span style="color: #f00;">*</span></label>
                                                        <select id="warranty_type" name="warranty_type" class="form-control" required="">
                                                            <option value="">Select</option>
                                                            <option value="Standard Warranty" <?php if($data['warranty_type']=='Standard Warranty') { echo "selected";} ?>>Standard Warranty</option>
                                                            <option value="Out of warranty" <?php if($data['warranty_type']=='Out of warranty') { echo "selected";} ?>>Out of warranty</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Category <span style="color: #f00;">*</span></label>
                                                        <select id="warranty_category" name="warranty_category" class="form-control" required="">
                                                            <option value="">Select</option>
                                                            <option value="Standard warranty" <?php if($data['warranty_category']=='Standard Warranty') { echo "selected";} ?>>Standard Warranty</option>
                                                            <option value="Out Warranty" <?php if($data['warranty_category']=='Out Warranty') { echo "selected";} ?>>Out Warranty</option>
                                                            <option value="Extended" <?php if($data['warranty_category']=='Extended') { echo "selected";} ?>>Extended</option>
                                                            <option value="International" <?php if($data['warranty_category']=='International') { echo "selected";} ?>>International</option>
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
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Product Detail<span style="color: #f00;">*</span></label>
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
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Product<span style="color: #f00;">*</span></label>
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
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Model<span style="color: #f00;">*</span></label>
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
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Manufacturer serial number<span style="color: #f00;">*</span></label>
						    <input name="man_ser_no" id="man_ser_no" value="<?php echo $data['man_ser_no']; ?>" placeholder="Man. Serial No" type="text" class="form-control" required></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer Name<span style="color: #f00;">*</span></label>
                                                        <input name="dealer_name" id="dealer_name" value="<?php echo $data['dealer_name']; ?>" placeholder="Dealer Name" type="text" class="form-control"  required></div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Purchase Date<span style="color: #f00;">*</span></label>
						    <input name="Bill_Purchase_Date" id="Bill_Purchase_Date" placeholder="Bill Purchase Date" type="text" class="form-control datepicker" value="<?php echo $data['Bill_Purchase_Date']; ?>" required></div>
                                                </div>
                                                
                                                
                                                
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty card <span style="color: #f00;">*</span></label>
                                                        <select id="warranty_card" name="warranty_card" class="form-control" onchange="get_estmt_charge();" required="">
                                                            <option value="">Select</option>
                                                            <option value="Yes" <?php if($data['warranty_card']=='Yes') {echo 'selected';} ?>>Yes</option>
                                                            <option value="No" <?php if($data['warranty_card']=='No') {echo 'selected';} ?>>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warrany Card/Invoice Copy <span style="color: #f00;">*</span></label>
                                                        <input name="warranty_card_copy" id="warranty_card_copy" type="file" class="form-control">
                                                <?php if($data['entry_type']=='walking') { ?>
                                                <?php
                                                            if(!empty($data['warranty_card_copy']))
                                                            {
                                                    ?>
                                                    <img src="http://14.97.29.227/supreme/storage/app/supreme/<?php echo $data['TagId']."/".$data['warranty_card_copy']; ?>" width="100px" height="100px"/>
                                                            <?php } ?>
                                                <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice <span style="color: #f00;">*</span></label>
                                                        <select id="invoice" name="invoice" class="form-control" onchange="get_estmt_charge();" required="">
                                                            <option value="">Select</option>
                                                            <option value="Yes" <?php if($data['invoice']=='Yes') {echo 'selected';} ?>>Yes</option>
                                                            <option value="No" <?php if($data['invoice']=='No') {echo 'selected';} ?>>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice No.</label>
                                                        <input name="invoice_no" id="invoice_no" value="<?php echo $data['invoice_no']; ?>" placeholder="Invoice No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" maxlength="10" ></div>
                                                </div>
                                                 <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Reported fault</label>
						    <input name="report_fault" id="report_fault" placeholder="Report Fault" type="text" value="<?php echo $data['report_fault']; ?>" class="form-control" ></div>
                                                </div>
                                                
						
						
                                                
						
                                            </div>
                                                    <div class="form-row">

                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab('', 'Customer_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab1('Product_Details', 'Set_Conditions');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>

                                                </div>
                                                </div>    
                                        
                                                <div id="Set_Conditions" class="tabFormcontent">
                                                    <h5 class="card-title">Set Conditions</h5>
                                            <div class="form-row">
                                            <?php 
                                                    $set_con_json = stripslashes($data['set_conditions']);
                                                    $set_con_value_master = json_decode($set_con_json,true);
                                                    $index = 1;
                                                    //print_r($set_con_master); exit;
                                                    
                                            foreach($set_con_master as $field_name=>$sub_field_name) { ?>
                                            
                                            
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><?php echo $field_name; ?> <span style="color: #f00;">*</span></label>
                                                        <select id="set_conditions<?php echo $index++; ?>" name="set_conditions[<?php echo $field_name; ?>]" class="form-control" required="">
                                                            <option value="">Select</option>
                                                            <?php 
                                                                foreach($sub_field_name as $sub)
                                                                {
                                                                    echo '<option value="'.$sub.'" ';
                                                                    if($set_con_value_master[$field_name]==$sub) { echo "selected";}
                                                                    echo '>'.$sub.'</option>';
                                                                }
                                                        ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            
                                            <?php } ?>
                                            </div>
                                            <h5 class="card-title">Accessory List</h5>
                                            <div class="form-row">
                                            <?php 
                                            $accesories_list_json = stripslashes($data['accesories_list']);
                                                    $accesories_list_value_master = json_decode($accesories_list_json,true);
                                            
                                            foreach($acc_master as $field_name=>$sub_field_name) { ?>
                                            
                                            
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><?php echo $sub_field_name; ?> <span style="color: #f00;">*</span></label>
                                                        <select id="accesories_list<?php echo $index++; ?>" name="accesories_list[<?php echo $sub_field_name; ?>]" class="form-control" required="">
                                                            <option value="">Select</option>
                                                            <option value="Yes" <?php if($accesories_list_value_master[$sub_field_name]=='Yes') { echo 'selected';} ?> >Yes</option>
                                                            <option value="No" <?php if($accesories_list_value_master[$sub_field_name]=='No') { echo 'selected';} ?>>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><?php echo $sub_field_name; ?> Status <span style="color: #f00;">*</span></label>
                                                        <select id="accesories_list_status<?php echo $index++; ?>" name="accesories_list[<?php echo $sub_field_name; ?> Status]" class="form-control" required="">
                                                            <option value="">Select</option>
                                                            <option value="Available">Available</option>
                                                            <option value="Not available">Not Available</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                            
                                            <?php } ?>
                                            </div>
                                            
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('', 'Product_Details');" class="mt-2 btn btn-danger"  style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('Set_Conditions', 'Part_Required');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                        </div>
                                        <div id="Part_Required" class="tabFormcontent">
                                        
                                            <div id="part_arr">
                                            <div class="form-row">
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Spare Part </label>
                                                                <select id="part_name1" name="SparePart[part_name][]"  class="form-control" onchange="get_partno('1',this.value)" >
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                            foreach($part_arr as $part)
                                                                            {
                                                                                ?>       <option value="<?php echo $part->part_name; ?>"><?php echo $part->part_name; ?></option>     
                                                                    <?php   }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Part No. </label>
                                                                <select id="part_no1" name="SparePart[part_no][]" class="form-control" onchange="get_hsn_code('1',this.value)" >
                                                                    <option value="">Select</option>
                                                                    
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">HSN Code </label>
                                                                <select id="hsn_code1" name="SparePart[hsn_code][]" class="form-control" >
                                                                    <option value="">Select</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><br/><br/>
                                                                <span class="fa fa-plus" style="width:80px;" onclick="add_part();"></span>
                                                            </div>
                                                        </div>
                                                
                                            </div>
                                            </div>       
                                                
                                                <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('', 'Set_Conditions');" class="mt-2 btn btn-danger"  style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('', 'Estimated_Cost');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                            </div>
                                                
                                                
                                               
                                            
                                        
                                        </div>
                                                <div id="Estimated_Cost" class="tabFormcontent">
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Additional (Special) Comment</label>
						    
                                                        <textarea name="add_cmnt" id="add_cmnt" placeholder="Additional Comment" type="text" class="form-control" ><?php echo $data['add_cmnt']; ?></textarea>
                                                    </div>
                                                    
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Estimated Charge.</label>
						    
                                                        <input name="estmt_charge" id="estmt_charge" placeholder="Estimated Charge" type="number" value="<?php echo $data['estmt_charge']; ?>"
                                                               class="form-control" onKeyPress="return checkNumber(this.value,event);" 
                                                                   >    
                                                    </div>
                                                    
                                                </div>
                                                
                                                

                                                
                                                
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Status</label>
                                                        <select name="observation" id="observation" class="form-control" >
                                                            <option value="">Select</option>
                                                            <option value="Part Required">Part Required</option>
                                                            <option value="Part not required">Part not required</option>
                                                            <option value="Out of warranty">Out of warranty</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                    
                                                  
                                                    
                                            
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('', 'Part_Required');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                                <?php if($data['job_accept']=='1') { ?>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                       
                                                        <button type="submit"  class="mt-2 btn btn-primary" style="float:right;">Save</button>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            
                                            
                                        </div>    
                                        </div>                                      
                                        <input type="hidden" name="tag_type" value="<?php echo $tag_type;?>" />
                                        <input type="hidden" name="TagId" value="<?php echo $data['TagId'];?>" />
                                      
                                        <div class="app-wrapper-footer">
                                            <div class="app-footer"></div>   
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
 
 function get_spare_part(div_id,model_id)
 {
    var brand_id = $('#Brand'+div_id).val();
    var product_category_id = $('#Product_Detail'+div_id).val();
    var product_id = $('#Product'+div_id).val();
    
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-name',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id
              },
              success: function(result){
                  $('#part_name1'+div_id).html(result);
              }});
 }
    
    function get_estmt_charge()
 {
     var warranty = "";
     var invoice="";
     
     warranty = $("#warranty_card").val();
     invoice = $("#invoice").val();
     
     if(warranty=='No' && invoice=='No')
     {
         document.getElementById('estmt_charge').disabled=false;
     }
     else
     {
         document.getElementById('estmt_charge').disabled=true;
     }
     
 }
    
    function get_partno(div_id,part_name)
 {

   var brand_id = $('#Brand').val();
    var product_category_id = $('#Product_Detail').val();
    var product_id = $('#Product').val();
    var model_id = $('#Model').val();

     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-no',
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

function get_hsn_code(div_id,part_no)
 {


var brand_id = $('#Brand').val();
    var product_category_id = $('#Product_Detail').val();
    var product_id = $('#Product').val();
    var model_id = $('#Model').val();
    var part_name = $('#part_name'+div_id).val();

     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-hsn-code',
              method: 'post',
              data: {
		 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id,
                 part_name:part_name,
                 part_no: part_no 
              },
              success: function(result){
                  $('#hsn_code'+div_id).html(result);
              }});
 }
    
 function add_part()
 {
     
     var brand_id = $('#Brand').val();
     var product_category_id = $('#Product_Detail').val();
     var product_id = $('#Product').val();
     var model_id = $('#Model').val();
     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-add-part',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id
              },
              success: function(result){
                  $('#part_arr').append(result);
              }});
 }
 
 function del_part(del_div)
 {
     $('#'+del_div).remove();
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
