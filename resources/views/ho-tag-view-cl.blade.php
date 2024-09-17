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
            //flag_valid = validate_complaint_details();
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
          
          if($('#pincode_cl').val()=='')
          {
            a = 0;
            $('#pincode_cl').after('<span style="color:red" class="removeid">Please Select Pincode</span>');
              
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
        $('#Product_cl').after('<span style="color:red" class="removeid">Please Select Product</span>');
            
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

        
        if($('#vehicle_variant').val()=='')
        {
        a = 0;
        $('#vehicle_variant').after('<span style="color:red" class="removeid">Please Enter Vehicle Model</span>');
            
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
                        alert('Vehicle Details Updated Successfully.');                        
                    }
                    else
                    {
                        alert('Vehicle Details Updatation Failed.');                        
                    }
                   
                }
                });    

            return false;
        }

}

function save_complaint_return(message)
{
 
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#complaint_save_observation");
    var formData = new FormData(form[0]);
    
    var a = 1;


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
        if($('#logs_taken').val()=='')
        {
            a = 0;
            $('#logs_taken').after('<span style="color:red" class="removeid">Please Fill Logs Taken</span>');
            
        }

        if($('#mobile_handset_model').val()=='')
        {
        a = 0;
        $('#mobile_handset_model').after('<span style="color:red" class="removeid">Please Fill Mobile handset model</span>');
            
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
                    if(message){
                        alert(result);
                        window.location.href = "call-registration-form";
                    }else{
                        alert('Customer Details Updated Successfully.'); 
                    }
                    
                }
            });  

            return false;
        }

}
// alert(result);
// window.location.href = "call-registration-form";
function open_gallery_cl()
{
    $('#file-input-cl').trigger('click');
    $('#file-input-type-cl').val('file-input-cl');
}

function open_camera_cl()
{
    $('#file-input-camera-cl').trigger('click');
    $('#file-input-type-cl').val('file-input-camera-cl');
}

function readURLCL(input) 
{
    
    if (input.files && input.files[0]) {
            var size = input.files[0].size;
            size = size/1024;
            var file_type=$('#file_type_cl').val();
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

                    var file_input = $('#file-input-cl').val();
                    //console.log(file_input);
                    var file_demo = $('#'+file_input);
                    var clone_file = file_demo.clone();

                    reader.onload = function (e) {
                    // Hide image preview
                    $('#img_demo_cl').hide();
                    
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
                    $('#img_demo_cl')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(200);
                };

                reader.readAsDataURL(input.files[0]);
                $('#img_demo_cl').show();
                //alert("Please select a valid video file.");
            }
        }
            
    }
}

function file_upload_cl()
{
    var file_type=$('#file_type_cl').val();
    var img = '';
    //console.log(img);
    if(file_type==='Job Card')
    {
        img = 'job_card';
    }
    if(file_type==='CRF')
    {
        img = 'crf';
    }
    if(file_type==='Video')
    {
        img = 'video';
    }
    if(file_type==='FTIR')
    {
        img = 'ftir';
    }
    
    if(file_type==='Video')
    {
        video_move_cl(img);
    }else{
        file_move_cl(img);
    }

    //file_move_cl(img);
    // $('#file-input-cl').val('');
    // $('#file-input-camera-cl').val('');
}

function file_move_cl(img)
{
    var image_no = '';
    var TagId = $('#TagId').val();

    var img_demo = $('#img_demo_cl');
                    
    if(img_demo.attr('src')==='' || img_demo.attr('src')==='#')
    {
        return 0;
    }


    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
    jQuery.ajax({
              url: 'get-image',
              method: 'post',
              data: {
                 img: img,
                 TagId:TagId
              },
              success: function(result){
                // console.log('result');
                // console.log(result);
                  var json = JSON.parse(result);
                    image_no = json.image_no;
                  $('#img_uploadCL').after(json.html);


                    var clone_img = img_demo.clone();
                    clone_img.attr("id",image_no+'_img');

                    $('#'+image_no+'_img_type').after(clone_img);
                    //$('#'+image_no+'_img'+'_disp').append(clone_img);
                    $('#'+image_no+'_cntr').show();
                    //img_demo.attr('src', '#');
                    img_demo.hide();
                    var file_input = $('#file-input-type-cl').val();
                    //console.log(file_input);
                    var file_demo = $('#'+file_input);
                    var clone_file = file_demo.clone();
                    clone_file.attr("id",image_no);
                    clone_file.attr("name",image_no);
                    $('#form'+image_no).append(clone_file);
                    img_demo.attr('src','#');

                    console.log(clone_file);
              }});

              //alert(clone_file);

         

         //console.log(clone);

}

function video_move_cl(video) {
    var video_no = '';
    var TagId = $('#TagId').val();

    var video_demo = $('#videoDemo');

    if (video_demo.attr('src') === '' || video_demo.attr('src') === '#') {
        return 0;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    $.ajax({
        url: 'get-video', 
        method: 'post',
        enctype: 'multipart/form-data',
        data: {
            video: video,
            TagId: TagId
        },
        success: function (result) {
            var json = JSON.parse(result);
            video_no = json.video_no;
            $('#img_uploadCL').after(json.html);

            var clone_video = video_demo.clone();
            clone_video.attr("id", video_no + '_video');

            $('#' + video_no + '_video_type').after(clone_video);
            $('#' + video_no + '_cntr').show();
            video_demo.attr('src', '#');
            video_demo.hide();

            var file_input = $('#file-input-type-cl').val();
            var file_demo = $('#' + file_input);
            var clone_file = file_demo.clone();
            clone_file.attr("id", 'video_'+video_no);
            clone_file.attr("name", 'video_'+video_no);
            console.log(clone_file);
            console.log(video_no);
            $('#form' + video_no).append(clone_file);
            
            video_demo.attr('src', '#');

            console.log(video_demo);
        }
    });

    //console.log(clone_file);
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

    $.ajax({
           type: "POST",
           url: 'save-image-first',
           data: formData,
           enctype: 'multipart/form-data',
           cache:false,
           processData: false,
           contentType: false,
           success: function(data)
           {
            //    alert(data); // show response from the php script.
            if(data==='1')
               {
                   alert('Image saved Successfully.');
                   $('#remove_'+id).remove();
                   $('#save_'+id).remove();
                   //$('#'+id+'_img_disp').remove();
                   $('#form'+id).removeClass("pop");

               }
               else
               {
                   alert('Image already Saved');

               }

           }
         });

    return false;
}

function save_video(id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var form = $("#form" + id);

    var formData = new FormData(form[0]);
    console.log(formData);

    $.ajax({
        type: "POST",
        url: 'save-video-first', 
        data: formData,
        enctype: 'multipart/form-data',
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            
            if (data === '1') {
                alert('Video saved Successfully.');
                $('#remove_' + id).remove();
                $('#save_' + id).remove();
                // $('#'+id+'_video_disp').remove();
                $('#form' + id).removeClass("pop");
            } else {
                alert('Video already Saved');
            }
        }
    });

    return false;
}


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

function validate_image(cl)
{
    var pop = $('.pop').length;
    // alert(pop);
    if($('.pop').length !== 0){
        alert('image is not save ! Please save or remove the image!');
        return false;
    }

    document.getElementById("imgpop"+cl).disabled = true;
    return true;
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
<style>


/* Style the tab */
.tabForm {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}


.tabFormlinksP.active {
    background-color: #3f6ad8;
    color: white;
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

.tabFormcontentP {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}

.tabFormcontentCL {
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
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                            @if(Session::has('error'))<h5><font color="red"> {!! Session::get('error') !!}</font></h5> @endif
                            <div class="tabForm">
                                <button type="button" id="Dealer1" class="tabFormlinksCL" onclick="openTab2(event, 'Dealer')" >Dealer/Customer Details</button>
                                <button type="button" id="Vehicle_Detail1" class="tabFormlinksCL" onclick="openTab2(event, 'Vehicle_Detail')">Vehicle Details</button>
                                <button type="button" id="Complaint_Details1" class="tabFormlinksCL" onclick="openTab2(event, 'Complaint_Details')">Complaint Details</button>
                                <button type="button" id="UploadDocuments1" class="tabFormlinksCL" onclick="openTab2(event, 'UploadDocuments')">Upload Documents</button>
                                
                            </div>

                            <div id="Dealer" class="tabFormcontentCL" style="display:block;">
                                <h5 class="card-title">Dealer/Customer Details</h5>
                                <form method="post" id="dealer_save_observation" name="dealer_save_observation" action="dealer-save-observation">
                                <div class="form-row">

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer Name<span style="color: #f00;">*</span></label>
                                            <input type="text" name="DealerName" value="<?php echo $data['dealer_name']; ?>" placeholder="Dealer Name" id="DealerNameCL" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Location<span style="color: #f00;">*</span></label>
                                        <input name="location" id="LocationCL" placeholder="Location" value="<?php echo $data['Landmark']; ?>" type="text" class="form-control" required></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Region<span style="color: #f00;">*</span></label>
                                        <select name="region_id" id="RegionCL"  class="form-control" required>
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
                                        <select name="state" id="state_cl" data-placeholder="" class="form-control" onchange="get_pincode('pincode_cl',this.value)"  required="">
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
                                            <select onchange="get_area(this.value)" name="pincode" id="pincode_cl" data-placeholder="" class="form-control" required="">
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
                                            <input name="Customer_Name" id="Customer_Name_CL" value="<?php echo $data['Customer_Name']; ?>" placeholder="Contact Person" type="text" class="form-control" required></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Contact No.<span style="color: #f00;">*</span></label>
                                            <input name="Contact_No" id="Contact_No_CL" placeholder="Contact No." value="<?php echo $data['Contact_No']; ?>"  type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required=""  maxlength="10">
                                            <span class="error" id="errornameccl" style="color:red"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-8"></div>


                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <br/>
                                            <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                            <!-- <button type="button" onclick="save_dealer_return()"  class="mt-2 btn btn-primary">Save</button> -->
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <br/>
                                            <button  type="button" onclick="save_dealer_return();openTab3('Dealer', 'Vehicle_Detail');" class="mt-2 btn btn-success">Next</button>
                                        </div>
                                    </div>
                                    
                                </div>
                                </form>
                            </div>

                            <div id="Vehicle_Detail" class="tabFormcontentCL" style="display: none;">
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
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Dealer Type<span style="color: #f00;">*</span></label>
                                        <select  name="product_category" id="product_category" class="form-control" onchange="get_product_cl('_cl',this.value)" required>
                                            <option value="">Select</option>
                                                <?php  foreach($cl_category_master as $cl_category)
                                                    {
                                                        #print_r($cl_category);
                                                        echo '<option value="'.$cl_category->product_category_id.'"';
                                                        if($cl_category->product_category_id==$data['product_category_id']) { echo 'selected';}
                                                        echo '>'.$cl_category->category_name.'</option>';
                                                    }
                                            ?>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Product<span style="color: #f00;">*</span></label>
                                        <select name="Product" id="Product_cl" class="form-control" onchange="get_model_cl('_cl',this.value)" required>
                                            <option value="">Select</option>
                                                <?php  
                                                // foreach($clarion_product_master as $product_id=>$product_name)
                                                //     {
                                                //         echo '<option value="'.$product_id.'"';
                                                //         if($product_name==$data['Product']) { echo 'selected';}
                                                //         echo '>'.$product_name.'</option>';
                                                //     }
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
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Vehicle Model<span style="color: #f00;">*</span></label>
                                            <input  name="vehicle_variant" id="vehicle_variant" value="<?php echo $data['vehicle_variant']; ?>" placeholder="Vehicle Model" type="text" class="form-control" required></div>
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
                                            <button type="button" style="float:left;" onclick="openTab2(event, 'Dealer');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                            <!-- <button type="button" onclick="save_vehicle_return()"  class="mt-2 btn btn-primary">Save</button> -->
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">

                                            <button type="button" style="float:right;" onclick="save_vehicle_return();openTab3('Vehicle_Detail','Complaint_Details');" class="mt-2 btn btn-success" >Next</button>
                                        </div>
                                    </div>

                                </div>
                                </form>
                            </div>

                            <div id="Complaint_Details" class="tabFormcontentCL" style="display: none;">
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
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Logs Taken<span style="color: #f00;">*</span></label>
                                            <select name="logs_taken" id="logs_taken" class="form-control" required>
                                                <option value="">Select</option>
                                                <option value="Yes" <?php if($data['logs_taken']=='Yes') { echo "selected";} ?> >Yes</option>
                                                <option value="No" <?php if($data['logs_taken']=='No') { echo "selected";} ?> >No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Mobile Handset Model </label>
                                            <input name="mobile_handset_model" value="<?php echo $data['mobile_handset_model']; ?>" id="mobile_handset_model"  type="text" class="form-control" >
                                        </div>
                                    </div>
                                    

                                </div>
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <button type="button" style="float:left;" onclick="openTab2(event, 'Vehicle_Detail');" class="mt-2 btn btn-danger" style="float:left;">Previous</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <input  type="hidden" id="TagId" name="TagId" value="<?php echo $data['TagId'];?>" />
                                            <input type="hidden" name="type" value="ExcelFormat">
                                            <!-- <button type="button" onclick="save_complaint_return()"  class="mt-2 btn btn-primary">Save</button> -->
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <button type="button" style="float:right;" onclick="save_complaint_return();openTab2('Complaint_Details','UploadDocuments');" class="mt-2 btn btn-success" >Next</button>
                                            <!-- <a href="javascript:void(0);" onclick="goBack()" class="mt-2 btn btn-danger btn-grad" style="float:right;" data-original-title="" title="">Exit</a> -->
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                            <div id="UploadDocuments" class="tabFormcontentCL" style="height:1800px;display: none;">
                                <div class="float-container" id="img_uploadCL">
                                    <div id="img_cntr" class="float-child">
                                        <div class="float-container">
                                            <div class="float-child">
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group">
                                                        <button type="button" id="camera" onclick="open_camera_cl()" style="width:100%" name="camera" class="mt-2 btn btn-primary">Take Photo</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group">
                                                        <button type="button" style="width:100%" id="gallery" name="gallery" onclick="open_gallery_cl()" class="mt-2 btn btn-primary">Open Gallery</button>
                                                        <input id="file-input-type-cl" value="" type="hidden"  style="display: none;" />
                                                        <input id="file-input-cl" type="file" onchange="readURLCL(this);" name="file_input" style="display: none;" />
                                                        <input id="file-input-camera-cl" type="file" onchange="readURLCL(this);" name="file_input_camera" accept="image/*;capture=camera" style="display: none;" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group">
                                                        <label for="file_type" class="">File Type</label>
                                                        <select id="file_type_cl" name="file_type" class="form-control">
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
                                                        <button type="button" id="upload" name="upload" onclick="file_upload_cl();" class="mt-2 btn btn-primary">Upload</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="float-child" style="height:280px;">
                                            <img id="img_demo_cl" src="#" alt="File" style="display:none" />
                                            <video id="videoDemo" width="320" height="240" controls style="display: none;"></video>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-child" style="width: 500px;"></div>
                                </div>
                                <div id="crf_cntr" style="display:none" class="float-container">
                                    <div  class="float-child" >
                                        <div class="float-container">
                                            <div class="float-child">
                                            <br>
                                            <br>
                                            <br>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: CRF</b></label>
                                                        <button type="button" style="width:100%" id="remove_crf" name="remove_wrrn" onclick="remove_img('crf')" class="mt-2 btn btn-primary">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="float-child" style="height:250px;" id="video_img_disp">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-child" style="width:500px;"></div>
                                </div>
                                <div id="video_cntr" style="display:none" class="float-container">
                                    <div class="float-child" >
                                        <div class="float-container">
                                            <div class="float-child">
                                            <br>
                                            <br>
                                            <br>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Video</b></label>
                                                        <button type="button" style="width:100%" id="remove_prcs" name="remove_video" onclick="remove_img('video')" class="mt-2 btn btn-primary">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="float-child" style="height:250px;" id="video_img_disp"> </div>
                                        </div>
                                    </div>
                                    <div class="float-child" style="width:500px;"></div>
                                </div>
                                <div id="srl_cntr" style="display:none" class="float-container">
                                    <div class="float-child" >
                                        <div class="float-container">
                                            <div class="float-child">
                                            <br>
                                            <br>
                                            <br>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Model & Serial No. Image</b></label>
                                                        <button type="button" style="width:100%" id="remove_srl" name="remove_srl" onclick="remove_img('srl')" class="mt-2 btn btn-primary">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="float-child" style="height:250px;" id="srl_img_disp"></div>
                                        </div>
                                    </div>
                                    <div class="float-child" style="width:500px;"></div>
                                </div>
                                <div id="ftir_cntr" style="display:none" class="float-container">
                                    <div  class="float-child" >
                                        <div class="float-container">
                                            <div class="float-child">
                                            <br>
                                            <br>
                                            <br>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: FTIR </b></label>
                                                        <button type="button" style="width:100%" id="remove_smtm1" name="remove_ftir" onclick="remove_img('ftir')" class="mt-2 btn btn-primary">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="float-child" style="height:250px;" id="ftir_img_disp">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-child" style="width:500px;"></div>
                                </div>
                                <div id="smtm2cl_cntr" style="display:none" class="float-container">
                                    <div  class="float-child" >
                                        <div class="float-container">
                                            <div class="float-child">
                                            <br>
                                            <br>
                                            <br>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Any special Approval</b></label>
                                                        <button type="button" style="width:100%" id="remove_smtm2cl" name="remove_smtm2cl" onclick="remove_img('smtm2_cl')" class="mt-2 btn btn-primary">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="float-child" style="height:280px;" id="smtm2cl_img_disp">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-child" style="width:500px;"></div>
                                </div>
                                <div id="smtm3cl_cntr" style="display:none" class="float-container">
                                    <div  class="float-child" >
                                        <div class="float-container">
                                            <div class="float-child">
                                            <br>
                                            <br>
                                            <br>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class=""><b>File Type: Symptom Image 3</b></label>
                                                        <button type="button" style="width:100%" id="remove_smtm3cl" name="remove_smtm3cl" onclick="remove_img('smtm3cl')" class="mt-2 btn btn-primary">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="float-child" style="height:280px;" id="smtm3cl_img_disp">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-child" style="width:500px;"></div>
                                </div>
                                <div class="clear"></div>
                                <div class="form-row">
                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <button type="button" onclick="openTab2(event, 'Complaint_Details');" class="mt-2 btn btn-danger" >Previous</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <button type="button" style="float:right;" onclick="save_complaint_return('message');" class="mt-2 btn btn-success" >Save</button>
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

    function get_product_cl(div_id,product_category_id)
    {



        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
        jQuery.ajax({
                url: 'get-cl-product-by-category-id',
                method: 'post',
                data: {
                    product_category_id:product_category_id
                },
                success: function(result){
                    $('#Product'+div_id).html(result);
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
