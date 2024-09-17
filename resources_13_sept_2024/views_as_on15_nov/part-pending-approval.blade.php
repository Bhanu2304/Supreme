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
                            <h5 class="card-title"> Spare Part Allocation</h5>
                            
                            <form method="post" action="save-part-approval-multiple">
                            
                               
                                
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                 
                                <?php 
                                        if(Session::has('st'))
                                        {
                                            $st = Session::get('st');
                                            echo '<table border="1">';
                                            echo '<tr><th>Allocated</th><td>'.$st['part']['Allocated'].'</td></tr>';
                                            echo '<tr><th>Job Case Close</th><td>'.implode(',',$st['case']['case_close']).'</td></tr>';
                                            echo '<tr><th>Stock Not Available</th><td>'.implode(',',$st['part']['Not Available']).'</td></tr>';
                                            echo '<tr><th>HO Request</th><td>'.$st['part']['ho request'].'</td></tr>';
                                            echo '<tr><th>Case Pending</th><td>'.implode(',',$st['case']['case_pending']).'</td></tr>';
                                            echo '</table>';
                                        }
                                ?>
                                 
                                 <br>
                                 
                                <h5 class="card-title">
                                    <button type="submit" name="stock_action" value="Approve" class="btn btn-success">Approve</button> 
                                    <button type="submit" name="stock_action" value="Reject" class="btn btn-danger">Reject</button>
                                </h5>
                                 
                                <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                   <tr>
                                      <th>Sr.No</th>
                                      <th><input type="checkbox" id="approve_all"  value="1" onclick="check_all('approve_all','approve');" />&nbsp; <a href="#"> Approve All</a></th>
                                      <th><input type="checkbox" id="request_all"  value="1" onclick="check_all('request_all','request');" />&nbsp; <a href="#"> Request To HO</a></th>
                                      <th>Status</th>
  <!--                                    <th>Center Name</th>-->
                                      <th>Spare Part Name</th>
                                      <th>Part No</th>
                                      <th>HSN Code</th>


                                      <th>Job Id</th>
                                   </tr>
                                </thead>
                                    <tbody>
                                        @php $i = 0; @endphp
                                          @foreach($job_part_pending as $Data)


                                       <tr id="row_{{$Data->spare_id}}">
                                          <td>{{++$i}}</td>
                                          <td><input type="checkbox" name="approve[]" value="{{$Data->spare_id}}" />&nbsp;<a href="#" onclick="approve_part('{{$Data->spare_id}}')"> Approve</a></td>
                                          <td>
                                              <input type="checkbox" name="request[]" value="{{$Data->spare_id}}" />&nbsp;
                                              <?php if($Data->request_to_ho=='1') { ?>
                                              <font color="green"> Requested</font>
                                              <?php } else { ?>
                                              <a href="#" onclick="request_ho('{{$Data->spare_id}}','{{$i}}');">Request </a>
                                              <?php } ?>
                                          </td>
                                          <td><font color="<?php if($Data->stock_status=='stock not available') echo 'red'; else 'green'; ?>">{{$Data->stock_status}}</font></td>
                                          <td>{{$Data->part_name}}</td>
                                          <td>{{$Data->part_no}}</td>
                                          <td>{{$Data->hsn_code}}</td>
                                          <td><a href="#">{{$Data->tag_id}}</a></td>
                                       </tr>
                                       @endforeach

                                    </tbody>
                                </table> 
                                <button type="submit" name="stock_action" value="Approve" class="btn btn-success">Approve</button> 
                                <button type="submit" name="stock_action" value="Reject" class="btn btn-danger">Reject</button>
                            </form>     
                        </div>    
                           
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <script>
   
   function approve_part(spare_part)
   {
       $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'save-part-approval',
              method: 'post',
              data: {
                 spare_part: spare_part 
              },
              success: function(result){
                  if(result=='1')
                  {
                      $('#row_'+spare_part).html("Approved Successfully.");
                     // alert('row_'+spare_part);
                  }
                  else if(result=='2' )
                  {
                      alert('Inventory to Part has been finished.');
                  }
              }});
   }
   
   function request_ho(spare_id,srno)
   {
       $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'ho-part-request',
              method: 'post',
              data: {
                 spare_id: spare_id,
                 srno:srno
              },
              success: function(result){
                  if(result!='2')
                  {
                      $('#row_'+spare_id).html(result);
                     // alert('row_'+spare_part);
                  }
                  else if(result=='2' )
                  {
                      alert('Request To HO For Part Pending failed.');
                  }
              }});
   }
   
    function check_all(all_check,check_all)
    {
        var chk_all = document.getElementById(all_check);
        var check_flag =  true;
        if(chk_all.checked==true)
        {
            check_flag =  true;
        }
        else
        {
            check_flag =  false;
        }
        
        
        var chk_arr = document.getElementsByName(check_all+'[]');
        var chklength = chk_arr.length;             
        var k=0;
        for(k=0;k< chklength;k++)
        {
            chk_arr[k].checked = check_flag;
        } 
    }
    
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
 
 $('#table_id').DataTable( );
</script>

@endsection
