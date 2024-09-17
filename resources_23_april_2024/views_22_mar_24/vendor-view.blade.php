@extends('layouts.app')

@section('content')



<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">View Vendor</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Vendor Name</th>
                                    <th>Mobile No</th>
                                    <th>Email ID</th>
                                    <th>PAN No.</th>
                                    <th>GST No.</th>
                                    <th>Status</th>
                                    
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @php $i = 0; @endphp
                                    @foreach($DataArr as $Data)
                                  
                                  
                                 <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$Data->Vendor_Name}}</td>
                                    <td>{{$Data->Vendor_MobileNo}}</td>
                                    <td class="emailid">{{$Data->Vendor_EmailId}}</td>
                                    <td class="emailid">{{$Data->Vendor_PanNo}}</td>
                                    <td class="Officer">{{$Data->Vendor_GSTNo}}</td>
                                    
                                    <td class="Status">@if($Data->Vendor_Status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="vendor-edit?Vendor_Id=<?php echo base64_encode($Data->Vendor_Id); ?>" >Edit</a></td>
                                 </tr>
                                 @endforeach;
                                
                              </tbody>
                           </table>
                       
                            <div class="form-group text-right">
<!--                              <a href="vendor-export" class="btn btn-success btn-grad btnr1" data-original-title="" title="">Export Records</a>-->

                              <a href="/home" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Exit</a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- /.inner -->
            </div>
            <!-- /.outer -->
         </div>
    </div> 
<script>
    
    
    
    function validate_vendor_creation(id)
{
    var Vendor_EmailId = $('#Vendor_EmailId'+id).val();
    var Vendor_MobileNo = $('#Vendor_MobileNo'+id).val();
    
    
    
    var checked = false;
    try{
        checked = document.querySelector('input[name = "Vendor_Status'+id+'"]:checked').value;
    }
    catch(err)
    {
        checked = false;
    }
    
    if(Vendor_EmailId=='')
    {
        $('#Vendor_EmailId'+id).after("Please Fill Email Id");
        $('#Vendor_EmailId'+id).focus();
        return false;
    }
    else if(Vendor_MobileNo=='')
    {
        $('#Vendor_MobileNo'+id).after("Please Fill Mobile No.");
        $('#Vendor_MobileNo'+id).focus();
        return false;
    }
    
    else if(checked=='')
    {
        $('#Vendor_Status'+id).after("Please choose Status");
        $('#Vendor_Status'+id).focus();
        return false;
    }
    
    
    var Form =  $("#frm"+id)[0];
    var formData = new FormData(Form);    
    
    
        $.ajax({
        url: "vendor-update",
        type: 'POST',
        enctype: 'multipart/form-data',
        data: formData,
        success: function (data) {
            $('#remarks'+id).html(data);
        },
        cache: false,
        contentType: false,
        processData: false
    });
        return false; 
    
    

    
    return false;
}



function FillBilling(id)
{
    var billingtoo = 'input[name = "billingtoo'+id+'"]:checked';
    //alert(billingtoo);
    var checked = false;
    try{
        checked = document.querySelector(billingtoo).value;
    }
    catch(err)
    {
        checked = false;
    }
    if(checked)
    {
        $('#Vendor_Permanent_Address1'+id).val($('#Vendor_Communication_Address1'+id).val());
        $('#Vendor_Permanent_Address2'+id).val($('#Vendor_Communication_Address2'+id).val());
        $('#Vendor_Permanent_Address3'+id).val($('#Vendor_Communication_Address3'+id).val());

        $('#Vendor_Permanent_State'+id).val($('#Vendor_Communication_State'+id).val());
        $('#Vendor_Permanent_Pincode'+id).val($('#Vendor_Communication_Pincode'+id).val());
    }
    else
    {
//        $('#Vendor_Permanent_Address1'+id).val('');
//        $('#Vendor_Permanent_Address2'+id).val('');
//        $('#Vendor_Permanent_Address3'+id).val('');
//
//        $('#Vendor_Permanent_State'+id).val('');
//        $('#Vendor_Permanent_Pincode'+id).val('');
    }
}
    
function checkMobileNumber(val,evt)
{    
    var charCode = (evt.which) ? evt.which : event.keyCode
	
    if (charCode> 31 && (charCode < 48 || charCode > 57)  || (val=='e' || val.length>=10))
    {            
            return false;
    }
    return true;
}
 function checkPinNumber(val,evt)
{    
    var charCode = (evt.which) ? evt.which : event.keyCode
	
    if (charCode> 31 && (charCode < 48 || charCode > 57)  || (val=='e' || val.length>=6))
    {            
            return false;
    }
    return true;
} 
function reloadPage()
{
    location.reload(true);
}
function alphanum(val,evt){
      var charCode = (evt.which) ? evt.which : event.keyCode;
	
    if( /[^a-zA-Z. ]/.test( val ) ) {
       return false;
    }
    else if( /[^a-zA-Z. ]/.test( String.fromCharCode(charCode) ) )
    {
     return false;   
    }
    return true;     
   }
   
   $('#table_id').DataTable( );
   
</script>
@endsection
