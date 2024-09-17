@extends('layouts.app')
@section('content')


<script>
                                    

menu_select('{{$url}}');    

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
                  $('#pincode').html(result);
              }});
 }

function get_state(region_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-state-by-region-id',
              method: 'post',
              data: {
                 region_id: region_id 
              },
              success: function(result){
                  $('#state_id').html(result);
              }});
 }

function get_asc_code(asc_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-asc-code-by-asc-id',
              method: 'post',
              data: {
                 asc_id: asc_id 
              },
              success: function(result){
                  $('#asc_name').val(result);
              }});
 }

function get_ticket_date(ticket_no)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-ticket-date-by-ticket-no',
              method: 'post',
              data: {
                 ticket_no: ticket_no 
              },
              success: function(result){
                  $('#ticket_date').val(result);
              }});
 }
 
 function reset_form()
 {
     //$("#search_form")[0].reset();
     $("#search_form").trigger('reset');
 }

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

 
</script>
<?php if(Session::get('UserType')=='ServiceCenter') {   ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {
   'packages': ['bar']
 });
 google.charts.setOnLoadCallback(drawTAT);
 google.charts.setOnLoadCallback(drawRepairs);

 function drawTAT() {
   var data = google.visualization.arrayToDataTable([
     ['Months'<?php echo ",'".implode("','",$tat_brand_arr),"',{ role: 'annotation' }"; ?>]
     <?php foreach($tat_month_arr as $mr) { echo ','; ?>
         
    [<?php echo "'$mr'"?><?php $new_arr = array(); foreach($tat_brand_arr as $br) {  $new_arr[]=$tat_data[$mr][$br];  ?><?php }  echo ",'".implode("','",$new_arr),"',''";  ?>]<?php } ?>
   ]);
   var options = {
     chart: {
       title: 'All Category - TAT %',
       //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
     },
     legend: { position: 'top', maxLines: 3 },
     bars: { groupWidth: '75%' }, // Required for Material Bar Charts.
     hAxis: {
       format: 'percent'
     },
     height: 400,
     series: {
    0:{color:'yellow'},
    1:{color:'orange'},
    2:{color:'green'},
    3:{color:'blue'}
    
  },
     //colors: ['#1b9e77', '#d95f02', '#7570b3','#7570b3'],
     legend: {position: 'top', maxLines: 3},
     isStacked: true
   };

   var chart = new google.charts.Bar(document.getElementById('barchart_tat'));

   chart.draw(data, google.charts.Bar.convertOptions(options));

   var btns = document.getElementById('btn-group');

   btns.onclick = function(e) {

     if (e.target.tagName === 'BUTTON') {
       options.hAxis.format = e.target.id === 'none' ? '' : e.target.id;
       chart.draw(data, google.charts.Bar.convertOptions(options));
     }
   }
 }

 function drawRepairs() {
   var data = google.visualization.arrayToDataTable([
     ['Months'<?php echo ",'".implode("','",$rep_brand_arr),"',{ role: 'annotation' }"; ?>]
     <?php foreach($rep_month_arr as $mr) { echo ','; ?>
         
    [<?php echo "'$mr'"?><?php $new_arr = array(); foreach($rep_brand_arr as $br) {  $new_arr[]=$rep_data[$mr][$br];  ?><?php }  echo ",'".implode("','",$new_arr),"',''";  ?>]<?php } ?>
   ]);
   var options = {
     chart: {
       title: 'Total Repairs - % I/W- %Cancel',
       //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
     },
     legend: { position: 'top', maxLines: 3 },
     bars: { groupWidth: '75%' }, // Required for Material Bar Charts.
     hAxis: {
       format: 'percent'
     },
     height: 400,
     series: {
    0:{color:'yellow'},
    1:{color:'orange'},
    2:{color:'green'},
    3:{color:'blue'},
    4:{color:'red'}
  },
     legend: {position: 'top', maxLines: 3},
     isStacked: true
   };

   var chart = new google.charts.Bar(document.getElementById('barchart_repairs'));

   chart.draw(data, google.charts.Bar.convertOptions(options));

   var btns = document.getElementById('btn-group');

   btns.onclick = function(e) {

     if (e.target.tagName === 'BUTTON') {
       options.hAxis.format = e.target.id === 'none' ? '' : e.target.id;
       chart.draw(data, google.charts.Bar.convertOptions(options));
     }
   }
 }     
      
      
    </script>
<?php } ?>



<div class="app-main__outer">
                    <div class="app-main__inner">
                        <div class="app-page-title">
                            <div class="page-title-wrapper">
                                <div class="page-title-heading">
                                    
                                    <div> Dashboard
<!--                                        <div class="page-title-subheading">This is an example dashboard created using build-in elements and components.
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <form id="search_form" method="get" action="dashboard" class="form-horizontal"  >
                           <?php if(Session::get('UserType')=='ServiceCenter' || Session::get('UserType')=='ServiceEngineer') {   ?>
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">State</label>
                                            <select id="state_id" name="state_id" class="form-control" onchange="get_pincode(this.value)">
                                                <option value="">State</option>
                                                <?php if(count($state_master)>1) {   ?>
                                                <option value="All">All</option>
                                                <?php } ?>
                                                @foreach($state_master as $state_id=>$state_name)
                                                    <option value="{{$state_name}}" <?php if( $state_name==$state) 
                                                        { echo 'selected';} ?>>{{$state_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Category </label>
                                        <select id="warranty_category" name="warranty_category" class="form-control" >
                                            <option value="">Select</option>
                                            <option value="Standard warranty">Standard Warranty</option>
                                            <option value="Out Warranty">Out Warranty</option>
                                            <option value="Extended">Extended</option>
                                            <option value="International">International</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type </label>
                                        <select id="service_type" name="service_type" class="form-control"  >
                                            <option value="">Select</option>
                                            <option value="Home Service">Home Service</option>
                                            <option value="Walk in Service">Walk in Service</option>
                                            <option value="Refurbrish">Refurbrish</option>
                                            <option value="Demo & Installation">Demo & Installation</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Job Status </label>
                                        <select id="job_status" name="job_status" class="form-control"  >
                                            <option value="">Select</option>
                                            <option value="Allocated">Allocated</option>
                                            <option value="Begin Repair/ Under Repair">Begin Repair/ Under Repair</option>
                                            <option value="Estimation Created">Estimation Created</option>
                                            <option value="Part Ordered">Part Ordered</option>
                                            <option value="Technical Support Required">Technical Support Required</option>
                                            <option value="Set transfer to Another ASC">Set transfer to Another ASC</option>
                                            <option value="Repair completed">Repair completed</option>
                                            <option value="Pending for deliver">Pending for deliver</option>
                                            <option value="LED delivered">LED delivered</option>
                                            <option value="Defective Created">Defective Created</option>
                                            <option value="Defective received by NPC">Defective received by NPC</option>
                                            <option value="Invoice Generated">Invoice Generated</option>
                                            <option value="Payment settled">Payment settled</option>
                                            <option value="Case Closed">Case Closed</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Pin Code</label>
                                        <input type="number" maxlength="6" id="pincode" name="pincode" placeholder="Pincode" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Job No.</label>
                                            <input class="form-control" type="text" id="job_no" name="job_no" value="<?php echo $job_no;?>" placeholder="Job No.">
                                        </div>
                                    </div>
                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Ticket No.</label>
                                            <input class="form-control" type="text" id="ticket_no" name="ticket_no" value="<?php echo $ticket_no;?>" placeholder="Ticket No.">
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Contact No. </label>
                                            <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Contact No.">
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" autocomplete="off" class="form-control datepicker" >
                                        </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" autocomplete="off" class="form-control datepicker" >
                                    </div>
                                </div>
                                
                                    
                                
                                    
                                
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">&nbsp;</label>
                                            <br>                   
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Search" >
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Download Report" >
                                        </div>
                                    </div>

 

                                 </div>

                           <?php } else { ?>     
                                <div class="form-row">
                                    <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Region</label>
                                            <select id="region_id" name="region_id" class="form-control" onchange="get_state(this.value)">
                                                <option value="">Region</option>
                                                <?php if(count($region_list)>1) {   ?>
                                                <option value="All">All</option>
                                                <?php } ?>
                                                @foreach($region_list as $region)
                                                    <option value="{{$region->region_id}}" <?php if( $region->region_id==$region_id) 
                                                        { echo 'selected';} ?>>{{$region->region_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">State</label>
                                            <select id="state_id" name="state_id" class="form-control" onchange="get_pincode(this.value)">
                                                <option value="">State</option>
                                                <?php if(count($state_master)>1) {   ?>
                                                <option value="All">All</option>
                                                <?php } ?>
                                                @foreach($state_master as $state_id=>$state_name)
                                                    <option value="{{$state_name}}" <?php if( $state_name==$state) 
                                                        { echo 'selected';} ?>>{{$state_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                
                                
                                
                                
                                                <div class="col-md-3">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type </label>
                                                        <select id="service_type" name="service_type" class="form-control"  >
                                                            <option value="">Select</option>
                                                            <option value="Home Service">Home Service</option>
                                                            <option value="Walk in Service">Walk in Service</option>
                                                            <option value="Refurbrish">Refurbrish</option>
                                                            <option value="Demo & Installation">Demo & Installation</option>
                                                        </select>
                                                    </div>
                                                </div>
                                    <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Service Center Name </label>
                                        <select id="asc_code" name="asc_code" onchange="get_asc_code(this.value)" class="form-control" >
                                                <option value="All">All</option>
                                                @foreach($asc_master as $asc)
                                                    <option value="{{$asc->center_id}}" <?php if( $center_id==$asc->center_id) 
                                                        { echo 'selected';} ?>>{{$asc->center_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                    <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Service Center Code </label>
                                        <input type="text" id="asc_name" name="asc_name" class="form-control" >
                                                
                                            
                                    </div>
                                </div>
                                    
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Ticket No.</label>
                                            <input class="form-control" type="text" id="ticket_no" onblur="get_ticket_date(this.value);" name="ticket_no" value="<?php echo $ticket_no;?>" placeholder="Ticket No.">
                                        </div>
                                    </div>
                                    
                                    <!-- <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Ticket Created Date</label>
                                            <input class="form-control" type="text" id="ticket_date" name="ticket_date" value="<?php echo $ticket_date;?>" placeholder="Ticket Created Date">
                                        </div>
                                    </div>     -->
                                    
                                                
                                    <!-- <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Customer Address </label>
                                            <input class="form-control" type="text" id="cust_add" name="cust_add" value="<?php echo $cust_add;?>" placeholder="Customer Address">
                                        </div>
                                    </div> -->
                                

                                
                                
                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Phone Number </label>
                                            <input class="form-control" type="text" id="contact_no" name="contact_no" onkeypress="return checkNumber(this.value,event)" value="<?php echo $contact_no;?>" placeholder="Phone No." maxlength="10">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Pin code </label>
                                            <input class="form-control" type="text" id="pincode" name="pincode" value="<?php echo $pincode;?>"  placeholder="Pin Code">
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" autocomplete="off" class="form-control datepicker" >
                                        </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" autocomplete="off" class="form-control datepicker" >
                                    </div>
                                </div>
                                
                                    
                                
                                    
                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">&nbsp;</label>
                                            <br>                   
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Search Option" >
                                            <button type="reset" id="reset" class="btn btn-primary btn-grad">Reset</button>
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Download In Excel" >
                                        </div>
                                    </div>

 

                                 </div>
                           <?php } ?>     
                        </form>
                            
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>ASC Name</th>
                                    <th>State</th>
                                    <th>Ticket No.</th>
                                    <th>Job No.</th>
                                    <th>Customer Name</th>
                                    <th>Phone No.</th>
                                    <th>Pin Code</th>
                                    
                                    <th>Brand</th>
                                    <th>Model No.</th>
                                    <th>Serial No.</th>
                                    <th>Warranty Category</th>
                                    <th>Service Type</th>
                                    
                                    <th>Job Status</th>
                                    <th>Job PDF</th>
                                    <th>Invoice</th>
                                 
                                   
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            //$record = json_decode($record1,true);
                                            echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$record->center_name.'</td>';
                                            echo '<td>'.$record->State.'</td>';
                                                    //echo '<td>'.$record->ticket_no.'</td>';
                                                    echo '<td><a href="ho-tag-view?TagId='.$record->TagId.'">'.$record->ticket_no.'</a></td>';
                                                    //echo '<td>'.$record->job_no.'</td>';
                                                    echo '<td><a href="edit-tagging-data?TagId='.$record->TagId.'"> '.$record->job_no.'</a></td>';
                                                    echo '<td>'.$record->Customer_Name.'</td>';
                                                    echo '<td>'.$record->Contact_No.'</td>';
                                                    echo '<td>'.$record->Pincode.'</td>';
                                                    echo '<td>'.$record->Brand.'</td>';
                                                    echo '<td>'.$record->Model.'</td>';
                                                    echo '<td>'.$record->Serial_No.'</td>';
                                                    echo '<td>'.$record->warranty_type.'</td>';
                                                    echo '<td>'.$record->service_type.'</td>';
                                                    
                                                    echo '<td>';
                                                    
                                                    if($record->warranty_card=='No' && $record->warranty_card=='No' && $record->observation=='Part Required')
                                                    {
                                                        if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status=='0')
                                                        {
                                                            echo 'Job No. Closed';
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status!='0')
                                                        {
                                                            echo $record->payment_status;
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='1' )
                                                        {
                                                            echo 'Payment Not Made';
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='1'  )
                                                        {
                                                            echo 'Invoice Not Made';
                                                        }
                                                        else if($record->case_close=='1' && $record->part_status=='1')
                                                        {
                                                            echo 'Part Allocation Pending';
                                                        }
                                                        else
                                                        {
                                                            echo 'Observation Pending';
                                                        }
                                                    }
                                                    else
                                                    {
                                                        echo 'Service Not Required';
                                                    }
                                                    
                                                    
                                                    echo '</td>';
                                                    
                                                    echo '<td>';
                                                    
                                                        echo '<a href="view-generate-pdf?TagId='.$record->TagId.'" target="_blank">View </a>';
                                                       echo '<a href="generate-pdf?TagId='.$record->TagId.'">PDF </a>';
                                                    
                                                    echo '</td>';
                                                    
                                                    echo '<td>';
                                                    if($record->case_close=='1' &&  $record->inv_status=='0' )
                                                        {
                                                            echo '<a href="generate-TXInvoice?TagId='.$record->TagId.'">Invoice</a>';
                                                        }
                                                    echo '</td>';
                                                    
                                                    
                                                    //echo '<td>'.$record->Customer_Address.'</td>';
                                                    
                                                    
                                                    
                                            echo '</tr>';
                                        }
                                 
                                 
                                 ?>
                                  
                                 
                                
                              </tbody>
                           </table>
                            
                            
                        </div>
                        
                        <?php if(Session::get('UserType')=='ServiceCenter') {   ?>
                        
                        <div class="row">
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-midnight-bloom">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;" >Total Open Calls &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                                            <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($dialer_open_total)) { ?>
                                                <table  style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($dialer_open_total as $tot)
                                                    {
                                                        
                                                        echo '<th >'. $tot->brand.'</th>'; 
                                                        echo '<th >'.$tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $open_total; ?></span></div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-arielle-smile">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Per day Incoming Calls</div>
                                            <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($tagging_perday_call)) { ?>
                                                <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($tagging_perday_call as $tot)
                                                    {
                                                        
                                                        echo '<th>'. $tot->brand.'</th>'; 
                                                        echo '<th>'.$tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $perday_call; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Per day Completed Calls</div>
                                                <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($dialer_completed_call)) { ?>
                                                <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($dialer_completed_call as $tot)
                                                    {
                                                        
                                                        echo '<th>'. $tot->brand.'</th>'; 
                                                        echo '<th>'.$tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $completed_call; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Total Part Pending Calls</div>
<!--                                            <div class="widget-subheading" style="opacity: 20 !important;">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span>0</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Total Defective Pending Cases</div>
<!--                                            <div class="widget-subheading" style="opacity: 20 !important;">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo 0; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Total Tickets pending for Allocation</div>
                                                <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($dialer_completed_call)) { ?>
                                                <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($dialer_completed_call as $tot)
                                                    {
                                                        
                                                        echo '<th>'. $tot->brand.'</th>'; 
                                                        echo '<th>'.$tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $completed_call; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <?php } else if(Session::get('UserType')=='Admin') { ?>
                        <div class="row">
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-midnight-bloom">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Total Open Calls All India</div>
                                            <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($dialer_open_total)) { ?>
                                                <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($dialer_open_total as $tot)
                                                    {
                                                        
                                                        echo '<th>'. $tot->brand.'</th>'; 
                                                        echo '<th>'. $tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $open_total; ?></span></div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-arielle-smile">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Per day Incoming Calls All India</div>
                                            <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($tagging_perday_call)) { ?>
                                                <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($tagging_perday_call as $tot)
                                                    {
                                                        
                                                        echo '<th>'. $tot->brand.'</th>'; 
                                                        echo '<th>'. $tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $perday_call; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Per day Completed Calls All India</div>
                                                <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($dialer_completed_call)) { ?>
                                                <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($dialer_completed_call as $tot)
                                                    {
                                                        
                                                        echo '<th>'. $tot->brand.'</th>'; 
                                                        echo '<th>'.$tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $completed_call; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Total Part Pending Calls All India</div>
<!--                                            <div class="widget-subheading" style="opacity: 20 !important;">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span>0</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;"> Defective Pending Calls All India</div>
<!--                                            <div class="widget-subheading" style="opacity: 20 !important;">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo 0; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading" style="opacity: 20 !important;">Reservation Pending All India</div>
                                                <div class="widget-subheading" style="opacity: 20 !important;"><?php if(!empty($dialer_completed_call)) { ?>
                                                <table style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                    <?php $tr=1; 
                                                    foreach($dialer_completed_call as $tot)
                                                    {
                                                        
                                                        echo '<th>'. $tot->brand.'</th>'; 
                                                        echo '<th>'.$tot->total.'</th>';
                                                        if($tr%2==0)
                                                        {
                                                            echo '</tr><tr>';
                                                        }
                                                        $tr++;
                                                    }
                                                    ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?></div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $allocation_pending; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <?php } ?>
                        
                        <div class="row" >
                            <div class="col-lg-6">
                                <div class="main-card mb-3 card">
                                    <div id="barchart_tat"></div>
                                </div>
                            </div>    
                            <div class="col-lg-6">
                                <div class="main-card mb-3 card">
                                    <div id="barchart_repairs"></div>
                                </div>
                            </div>
                            </div>
                    </div>

            </div>

@endsection
