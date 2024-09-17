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
    .fixed-header .app-main {
        padding-top: 20px !important;
    }
</style>
<script type="text/javascript">
    var k = jQuery.noConflict();
    k(function(){
        k("#sch_date").datepicker({ altField: "#job_date",changeMonth: true,changeYear: true,require:true, dateFormat: "dd-mm-yy"});
   });  

   var k = jQuery.noConflict();
    k(function(){
        k("#sch_date1").datepicker({ altField: "#delivery_date",changeMonth: true,changeYear: true,require:true, dateFormat: "dd-mm-yy"});
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



function npc_approve_save()
{
    var quantity= $('#no_pen_part').val();
    if(quantity=='')
    {
        alert("Pls. mention the part quantity to order the part.");
        return false;
    }

    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')           
              }
          });
    var form = $("#request_to_npc");
    var url = form.attr('action');
    
    jQuery.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           success: function(result)
           {
               if(result==='1')
               {
                   alert('Request To NPC Approval has been Generated.');
                   get_added_part();
                   //$('#npc_approve').prop('disabled',true);
                   //$('#npc_approve').html("Part price pending with NPC");
		   
               }
               else
               {
                   alert('Request To NPC Approval has been Failed.');
               }
           }
         });    

    return false;
}

function npc_reestimate_save()
{
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')           
              }
          });
    var form = $("#request_to_npc");
    //var url = form.attr('action');
    var url = 'request-to-reestimate';
    
    jQuery.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           success: function(result)
           {
               if(result==='1')
               {
                   alert('Request To NPC Re-Estimation has been Generated.');
                   get_added_part();
                   //$('#npc_approve').prop('disabled',true);
                   //$('#npc_approve').html("Part price pending with NPC");
		   
               }
               else
               {
                   alert('Request To NPC Re-Estimation has been Failed.');
               }
           }
         });    

    return false;
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
                    //$('#thead').html(table_rows);
                    
                    $('#part_npc').append(table_rows);
        $('.remove_npc_part1').prop('disabled',true);
            }});
}


function openTab(evt, tabName) {
    
    var flag_valid = false;
    if(tabName=='Dealer')
    {
        flag_valid = true;
    }
    else 
    {
        //console.log('1');
        //flag_valid = validate_customer_details();
        flag_valid = validate_dealer_details();
        
        //alert(flag_valid);
        if(flag_valid===true && tabName!=='Vehicle_Detail')
        {
            //console.log('2');
            flag_valid = validate_vehicle_details();
        }
    }
        if(flag_valid===true && tabName!=='file_upload')
        {
            flag_valid = validate_image();
        }
        // if(flag_valid===true && tabName!=='file_upload')
        // {
        //     flag_valid = validate_image();
        // }
    //console.log('3');
    if(flag_valid==true)
    {
        //console.log('4');
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
        //console.log(evt);
        //evt.currentTarget.className += " active";
        document.getElementById(tabName+'1').className += " active";
    }
}

function openTab1(div_name, tabName) 
{
    
    var flag_valid = false;
    //console.log("1");
    if(div_name==='file_upload')
    {
        flag_valid = validate_image();
    }
    else
    {
        flag_valid = true;
    }

    if(div_name==='Dealer')
    {
        flag_valid = validate_dealer_details();
        
    }
    if(div_name==='Vehicle_Detail')
    {
        flag_valid = validate_vehicle_details();
    }
    else if(div_name==='Product_Details')
    {
         flag_valid = validate_product_details();
    }
    else if(div_name==='Complaint_Details')
    {
         flag_valid = validate_complaint_details();
    }
    else if(div_name==='Set_Conditions')
    {
         flag_valid = validate_set_conditions();
    }
    
    //console.log(flag_valid);
    
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

        //console.log(tabName);
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName+'1').className += " active";
    }
}


function validate_image()
{
    if($("#warraty_card_save").length !== 0){
        alert('image is not save ! Please save or remove the image!');
        return false;
    }
    return true;
    
}

    function validate_dealer_details()
    {

        var phoneno = /^[1-9]{1}[0-9]+/;
        var matchpt = document.getElementById('Contact_No_CL').value.match(phoneno);
        //console.log(matchpt);

        if(document.getElementById('DealerNameCL').value==='')
        {
            alert('Please Fill Delaer Name');
            return false;
        }
        else if(document.getElementById('LocationCL').value==='')
        {
            alert('Please Fill Dealer Location');
            return false;
        }
        else if(document.getElementById('RegionCL').value==='')
        {
            alert('Please Select Region');
            return false;
        }
        else if(document.getElementById('state_cl').value=='')
        {
            alert('Please Select State.');
            return false;
        }
        else if(document.getElementById('pincode').value=='')
        {
            alert('Please Select Pincode.');
            return false;
        }



        else if(document.getElementById('Customer_Name_CL').value==='')
        {
            alert('Please Fill Contact Person Name');
            return false;
        }
        else if(document.getElementById('Contact_No_CL').value!=='' && document.getElementById('Contact_No_CL').value.length!==10)
        {
            alert('Please Fill Right Contact No.');
            return false;
        }

        else if(!matchpt){
            //console.log("test");
            $('#errornameccl').fadeIn().text("Please Fill Correct Contact No.");
                    setTimeout(function() {
                        $('#errornameccl').fadeOut("slow");
                    }, 5000 );

            return false;
        }


        return true;
    }

    function validate_vehicle_details()
    {

        var phoneno = /^[1-9]{1}[0-9]+/;
        var matchpt = document.getElementById('Contact_No_CL').value.match(phoneno);

        if(document.getElementById('vehicle_sale_date').value==='')
        {
            alert('Please Fill Vehicle Sale Date');
            return false;
        }
        else if(document.getElementById('vin_no').value==='')
        {
            alert('Please Fill VIN No.');
            return false;
        }
        else if(document.getElementById('mielage').value==='')
        {
            alert('Please Fill Mielage');
            return false;
        }
        else if(document.getElementById('warranty_type_cl').value=='')
        {
            alert('Please Select Warranty Status.');
            return false;
        }
        else if(document.getElementById('Product_cl').value==='')
        {
            alert('Please Select Vehicle Model');
            return false;
        }
        else if(document.getElementById('Model_cl').value==='')
        {
            alert('Please Select DA2 - Part number');
            return false;
        }
        else if(document.getElementById('system_sw_version').value==='')
        {
            alert('Please Fill System SW Versiion');
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
        alert('Please Select Model No.');
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
    else if(document.getElementById('ccsc').value==='')
    {
        alert('Please Fill Add Comments.');
        return false;
    }

    else if(document.getElementById('warranty_card').value==='')
    {
        alert('Please Select Warranty Card');
        return false;
    }
    else if(document.getElementById('invoice').value==='')
    {
        alert('Please Select Purcase Invoice Availability');
        return false;
    }

        var date = new Date();
        var selectdate = document.getElementById('Bill_Purchase_Date').value;
        var sel_arr = selectdate.split('-');
        var new_sel_arr = sel_arr[2]+'/'+sel_arr[1]+'/'+sel_arr[0]  ;          
        
        var js_selecteddate = new Date(new_sel_arr);    

        if(js_selecteddate > date)
        {
            
            $('#errorname').fadeIn().text("Please enter a valid Bill Purchase Date");
                    setTimeout(function() {
                        $('#errorname').fadeOut("slow");
                    }, 5000 );

            return false;
            
        }
    return true;
}

function validate_complaint_details()
{

    var phoneno = /^[1-9]{1}[0-9]+/;
    var matchpt = document.getElementById('Contact_No_CL').value.match(phoneno);

    if(document.getElementById('ccsc_cl').value==='')
    {
        alert('Please Fill Customer Complaint ');
        return false;
    }
    else if(document.getElementById('job_card').value==='')
    {
        alert('Please Fill New Job Card');
        return false;
    }
    else if(document.getElementById('videos').value==='')
    {
        alert('Please Select Videos');
        return false;
    }
    else if(document.getElementById('crf').value=='')
    {
        alert('Please Select CRF');
        return false;
    }
    else if(document.getElementById('ftir').value=='')
    {
        alert('Please Select FTIR.');
        return false;
    }
    else if(document.getElementById('supr_analysis').value==='')
    {
        alert('Please Fill Supreme 1st Analysis ');
        return false;
    }
    else if(document.getElementById('issue_type').value==='')
    {
        alert('Please Select Type of Issue Suspected ');
        return false;
    }
    else if(document.getElementById('issue_cat').value==='')
    {
        alert('Please Select Issue Category ');
        return false;
    }
    else if(document.getElementById('visit_type').value==='')
    {
        alert('Please Select Visit Type ');
        return false;
    }
    else if(document.getElementById('part_replace').value==='')
    {
        alert('Please Select Part Replace');
        return false;
    }
    else if(document.getElementById('part_replace').value==='Yes' && document.getElementById('part_replace_date').value==='')
    {
        alert('Please Fill Part Replace Date');
        return false;
    }
    else if(document.getElementById('Job_Status').value==='')
    {
        alert('Please Select Job Status');
        return false;
    }
    
    else if(document.getElementById('dispatch_date').value==='')
    {
        alert('Please Fill Dispatch Date.');
        return false;
    }
    
    else if(document.getElementById('tat_delay_remarks').value==='')
    {
        alert('Parts Fill TAT Delay Remarks');
        return false;
    }
    
    else if(!matchpt){

        $('#errornamec').fadeIn().text("Please Fill Correct Contact No.");
            setTimeout(function() {
                $('#errornamec').fadeOut("slow");
            }, 5000 );

        return false;
    }


    return true;
}

function calculateTAT()
{
    var issueResolveDateString = $("#issue_resolved_date").val();
    var parts = issueResolveDateString.split("-");
    var issueResolveDate = new Date(parts[2], parts[1] - 1, parts[0]);

    var dateOfRegisterString = $("#date_of_register").val();
    var parts1 = dateOfRegisterString.split("-");
    var dateOfRegister = new Date(parts1[2], parts1[1] - 1, parts1[0]);

    var tatMilliseconds = issueResolveDate - dateOfRegister;
    var tatDays = tatMilliseconds / (1000 * 60 * 60 * 24);

    $("#tat").val(tatDays);
}



function save_shd_time()
{
    $('#field').val('');
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

        var date = new Date();

        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;

        var today = day + "-" + month + "-" + year;

        if(job_date < today)
        {
            alert("Pls. select the correct date to reschedule the appointment");
            return false;
            
        }
                
    
    $.post('se-job-save',{tagId:tagId,job_date:job_date,job_hour:job_hour,job_minute:job_minute,job_remarks:job_remarks}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {    
            $('#rsch').html(obj.resp_table);
            $('#job_remarks').val('');
            $('#job_hour').val('00');
            $('#job_minute').val('00');
            $('#job_date').reset();
        }
    }); 
}

function save_delivery_time()
{
    $('#field').val('');
    $('#scc').html("");
    $('#err').html("");
    var tagId = $('#TagId').val();
    var delivery_date = $('#delivery_date').val();
    var delivery_remarks = $('#delivery_remarks').val();
    
    if(delivery_remarks == '')
    {
        alert("Please Fill Remark.");
        return false;
    }

        var date = new Date();

        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;

        var today = day + "-" + month + "-" + year;

        if(delivery_date < today)
        {
            alert("Pls. select the correct date to Delivery");
            return false;
        }

    $.post('se-delivery-save',{tagId:tagId,delivery_date:delivery_date,delivery_remarks:delivery_remarks}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {    
           $('#delivery').html(obj.resp_table);
           $('#delivery_remarks').val('');
           $('#delivery_date').reset();
        }
    }); 
}
function save_follow_up()
{    
    var tagId = $('#TagId').val();
    var se_followup_sub = $('#se_followup_sub').val();
    var se_followup_remarks = $('#se_followup_remarks').val();
    
    
    if(se_followup_sub=='' || se_followup_sub == null)
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
            $('#se_followup_remarks').val('');
            $('#se_followup_sub').val('');
        }
        
    }); 
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
            var file_type=$('#file_type').val();
            //console.log(file_type);
            if(size>80 && file_type!='Video')
            {
                alert("File Size not more than 80 KB.");
            }
            else {
            var reader = new FileReader();
            var file = input.files[0];
            
            // Check if the file is a video
            if (file.type.startsWith('video/')) {

                if (size > 10240) {
                    alert("Video Size should not exceed 10 MB.");
                } else{

                    reader.onload = function (e) {
                    // Hide image preview
                    $('#img_demo').hide();
                    
                    // Show video preview
                    $('#videoDemo')
                        .attr('src', e.target.result)
                        .show();
                };

                reader.readAsDataURL(file);

                }
                
            } else {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#img_demo')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
                $('#img_demo').show();
                //alert("Please select a valid video file.");
            }
        }
            
        }
}

// function readURL(input) {
//     if (input.files && input.files[0]) {
//         var size = input.files[0].size;
//         size = size / 1024;
//         var file_type = $('#file_type').val();
//         console.log(file_type);

//         if (size > 80 && file_type != 'Video') {
//             alert("File Size not more than 80 KB.");
//         } else {
//             var reader = new FileReader();
//             var file = input.files[0];

//             // Check if the file is an image
//             if (file.type.startsWith('image/')) {
//                 reader.onload = function (e) {
//                     // Hide video preview
//                     $('#videoDemo').hide();

//                     // Show image preview
//                     $('#imgDemo')
//                         .attr('src', e.target.result)
//                         .show();
//                 };

//                 reader.readAsDataURL(file);
//             }
            
//             else if (file.type.startsWith('video/')) {
//                 reader.onload = function (e) {
//                     // Hide image preview
//                     $('#imgDemo').hide();

//                     // Show video preview
//                     $('#videoDemo')
//                         .attr('src', e.target.result)
//                         .show();
//                 };

//                 reader.readAsDataURL(file);
//             } else {
//                 alert("Please select a valid image or video file.");
//             }
//         }
//     }
// }

function file_upload()
    {
    var file_type=$('#file_type').val();
    var img = '';
    var second = new Date().getSeconds();

    if(file_type==='Job Card')
    {
        img = 'job_card'+second; 
    }
    if(file_type==='CRF')
    {
        img = 'crf'+second; 
    }
    if(file_type==='Video')
    {
        img = 'video'+second; 
    }
    if(file_type==='FTIR')
    {
        img = 'ftir'+second; 
    }
    
    if(file_type==='Video')
    {
        video_move(img,second);
    }else{
        file_move(img,second);
    }
    
    $('#file-input').val('');
    $('#file-input-camera').val('');

}

    
    function file_move(img,second)
    {
        var img_demo = $('#img_demo');
        //console.log(img_demo.attr('src'));
        if(img_demo.attr('src')==='' || img_demo.attr('src')==='#')
        {
            return 0;
        }
        var clone_img = img_demo.clone();
        clone_img.attr("id",img+'_img');
        var file_type = $('#file_type').val();
        var save_btn = '<button type="button" name="submit" class="btn btn-primary" id="warraty_card_save" onclick="save_image('+"'"+img+"'"+')" style="float:right;margin-right: 70px;">save</button>';
        var divclone = '<div id="'+img+'_cntr" class="float-container"><div class="float-child1"><div class="float-container"><div class="float-child"> <div class="form-row"> <div class="col-md-12"><div class="position-relative form-group"> <label for="examplePassword11"><br><b>Image Type: '+file_type+'</b></label> <button type="button" style="width:100%" id="remove_wrrn" name="remove_wrrn" onclick="remove_img('+"'"+img+"'"+')" class="mt-2 btn btn-primary">Remove</button></div><div class="position-relative form-group"><button type="button" style="width:100%" onclick="download_img('+"'"+img+"'"+')" class="btn btn-primary">Download</button></div> </div></div> </div><div class="float-child" style="height:250px;" id="'+img+'_img_disp"></div></div></div> </div>';
        $('#wrrn').prepend(divclone);
        $('#'+img+'_img'+'_disp').html(clone_img);
        $('#'+img+'_img'+'_disp').append(save_btn);
        img_demo.attr('src', '#');
        img_demo.hide();
        var file_input = $('#file-input-type').val();
        //console.log(file_input);
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


function video_move(video, second) {
    var videoDemo = $('#videoDemo');
    
    // Check if the video preview is not empty
    if (videoDemo.attr('src') === '' || videoDemo.attr('src') === '#') {
        return 0;
    }

    // Clone the video preview
    var cloneVideo = videoDemo.clone();
    cloneVideo.attr("id", video + '_video');

    // Get file type and create save button
    var fileType = $('#file_type').val();
    var saveBtn = '<button type="button" name="submit" class="btn btn-primary" id="video_save" onclick="save_video(' + "'" + video + "'" + ')" style="float:right;margin-right: 70px;">Save</button>';

    // Create container and append cloned video preview and save button
    var videoContainer = '<div id="' + video + '_cntr" class="float-container"><div class="float-child1">' +
                         '<div class="float-container"><div class="float-child"><div class="form-row">' +
                         '<div class="col-md-12"><div class="position-relative form-group">' +
                         '<label for="examplePassword11"><br><b>Video Type: ' + fileType + '</b></label>' +
                         '<button type="button" style="width:100%" id="remove_video" name="remove_video" onclick="remove_video(' + "'" + video + "'" + ')" class="mt-2 btn btn-primary">Remove</button>' +
                         '</div><div class="position-relative form-group"><button type="button" style="width:100%" onclick="download_video(' + "'" + video + "'" + ')" class="btn btn-primary">Download</button></div>' +
                         '</div></div></div><div class="float-child" style="height:250px;" id="' + video + '_video_disp"></div></div></div></div>';

    // Append the container, cloned video, and save button to the main container
    $('#wrrn').prepend(videoContainer);
    $('#' + video + '_video_disp').html(cloneVideo);
    $('#' + video + '_video_disp').append(saveBtn);

    // Clear the original video preview
    videoDemo.attr('src', '#');
    videoDemo.hide();

    // Get file input and clone it
    var fileInputType = $('#file-input-type').val();
    var fileDemo = $('#' + fileInputType);
    var cloneFile = fileDemo.clone();
    cloneFile.attr("id", video);
    cloneFile.attr("name", video);

    $('#form' + video).remove();
    $('#' + video + '_video_disp').append('<form id="form' + video + '" name="form' + video + '"></form>');
    $('#' + "form" + video).append(cloneFile);

    // Add necessary hidden input fields
    $('#' + "form" + video).append('<input type="hidden" name="TagId" value="' + $('#TagId').val() + '">');
    $('#' + "form" + video).append('<input type="hidden" name="file_type" value="' + fileType + '">');
}

function remove_img(img)
{
    $('#'+img+'_img').remove();
    $('#'+img+'_cntr').hide();
    $('#'+img).remove();
    $('#file-input').val('');
    $('#file-input-camera').val('');
    $('#'+img+'_cntr').empty();
    
}

function remove_video(video) {
    $('#' + video + '_video').remove();
    $('#' + video + '_cntr').hide();
    $('#' + video).remove();
    $('#file-input').val('');
    $('#file-input-camera').val('');
    $('#' + video + '_cntr').empty();
}

function download_video(video) {
    // Get the video element
    var videoElement = document.getElementById(video + '_video');

    // Get the source of the video
    var videoSource = videoElement.src;

    // Create an anchor element
    var downloadLink = document.createElement('a');

    // Set the download attribute with a desired filename
    downloadLink.download = 'video_download.mp4'; // You can set any desired filename here

    // Set the href attribute with the video source
    downloadLink.href = videoSource;

    // Append the anchor element to the document
    document.body.appendChild(downloadLink);

    // Trigger a click on the anchor element to start the download
    downloadLink.click();

    // Remove the anchor element from the document
    document.body.removeChild(downloadLink);
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
                                            <button style="background: yellow;font-weight: 600;float: right;border: yellow;font-size: 15px;padding: 8px;"><b>Job Number -</b> {{$data['job_no']}}</button>
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
                                            <button type="button" id="Dealer1" class="tabFormlinks active" onclick="openTab(event, 'Dealer')" >Dealer/Customer Details</button>
                                            <button id="Vehicle_Detail1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Vehicle_Detail')">Vehicle Details</button>
                                            <button id="Complaint_Details1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Complaint_Details')">Complaint Details</button>
                                        
                                            <button id="Reschedule1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Reschedule')">Reschedule Date & Calling Remarks</button>
                                            <button id="file_upload1" type="button"  class="tabFormlinks" onclick="openTab(event, 'file_upload')">Image Upload</button>
                                            <button id="Estimated_Cost1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Estimated_Cost')">Create Estimation</button>

                                            <button id="Part_Required1" type="button"  class="tabFormlinks" onclick="openTab(event, 'Part_Required');get_po_parts();">Order Part</button>
                                            {{-- <button id="PO_Raise1" type="button"  class="tabFormlinks" onclick="get_po_parts();openTab(event, 'PO_Raise')">PO Raise</button> --}}
                                            <button id="sp1" type="button"  class="tabFormlinks" onclick="openTab(event, 'sp')">Special Approvals</button>

                                            <button id="CC" type="button"  class="tabFormlinks" onclick="openTab(event, 'CCs')">Closure Codes</button>

                                            <button id="DSS1" type="button"  class="tabFormlinks" onclick="openTab(event, 'DSS')">Delivery Status</button>
                                            
                                          </div>
                                            <div id="Dealer" class="tabFormcontent" style="display:block;">
                                                <h5 class="card-title">Dealer/Customer Details</h5>
                                                <form method="post" id="dealer_save_observation" name="dealer_save_observation" action="dealer-save-observation" enctype="multipart/form-data">
                                                    <div class="form-row">

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer Name<span style="color: #f00;">*</span></label>
                                                                <input type="text"  name="DealerName" value="<?php echo $data['dealer_name']; ?>" placeholder="Dealer Name" id="DealerNameCL" class="form-control" required>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Location<span style="color: #f00;">*</span></label>
                                                            <input  name="location" id="LocationCL" placeholder="Location" value="<?php echo $data['Landmark']; ?>" type="text" class="form-control" required></div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Region<span style="color: #f00;">*</span></label>
                                                            <select  name="region_id" id="RegionCL"  class="form-control" required>
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
                                                            <select  name="state" id="state_cl" data-placeholder="" class="form-control" onchange="get_pincode(this.value)"  required="">
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
                                                                <select  onchange="get_area(this.value)" name="pincode" id="pincode" data-placeholder="" class="form-control" required="">
                                                                    <option value="">Select</option>
                                                                    
                                                                    <?php  foreach($pin_master as $pin_id=>$pincode)
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
                                                                <input  name="Customer_Name" id="Customer_Name_CL" value="<?php echo $data['Customer_Name']; ?>" placeholder="Contact Person" type="text" class="form-control" required></div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Contact No.<span style="color: #f00;">*</span></label>
                                                                <input  name="Contact_No" id="Contact_No_CL" placeholder="Contact No." value="<?php echo $data['Contact_No']; ?>"  type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required=""  maxlength="10">
                                                                <span class="error" id="errornameccl" style="color:red"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4"></div>
                                                        <div class="col-md-4"></div>

                                                        <!-- <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <br/>
                                                                <button  type="button" onclick="openTab3('Dealer', 'Vehicle_Detail');" class="mt-2 btn btn-success">Next</button>
                                                            </div>
                                                        </div> -->
                                                        <br>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <br/>
                                                                <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                                                <button type="button" onclick="save_dealer_return()"  class="mt-2 btn btn-primary">Save</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <br/>
                                                                <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">

                                                            <div class="position-relative form-group">
                                                                <br/>
                                                                <button type="button" onclick="openTab1('Dealer', 'Vehicle_Detail');" class="mt-2 btn btn-success" style="float: right;">Next</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
						                    </div>
                                            <div id="Vehicle_Detail" class="tabFormcontent" style="display: none;">
                                                <form method="post" id="vehicle_save_observation" name="vehicle_save_observation" action="vehicle-save-observation" enctype="multipart/form-data">
                                                    <div class="form-row">
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Vehicle Sale Date<span style="color: #f00;">*</span></label>
                                                                <input name="vehicle_sale_date" id="vehicle_sale_date" value="<?php echo $data['vehicle_sale_date']; ?>"  maxlength="10" placeholder="Vehicle Sale Date" type="text" class="form-control datepicker" required>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">VIN No.<span style="color: #f00;">*</span></label>
                                                                <input name="vin_no" id="vin_no" value="<?php echo $data['vin_no']; ?>"  maxlength="50" placeholder="VIN No." type="text" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Mielage Km. / PDI <span style="color: #f00;">*</span></label>
                                                                <input name="mielage" id="mielage" value="<?php echo $data['mielage']; ?>"  maxlength="6" placeholder="Mielage Km. / PDI" onkeypress="return checkNumber(this.value,event)" type="text" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Status <span style="color: #f00;">*</span></label>
                                                                <select name="warranty_type" class="form-control" required="" id="warranty_type_cl">
                                                                <option value="">Select</option>
                                                                    <option value="IN" <?php if($data['warranty_type']=='IN') { echo "selected";} ?>>IN</option>
                                                                    <option value="OUT" <?php if($data['warranty_type']=='OUT') { echo "selected";} ?>>OUT</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                                <div class="position-relative form-group"><label for="examplePassword11" class="">Vehicle Model<span style="color: #f00;">*</span></label>
                                                                <select name="Product" id="Product_cl" class="form-control" onchange="get_model_cl('_cl',this.value)" required>
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
                                                                <select name="Model" id="Model_cl" onchange="get_model_dependents('_cl',this.value)" class="form-control" required>
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
                                                                <input maxlength="50" name="system_sw_version" id="system_sw_version" value="<?php echo $data['system_sw_version']; ?>" placeholder="System SW Version" type="text" class="form-control" required></div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Manufacturer serial number</label>
                                                            <input name="man_ser_no" id="man_ser_no" value="<?php echo $data['man_ser_no']; ?>" placeholder="Man. Serial No" type="text" class="form-control" ></div>
                                                        </div>
                                                    </div>
                                                    <div class="form-row">

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <button type="button" style="float:left;" onclick="openTab(event, 'Dealer');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                                                <button type="button" onclick="save_vehicle_return()"  class="mt-2 btn btn-primary">Save</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">

                                                                <button type="button" style="float:right;" onclick="openTab1('Vehicle_Detail','Complaint_Details');" class="mt-2 btn btn-success" >Next</button>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                            </div>
                                            <div id="Complaint_Details" class="tabFormcontent" style="display: none;">
                                                <form method="post" id="complaint_save_observation" name="complaint_save_observation" action="complaint-save-observation" enctype="multipart/form-data">
                                                    <div class="form-row">
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Complaint <span style="color: #f00;">*</span></label>
                                                                <input name="ccsc" id="ccsc_cl" placeholder="Customer Complaint" value="<?php echo $data['ccsc']; ?>" type="text" class="form-control" required="" ></div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">New Job Card<span style="color: #f00;">*</span></label>
                                                                <select name="job_card" id="job_card" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yes" <?php if($data['job_card']=='Yes') { echo "selected";} ?>>Yes</option>
                                                                    <option value="No" <?php if($data['job_card']=='No') { echo "selected";} ?>>No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Videos<span style="color: #f00;">*</span></label>
                                                                <select name="videos" id="videos" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yes" <?php if($data['videos']=='Yes') { echo "selected";} ?> >Yes</option>
                                                                    <option value="No" <?php if($data['videos']=='No') { echo "selected";} ?> >No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">CRF<span style="color: #f00;">*</span></label>
                                                                <select name="crf" id="crf" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yes" <?php if($data['crf']=='Yes') { echo "selected";} ?> >Yes</option>
                                                                    <option value="No" <?php if($data['crf']=='No') { echo "selected";} ?> >No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">FTIR<span style="color: #f00;">*</span></label>
                                                                <select name="ftir" id="ftir" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yes" <?php if($data['ftir']=='Yes') { echo "selected";} ?> >Yes</option>
                                                                    <option value="No" <?php if($data['ftir']=='No') { echo "selected";} ?> >No</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">FTIR No. </label>
                                                                <input name="ftir_no" id="ftir_no" value="<?php echo $data['ftir_no']; ?>"  type="text" class="form-control" >
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Supreme 1st Analysis <span style="color: #f00;">*</span></label>
                                                                <input name="supr_analysis" id="supr_analysis" value="<?php echo $data['supr_analysis']; ?>" type="text" class="form-control" required="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <label for="examplePassword11" class="">Remarks </label>
                                                                <input name="remarks" id="remarks"  value="<?php echo $data['remarks']; ?>" type="text" class="form-control" >
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Type of Issue Suspected <span style="color: #f00;">*</span></label>
                                                                <select name="issue_type" id="issue_type" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="HW" <?php if($data['issue_type']=='HW') { echo "selected";} ?> >HW</option>
                                                                    <option value="SW" <?php if($data['issue_type']=='SW') { echo "selected";} ?> >SW</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Issue Category <span style="color: #f00;">*</span></label>
                                                                <input name="issue_cat" id="issue_cat" value="<?php echo $data['issue_cat']; ?>"  type="text" class="form-control" required="">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Mobile Handset Model </label>
                                                                <input name="mobile_handset_model" value="<?php echo $data['mobile_handset_model']; ?>" id="mobile_handset_model"  type="text" class="form-control" >
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <label for="examplePassword11" class="">Alternate No.</label>
                                                                <input  name="Alt_No" id="Alt_No" value="<?php echo $data['Alt_No']; ?>" placeholder="Alternate No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)"  maxlength="10" >
                                                                <span class="error" id="errornamea" style="color:red"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Visit Type<span style="color: #f00;">*</span></label>
                                                                <select name="visit_type" id="visit_type" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Site Visit" <?php if($data['visit_type']=='Site Visit') { echo "selected";} ?> >Site Visit</option>
                                                                    <option value="Online" <?php if($data['visit_type']=='Online') { echo "selected";} ?> >Online</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Site Visit Date</label>
                                                                <input name="site_visit_date" id="site_visit_date" value="<?php echo $data['site_visit_date']; ?>"  placeholder="Site Visit Date" type="text" class="form-control datepicker" required>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Part Replaced<span style="color: #f00;">*</span></label>
                                                                <select name="part_replace" id="part_replace" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yes" <?php if($data['part_replace']=='Yes') { echo "selected";} ?> >Yes</option>
                                                                    <option value="No" <?php if($data['part_replace']=='No') { echo "selected";} ?> >No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Date of Part Replaced</label>
                                                                <input name="part_replace_date" id="part_replace_date" value="<?php echo $data['part_replace_date']; ?>"  placeholder="Date of Part Replaced" type="text" class="form-control datepicker" required>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Issue Resolved Date</label>
                                                            <input name="date_of_register" id="date_of_register"  value="<?php echo date('d-m-Y', strtotime($data['created_at'])); ?>" type="hidden">
                                                                <input name="issue_resolved_date" id="issue_resolved_date" onchange='calculateTAT()' value="<?php echo $data['issue_resolved_date']; ?>"  placeholder="Issue Resolved Date" type="text" class="form-control datepicker" required>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Status Of Job<span style="color: #f00;">*</span></label>
                                                                <select name="Job_Status" id="Job_Status" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="In Process" <?php if($data['Job_Status']=='In Process') { echo "selected";} ?> >In Process</option>
                                                                    <option value="Resolved" <?php if($data['Job_Status']=='Resolved') { echo "selected";} ?> >Resolved</option>
                                                                    <option value="Pending" <?php if($data['Job_Status']=='Pending') { echo "selected";} ?> >Pending</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Date of Dispatch<span style="color: #f00;">*</span></label>
                                                                <input name="dispatch_date" id="dispatch_date" value="<?php echo $data['dispatch_date']; ?>"  placeholder="Date of Dispatch" type="text" class="form-control datepicker" required>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">TAT (In Days) </label>
                                                                <input name="tat" id="tat" value="<?php echo $data['tat']; ?>"  placeholder="TAT (In Days)" type="text" class="form-control" onkeypress="return checkNumber(this.value,event)"  maxlength="2" disabled>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>


                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Tat Delay Remarks<span style="color: #f00;">*</span></label>
                                                                <input name="tat_delay_remarks" id="tat_delay_remarks" value="<?php echo $data['tat_delay_remarks']; ?>"  placeholder="Tat Delay Remarks" type="text" class="form-control" required>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Defective Part Received at Supreme/CIL</label>
                                                                <select name="defective_part_rcv" id="defective_part_rcv" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Supreme" <?php if($data['defective_part_rcv']=='Supreme') { echo "selected";} ?> >Supreme</option>
                                                                    <option value="Clarion" <?php if($data['defective_part_rcv']=='Clarion') { echo "selected";} ?> >Clarion</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Defective Received at CIL Date/Final Job Close Date</label>
                                                                <input name="final_job_close_date" id="final_job_close_date" value="<?php echo $data['final_job_close_date']; ?>"  placeholder="Defective Received at CIL Date/Final Job Close Date" type="text" class="form-control datepicker" required>
                                                                <span class="error" id="errornamercl" style="color:red"></span>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group"><label for="examplePassword11" class="">Final Status of the Job</label>
                                                                <select name="final_job_status" id="final_job_status" class="form-control" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Open" <?php if($data['final_job_status']=='Open') { echo "selected";} ?> >Open</option>
                                                                    <option value="Closed" <?php if($data['final_job_status']=='Closed') { echo "selected";} ?> >Closed</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="form-row">
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <button type="button" style="float:left;" onclick="openTab(event, 'Vehicle_Detail');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">
                                                                <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                                                <button type="button" onclick="save_complaint_return()"  class="mt-2 btn btn-primary">Save</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="position-relative form-group">

                                                                <button type="button" style="float:right;" onclick="openTab1('Complaint_Details','Reschedule');" class="mt-2 btn btn-success" >Next</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>   
                                        
                                            <div id="Reschedule" class="tabFormcontent">
                                                <table style="width:100%;background: #8c8c8c;">
                                                    <tr>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Reschedule Date & Time</b></td>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Reschedule Remarks</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="height:200px;width:500px;">
                                                            <div id="sch_date"></div>
                                                            
                                                            <div align="right">
                                                                <table border="2" style="font-size:18px;width:220px;">
                                                                    <tr>
                                                                        <th style="background:#ffa500;text-align: center;color: black;">Time</th>
                                                                        <td>
                                                                            <select  id="job_hour" style="background:null;width:100%;height:100%;">
                                                                                <?php for($tt=0;$tt<=23;$tt++) { ?>
                                                                                <option value="<?php echo str_pad($tt, 2, '0', STR_PAD_LEFT);?>"><?php echo str_pad($tt, 2, '0', STR_PAD_LEFT);?></option>
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
                                                                <table border="2" style="font-size:16px;width:100%;">
                                                                    <tr>
                                                                        <th style="background:#ffa500;color:black;">Reason of Reschedule</th>
                                                                        <td>
                                                                            <input type="text"  id="job_remarks" style="background:null;width:100%;">
                                                                            <input type="hidden" id="job_date" value="">
                                                                        </td>
                                                                        <td>
                                                                        <button type="button" style="background:#ffa500;color:black;width:100%;" class="btn btn-secondary" onclick="save_shd_time()" >Apply</button>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div> <br>                                                       
                                
                                                        </td>
                            
                                                        <td style="vertical-align:top;margin-top: 10px;">
                                                            <br>
                                                            <?php $history_json = $data['se_sdl_history'];?>
                                                            <div style="height:280px;width:100%;overflow:auto;padding:3%">
                                                            <table style="font-size:14px;width:100%;" id="rsch">
                                                            <?php
                                                            $history_arr = json_decode($history_json,true);
                                                            $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
                                                            $index_bg = 0;
                                                            //print_r($history_arr);
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
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Additional Calling Remarks (Follow Remarks)</b></td>
                                                        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Calling or Followup Remarks</b></td>
                                                    </tr>
                                                    
                                                    <tr style="background:#739900;">
                                                        <td style="height:250px;" >
                                                            <div class="card-body" style="background:#ffeecc;">
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
                                                            <div style="height:200px;width:100%;overflow:auto;padding:3%">
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
                                                        </div>
                                                               
                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                <div class="form-row">
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab(event, 'Complaint_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <!-- <button type="button" onclick="openTab(event, 'Product_Details');" class="mt-2 btn btn-danger" style="float:left;">Previous</button> -->
                                                            <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                                                        </div>
                                                    </div> 
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab1('Reschedule', 'file_upload');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
						    
                                            </div>

                                            <!-- Code Start From Here -->
                                          
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
                                                                                <select id="file_type" name="file_type" class="form-control">
                                                                                    <!-- <option value="Warranty card">Warranty card</option>
                                                                                    <option value="Purchase Invoice">Purchase Invoice
                                                                                    </option>
                                                                                    <option value="Serial No. Image">Model & Serial No.
                                                                                        Image</option>
                                                                                    <option value="Symptom Image 1">Symptom Image</option>
                                                                                    <option value="Symptom Image 2">Any special Approval
                                                                                    </option>  -->

                                                                                    <!-- <option value="Any special Approval">Any special Approval
                                                                                    </option>-->
                                                                                    <option value="Job Card">Job Card</option>
                                                                                    <option value="CRF">CRF</option>
                                                                                    <option value="Video">Video</option>
                                                                                    <option value="FTIR">FTIR</option>

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
                                                                <video id="videoDemo" width="320" height="240" controls style="display: none;"></video>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">

                                                    <div class="row">

                                                        <div class="col-md-12" id="wrrn"> </div>

                                                        </div>

                                                    <div id="wrrn_cntr256" class="float-container mb-4"></div>

                                                        <?php foreach ($imagedata as $item)
                                                            {  
                                                                ?>       
                                                        <div id="wrrn_cntr256" class="float-container mb-4">
                                                        
                                                                            <div class="form-row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="position-relative form-group">
                                                                                            <label for="examplePassword11" class="">
                                                                                                <b>Image Type: <?php if($item['image_type']==='Symptom Image 2') {echo "Any special Approval";} else { echo $item['image_type']; } ?></b></label>
                                                                                            
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

                                                                                        <?php if($item['image_type']==='Video'){ ?>
                                                                                            <div>
                                                                                            <video id="wrrn<?php echo $item['ImagId'];?>_img" controls class="img-fluid">
                                                                                            <source src="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$item['img_url']; ?>" type="video/mp4">
                                                                                            Your browser does not support the video tag.
                                                                                            </video>

                                                                                                <!-- <video id="videoDemo" width="320" height="240" controls style="display: none;"></video> -->
                                                                                            </div>
                                                                                        <?php } else{?>
                                                                                        <div>
                                                                                            <img id="wrrn<?php echo $item['ImagId'];?>_img" src="<?php echo "{$str_server}/storage/app/supreme/".$data['TagId']."/".$item['img_url']; ?>" class="img-fluid" />
                                                                                        </div>
                                                                                        <?php }?>

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

                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab(event, 'Reschedule');"
                                                                class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <!-- <button type="button" onclick="openTab(event, 'Reschedule');"
                                                                class="mt-2 btn btn-danger" style="float:left;">Previous</button> -->
                                                                <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab1('file_upload', 'Estimated_Cost');"
                                                                class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!--- code End Here --->

                                            <div id="Estimated_Cost" class="tabFormcontent">
                                                <a href="se-job-view-contact?contact_no=<?php echo $data['Contact_No'];?>&brand_id=<?php echo $data['brand_id'];?>&product_category_id=<?php echo $data['product_category_id'];?>&product_id=<?php echo $data['product_id'];?>&model_id=<?php echo $data['model_id'];?>" class="mt-2 btn btn-primary" style="float:right;" target="_blank">Check Repair History</a>
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
    <td id="rowno"><?php echo '1'; ?></td>
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
        <input maxlength="5" onKeyPress="return checkNumber(this.value,event);" id="no_pen_part" name="SparePart[0][no_pen_part]" class="form-control"  type="text"  value="" >
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
                                                        
                                                            
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td colspan="3" style="marging-left:20px;"><button type="button" class="mt-2 btn btn-primary" onclick="add_part('1');" >Add Row</button> </td>
                                                            <td colspan="3" align="right"><button id="npc_approve"   type="button" class="mt-2 btn btn-primary" onclick="return npc_approve_save();" >Request to NPC for part price</button> </td>
                                                            <?php if(!empty($SparePart_arr1) || !empty($LabPart_arr1)) {?>
                                                           <td colspan="4" align="right"><button id="npc_approve"   type="button" class="mt-2 btn btn-primary" onclick="return npc_reestimate_save();" >Re-Estimation to NPC for part price</button> </td>
                                                            <?php }?>
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
                                                            <?php $i = 2; foreach($tagg_part_npc as $tpart) { ?>
                                                                <tr id="tr<?php echo $tpart->part_id; ?>">
                                                                    <td>{{$i++}}</td>
                                                                    <td>
                                                                        <!-- <select form="estmt_approve_save" name="SparePart[<?php //echo $tpart->part_id;?>][spare_id]" id="part_name<?php //echo $tpart->part_id; ?>"  class="form-control"  >
                                                                            <option value="<?php //echo $tpart->spare_id; ?>"><?php //echo $tpart->part_name; ?></option>
                                                                        </select> -->
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
                                                                            <!-- <option value="<?php //echo $tpart->charge_type; ?>"><?php //echo $tpart->charge_type; ?></option> -->
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
                                                                    <button id="estmt_cancel" type="button" class="mt-2 btn btn-primary npc_cancel_class" onclick="return estmt_cancelled();" >Cancel</button> 
                                                                    <button id="estmt_not_approve"  type="button" class="mt-2 btn btn-primary npc_notapprove_class" onclick="return estmt_not_approved();" >Not Approved</button> 
                                                                    <button form="estmt_approve_save" id="estmt_approve" name="submit" value="approve" type="button" onclick="return estmt_approved();" class="mt-2 btn btn-primary"  >Approved</button> 
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>  
                                                    <input form="estmt_approve_save" type="hidden" id="tag_id2" name="tag_id" value="<?php echo $data['TagId'];?>" />
                                                    
                                                </div>
                                                <?php } ?>
                                                

                                                <!-- view part pending npc -->
                                                <table border="1" style="width: 100%;" id="part_npc">
                                                        <!-- <thead id="thead">
                                                        <tr>
                                                            <th>Sr. No.</th>
                                                            <th>Part Name1234</th>
                                                            <th>Part Number</th>
                                                            <th>Quantity</th>
                                                            <th>Color</th>
                                                            <th>Part Type</th>
                                                            <th>Customer Price Per Unit</th>
                                                            <th>GST %</th>
                                                            <th>Total Amount</th>
                                                            
                                                        </tr>  
                                                            
                                                        </thead> -->
                                                        
                                                    </table>
                                                <?php if(!$tagg_part->isEmpty()){?>
                                                    <table border="1" style="width: 100%;">
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
                                                            <th>Remarks</th>
                                                        </tr>  
                                                            
                                                        </thead>
                                                    <?php $i = 1; foreach($tagg_part as $tpart) { ?>
                                                <tr id="tr<?php echo $tpart->part_id; ?>">
                                                    <td>{{$i++}}</td>
                                                    <td>
                                                    <select id="part_name<?php echo $tpart->part_id; ?>"   class="form-control" onchange="get_partno('<?php echo $tpart->part_id; ?>',this.value)" >
                                                        
                                                        <option value="<?php echo $tpart->spare_id; ?>"><?php echo $tpart->part_name; ?></option>     
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
                                                            <!-- <option value="Chargeable" <?php //if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option> -->
                                                            <!-- <option value="Non Chargeable" <?php //if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option> -->
                                                            <option value="<?php echo $tpart->charge_type;?>"><?php echo $tpart->charge_type;?></option>
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
                                                    <td>Pending To NPC</td>
                                                    <td>
                                                        <button type="button" class="mt-2 btn btn-danger remove_npc_part" onclick="del_part('<?php echo $tpart->part_id; ?>');" >Remove</button>
                                                    </td>
                                                    
                                                </tr>

                                            <?php } ?>                                                                          
                                                    </table>
                                                    <?php } ?>
                                                
                                                <!-- end view part pending npc -->
                                                    <div class="form-row">
                                                      <div class="col-md-12">
                                                       <p id="user_message" style="display:none;background: green;padding: 5px 10px;margin-top: 45px;color: white;border-radius: 5px;"></p>
                                                      </div>
                                                    </div>
                                        
                                                <div class="form-row">
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab(event, 'file_upload');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <!-- <button type="button" onclick="openTab(event, 'file_upload');" class="mt-2 btn btn-danger" style="float:left;">Previous</button> -->
                                                            <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
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
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                            <button type="button" onclick="openTab(event, 'Estimated_Cost');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
                                                        <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-4">
                                                        <div class="position-relative form-group">
								<button type="button" onclick="openTab1('PO_Raise', 'sp');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="PO_Raise" class="tabFormcontent">
                                                
                                                
                                                    
                                                
                                                <div class="form-row">

                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <button type="button" onclick="openTab(event, 'Part_Required');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="position-relative form-group">
                                                        <!-- <button type="button" onclick="openTab(event, 'Part_Required');" class="mt-2 btn btn-danger" style="float:left;">Previous</button> -->
                                                        <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
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

                                            <div class="col-md-2">
                                                <div class="position-relative form-group">
                                                <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                                                </div>
                                            </div>

                                            <div class="col-md-4f">
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
        <?php if(empty($DataArr) || empty($DataArr2)){ ?>
        <form method="post" id="observation_closure_code_save" name="observation_closure_code_save" action="vendor-closure-code-save-observation">                                         
            <div class="col-md-6">
                <div class="position-relative form-group">
                    <label for="examplePassword11" class="">Closure Codes<span style="color: #f00;">*</span></label>
                   
                    
                   
                        <select id="closure_codes" name="closure_codes" class="form-control"  required="" size="5">
                            <option value="">Select</option>
                            <?php $i = 1;?>
                            @foreach($closure_code as $closure)
                            <option value="{{$closure->closure_code}}" <?php if($data['closure_codes'] == $closure->closure_code) { echo 'selected';} ?>>{{$i++}} - {{$closure->closure_code}}</option>
                            @endforeach

                            <!-- <option value="01 - IW Repairable Product but Complete unit replaced" <?php if($data['closure_codes']=='01 - IW Repairable Product but Complete unit replaced') { echo 'selected';} ?>>01 - IW Repairable Product but Complete unit replaced</option>
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
                            <option value="24 - Dealer (Broken & indused defect) - Cost sharing approval received from Management side" <?php if($data['closure_codes']=='24 - Dealer (Broken & indused defect) - Cost sharing approval received from Management side') { echo 'selected';} ?>>24 - Dealer (Broken & indused defect) - Cost sharing approval received from Management side</option> -->
    
                        </select> 
    
                    
                </div>    
            </div>                                       
            
            <div class="form-row">     
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <button type="button" onclick="openTab1('', 'sp');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                        </div>
                    </div>
    
                    <div class="col-md-2">
                        <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                        <button type="button" onclick="save_closure_code_return()"  class="mt-2 btn btn-primary" >Save</button>
                    </div>
                    <div class="col-md-2">
                    
                        <a class="mt-2 btn btn-warning" href="vendor-view-complaint" role="button">Exit</a>
                    </div>
                    
    
    
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                        <button type="button" onclick="openTab1('DSS', 'DSS');" class="mt-2 btn btn-success" style="float:right;">Next</button>
                        </div>
                    </div>
    
            </div>
        </form>
        <?php } ?>
    </div>
    
    
    {{-- end closure codes --}}
    
    {{-- start Delivery status --}}
    
    <div id="DSS" class="tabFormcontent">
        <!-- <form method="post" id="observation_delivery_status_save" name="observation_delivery_status_save" action="vendor-closure-code-save-observation1">                                         
            <div class="col-md-6">
                <div class="position-relative form-group">
                    <div id="sch_date1"></div>

                    
                </div>    
            </div>                                        -->
            <table style="width:100%;background: #8c8c8c;">
    <tr>
        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Delivery Date</b></td>
        <td style="background:#ffa500;text-align: center;font-size: 18px;color: black;"><b>Remarks</b></td>
    </tr>
    <tr>
        <td style="height:200px;width:500px;">
            <div id="sch_date1"></div>
            
            <div>
                <table border="2" style="font-size:16px;width:100%;">
                    <tr>
                        <th style="background:#ffa500;color:black;">Remarks</th>
                        <td>
                            <input type="text"  id="delivery_remarks" style="background:null;width:100%;">
                            <input type="hidden" id="delivery_date" value="">
                        </td>
                        <td>
                        <button type="button" style="background:#ffa500;color:black;width:100%;" class="btn btn-secondary" onclick="save_delivery_time()">Apply</button>
                        </td>
                    </tr>
                </table>
            </div>                                                      

        </td>

        <td style="vertical-align:top;margin-top: 10px;">
            <br>
            <?php $history_json = $data['se_del_history'];?>
            <div style="height:280px;width:100%;overflow:auto;padding:3%">
            <table style="font-size:14px;width:100%;" id="delivery">
            <?php
            $history_arr = json_decode($history_json,true);
            $bg_color = array('0'=>'#b9f2ff','1'=>'#008b8b');
            $index_bg = 0;
            //print_r($history_arr);
            foreach($history_arr as $his)
            {
                    $index_bg++;
                    $index_cl = $bg_color[$index_bg%2];
                        $entry_date = strtotime($his['se_del_date']);
                        $entry_date_str = date('d/m/Y',$entry_date);
                        $entry_time_str = date('h:i A',$entry_date);

                        
            
                        $job_date = strtotime($his['delivery_date']);
                        $job_date_str = date('d/m/Y',$job_date);
                        //$job_time_str = date('h:i A',$job_date);
                    
                        $user = $his['user'];
                echo "<tr style=\"background:$index_cl;\"><td>";     
                        echo "<b>$entry_date_str at $entry_time_str by $user --</b> Delivery Date  $job_date_str";
                echo '</td></tr>';
                // echo "<tr style=\"background:$index_cl;\"><td>";
                //     echo "for $job_date_str";
                // echo '</td></tr>';
                echo "<tr style=\"background:$index_cl;\"><td>";
                        $reason = $his['se_del_remarks'];
                        echo "<b>Remarks -- </b>$reason";
                echo '</td></tr>';
                
            }?>
            </table>
            </div>
        </td>
    </tr>
</table>
            
            <div class="form-row">     
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <button type="button" onclick="openTab1('CC', 'CCs');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                        <!-- <a href="vendor-tag-view" class="mt-2 btn btn-danger" data-original-title="" title="">Exit</a> -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                        <button type="button" onClick="location.href='vendor-view-complaint'"  class="mt-2 btn btn-danger" style="float: right;">Exit</button>
                    </div>
                         
            </div>
        </form>
    </div>
    
    {{-- end Delivery Status --}}
                    </div>                                       
                        <input form="observation_save" type="hidden" name="tag_ type" value="<?php echo $tag_type;?>" />
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

function order_apply(part_id)
{
    $('#scc').html("");
    $('#err').html("");
    $.post('accept-order',{part_id:part_id}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {
            //$('#scc').html('<h5><font color="green">Part '+obj.part_id+' Apply Successfully.</font></h5>');
            //$('#td'+tagId).html('<a onclick="job_reject('+"'"+tagId+"'"+');" href="#">Reject</a>');
            $('#td'+part_id).html("Apply Successfully");
            //$('#tr'+part_id).remove();
        }
        else
        { 
            $('#err').html('<h5><font color="red"> Part Already Apply</font></h5>');
        }
        
    }); 
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


// dealer details

function save_dealer_return()
{
    
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#dealer_save_observation");
    var formData = new FormData(form[0]);
 
    var a = 1;

     $('.removeid').remove();

          if($('#DealerNameCL').val()=='')
          {
            a = 0;
            $('#DealerNameCL').after('<span style="color:red" class="removeid">Please Fill Dealer Name</span>');
              
          }

          if($('#LocationCL').val()=='')
          {
            a = 0;
            $('#LocationCL').after('<span style="color:red" class="removeid">Please Fill Location</span>');
              
          }

          if($('#state_cl').val()=='')
          {
            a = 0;
            $('#state_cl').after('<span style="color:red" class="removeid">Please Select State</span>');
              
          }
          
          if($('#pincode').val()=='')
          {
            a = 0;
            $('#pincode').after('<span style="color:red" class="removeid">Please Select Pincode</span>');
              
          }

          if($('#Customer_Name_CL').val()=='')
          {
            a = 0;
            $('#Customer_Name_CL').after('<span style="color:red" class="removeid">Please Fill Customer Name</span>');
              
          }

          
          if($('#Contact_No_CL').val()=='')
          {
            a = 0;
            $('#Contact_No_CL').after('<span style="color:red" class="removeid">Please Fill Contact No. </span>');
            return false;
              
          }

          
            //var phoneno = /^[1-9]{1}[0-9]+/;
            var phoneno = /^[1-9]\d{9}$/;
            var matchpt = document.getElementById('Contact_No_CL').value.match(phoneno);
           

        if(!matchpt){
            a = 0;
            $('#Contact_No_CL').after('<span style="color:red" class="removeid">Please Fill Correct Number</span>');
            return false;
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
                    //return false;
                    if(result == '2')
                    {
                        alert('No details changed by user');                   
                        
                    }
                    else if(result == '1')
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


// end dealer details

// vehicle details

function save_vehicle_return()
{
    
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#vehicle_save_observation");
    var formData = new FormData(form[0]);
    
    var a = 1;

    $('.removeid').remove();

        if($('#vehicle_sale_date').val()=='')
        {
        a = 0;
        $('#vehicle_sale_date').after('<span style="color:red" class="removeid">Please Select Vehicle Sale Date</span>');
            
        }

        if($('#vin_no').val()=='')
        {
        a = 0;
        $('#vin_no').after('<span style="color:red" class="removeid">Please Fill Vin No</span>');
            
        }

        if($('#mielage').val()=='')
        {
        a = 0;
        $('#mielage').after('<span style="color:red" class="removeid">Please Fill Mielage Km. / PDI</span>');
            
        }

        if($('#warranty_type_cl').val()=='')
        {
        a = 0;
        $('#warranty_type_cl').after('<span style="color:red" class="removeid">Please Fill Warranty Status</span>');
            
        }

        if($('#Product_cl').val()=='')
        {
        a = 0;
        $('#Product_cl').after('<span style="color:red" class="removeid">Please Select Vehicle Model</span>');
            
        }


        if($('#Product').val()=='')
        {
        a = 0;
        $('#Product').after('<span style="color:red" class="removeid">Please Select Model No. </span>');
            
        }


        if($('#Model_cl').val()=='')
        {
        a = 0;
        $('#Model_cl').after('<span style="color:red" class="removeid">Please Select Part Number</span>');
            
        }

        if($('#system_sw_version').val()=='')
        {
        a = 0;
        $('#system_sw_version').after('<span style="color:red" class="removeid">Please Fill System SW Version</span>');
            
        }



        if($('#man_ser_no').val()=='')
        {
        a = 0;
        $('#man_ser_no').after('<span style="color:red" class="removeid">Please Fill Manufacturer Serial Number</span>');
            
        }

        
        
        // var date = new Date();
        // var selectdate = document.getElementById('Bill_Purchase_Date').value;
        // var sel_arr = selectdate.split('-');
        // var new_sel_arr = sel_arr[2]+'/'+sel_arr[1]+'/'+sel_arr[0]  ;          
        
        // var js_selecteddate = new Date(new_sel_arr);    

        // if(js_selecteddate > date)
        // {
            
        //     $('#errorname').fadeIn().text("Please enter a valid Bill Purchase Date");
        //             setTimeout(function() {
        //                 $('#errorname').fadeOut("slow");
        //             }, 5000 );

        //     return false;
            
        // }
        
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
                    if(result == '2')
                    {
                        alert('No details changed by user');                   
                        
                    }
                    else if(result == '1')
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

// end vehicle details

function save_complaint_return()
{
    $("#tat").prop("disabled", false);
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#complaint_save_observation");
    var formData = new FormData(form[0]);
    
    var a = 1;
    
    $("#tat").prop("disabled", true);

    $('.removeid').remove();

        if($('#ccsc_cl').val()=='')
        {
        a = 0;
        $('#ccsc_cl').after('<span style="color:red" class="removeid">Please Fill Customer Complaint</span>');
            
        }

        if($('#job_card').val()=='')
        {
        a = 0;
        $('#job_card').after('<span style="color:red" class="removeid">Please Select Job Card</span>');
            
        }

        if($('#videos').val()=='')
        {
        a = 0;
        $('#videos').after('<span style="color:red" class="removeid">Please Select Videos</span>');
            
        }

        if($('#crf').val()=='')
        {
        a = 0;
        $('#crf').after('<span style="color:red" class="removeid">Please Select CRF</span>');
            
        }

        if($('#ftir').val()=='')
        {
        a = 0;
        $('#ftir').after('<span style="color:red" class="removeid">Please Select FTIR</span>');
            
        }


        if($('#ftir_no').val()=='')
        {
        a = 0;
        $('#ftir_no').after('<span style="color:red" class="removeid">Please Fill Ftir No. </span>');
            
        }


        if($('#supr_analysis').val()=='')
        {
        a = 0;
        $('#supr_analysis').after('<span style="color:red" class="removeid">Please Fill Supreme 1st Analysis</span>');
            
        }

        if($('#remarks').val()=='')
        {
        a = 0;
        $('#remarks').after('<span style="color:red" class="removeid">Please Fill Remarks</span>');
            
        }



        if($('#issue_type').val()=='')
        {
            a = 0;
            $('#issue_type').after('<span style="color:red" class="removeid">Please Select Type of Issue Suspected</span>');
            
        }
        if($('#issue_cat').val()=='')
        {
            a = 0;
            $('#issue_cat').after('<span style="color:red" class="removeid">Please Fill Issue Category</span>');
            
        }
        if($('#mobile_handset_model').val()=='')
        {
        a = 0;
        $('#mobile_handset_model').after('<span style="color:red" class="removeid">Please Fill Mobile handset model</span>');
            
        }

        if($('#visit_type').val()=='')
        {
            a = 0;
            $('#visit_type').after('<span style="color:red" class="removeid">Please Select Visit Type</span>');
            return false;
        }
        if($('#part_replace').val()=='')
        {
            a = 0;
            $('#part_replace').after('<span style="color:red" class="removeid">Please Select Part Replace</span>');
            return false;
        }
        if($('#part_replace').val()==='Yes' && $('#part_replace_date').val()==='')
        {
            a = 0;
            $('#part_replace_date').after('<span style="color:red" class="removeid">Please Fill Part Replace Date</span>');
            return false;
        }
        if($('#Job_Status').val()==='')
        {
            a = 0;
            $('#Job_Status').after('<span style="color:red" class="removeid">Please Select Job Status</span>');
            return false;
        }

        if($('#dispatch_date').val()==='')
        {
            a = 0;
            $('#Job_Status').after('<span style="color:red" class="removeid">Please Fill Dispatch Date.</span>');
            return false;
        }

        else if($('#tat_delay_remarks').val()==='')
        {
            a = 0;
            $('#tat_delay_remarks').after('<span style="color:red" class="removeid">Parts Fill TAT Delay Remarks.</span>');
            return false;
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
                    if(result == '2')
                    {
                        alert('No details changed by user');
                        
                    }
                    else if(result == '1')
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
            if(result == '2')
            {
                alert('No details changed by user');                   
                
            }
            else if(result == '1')
            {                        
                alert('Closure Code Updated Successfully.');                        
            }
            else
            {
                alert('Closure Code Updatation Failed.');                        
            }    
               
            //    if(result==='1')
            //    {
            //        alert('Closure Code Updated Successfully.');
             
                   
            //    }
            //    else
            //    {
            //        alert('Closure Code Updatation Failed.');
                   
            //    }
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
                //console.log(data);
              //alert(obj); // show response from the php script.

              json_obj = data;
              var img = json_obj.image; 
              var type = json_obj.file_type;


                if(data)
                {
                    remove_img(id);
                    alert('Image saved Successfully.');
                    $('#wrrn_cntr256').prepend('<div class="form-row"><div class="col-md-6"><div class="position-relative form-group"><label for="examplePassword11" class=""><b>Image Type:'+type+'</b></label></div><div class="position-relative form-group" id="downloadbtn"></div></div><div class="col-md-6"><img id="'+id+'" src="'+img+'" class="img-fluid"></div></div>');
                    $('#downloadbtn').prepend("<button type='button' id='download' style='width:100%' class='btn btn-primary'>Download</button>");

                        $("#download").on("click", function () {
                            var imagePath = img;
                            var fileName = "img.jpg";
                            download(imagePath, fileName);  
                        });
                   
                }
                else
                {
                    alert('Image already Saved');
                    //$('#remove_wrrn').hide();
                    remove_img(id);
                    
                }
                
            }
            });    

        return false;
    //}

}


function download(imagePath, fileName) {
              
    var a = document.createElement('a');
        a.href = imagePath;
        a.download = fileName;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
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

  function save_video(id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var form = $("#form" + id);
    var formData = new FormData(form[0]);

    $.ajax({
        type: "POST",
        url: 'save-video', // Adjust the URL accordingly
        data: formData,
        enctype: 'multipart/form-data',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            // console.log(data);

            json_obj = data;
            var video = json_obj.image;
            var type = json_obj.file_type;

            if (data) {
                remove_video(id);
                alert('Video saved Successfully.');
                var source_id = id;

                $('#wrrn_cntr256').prepend('<div class="form-row"><div class="col-md-6"><div class="position-relative form-group"><label for="examplePassword11" class=""><b>Video Type:' + type + '</b></label></div><div class="position-relative form-group" id="downloadbtn"></div></div><div class="col-md-6"><video id="' + id + '" controls class="img-fluid"><source src="' + video + '" type="video/mp4"></video></div></div>');
                $('#downloadbtn').prepend("<button type='button' id='download' style='width:100%' class='btn btn-primary'>Download</button>");

                $("#download").on("click", function () {
                    var videoPath = video;
                    var fileName = "video.mp4";
                    download(videoPath, fileName, source_id);
                });

            } else {
                alert('Video already Saved');
                //$('#remove_video').hide();
                remove_video(id);
            }
        }
    });

    return false;
}


</script>


@endsection