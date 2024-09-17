@extends('layouts.app')

@section('content') 

<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<script>
 

function openTab(evt, tabName) {
    
    var flag_valid = true;
    
    
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






function get_po_parts()
 {
     var TagId = $('#TagId').val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'se-get-raise-po',
              method: 'post',
              data: {
                 TagId:TagId
              },
              success: function(result){
                  $('#po_part_arr').html(result);
              }});
 }

function openTab1(div_name, tabName) 
{
    
    var flag_valid = true;
    
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

<div class="app-main"> 
 <div class="app-main__outer">
                    <div class="app-main__inner">
                         <div class="tab-content">
                            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                                <div class="main-card mb-3 card">
                                    
                                        <div class="card-body">
                                        
                                        <div class="tabForm">
                                            <button type="button" id="Customer_Details1" class="tabFormlinks active" onclick="openTab(event, 'Customer_Details')" >Customer Details</button>
                                            <button id="Product_Details1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Product_Details')">Product Details</button>
                                            
                                                <button id="Reschedule1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Reschedule')">Reschedule & Calling Remarks</button>
                                                <button id="file_upload1" type="button"  class="tabFormlinks" onclick="openTab(event, 'file_upload')">File Upload</button>
                                                <button id="Estimated_Cost1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Estimated_Cost')">Create Estimation</button>

                                                <button id="Part_Required1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Part_Required')">Order Part</button>
                                                <button id="PO_Raise1" type="button"  class="tabFormlinks" onclick="get_po_parts();openTab(event, 'PO_Raise')">PO Raise</button>
                                                <button id="sp1" type="button"  class="tabFormlinks" onclick="openTab(event, 'sp')">Special Approvals</button>

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
                                                        <div class="position-relative form-group">
                                                            <label for="examplePassword11" class="">Customer Name<span style="color: #f00;">*</span></label>
                                                            <input name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" value="<?php echo $data['Customer_Name']; ?>" class="form-control" required>
                                                    </div>
                                                    </div>
                                                   
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <label for="examplePassword11" class="">Communication Address<span style="color: #f00;">*</span></label>
                                                        <input name="Customer_Address" id="Customer_Address" placeholder="Communication Address" type="text" value="<?php echo $data['Customer_Address']; ?>" class="form-control" required>
                                                    </div>
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
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Alternate No.</label>
                                                        <input name="Alt_No" id="Alt_No" value="<?php echo $data['Alt_No']; ?>" placeholder="Alternate No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)"  maxlength="12" >
                                                </div>
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
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer GST No.</label>
                                                        <input name="Gst_No" id="Gst_No" value="<?php echo $data['Gst_No']; ?>" placeholder="Customer GST No." type="text" class="form-control"   ></div>
                                                </div>


                                                    <div class="col-md-4">

                                                        <div class="position-relative form-group">
                                                            <br/>
                                                            <button type="button" onclick="openTab1('Customer_Details', 'Product_Details');" class="mt-2 btn btn-success">Next</button>
                                                            &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
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
                                                        <select name="Brand" id="Brand" class="form-control"  required>
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
						    <select name="Product_Detail" id="Product_Detail" class="form-control"  required>
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
						    <select name="Product" id="Product" class="form-control"  required>
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
                                                        <select name="Model" id="Model" class="form-control"  required>
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
                                                        <select id="warranty_card" name="warranty_card" class="form-control"  required="">
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
                                                        &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('Product_Details', 'Reschedule');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>

                                                </div>
                                        </div>    
                                        
                                            <div id="Reschedule" class="tabFormcontent">
                                                <?php $history_json = $data['se_sdl_history'];
                                                       if(!empty($history_json)) {
                                                ?>
                                                <table class="table" id="rsch">
                                                    <tr>
                                                        <th>Visit Date</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                
                                                                    <?php
                                                            $history_arr = json_decode($history_json,true);
                                                            $last_date = '';
                                                            foreach($history_arr as $his)
                                                                            {
                                                                echo '<tr>';
                                                                    echo '<td>'.$his['job_date'].'</td>';
                                                                    echo '<td>'.$his['se_sdl_remarks'].'</td>';
                                                                    $last_date = $his['job_date'];
                                                                echo '</tr>';
                                                            }
                                                       } ?>
                                                
                                                </table>
                                                <?php
                                                                    
                                                 
                                                        
                                                ?>
                                                
                                                <div class="form-row">
                                                        
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Product_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('Reschedule', 'file_upload');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                                
                                        </div>
						    
                                                    </div>
                                                    
                                            <div id="file_upload" class="tabFormcontent">
                                                          
                                                
                                                <div id="wrrn_cntr" style="<?php if(empty($data['warranty_card_copy'])) { ?>display:none <?php } ?>" class="float-container">
                                                    <div  class="float-child" > 
                                                        <div class="float-container">
                                                            <div class="float-child"> 
                                                                <br>
                                                                <br>
                                                                <br>
                                                                    <div class="form-row">
                                                                        <div class="col-md-12">
                                                                            <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Warranty card</b></label>
                                                                            </div>
                                                                        </div>
                                                                    </div>  
                                                            </div>
                                                            <div class="float-child" style="height:250px;" id="wrrn_img_disp">
                                                                <img src="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$data['warranty_card_copy']; ?>" width="100px" height="100px"/>
                                                            </div> 
                                                        </div>    
                                                    </div>
                                                    <div class="float-child" style="width:500px;"></div>
                                                </div>
                                                        
                                                <div id="prcs_cntr" style="<?php if(empty($data['purchase_copy'])) { ?>display:none <?php } ?>" class="float-container">

                                                    <div class="float-child" > 
                                                        <div class="float-container">
                                                    <div class="float-child"> 
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Purchase Invoice</b></label>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>  
                                                        
                                                    

                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="prcs_img_disp">
                                                        <img src="<?php echo "$str_server/storage/app/supreme/".$data['TagId']."/".$data['purchase_copy']; ?>" width="100px" height="100px"/>
                                                    </div> 
                                                </div>
                                                    </div>
                                                    <div class="float-child" style="width:500px;"></div>
                                                </div>
                                                    
                                                <div id="mdl_cntr" style="<?php if(empty($data['model_no_copy'])) { ?>display:none <?php } ?>" class="float-container">

                                                    <div class="float-child" > 
                                                        <div class="float-container">
                                                    <div class="float-child"> 
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Model No. Image</b></label>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>  
                                                        
                                                    

                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="mdl_img_disp">
                                                        <img src="<?php echo $str_server."storage/app/supreme/".$data['TagId']."/".$data['model_no_copy']; ?>" width="100px" height="100px"/>
                                                    </div> 
                                                </div>
                                                    </div>
                                                    <div class="float-child" style="width:500px;"></div>
                                                </div>    
                                                
                                                <div id="srl_cntr" style="<?php if(empty($data['serial_no_copy'])) { ?>display:none <?php } ?>" class="float-container">
                                                    <div class="float-child" > 
                                                        <div class="float-container">
                                                            <div class="float-child"> 
                                                                <br>
                                                                <br>
                                                                <br>
                                                    
                                                            <div class="form-row">
                                                                <div class="col-md-12">
                                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Model & Serial No. Image</b></label>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>  
                                                        
                                                    

                                                    </div>
                                                            <div class="float-child" style="height:250px;" id="srl_img_disp">
                                                                <img src="<?php echo $str_server."storage/app/supreme/".$data['TagId']."/".$data['serial_no_copy']; ?>" width="100px" height="100px"/>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="float-child" style="width:500px;"></div>    
                                                </div>

                                                <div id="smtm1_cntr" style="<?php if(empty($data['symptom_photo1'])) { ?>display:none <?php } ?>" class="float-container">
                                                    <div  class="float-child" >
                                                        <div class="float-container">
                                                    <div class="float-child"> 
                                                        <br>
                                                        <br>
                                                        <br>
                                                    
                                                        <div class="form-row">
                                                        <div class="col-md-12">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Symptom Image</b></label>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>  
                                                        
                                                    

                                                    </div>
                                                    <div class="float-child" style="height:250px;" id="smtm1_img_disp">
                                                        <img src="<?php echo $str_server."storage/app/supreme/".$data['TagId']."/".$data['symptom_photo1']; ?>" width="100px" height="100px"/>
                                                    </div> 
                                                </div>
                                                    </div>
                                                <div class="float-child" style="width:500px;"></div>      
                                            </div>        

                                                <div id="smtm2_cntr" style="<?php if(empty($data['symptom_photo2'])) { ?>display:none <?php } ?>" class="float-container">
                                                    <div  class="float-child" > 
                                                        <div class="float-container">
                                                    <div class="float-child"> 

                                                    <br>
                                                        <br>
                                                        <br>
                                                        <div class="form-row">
                                                            <div class="col-md-12">
                                                                <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Any special Approval</b></label>
                                                                    
                                                                </div>
                                                            </div>
                                                        </div>  
                                                        
                                                    

                                                    </div>
                                                    <div class="float-child" style="height:280px;" id="smtm2_img_disp">
                                                        <img src="<?php echo "{$str_server}storage/app/supreme/".$data['TagId']."/".$data['symptom_photo2']; ?>" width="100px" height="100px"/>
                                                    </div> 
                                                        </div>        
                                                </div>
                                                <div class="float-child" style="width:500px;"></div>      
                                            </div> 

                                                <div id="smtm3_cntr" style="<?php if(empty($data['symptom_photo3'])) { ?>display:none <?php } ?>" class="float-container">
                                                    <div  class="float-child" > 
                                                        <div class="float-container">
                                                    <div class="float-child"> 

                                                        <br>
                                                        <br>
                                                        <br>
                                                        <div class="form-row">
                                                        <div class="col-md-12">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Symptom Image 3</b></label>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                        
                                                    

                                                    </div>
                                                    <div class="float-child" style="height:280px;" id="smtm3_img_disp">
                                                        <img src="<?php echo "{$str_server}storage/app/supreme/".$data['TagId']."/".$data['symptom_photo3']; ?>" width="100px" height="100px"/>
                                                    </div> 
                                                </div>
                                                    </div>
                                                    <div class="float-child" style="width:500px;"></div>     
                                            </div> 

                                                <div class="clear"></div>
                                            <div class="form-row">
                                                    
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Reschedule');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('file_upload', 'Estimated_Cost');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                            </div>

                                            <div id="Estimated_Cost" class="tabFormcontent">
                                                <a href="se-job-view-contact?contact_no=<?php echo $data['Contact_No'];?>" class="mt-2 btn btn-primary" style="float:right;">Check Repair History</a>
                                            <div class="col-md-4">
                                                <div class="position-relative form-group"><label for="examplePassword11" class="">Additional (Special) Comment</label>

                                                    <textarea name="add_cmnt" id="add_cmnt" placeholder="Additional Comment" type="text" class="form-control" ><?php echo $data['add_cmnt']; ?></textarea>
                                                </div>

                                            </div>
                                            <div class="col-md-4">
                                                <div class="position-relative form-group"><label for="examplePassword11" class="">Estimated Charge.</label>

                                                    <input name="estmt_charge" id="estmt_charge" placeholder="Estimated Charge" type="number" value="<?php echo $data['estmt_charge']; ?>"
                                                           class="form-control" onKeyPress="return checkNumber(this.value,event);" >    
                                                </div>

                                            </div>
                                            <div class="form-row">

                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'file_upload');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab1('Estimated_Cost', 'Part_Required');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            </div>  

                                            <div id="Part_Required" class="tabFormcontent">
                                                <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Job Status</label>
                                            <select name="observation" id="observation" class="form-control" onclick="show_part_arr(this.value)">
                                                <option value="">Select</option>
                                                <option value="Open" <?php if($data['observation']=='Open') { echo 'selected';} ?>>Open</option>
                                                <option value="Close" <?php if($data['observation']=='Close') { echo 'selected';} ?>>Close</option>
                                                <option value="Part Pending" <?php if($data['observation']=='Part Pending') { echo 'selected';} ?>>Part Pending</option>
                                            </select>
                                        </div>
                                    </div>
                                            <div id="part_arr"  style="<?php if(empty($tagg_part)) {  ?>display:none;<?php } ?>">
                                            <?php foreach($tagg_part as $tpart) { ?>
                                                <div class="form-row">
                                                        

                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Part Name </label>
                                                                <select id="part_name<?php echo $tpart->part_id;?>" readonly=""  class="form-control"  >
                                                                    <option value="<?php echo $tpart->part_name;?>"><?php echo $tpart->part_name;?></option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-md-3">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Part No. </label>
                                                                <select class="form-control" id="part_no<?php echo $tpart->part_id;?>" readonly="" >
                                                                    <option value="<?php echo $tpart->part_no;?>"><?php echo $tpart->part_no;?></option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">No. of Pending Parts </label>
                                                                <input maxlength="5" class="form-control" readonly="" type="text"  value="<?php echo $tpart->pending_parts;?>" >
                                                                
                                                            </div>
                                                        </div>
                                                    
                                            </div>
                                            <?php } ?>  
                                               
                                            </div>       

                                            <div class="form-row">

                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Estimated_Cost');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="get_po_parts();openTab1('Part_Required', 'PO_Raise');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                    </div>
                                                </div>
                                                       
                                                    </div>
                                                </div>
                                            <div id="PO_Raise" class="tabFormcontent">
                                                
                                                <div id="po_part_arr"></div>
                                                    
                                                
                                                <div class="form-row">

                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Part_Required');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
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
                                                <div class="position-relative form-group"><label for="examplePassword11" class="">Closure Codes<span style="color: #f00;">*</span></label>
                                                    <select id="closure_codes" name="closure_codes" class="form-control"  required="">
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
                                                    <button type="button" onclick="openTab1('', 'PO_Raise');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                    &nbsp; <a class="mt-2 btn btn-primary" href="<?php echo "$back_url?$whereTag";?>">Back</a>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="position-relative form-group">

                                                    
                                                </div>
                                            </div>

                                        </div>
                                            </div>
                                        </div>                                       
                                        
                                      
                                    <div class="app-wrapper-footer">
                                        <div class="app-footer"></div>   
                                    </div>  
                                        <input type="hidden" id="TagId" name="TagId" value="<?php echo $TagId;?>" /> 
                                    
                            </div>
                         </div>
                    </div>
 </div>
 </div>  
</div> 



@endsection
