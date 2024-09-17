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
</script>
<div class="app-main"> 
 <div class="app-main__outer">
                    <div class="app-main__inner">
                         <div class="tab-content">
                            <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                                <div class="main-card mb-3 card">
                                    <form method="post" action="save-tagging">
                                    <div class="card-body"><h5 class="card-title">Customer Details</h5>
                                        
                                            <div class="form-row">
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
							<label for="exampleSelect" class="">Customer Category<span style="color: #f00;">*</span></label>
							<select name="Customer_Category" id="Customer_Category" class="form-control" required>
                                                        <option value="Customer">Customer</option>
                                                        <option value="Dealership">Dealership</option>
                                                        </select>
                                                    </div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <label for="examplePassword11" class="">Pincode<span style="color: #f00;">*</span></label>
						    
                                                    <select id="Pincode" name="Pincode" class="form-control" required="">
                                                        <option value="">Select</option>
                                                        @foreach($pin_master as $pin)
                                                            <option value="{{$pin}}" >{{$pin}}</option>
                                                        @endforeach
                                                    </select>
                                                    </div>
                                                    
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Name<span style="color: #f00;">*</span></label>
						    <input name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" class="form-control" required></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Mobile No.<span style="color: #f00;">*</span></label>
                                                        <input name="Contact_No" id="Contact_No" placeholder="Contact no." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required="" maxlength="10" ></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Communication Address<span style="color: #f00;">*</span></label>
						    <input name="Customer_Address" id="Customer_Address" placeholder="Customer Address" type="text" class="form-control" required></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Brand<span style="color: #f00;">*</span></label>
                                                        <select name="Brand" id="Brand" class="form-control" onclick="" required>
                                                        <option value="LED TV">AIWA</option>
                                                    </select>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Product<span style="color: #f00;">*</span></label>
						    <select name="Product" id="Product" class="form-control" required>
                                                        <option value="">Select</option>
                                                        @foreach($product_master as $product_name)
                                                    <option value="{{$product_name}}" >{{$product_name}}</option>
                                                @endforeach
                                                    </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Model<span style="color: #f00;">*</span></label>
						    <select name="Model" id="Model" class="form-control" required>
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
                                                    </select>
                                                    </div>	 
						</div>
						
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Serial Number<span style="color: #f00;">*</span></label>
						    <input name="Serial_No" id="Serial_No" placeholder="Serial No" type="text" class="form-control" required></div>
                                                </div>
                                                
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Date of Purchase<span style="color: #f00;">*</span></label>
						    <input name="Bill_Purchase_Date" id="Bill_Purchase_Date" placeholder="Bill Purchase Date" type="text" class="form-control datepicker" required></div>
                                                </div>
                                                
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">ASC Code<span style="color: #f00;">*</span></label>
						    <input name="asc_code" id="asc_code" placeholder="ASC Code" type="text" class="form-control datepicker" required></div>
                                                </div>
                                                
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty card</label>
                                                        <select id="warranty_card" name="warranty_card" class="" required="">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice</label>
                                                        <select id="warranty_card" name="warranty_card" class="" required="">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                 <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Reported fault</label>
						    <input name="report_fault" id="report_fault" placeholder="Report Fault" type="text" class="form-control" ></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Required<span style="color: #f00;">*</span></label>
						    
                                                    <input name="service_required" id="service_required" placeholder="Service Required" type="text" class="form-control" ></div>
                                                    
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Symptoms Code<span style="color: #f00;">*</span></label>
						    
                                                    
                                                    </div>
                                                </div>
						
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Conditions</label>
						    <input name="email" id="email" placeholder="Email" type="text" class="form-control"></div>	
						</div>
                                                
                                                
                                                
                                                
                                            </div> 
                                        
                                            
                                        
                                            

                                            

                                                                          
                                        <input type="hidden" name="tag_type" value="<?php echo $tag_type;?>" />
                                        </form>   
                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            <div class="app-footer__inner">
                                <div class="app-footer-left">
                                    <ul class="nav">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 1
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 2
                                            </a>
                                        </li>
                                    </ul>
                                </div> 
                                <div class="app-footer-right">
                                    <ul class="nav">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 3
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                <div class="badge badge-success mr-1 ml-0">
                                                    <small>NEW</small>
                                                </div>
                                                Footer Link 4
                                            </a>
                                        </li>
                                    </ul>
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
