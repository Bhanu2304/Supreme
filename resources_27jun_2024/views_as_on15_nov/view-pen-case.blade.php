@extends('layouts.app')

@section('content') 

<div class="app-main"> 
 <div class="app-main__outer">
                    <div class="app-main__inner">
                         <div class="tab-content">
                            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                                <div class="main-card mb-3 card">
                                    
                                    <div class="card-body"><h5 class="card-title">Case View</h5>
                                        
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
							<label for="exampleSelect" class="">Customer Category</label>
							<select name="Customer_Category" id="Customer_Category" class="form-control" readonly="">
                                                        <option value="Customer" <?php if($data['Customer_Category']=='Customer') { echo "selected";} ?>>Customer</option>
                                                        <option value="Dealership" <?php if($data['Customer_Category']=='Dealership') { echo "selected";} ?>>Dealership</option>
                                                    </select></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Pincode<span style="color: #f00;">*</span></label>
						    <input name="Pincode" id="Pincode" placeholder="Pincode" type="text" value="<?php echo $data['Pincode']; ?>" class="form-control" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Name<span style="color: #f00;">*</span></label>
						    <input name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" value="<?php echo $data['Customer_Name']; ?>" class="form-control" readonly=""></div>
                                                </div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Address<span style="color: #f00;">*</span></label>
						    <input name="Customer_Address" id="Customer_Address" placeholder="Customer Address" type="text" value="<?php echo $data['Customer_Address']; ?>" class="form-control" readonly=""></div>	
						</div>
                                                
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Landmark</label>
						    <input name="Customer_Address_Landmark" id="Customer_Address_Landmark" placeholder="Landmark" type="text" value="<?php echo $data['Customer_Address_Landmark']; ?>" class="form-control" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Contact no.<span style="color: #f00;">*</span></label>
						    <input name="Contact_No" id="Contact_No" placeholder="Contact no." type="text" value="<?php echo $data['Contact_No']; ?>" class="form-control" readonly=""></div>
                                                </div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Alternate Contact number</label>
						    <input name="Alternate_Contact_No" id="Alternate_Contact_No" placeholder="Alternate Contact number" type="text" value="<?php echo $data['Alternate_Contact_No']; ?>" class="form-control" readonly=""></div>	
						</div>
                                                 
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">State<span style="color: #f00;">*</span></label>
						    <input name="State" id="State" placeholder="State" type="text" class="form-control" value="<?php echo $data['State']; ?>" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">City<span style="color: #f00;">*</span></label>
						    <input name="City" id="City" placeholder="City" type="text" value="<?php echo $data['City']; ?>" class="form-control" readonly=""></div>
                                                </div>
						
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Email</label>
						    <input name="email" id="email" placeholder="Email" type="text" value="<?php echo $data['email']; ?>" class="form-control" readonly=""></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Residence Number</label>
						    <input name="Residence_No" id="Residence_No" placeholder="Residence Number" type="text" value="<?php echo $data['Residence_No']; ?>" class="form-control" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">GST no.</label>
						    <input name="Gst_No" id="Gst_No" placeholder="GST no." type="text" value="<?php echo $data['Gst_No']; ?>" class="form-control" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Registration Name</label>
						    <input name="Registration_Name" id="Registration_Name" placeholder="Registration Name" type="text" value="<?php echo $data['Registration_Name']; ?>" class="form-control" readonly=""></div>	
						</div>
                                            </div>    
                                                
                                            <div class="card-body"><h5 class="card-title">PRODUCT DETAILS</h5>
                                        
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Product<span style="color: #f00;">*</span></label>
						    <select name="Product" id="Product" class="form-control" readonly="">
                                                        <?php 
                                                                foreach($ProductMaster as $product)
                                                                { ?>
                                                                    <option value="<?php echo $product['product_name'];?>" <?php if($data['Product']==$product['product_name']) { echo "selected";} ?>>
                                                                        <?php echo $product['product_name'];?>
                                                                    </option>';
                                                        <?php    
                                                                }
                                                        ?>
                                                        
                                                    </select></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Brand<span style="color: #f00;">*</span></label>
						    <select name="Brand" id="Brand" class="form-control" readonly="">
                                                        <option value="AIWA" <?php if($data['Brand']=='AIWA') { echo "selected";} ?>>AIWA</option>
                                                    </select></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Serial Number<span style="color: #f00;">*</span></label>
						    <input name="Serial_No" id="Serial_No" placeholder="Serial No" type="text" class="form-control" value="<?php echo $data['Serial_No']; ?>" readonly=""></div>
                                                </div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Model<span style="color: #f00;">*</span></label>
						    <select name="Model" id="Model" class="form-control" readonly="">
                                                        <option value="<?php echo $data['Model'];?>"><?php echo $data['Model'];?></option>
                                                        <option value="AW240">AW240</option>
                                                        <option value="AW320">AW320</option>
							<option value="AW320S">AW320S</option>
							<option value="AW320S">AW320S </option>
							<option value="Frameless">Frameless</option>
							<option value="AW400">AW400</option>
							<option value="AW400S">AW400S</option>
							<option value="AW430S">AW430S</option>
							<option value="AW430US">AW430US</option>
							<option value="AW431S">AW431S</option>
							<option value="AW500S">AW500S</option>
							<option value="AW500US">AW500US</option>
							<option value="AW501US">AW501US</option>
							<option value="AW550US">AW550US</option>
							<option value="AW551US">AW551US</option>
							<option value="AW650US">AW650US</option>
							<option value="AW750US">AW750US</option>
                                                    </select></div>	 
						</div>
                                                
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Bill Purchase Date<span style="color: #f00;">*</span></label>
						    <input name="Bill_Purchase_Date" id="Bill_Purchase_Date" value="<?php echo $data['Bill_Purchase_Date'];?>" 
                                                           placeholder="Bill Purchase Date" type="text" class="form-control datepicker" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty End Date</label>
						    <input name="Warrenty_End_Date" id="Warrenty_End_Date" 
                                                           value="<?php echo $data['Warrenty_End_Date'];?>" 
                                                           placeholder="Warrenty End Date" type="text" class="form-control datepicker" readonly=""></div>
                                                </div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Date Of Installation</label>
						    <input name="Date_of_Installation" id="Date_of_Installation" value="<?php echo $data['Date_of_Installation'];?>" 
                                                           placeholder="Date of Installation" type="text" class="form-control datepicker" readonly=""></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Call Type<span style="color: #f00;">*</span></label>
						    <select name="call_type" id="call_type" class="form-control" readonly="">
                                                        <option value="<?php echo $data['call_type'];?>"><?php echo $data['call_type'];?></option>
                                                        <option value="Installation & Demo">Installation & Demo</option>
                                                        <option value="Repair">Repair</option>
							<option value="PDI">PDI</option>
							<option value="Dealer Break down">Dealer Break down</option>

                                                    </select></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">AMC no.</label>
						    <input name="amc_no" id="amc_no" value="<?php echo $data['amc_no'];?>" 
                                                           placeholder="AMC no." type="text" class="form-control" readonly=""></div>
                                                </div>
						
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">AMC Expiry Date</label>
						    <input name="amc_expiry_date" id="amc_expiry_date" value="<?php echo $data['amc_expiry_date'];?>" 
                                                           placeholder="AMC Expiry date" type="text" class="form-control" readonly=""></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty status<span style="color: #f00;">*</span></label>
						    <input name="warranty_status" id="warranty_status" value="<?php echo $data['warranty_status'];?>" 
                                                           placeholder="Warranty status" type="text" class="form-control" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer name</label>
						    <input name="dealer_name" id="dealer_name" value="<?php echo $data['dealer_name'];?>" 
                                                           placeholder="Dealer name" type="text" class="form-control" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice no.</label>
						    <input name="invoice_no" id="invoice_no" value="<?php echo $data['invoice_no'];?>" 
                                                           placeholder="Invoice no." type="text" class="form-control" readonly=""></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type</label>
						    <select name="service_type" id="service_type" class="form-control" readonly="">
                                                        <option value="<?php echo $data['service_type'];?>"><?php echo $data['service_type'];?></option>
                                                        <option value="Carry in">Carry in</option>
                                                        <option value="Onsite">Onsite</option>
                                                    </select></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Entity Name</label>
						    <input name="entity_name" id="entity_name" placeholder="Entity Name" 
                                                           value="<?php echo $data['entity_name'];?>" type="text" class="form-control" readonly=""></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Accessories Required</label>
						    <select name="accessories_required" id="accessories_required" class="form-control" readonly="">
                                                        <option value="<?php echo $data['accessories_required'];?>"><?php echo $data['accessories_required'];?></option>
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select></div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Remarks</label>
						    <input name="product_remark" id="product_remark" 
                                                           value="<?php echo $data['product_remark'];?>" 
                                                           placeholder="Remark" type="text" class="form-control" readonly="">
                                                    </div>
                                                </div>
                                                
                                            </div>  

                                            
                                        
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">VOC<span style="color: #f00;">*</span> </label>
						    <select name="tag_voc" id="tag_voc" class="form-control" readonly="">
                                                        <option value="<?php echo $data['tag_voc'];?>"><?php echo $data['tag_voc'];?></option>
                                                        <option value="Installation">Installation</option>
                                                        <option value="demo">demo</option>
							<option value="DEAD">DEAD</option>
							<option value="Sound problem">Sound problem</option>
							<option value="installation">installation</option>
							<option value="panel display issue">panel display issue</option>
							<option value="remote issue">remote issue</option>
							<option value="power cable issue">power cable issue</option>
							<option value="speaker issue">speaker issue</option>
							<option value="software issue">software issue</option>
							<option value="others">others</option>
                                                    </select></div>
                                                </div>
						
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Remark</label>
						    <input name="remark" id="remark" value="<?php echo $data['remark'];?>" placeholder="Remark" type="text" class="form-control" readonly=""></div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">SE VOC</label>
                                                        <select name="se_voc" id="se_voc" class="form-control">
                                                            <option value="<?php echo $data['se_voc'];?>"><?php echo $data['se_voc'];?></option>
                                                            <option value="Accept">Accept</option>
                                                            <option value="ongoing">ongoing</option>
                                                            <option value="Finished">Finished</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">SE Remark<span style="color: #f00;">*</span></label>
                                                        <input name="se_remark" id="se_remark" placeholder="Remark" type="text" value="<?php echo $data['se_remark'];?>" class="form-control" required="">
                                                    </div>
                                                </div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Device Image</label>
                                                    <?php
                                                            if(!empty($data['device_image']))
                                                            {
                                                    ?>
                                                    <img src="http://103.140.219.38/aiwa/storage/app/aiwa/<?php echo $data['TagId']."/".$data['device_image']; ?>" width="100px" height="100px"/>
                                                            <?php } ?>
                                                    </div>	 
						</div>
                                                
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice copy<span style="color: #f00;">*</span></label>
						    
                                                    <?php
                                                            if(!empty($data['invoice_copy']))
                                                            {
                                                    ?>
                                                    <img src="http://103.140.219.38/aiwa/storage/app/aiwa/<?php echo $data['TagId']."/".$data['invoice_copy']; ?>" width="100px" height="100px"/>
                                                            <?php } ?>
                                                    </div>
                                                </div>
                                                
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Product serial number</label>
						    
                                                    <?php
                                                            if(!empty($data['product_serial_no']))
                                                            {
                                                    ?>
                                                    <img src="http://103.140.219.38/aiwa/storage/app/aiwa/<?php echo $data['TagId']."/".$data['product_serial_no']; ?>" width="100px" height="100px"/>
                                                            <?php } ?>
                                                </div></div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Product photo 1</label>
						    
                                                    <?php
                                                            if(!empty($data['product_photo1']))
                                                            {
                                                    ?>
                                                    <img src="http://103.140.219.38/aiwa/storage/app/aiwa/<?php echo $data['TagId']."/".$data['product_photo1']; ?>" width="100px" height="100px"/>
                                                            <?php } ?>
						</div></div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Product photo 2</label>
						    
                                                    <?php
                                                            if(!empty($data['product_photo2']))
                                                            {
                                                    ?>
                                                    <img src="http://103.140.219.38/aiwa/storage/app/aiwa/<?php echo $data['TagId']."/".$data['product_photo2']; ?>" width="100px" height="100px"/>
                                                            <?php } ?>
                                                    </div>
                                                </div>
                                                                                            
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Observation Entry</label>
                                                        <select name="observation" id="observation" class="form-control" readonly="">
                                                            <option value="<?php echo $data['observation'];?>"><?php echo $data['observation'];?></option>
                                                            <option value="Installation">Installation</option>
                                                            <option value="Repair">Repair</option>
                                                            <option value="PDI">PDI</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Call Status</label>
                                                        <select name="call_status" id="call_status" class="form-control" readonly="">
                                                            <option value="<?php echo $data['call_status'];?>"><?php echo $data['call_status'];?></option>
                                                            <option value="Open">Open</option>
                                                            <option value="Close">Close</option>
                                                            <option value="Cancel">Cancel</option>
                                                            <option value="Part Pending">Part Pending</option>
                                                            <option value="RMA">RMA</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div> 
                                        
                                         

  
                            </div>
                                    </div>
                                    
                            </div>
                         </div>
                    </div>
 </div>
 </div> 
</div>    
@endsection
