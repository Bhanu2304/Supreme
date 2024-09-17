@extends('layouts.app')
@section('content')
<script type="text/javascript">
   function viewDash() {
      $("#view_brand_form").submit();
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



<div class="app-main__outer">
   <div class="app-main__inner">
      <div class="app-page-title">
         <div class="page-title-wrapper">
            <div class="page-title-heading">
               <div> Brand Dashboard </div>
            </div>
         </div>
      </div>
      <div class="row">
         <form id="view_brand_form" method="get" class="form-horizontal">
            <div class="form-row">
               <div class="col-md-8">
                  <div class="position-relative form-group">
                     <label>Brand</label>
                     <select name="brand_id" class="form-control" required style="width:280px;" onchange="viewDash();">
                           <option value="">Select Brand</option>
                           @foreach($brand_master as $product_id=>$product_name)
                              <option value="{{$product_id}}" <?php if( $product_id==$brand_id) { echo 'selected';} ?>>{{$product_name}}</option>
                           @endforeach
                     </select>
                  </div>
               </div>
            </div>
         </form>
         <?php if(isset($brand_id) && $brand_id != ""){ ?>
         <form id="search_form" method="get" action="brand-dashboard" class="form-horizontal">
            <input type="hidden" name="brand_id" value="{{$brand_id}}">

            <div class="form-row">
               <?php if($brand_id == "4"){ ?>
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Date of Register</label>
                     <input name="date_of_register" id="date_of_register" placeholder="Date of Register" type="text" value="<?php echo $date_of_register; ?>" autocomplete="off" class="form-control datepicker">
                  </div>
               </div>
               <?php } ?>
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Ticket No.</label>
                     <!-- Add the "select2" class to the input element -->
                     <input class="form-control" type="text" id="ticket_no" name="ticket_no" value="<?php echo $ticket_no;?>" placeholder="Ticket No." maxlength="12">
                     <!-- <select class="form-control select2" id="ticket_no" name="ticket_no[]" placeholder="Ticket No."></select> -->
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Job No.</label>
                     <input class="form-control" type="text" id="job_no" name="job_no" value="<?php echo $job_no;?>" placeholder="Job No." maxlength="20">
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Dealer Name</label>
                     <select id="dealer_name" name="dealer_name" class="form-control">
                        <option value="">Select</option>
                        @foreach($dealer_name as $state_id=>$dealer)
                        <option value="{{$dealer}}" <?php if($dealer==$dealer_name1) { echo "selected";} ?>>{{$dealer}}</option>
                        @endforeach
                     </select>
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Location</label>
                     <select id="location" name="location" class="form-control">
                        <option value="">Select</option>
                        @foreach($location as $loc)
                        <option value="{{$loc}}" <?php if($loc==$location1) { echo "selected";} ?> >{{$loc}}</option>
                        @endforeach
                     </select>
                  </div>
               </div>

               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Contact No. </label>
                     <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" data-original-title="Mobile No." maxlength='10' onkeypress="return checkNumber(this.value,event)" data-placement="top" placeholder="Contact No.">
                  </div>
               </div>
               <?php if($brand_id == "4"){ ?>
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Vin No. </label>
                     <input class="form-control" type="text" id="vin_no" name="vin_no" value="<?php echo $vin_no;?>" data-original-title="Vin No." data-placement="top" placeholder="Vin No.">
                  </div>
               </div>


               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Vehicle Model</label>
                     <select id="vehicle_model" name="vehicle_model" class="form-control" onchange="get_model_dash(this.value)">
                        <option value="">Select</option>
                        <?php foreach($clarion_product_master as $product_id=>$product_name)
                              {
                                 echo '<option value="'.$product_id.'"';
                                  if($product_id==$vehicle_model) { echo "selected";} 
                                 echo '>'.$product_name.'</option>';
                              }
                        ?>
                     </select>
                  </div>
               </div>

               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">DA2 - Model</label>
                     <select id="da2_model" name="da2_model" class="form-control">
                        <option value="">Select</option>
                        <?php  foreach($model_master as $model_id=>$model_name)
                              {
                                    echo '<option value="'.$model_id.'"';
                                    if($model_id==$da2_model) { echo 'selected';}
                                    echo '>'.$model_name.'</option>';
                              }
                        ?>

                     </select>
                  </div>
               </div>
               
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">FTIR</label>
                     <select id="ftir" name="ftir" class="form-control" >
                        <option value="">Select</option>
                        <option value="Yes" <?php if($ftir=="Yes") { echo "selected";} ?> >Yes</option>
                        <option value="No" <?php if($ftir=="No") { echo "selected";} ?>>No</option>
                        
                     </select>
                  </div>
               </div>

               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">FTIR No. <?php echo $ftir_no; ?></label>
                     <input name="ftir_no" id="ftir_no" value="<?php echo $ftir_no; ?>"  type="text" placeholder="FTIR No." class="form-control">
                  </div>
               </div>

               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Visit Type</label>
                     <select name="visit_type" id="visit_type" class="form-control">
                        <option value="">Select</option>
                        <option value="Site Visit" <?php if($visit_type=='Site Visit') { echo "selected";} ?> >Site Visit</option>
                        <option value="Online" <?php if($visit_type=='Online') { echo "selected";} ?> >Online</option>
                     </select>
                  </div>
               </div>
               <?php } ?>

               <div class="col-md-2">
                  <div class="position-relative form-group">
                        <label for="examplePassword11" class="">Service Center Name</label>
                        <select id="asc_code" name="asc_code" class="form-control">
                        <?php if(Session::get('UserType')=='Admin'){ echo '<option value="All">All</option>'; }?>
                           
                           @foreach($asc_master as $asc)
                                 <option value="{{$asc->center_id}}" <?php if( $asc_code==$asc->center_id) 
                                    { echo 'selected';} ?>>{{$asc->center_name}}</option>
                           @endforeach
                        </select>
                  </div>
               </div>
               
               
               <div class="col-md-2">
                  <div class="position-relative form-group">
                     <label for="examplePassword11" class="">Status of The Job</label>
                     <select id="job_status" name="job_status" class="form-control">
                        <option value="">Select</option>
                        <option value="In Process" <?php if($job_status=='In Process') { echo "selected";} ?> >In Process</option>
                        <option value="Resolved" <?php if($job_status=='Resolved') { echo "selected";} ?> >Resolved</option>
                        <option value="Pending" <?php if($job_status=='Pending') { echo "selected";} ?> >Pending</option>
                     </select>
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
               
         </form>
         <?php if($brand_id == "4"){ ?>
         <table id="table1" class="table table-striped table-bordered" style="width:100%">
            <thead>
               <tr>
                  <th>Sr.No</th>
                  <th>Date Of Register</th>
                  <th>Ticket No.</th>
                  <th>Job No.</th>
                  <th>Dealer Name</th>
                  <th>Location</th>
                  <th>Contact No.</th>
                  <th>Vin No .</th>
                  <th>Vehicle Model</th>
                  <th>DA2- Model</th>
                  <th>System SW Version</th>
                  <th>FTIR</th>
                  <th>FTIR No.</th>
                  <th>Visit Type</th>
                  <th>Site Visit Date</th>
                  <th>Logs Taken</th>
                  <th>Issue Resolve Date</th>
                  <th>Part Replace</th>
                  <th>Date of Part Replace</th>
                  <th>Supreme 1st Analysis</th>
                  <th>Job Status</th>
                  <th>Remarks</th>
                  <th>ASC Name</th>
                  <th>ASC Location</th>
                  <th>Date of dispatch</th>
                  <th>Final Status of Job</th>
                  <th>Job PDF</th>
               </tr>
            </thead>
            <tbody>
               <?php $srno = 1;
                  foreach($DataArr as $record)
                  {
                      //$record = json_decode($record1,true);
                      echo '<tr>';
                      echo '<td>'.$srno++.'</td>';
                      echo '<td>'.date('d-m-Y', strtotime($record->created_at)).'</td>';
                      echo '<td>';
                        if($record->brand_id == '4')
                        {
                              echo '<a href="ho-tag-view-cl?TagId='.$record->TagId.'">'.$record->ticket_no.'</a>';
                        }else{
                              echo '<a href="ho-tag-view?TagId='.$record->TagId.'">'.$record->ticket_no.'</a>';
                        }
                   
                      echo '</td>';
                      echo '<td><a href="edit-tagging-data?TagId='.$record->TagId.'"> '.$record->job_no.'</a></td>';
                      echo '<td>'.$record->dealer_name.'</td>';
                      echo '<td>'.$record->Landmark.'</td>';
                      echo '<td>'.$record->Contact_No.'</td>';
                      echo '<td>'.$record->vin_no.'</td>';
                      echo '<td>'.$record->Product.'</td>';
                      echo '<td>'.$record->Model.'</td>';
                      echo '<td>'.$record->system_sw_version.'</td>';
                      echo '<td>'.$record->ftir.'</td>';
                      echo '<td>'.$record->ftir_no.'</td>';
                      echo '<td>'.$record->visit_type.'</td>';
                      echo '<td>'.$record->site_visit_date.'</td>';
                      echo '<td>'.$record->logs_taken.'</td>';
                      echo '<td>'.$record->issue_resolved_date.'</td>';
                      echo '<td>'.$record->part_replace.'</td>';
                      echo '<td>'.$record->part_replace_date.'</td>';
                      
                      echo '<td>'.$record->supr_analysis.'</td>';
                      
                      
                      echo '<td>'.$record->Job_Status.'</td>';
                      echo '<td>'.$record->remarks.'</td>';
                      echo '<td>'.$record->center_name.'</td>';
                      
                      echo '<td>'.$record->state_name.'</td>';
                      echo '<td>'.$record->dispatch_date.'</td>';

                      echo '<td>'.$record->final_job_status.'</td>';
                              //echo '<td>'.$record->ticket_no.'</td>';
                              
                              //echo '<td>'.$record->job_no.'</td>';
                              
                              // echo '<td>'.$record->Customer_Name.'</td>';
                              // echo '<td>'.$record->Contact_No.'</td>';
                              // echo '<td>'.$record->Pincode.'</td>';
                              // echo '<td>'.$record->Brand.'</td>';
                              // echo '<td>'.$record->Model.'</td>';
                              // echo '<td>'.$record->Serial_No.'</td>';
                              // echo '<td>'.$record->warranty_type.'</td>';
                              // echo '<td>'.$record->service_type.'</td>';

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
                  
                  if(empty($DataArr))
                  {
                      echo '<tr><td colspan="27">No Records Found.</td></tr>';
                  }
               ?>
            </tbody>
         </table>
         <?php }else { ?>

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
                                 
                                        if(empty($DataArr))
                                        {
                                            echo '<tr><td colspan="16">No Records Found.</td></tr>';
                                        }
                                 ?>
                                  
                                 
                                
                              </tbody>
                           </table>
         <?php }
         } ?>
      </div>
      <?php if(isset($brand_id) && $brand_id != ""){ ?>
      <div class="row">
         <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-midnight-bloom">
               <div class="widget-content-wrapper text-white">
                  <div class="widget-content-left">
                     <div class="widget-heading" style="opacity: 20 !important;" >Total Open Calls &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                     <div class="widget-subheading" style="opacity: 20 !important;">
                        <?php if(!empty($dialer_open_total)) { ?>
                        <table  style="width: 100%;">
                           <tbody>
                              <tr>
                                 <?php $tr=1; 
                                    foreach($dialer_open_total as $tot)
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
                        <?php } ?>
                     </div>
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
                     <div class="widget-subheading" style="opacity: 20 !important;">
                        <?php if(!empty($tagging_perday_call)) { ?>
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
                        <?php } ?>
                     </div>
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
                     <div class="widget-subheading" style="opacity: 20 !important;">
                        <?php if(!empty($dialer_completed_call)) { ?>
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
                        <?php } ?>
                     </div>
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
                     <!--    <div class="widget-subheading" style="opacity: 20 !important;">People Interested</div>   -->
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
                     <div class="widget-subheading" style="opacity: 20 !important;">
                        <?php if(!empty($dialer_completed_call)) { ?>
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
                        <?php } ?>
                     </div>
                  </div>
                  <div class="widget-content-right">
                     <div class="widget-numbers text-white"><span><?php echo $completed_call; ?></span></div>
                  </div>
               </div>
            </div>
         </div>
      </div>

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
      <?php } ?>
   </div>
</div>

@endsection