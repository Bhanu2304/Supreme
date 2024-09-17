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

function openTab2(evt, tabName) 
{

    var flag_valid = false;

    //if(tabName==='Estimated_Cost')
    //console.log(tabName);
    if(tabName==='Dealer')
    {
        
        flag_valid = true;
    }
    else if(tabName==='Vehicle_Detail')
    {
        flag_valid = validate_dealer_details();
        //alert(flag_valid);
        if(flag_valid===true && tabName!=='Vehicle_Detail')
        {
            flag_valid = validate_vehicle_details();
        }
    }
    else
    {
        flag_valid = validate_vehicle_details();
        
        //alert(flag_valid);
        if(flag_valid===true && tabName!=='Complaint_Details')
        {
            flag_valid = validate_complaint_details();
        }
    }


    if(flag_valid===true)
    {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabFormcontentCL");
        for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tabFormlinksCL");
        for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }
}

function openTab3(div_name, tabName) {
    var flag_valid = false;
    //console.log(div_name);
    if(div_name==='Dealer')
    {
         flag_valid = validate_dealer_details();
    }
    if(div_name==='Vehicle_Detail')
    {
         flag_valid = validate_vehicle_details();
    }
    if(div_name==='Complaint_Details')
    {
         flag_valid = validate_complaint_details();
    }
    if(flag_valid===true)
    {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabFormcontentCL");
        for (i = 0; i < tabcontent.length; i++) {
          tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tabFormlinksCL");
        for (i = 0; i < tablinks.length; i++) {
          tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName+'1').className += " active";
    }

}
function validate_dealer_details()
{
    
    return true;
}

function validate_vehicle_details()
{
    
    
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

    function get_model_cl(div_id,product_id)
    {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
          });
        jQuery.ajax({
              url: 'get-cl-model-by-product-id',
              method: 'post',
              data: {
                 product_id:product_id
              },
              success: function(result){
                  $('#Model'+div_id).html(result);
              }});
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
                        <form >
                        <div class="card-body">
                            
                            <div class="tabForm">
                                <button type="button" id="Dealer1" class="tabFormlinksCL" onclick="openTab2(event, 'Dealer')" >Dealer/Customer Details</button>
                                <button type="button" id="Vehicle_Detail1" class="tabFormlinksCL" onclick="openTab2(event, 'Vehicle_Detail')">Vehicle Details</button>
                                <button type="button" id="Complaint_Details1" class="tabFormlinksCL" onclick="openTab2(event, 'Complaint_Details')">Complaint Details</button>
                                
                            </div>

                            <div id="Dealer" class="tabFormcontentCL" style="display:block;">
                                <h5 class="card-title">Dealer/Customer Details</h5>

                                <div class="form-row">

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer Name<span style="color: #f00;">*</span></label>
                                            <input type="text" form="save_tagging_cl" name="DealerName" value="<?php echo $data['dealer_name']; ?>" placeholder="Dealer Name" id="DealerNameCL" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Location<span style="color: #f00;">*</span></label>
                                        <input form="save_tagging_cl" name="location" id="LocationCL" placeholder="Location" value="<?php echo $data['Landmark']; ?>" type="text" class="form-control" required></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Region<span style="color: #f00;">*</span></label>
                                        <select form="save_tagging_cl" name="region_id" id="RegionCL"  class="form-control" required>
                                            <?php   foreach($reg_master as $reg_id=>$reg)
                                                    {
                                                        echo '<option value="'.$reg_id.'" ';
                                                        if($data['region_id']==$reg_id) { echo "selected";} 
                                                        echo '>'.$reg.'</option>';
                                                    }
                                                ?>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">State <span style="color: #f00;">*</span></label>
                                        <select form="save_tagging_cl" name="state" id="state_cl" data-placeholder="" class="form-control" onchange="get_pincode('pincode_cl',this.value)"  required="">
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
                                            <select form="save_tagging_cl" onchange="get_area(this.value)" name="pincode" id="pincode_cl" data-placeholder="" class="form-control" required="">
                                                <option value="">Select</option>
                                                <option value="<?php //echo $data['Pincode']; ?>"><?php //echo $data['Pincode']; ?></option>
                                                <?php   foreach($pin_master as $pin_id=>$pincode)
                                                        {
                                                            echo '<option value="'.$pincode.'" ';
                                                            if($data['Pincode']==$pincode) { echo "selected";} 
                                                            echo '>'.$pincode.'</option>';
                                                        }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Contact Person<span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="Customer_Name" id="Customer_Name_CL" value="<?php echo $data['Customer_Name']; ?>" placeholder="Contact Person" type="text" class="form-control" required></div>
                                    </div>
                                        <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Contact No.<span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="Contact_No" id="Contact_No_CL" placeholder="Contact No." value="<?php echo $data['Contact_No']; ?>"  type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required=""  maxlength="10">
                                            <span class="error" id="errornameccl" style="color:red"></span>
                                        </div>
                                        </div>



                                    <div class="col-md-4">

                                        <div class="position-relative form-group">
                                            <br/>
                                            <button  type="button" onclick="openTab3('Dealer', 'Vehicle_Detail');" class="mt-2 btn btn-success">Next</button>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div id="Vehicle_Detail" class="tabFormcontentCL" style="display: none;">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Vehicle Sale Date<span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="vehicle_sale_date" id="vehicle_sale_date" value="<?php echo $data['vehicle_sale_date']; ?>"  maxlength="10" placeholder="Vehicle Sale Date" type="text" class="form-control datepicker" required>
                                            <span class="error" id="errornamercl" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">VIN No.<span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="vin_no" id="vin_no" value="<?php echo $data['vin_no']; ?>"  maxlength="50" placeholder="VIN No." type="text" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Mielage Km. / PDI <span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="mielage" id="mielage" value="<?php echo $data['mielage']; ?>"  maxlength="6" placeholder="Mielage Km. / PDI" onkeypress="return checkNumber(this.value,event)" type="text" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Status <span style="color: #f00;">*</span></label>
                                            <select form="save_tagging_cl" name="warranty_type" class="form-control" required="" id="warranty_type_cl">
                                            <option value="">Select</option>
                                                <option value="IN" <?php if($data['warranty_type']=='IN') { echo "selected";} ?>>IN</option>
                                                <option value="OUT" <?php if($data['warranty_type']=='OUT') { echo "selected";} ?>>OUT</option>
                                            </select>
                                        </div>
                                    </div>

                                <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Vehicle Model<span style="color: #f00;">*</span></label>
                                        <select form="save_tagging_cl" name="Product" id="Product_cl" class="form-control" onchange="get_model_cl('_cl',this.value)" required>
                                            <option value="">Select</option>
                                                <?php  foreach($clarion_product_master as $product_id=>$product_name)
                                                    {
                                                        echo '<option value="'.$product_id.'"';
                                                        if($product_name==$data['Product']) { echo 'selected';}
                                                        echo '>'.$product_name.'</option>';
                                                    }
                                            ?>
                                        </select>
                                        </div>
                                </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">DA2 - Part number<span style="color: #f00;">*</span></label>
                                            <select form="save_tagging_cl" name="Model" id="Model_cl" onchange="get_model_dependents('_cl',this.value)" class="form-control" required>
                                            <option value="">Select</option>
                                            <?php  foreach($model_master as $model_id=>$model_name)
                                                    {
                                                        echo '<option value="'.$model_id.'"';
                                                        if($model_id==$data['model_id']) { echo 'selected';}
                                                        echo '>'.$model_name.'</option>';
                                                    }
                                            ?>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">System SW Version<span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" maxlength="50" name="system_sw_version" id="system_sw_version" value="<?php echo $data['system_sw_version']; ?>" placeholder="System SW Version" type="text" class="form-control" required></div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Manufacturer serial number</label>
                                        <input form="save_tagging_cl" name="man_ser_no" id="man_ser_no" value="<?php echo $data['man_ser_no']; ?>" placeholder="Man. Serial No" type="text" class="form-control" ></div>
                                    </div>
                                </div>
                                <div class="form-row">

                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <button type="button" style="float:left;" onclick="openTab2(event, 'Dealer');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                        </div>
                                    </div>
                                <div class="col-md-6">
                                        <div class="position-relative form-group">

                                            <button type="button" style="float:right;" onclick="openTab3('Vehicle_Detail','Complaint_Details');" class="mt-2 btn btn-success" >Next</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div id="Complaint_Details" class="tabFormcontentCL" style="display: none;">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Complaint <span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="ccsc" id="ccsc_cl" placeholder="Customer Complaint" value="<?php echo $data['ccsc']; ?>" type="text" class="form-control" required="" ></div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">New Job Card<span style="color: #f00;">*</span></label>
                                            <select form="save_tagging_cl" name="job_card" id="job_card" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="Yes" <?php if($data['job_card']=='Yes') { echo "selected";} ?>>Yes</option>
                                                <option value="No" <?php if($data['job_card']=='No') { echo "selected";} ?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Videos<span style="color: #f00;">*</span></label>
                                            <select form="save_tagging_cl" name="videos" id="videos" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="Yes" <?php if($data['videos']=='Yes') { echo "selected";} ?> >Yes</option>
                                                <option value="No" <?php if($data['videos']=='No') { echo "selected";} ?> >No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">CRF<span style="color: #f00;">*</span></label>
                                            <select form="save_tagging_cl" name="crf" id="crf" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="Yes" <?php if($data['crf']=='Yes') { echo "selected";} ?> >Yes</option>
                                                <option value="No" <?php if($data['crf']=='No') { echo "selected";} ?> >No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">FTIR<span style="color: #f00;">*</span></label>
                                            <select form="save_tagging_cl" name="ftir" id="ftir" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="Yes" <?php if($data['ftir']=='Yes') { echo "selected";} ?> >Yes</option>
                                                <option value="No" <?php if($data['ftir']=='No') { echo "selected";} ?> >No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">FTIR No. </label>
                                            <input form="save_tagging_cl" name="ftir_no" id="ftir_no" value="<?php echo $data['ftir_no']; ?>"  type="text" class="form-control" >
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Supreme 1st Analysis <span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="supr_analysis" id="supr_analysis" value="<?php echo $data['supr_analysis']; ?>" type="text" class="form-control" required="">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Remarks </label>
                                            <input form="save_tagging_cl" name="remarks" id="remarks"  value="<?php echo $data['remarks']; ?>" type="text" class="form-control" >
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Type of Issue Suspected <span style="color: #f00;">*</span></label>
                                            <select form="save_tagging_cl" name="issue_type" id="issue_type" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="HW" <?php if($data['issue_type']=='HW') { echo "selected";} ?> >HW</option>
                                                <option value="SW" <?php if($data['issue_type']=='SW') { echo "selected";} ?> >SW</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Issue Category <span style="color: #f00;">*</span></label>
                                            <input form="save_tagging_cl" name="issue_cat" id="issue_cat" value="<?php echo $data['issue_cat']; ?>"  type="text" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Mobile Handset Model </label>
                                            <input form="save_tagging_cl" name="mobile_handset_model" value="<?php echo $data['mobile_handset_model']; ?>" id="mobile_handset_model"  type="text" class="form-control" >
                                        </div>
                                    </div>
                                    

                                </div>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <button type="button" style="float:left;" onclick="openTab2(event, 'Vehicle_Detail');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <!-- <button type="button" style="float:right;" onclick="openTab3('Complaint_Details','UploadDocuments');" class="mt-2 btn btn-success" >Exit</button> -->
                                            <a href="javascript:void(0);" onclick="goBack()" class="mt-2 btn btn-danger btn-grad" style="float:right;" data-original-title="" title="">Exit</a>
                                        </div>
                                    </div>
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


    function goBack() {
        window.history.back();
    }
    
    function get_pincode(field_id,state_name)
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
                    $('#'+field_id).html(result)
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
