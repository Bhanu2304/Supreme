@extends('layouts.app')

@section('content') 
<script>
                                    

menu_select('{{$url}}');                                                              
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
							<label for="exampleSelect" class="">Customer Category</label>
							<select name="Customer_Category" id="Customer_Category" class="form-control">
                                                        <option value="Customer">Customer</option>
                                                        <option value="Dealership">Dealership</option>
                                                    </select></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Pincode</label>
						    <input name="Pincode" id="Pincode" placeholder="Pincode" type="text" class="form-control"></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Name</label>
						    <input name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" class="form-control"></div>
                                                </div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Address</label>
						    <input name="Customer_Address" id="Customer_Address" placeholder="Customer Address" type="text" class="form-control"></div>	
						</div>
                                                
                                                <div class="col-md-4"> 
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Landmark</label>
						    <input name="Customer_Address_Landmark" id="Customer_Address_Landmark" placeholder="Ladmark" type="text" class="form-control"></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Contact no.</label>
						    <input name="Contact_No" id="Contact_No" placeholder="Contact no." type="text" class="form-control"></div>
                                                </div>

						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Alternate Contact number</label>
						    <input name="Alternate_Contact_No" id="Alternate_Contact_No" placeholder="Alternate Contact number" type="text" class="form-control"></div>	
						</div>
                                                 
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">State</label>
						    <input name="State" id="State" placeholder="State" type="text" class="form-control"></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">City</label>
						    <input name="City" id="City" placeholder="City" type="text" class="form-control"></div>
                                                </div>
						
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Email</label>
						    <input name="email" id="email" placeholder="Email" type="text" class="form-control"></div>	
						</div>
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Residence Number</label>
						    <input name="Residence_No" id="Residence_No" placeholder="Residence Number" type="text" class="form-control"></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">GST no.</label>
						    <input name="Gst_No" id="Gst_No" placeholder="GST no." type="text" class="form-control"></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Registration Name</label>
						    <input name="Registration_Name" id="Registration_Name" placeholder="Registration Name" type="text" class="form-control"></div>	
						</div>
                                                
                                                
                                            </div> 

 
</div>

@endsection
