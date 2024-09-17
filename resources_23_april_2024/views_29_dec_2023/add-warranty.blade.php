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
                            <h5 class="card-title">Warranty Status</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('sypt_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','sypt_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="sypt_form" method="post" action="save-warranty" style="display:none;">
                                
                                

                                

                                

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Warranty Status  <font color="red">*</font></label>
                                            <input name="warranty_name" id="warranty_name" placeholder="Warranty Status" type="text"  class="form-control" required="">
                                        </div>
                                    </div>
                                </div>
                                 
                                 

                               
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" class="mt-2 btn btn-danger" onclick="form_toggle('table_id','sypt_form');"  title="Cancel">Cancel</a>
                            </form>
                                 
                        </div>    
                            <div class="card-body">    
                                <table class="table table-bordered data-table" id="table_id">
                                <thead>
                                  <tr>	
                                      <th>S.No.</th> 
                                      <th>Warranty Name</th>
                                      
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
                                          <td><?php echo $Data->warranty_name;?></td>
                                           
                                          <td class="Status">@if($Data->warranty_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                          <td class="Officer"><a href="edit-warranty?warranty_id=<?php echo base64_encode($Data->warranty_id); ?>" >Edit</a></td>
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
 
 
</script>

@endsection
