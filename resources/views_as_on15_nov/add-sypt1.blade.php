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
                        <div class="card-body">
                            <h5 class="card-title">Symptom</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('sypt_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','sypt_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="sypt_form" method="post" action="save-symptom" style="display:none;">
                                
                                

                                

                                

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Option  <font color="red">*</font></label>
                                            <input name="field_name" id="field_name" placeholder="Option" type="text"  class="form-control" required="">
                                        </div>
                                    </div>
                                </div>
                                 
                                 <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Code <font color="red">*</font></label>
                                            <input name="field_code" id="field_code" placeholder="Code" type="text"  class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Type </label>
                                            <input name="field_type" id="field_type" placeholder="Type" type="text"  class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Remark </label>
                                            <input name="remark" id="remark" placeholder="Remark" type="text"  class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" class="mt-2 btn btn-danger" onclick="form_toggle('table_id','sypt_form');"  title="Cancel">Cancel</a>
                            </form>
                                 
                        </div>    
                            <div class="card-body">    
                                <table class="table table-striped table-bordered" id="table_id">
                                <thead>
                                  <tr>	
                                      <th>S.No.</th> 
                                      <th>Option</th>
                                      <th>Code</th>
                                      <th>Type</th> 
                                      <th>Remark</th> 
                                      <th>Status</th>
                                      <th>Action</th> 
                                  </tr>
                                </thead>
                                <tbody>
                                   <?php  
                                      //$Select = "SELECT * FROM `tbl_agent` WHERE ChannelType = 'Agent'";
                                      //$Query  = mysql_query($Select); 
                                      $i = 1;
                                    foreach($DataArr as $Data)
                                    {
                                   ?> 
                                          <tr> 
                                          <td><?php echo $i++;?></td>
                                          <td><?php echo $Data->field_name;?></td>
                                           <td><?php echo $Data->field_code;?></td>
                                          <td><?php echo $Data->field_type; ?></td>
                                          <td><?php echo $Data->remark; ?></td>
                                          <td class="Status">@if($Data->sypt_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                          <td class="Officer"><a href="edit-symptom?symptom_id=<?php echo base64_encode($Data->symptom_id); ?>" >Edit</a></td>
                                          </tr> 
                              <?php }  ?>
                                </tbody>
                            </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <script>
     
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
 
 function delete_record(id,url)
{
    //alert(url); return false;
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: url,
              method: 'post',
              data: {
                 Id: id 
              },
              success: function(result){
                  var msg = '';

                  if(result=='succ')
                  {
                      msg="Contact Deleted Successfully";
                      alert(msg);
                      location.reload();
                  }
                  else if(result=='unsucc')
                  {
                      msg="Contact Not Deleted.Contact To Admin";
                      alert(msg);
                  }
                  else if(result=='exist')
                  {
                      msg="Contact Have Records. Can't Be Deleted";
                      alert(msg);
                  }
              }});
}


$('#table_id').DataTable( );
</script>

@endsection
