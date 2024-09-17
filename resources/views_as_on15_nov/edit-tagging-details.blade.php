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
                                    <form method="post" action="update-tagging-data">
                                    <div class="card-body"><h5 class="card-title">Customer Details</h5>
                                        
                                            <div class="form-row">
                                                
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Name<span style="color: #f00;">*</span></label>
                                                        <input name="Customer_Name" id="Customer_Name" placeholder="Customer Name" type="text" value="<?php echo $data['Customer_Name']; ?>" class="form-control" required></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Mobile No.<span style="color: #f00;">*</span></label>
                                                        <input name="Contact_No" id="Contact_No" placeholder="Contact no." type="text" class="form-control" value="<?php echo $data['Contact_No']; ?>" onkeypress="return checkNumber(this.value,event)" required="" maxlength="10" ></div>
                                                </div>
						<div class="col-md-4">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Communication Address<span style="color: #f00;">*</span></label>
						    <input name="Customer_Address" id="Customer_Address" placeholder="Communication Address" type="text" value="<?php echo $data['Customer_Address']; ?>" class="form-control" required></div>	
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

                                                    <select name="pincode" id="pincode" data-placeholder="" class="form-control chzn-select chzn-rtl multiselect" tabindex="9" required="">
                                                        
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
                                                
                                                
                                                
                                                
                                                
                                                
                                            </div> 
                                        
                                        <div class="form-row">
                                
                                
                                     <div class="col-md-6">
                                <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Update" >
                                &nbsp;<a href="home" class="btn btn-danger btn-grad" data-original-title="" title="">Back</a>
                            </div>
                            </div> 

  
                            </div>
                                        <input type="hidden" name="TagId" value="<?php echo $TagId;?>" />
                                    </form>
                                </div>            
                            </div>
                         </div>
                    </div>
 </div>
 </div>    
@endsection
