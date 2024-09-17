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
                            <h5 class="card-title">Edit Symptom</h5>
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="sypt_form" method="post" action="update-symptom" >
                                
                                

                                

                                

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Option  <font color="red">*</font></label>
                                            <input name="field_name" id="field_name" placeholder="Option" type="text" value="<?php echo $data['field_name']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>
                                 
                                 <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Code <font color="red">*</font></label>
                                            <input name="field_code" id="field_code" placeholder="Code" type="text" value="<?php echo $data['field_code']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Type </label>
                                            <input name="field_type" id="field_type" placeholder="Type" type="text" value="<?php echo $data['field_type']; ?>" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Remark </label>
                                            <input name="remark" id="remark" placeholder="Remark" type="text" value="<?php echo $data['remark']; ?>" class="form-control" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                     <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="sypt_status" name="sypt_status" class="form-control" required="">
                                            <option value="1" <?php if($data['sypt_status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['sypt_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                 </div>
                                 <a href="add-symptom" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                                <input type="hidden" name="symptom_id" value="<?php echo $data['symptom_id']; ?>" >
                            </form>
                                 
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
</script>

@endsection
