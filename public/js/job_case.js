
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
        if(flag_valid===true && tabName!=='Product_Details')
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
    var phoneno = /^[1-9]{1}[0-9]+/;
    var matchpt = document.getElementById('Contact_No').value.match(phoneno);
    var matchalt = document.getElementById('Alt_No').value.match(phoneno);
    var matchrec = document.getElementById('call_rcv_frm').value.match(phoneno);


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
    else if(!matchpt){
        
          $('#errornamec').fadeIn().text("Please Fill Correct Number");
				setTimeout(function() {
					$('#errornamec').fadeOut("slow");
				}, 5000 );

        return false;
    }
    else if(!matchalt && document.getElementById('Alt_No').value!==''){
        $('#errornamea').fadeIn().text("Please Fill Correct Alt Number.");
				setTimeout(function() {
					$('#errornamea').fadeOut("slow");
				}, 5000 );

        return false;
    }
    else if(!matchrec &&  document.getElementById('call_rcv_frm').value != 'Automatic' && document.getElementById('call_rcv_frm').value != 'automatic'){
        
          $('#errornamer').fadeIn().text("Please Fill Correct Call Receive From");
				setTimeout(function() {
					$('#errornamer').fadeOut("slow");
				}, 5000 );
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
    
    else if(document.getElementById('Brand').value==='')
    {
        alert('Please Select Brand');
        return false;
    }
    else if(document.getElementById('Product_Detail').value==='')
    {
        alert('Please Select Product Category');
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
    else if(document.getElementById('invoice').value==='')
    {
        alert('Please Select Purchase Invoice Availability');
        return false;
    }
    else if(document.getElementById('ccsc').value==='')
    {
        alert('Please Fill Customer Complaint');
        document.getElementById('ccsc').focus= true;
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

function openTab1(div_name, tabName) 
{
    
    var flag_valid = false;
    if(div_name==='Customer_Details')
    {
         flag_valid = validate_customer_details();
    }
    else if(div_name==='Product_Details')
    {
         flag_valid = validate_product_details();
    }
    else if(div_name==='Set_Conditions')
    {
         flag_valid = validate_set_conditions();
    }
    else
    {
        flag_valid = true;
    }
    if(flag_valid===true && tabName!=='file_upload')
    {
        flag_valid = validate_image();
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

function open_gallery()
{
    $('#file-input').trigger('click');
    $('#file-input-type').val('file-input');
}

function open_camera()
{
    $('#file-input-camera').trigger('click');
    $('#file-input-type').val('file-input-camera');
}

function readURL(input) 
{
    
        if (input.files && input.files[0]) {
            var size = input.files[0].size;
            size = size/1024;
            if(size>80)
            {
                alert("File Size not more than 80 KB.");
            }
            else
            {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#img_demo')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
                $('#img_demo').show();
            }
            
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
    $('#'+img+'_cntr').show();
    img_demo.attr('src', '#');
    img_demo.hide();
    var file_input = $('#file-input-type').val();
    var file_demo = $('#'+file_input);
    var clone_file = file_demo.clone();
    clone_file.attr("id",img);
    clone_file.attr("name",img);
    $('#'+img+'_img'+'_disp').append(clone_file);
    
    //console.log(clone);
            
}

function file_upload()
{
    var file_type=$('#file_type').val();
    var img = '';
    if(file_type==='Warranty card')
    {
        img = 'wrrn'; 
    }
    if(file_type==='Purchase Invoice')
    {
        img = 'prcs'; 
    }
    if(file_type==='Model No. Image')
    {
        img = 'mdl'; 
    }
    if(file_type==='Serial No. Image')
    {
        img = 'srl'; 
    }
    if(file_type==='Symptom Image 1')
    {
        img = 'smtm1'; 
    }
    if(file_type==='Symptom Image 2')
    {
        img = 'smtm2'; 
    }
    if(file_type==='Symptom Image 3')
    {
        img = 'smtm3'; 
    }
    file_move(img);
}


function set_tagId(TagId,visit_type)
{
    $('#job_tag_id').val(TagId);
    $('#first_date').hide();
    if(visit_type==='first_visit')
    {
        $('#exampleModalLabel').html('Job Schedule');
    }
    else
    {
        $('#first_date').val($('#td_'+TagId).val());
        $('#first_date').show();
        $('#exampleModalLabel').html('Job Reschedule');
    }
}

function save_shd_time()
{
    $('#scc').html("");
    $('#err').html("");
    var tagId = $('#TagId').val();
    var job_date = $('#job_date').val();
    //alert(job_date); 
    //return false;
    var job_hour = $('#job_hour').val();
    var job_minute = $('#job_minute').val();
    var job_remarks = $('#job_remarks').val();
    
    if(job_remarks=='')
    {
        alert("Please Fill Reason of Reschedule.");
        return false;
    }
    
    
    $.post('se-job-save',{tagId:tagId,job_date:job_date,job_hour:job_hour,job_minute:job_minute,job_remarks:job_remarks}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {    
            $('#rsch').html(obj.resp_table);
        }
    }); 
}

function save_follow_up()
{
    
    var tagId = $('#TagId').val();
    var se_followup_sub = $('#se_followup_sub').val();
    var se_followup_remarks = $('#se_followup_remarks').val();
    
    
    if(se_followup_sub=='')
    {
        alert("Please Select Subject");
        return false;
    }
    else if(se_followup_remarks=='')
    {
        alert("Please Fill Remarks");
        return false;
    }
    
    
    $.post('se-follow-save',{tagId:tagId,se_followup_sub:se_followup_sub,se_followup_remarks:se_followup_remarks}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {
            $('#tbl_follow').html(obj.resp_table);
        }
        
    }); 
}

function show_part_arr(job_status)
    {
        if(job_status==='Part Pending')
        {
            $('#part_arr').show();
        }
        else
        {
            $('#part_arr').hide();
        }
    }
    
    
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
    
    
function remove_img(img)
{
    var img_prev = $('#'+img+'_img').remove();
    $('#'+img+'_cntr').hide();
    $('#'+img).remove();
}


    
 function add_part(div_id)
 {
     var TagId = $('#TagId').val();
     var brand_id = $('#Brand').val();
     var product_category_id = $('#Product_Detail').val();
     var product_id = $('#Product').val();
     var model_id = $('#Model').val();
     
     /*var part_no = $('#part_no'+div_id).val();
     var part_name = $('#part_name'+div_id).val();
     var pending_parts = $('#pending_parts'+div_id).val();
          
     if(part_no=='')
     {
        alert("Please Select Part No.");
        $('#part_no'+div_id).focus();
        return false;
     }
     else if(part_name=='')
     {
        alert("Please Select Part Name");
         $('#part_name'+div_id).focus();
         return false;
     }
     else if(pending_parts=='')
     {
        alert("Please Fill No. of Pending Parts");
        $('#pending_parts'+div_id).focus();
        return false;
     }*/
     
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
                 model_id:model_id,
                 /*part_no:part_no,
                 part_name:part_name,
                 pending_parts:pending_parts,*/
                 TagId:TagId,
                 div_id:div_id
              },
              success: function(tblRow){
                  //$('#part_arr').append(result);
                  //var span = '<span  class="fa fa-minus" style="width:80px;" onclick="del_part('+div_id+');"></span>';
                  //$('#span'+div_id).html(span);
                  $("#thead").append(tblRow);
                  var rowno = $("#rowno");
                  rowno.html(document.getElementById("tbl_part").rows.length-2);
                  rowno.removeAttr('id');
                  $('#npc_approve').prop('disabled',false);
              }});
 }
 
 function del_part(part_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
          });
    jQuery.ajax({
              url: 'job-del-part-po',
              method: 'post',
              data: {
                 part_id: part_id
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                  }
                  else
                  {
                      
                  }
              }});
     
 }
 
 function del_part_temp(part_id)
 {
     $('#tr'+part_id).remove();
 }
 
 function get_added_part()
 {
     var TagId = $('#TagId').val();
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
          });
    jQuery.ajax({
              url: 'get-added-part',
              method: 'post',
              data: {
                 tag_id: TagId
              },
              success: function(table_rows){
                      $('#thead').html(table_rows);
			$('.remove_npc_part').prop('disabled',true);
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
                 TagId:TagId,
                 po_raise:1
              },
              success: function(result){
                  $('#po_part_arr').html(result);
              }});
 }
 function raise_po(part_id)
{
    var po_type = $('#po_type'+part_id).val();
    var color = $('#color'+part_id).val();
    var remarks = $('#remarks'+part_id).val();
    var pending_parts = $('#pending_parts'+part_id).val();
    var pending_parts_int = parseInt(pending_parts);
    
    
    if(pending_parts==='')
    {
        alert("No. of Pending Parts should not be empty.");
        $('#pending_parts'+part_id).focus();
        return false;
    }
    
    if(remarks==='')
    {
        alert("Remarks should not be empty.");
        $('#remarks'+part_id).focus();
        return false;
    }
    
    
    if(pending_parts_int<=0)
    {
        alert("No. of Pending Parts not less than 1");
        $('#pending_parts'+part_id).focus();
        return false;
    }
    
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
          
    jQuery.ajax({
              url: 'se-raise-po',
              method: 'post',
              data: {
                 part_id: part_id,
                 po_type:po_type,
                 color:color,
                 pending_parts:pending_parts,
                 remarks:remarks
              },
              success: function(result){
                  //alert(result);
                  const obj = JSON.parse(result);
                  if(obj.status==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#scc').html(obj.job_remarks);
                      $('#scc').show();
                      $('#err').hide();
                  }
                  else 
                  {
                      $('#scc').hide();
                      $('#err').html(obj.job_remarks);
                      $('#err').show();
                  }
                  
                  
              }});      
    
    
}

function estmt_cancelled()
{
	$('.npc_cancel_popup').show();
}

function estmt_not_approved()
{
	$('.npc_notapprove_popup').show();
}

