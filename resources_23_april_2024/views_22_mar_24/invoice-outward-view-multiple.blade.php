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


</script>



<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
            <li class="nav-item">
                <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>Invoice Creation</span>
                </a>
                
            </li>
            <li class="nav-item">
            <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>Invoice View</span>
                </a>
            </li>
        </ul>
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                <form method="post">
                    <div class="card-body">                                        
                        <h5 class="card-title">PO Request</h5>
                        <table border="1" style="font-size: 13px;" id="table_id">
                            <tr>
                                <th>#</th>
                                <th>PO Request Date</th>
                                <th>Part PO Number</th>
                                <th>PO Type</th>
                                <th>Job No.</th>
                                <th>ASC Name</th>
                                <th>ASC Code</th>
                                <th>Brand Name</th>
                                <th>Model No.</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th>Colour</th>
                                <th>HSN Code</th>
                                <th>GST Rate</th>
                                <th>ASC Amount</th>
                                <th>Customer Amount</th>
                                <th>Requested Qty</th>
                                <th>Issued Qty</th>
                                <th>Discount</th>
                                <th>Remarks</th>
                                <th>Select to Create Invoice</th>
                            </tr>
                            
                            <tbody>
                            <?php   
                                if(!empty($data_arr))
                                {
                                    $srno = 1;
                                    foreach($data_arr as $po_job)
                                    {
                                        echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.date('d-m-y',strtotime($po_job->po_date)).'</td>';
                                            echo '<td align="center">'.$po_job->po_no.'</td>';
                                            echo '<td align="center">'.$po_job->po_type.'</td>';
                                            echo '<td align="center">'.$po_job->job_no.'</td>';
                                            echo '<td align="center">'.$po_job->asc_name.'</td>';
                                            echo '<td align="center">'.$po_job->asc_code.'</td>';
                                            echo '<td align="center">'.$po_job->brand_name.'</td>';
                                            echo '<td align="center">'.$po_job->model_name.'</td>';
                                            echo '<td align="center">'.$po_job->part_no.'</td>';
                                            echo '<td align="center">'.$po_job->part_name.'</td>';
                                            echo '<td align="center">'.$po_job->color.'</td>';
                                            echo '<td align="center">'.$po_job->hsn_code.'</td>';
                                            echo '<td align="center">'.$po_job->gst.'</td>';
                                            echo '<td align="center">'.$po_job->asc_amount.'</td>';
                                            echo '<td align="center">'.$po_job->customer_amount.'</td>';
                                            echo '<td align="center">'.$po_job->req_qty.'</td>';
                                            echo '<td align="center">'.$po_job->issued_qty.'</td>';
                                            echo '<td align="center">'.$po_job->discount.'</td>';
                                            echo '<td align="center">'.$po_job->remarks.'</td>';
                                            echo '<td align="center">';
                                            echo '<input type="checkbox" id="'.$po_job->out_id.'" name="chk_inv[]" value="'.$po_job->out_id.'"></td>';
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                else
                                {
                                    echo '<tr><td colspan="21">No Records Found.</td></tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                        <br>
                        <table style="width:100%">
                            <tr>
                                <td id="disp_inv_no">
                                    
                                </td>
                                <td>
                                    <div style="text-align: right;">
                                        <button type="button" name="create_invoice" id="create_invoice" value="create_invoice" onclick="create_invoices();" class="btn btn-primary">Create Invoice Number</button>
                                    </div>    
                                </td>
                            </tr>
                        </table>
                        
                        
                              
                    </div>                                      
                </form>


                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                
        </div>
         </div>
        
         <div class="tab-pane tabs-animation fade " id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">View Invoice</h5>

                        <table border="1" style="width:100%;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice No.</th>
                                <th>PO Request Date</th>
                                <th>Part PO Number</th>
                                <th>PO Type</th>
                                <th>Job No.</th>
                                <th>ASC Name</th>
                                <th>ASC Code</th>
                                <th>Brand Name</th>
                                <th>Model No.</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th>Colour</th>
                                <th>HSN Code</th>
                                <th>GST Rate</th>
                                <th>ASC Amount</th>
                                
                                <th>Issued Qty</th>
                                <th>Discount</th>
                                <th>Remarks</th>
                                <th>PDF</th>
                            </tr>
<!--                                <tr>
                                <th>#</th>
                                <th>Invoice No.</th>
                                <th>PO Type</th>
                                <th>PO Request Date</th>
                                <th>Part PO Number</th>
                                <th>ASC Name</th>
                                <th>ASC Code</th>
                                <th>Total</th>
                                <th>GST</th>
                                
                                
                                <th>PDF</th>
                            </tr>-->
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                    
                                    foreach($invoice_arr as $po_job)
                                    {
                                        /*echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.$po_job->invoice_no.'</td>';
                                            echo '<td align="center">'.$po_job->po_type.'</td>';
                                            $po_arr = json_decode($po_job->po_no,true);
                                            if(empty($po_arr))
                                            {
                                                echo '<td></td>';
                                                echo '<td></td>';
                                            }
                                            else
                                            {
                                                 echo '<td align="center">';
                                                foreach($po_arr as $po=>$po_no_arr)
                                                {
                                                    echo date('d-m-Y',strtotime($po_no_arr['po_date'])).'<br>';
                                                }
                                                echo '</td>';
                                                echo '<td align="center">';
                                                foreach($po_arr as $po=>$po_no_arr)
                                                {
                                                    echo $po_no_arr['po_no'].'<br>';
                                                }
                                                echo '</td>';
                                            }
                                           
                                            //echo date('d-m-y',strtotime($po_job->po_date));
                                            
                                            //echo '<td align="center">'.$po_job->po_no.'</td>';
                                            echo '<td align="center">'.$po_job->asc_name.'</td>';
                                            echo '<td align="center">'.$po_job->asc_code.'</td>';
                                            echo '<td align="center">'.$po_job->total.'</td>';
                                            echo '<td align="center">'.$po_job->gst_amount.'</td>';
                                            echo '<td align="center">';
                                            echo '<a href=ho-invoice-pdf?invoice_id='.$po_job->invoice_id.'>Pdf</td>';
                                            echo '</td>';
                                        echo '</tr>';*/
                                        echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.$po_job->invoice_no.'</td>';
                                            echo '<td align="center">'.date('d-m-y',strtotime($po_job->po_date)).'</td>';
                                            echo '<td align="center">'.$po_job->po_no.'</td>';
                                            echo '<td align="center">'.$po_job->po_type.'</td>';
                                            echo '<td align="center">'.$po_job->job_no.'</td>';
                                            echo '<td align="center">'.$po_job->asc_name.'</td>';
                                            echo '<td align="center">'.$po_job->asc_code.'</td>';
                                            echo '<td align="center">'.$po_job->brand_name.'</td>';
                                            echo '<td align="center">'.$po_job->model_name.'</td>';
                                            echo '<td align="center">'.$po_job->part_no.'</td>';
                                            echo '<td align="center">'.$po_job->part_name.'</td>';
                                            echo '<td align="center">'.$po_job->color.'</td>';
                                            echo '<td align="center">'.$po_job->hsn_code.'</td>';
                                            echo '<td align="center">'.$po_job->gst.'</td>';
                                            echo '<td align="center">'.$po_job->asc_amount.'</td>';
                                            echo '<td align="center">'.$po_job->issued_qty.'</td>';
                                            echo '<td align="center">'.$po_job->discount.'</td>';
                                            echo '<td align="center">'.$po_job->remarks.'</td>';
                                            echo '<td align="center">';
                                            echo '<a href=ho-invoice-pdf?invoice_id='.$po_job->invoice_id.'>Pdf</td>';
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                            ?>
                            </tbody>
                        </table>       
                            

                              
                    </div>                                      



                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                
        </div>
     </div>
    </div>
            
        </div>
    </div>  
</div> 

<script>
    
    function remove_row(tr_id,row_no)
    {
        $('#row'+tr_id).remove();
        var rows = document.querySelectorAll('#part_arr tr');
        
        for(var i=0;i<rows.length;i++){
          var row = rows[i];
          var cell = row.cells[0];
          cell.innerHTML = i+1;
          console.log(cell);
        }
        
        
    }
        
    function create_invoices() 
    {
        $('#disp_inv_no').html('');
        //alert(chk_inv.length);
        
             
        var chk_inv = document.querySelectorAll('[name="chk_inv[]"]:checked');     
        var out_str = "";
        for(var i=0; i<chk_inv.length; i++)       
        {
            if(chk_inv[i].type==='checkbox' && chk_inv[i].checked===true)
            {
                out_str += chk_inv[i].value+",";
            }
        }
        $.ajaxSetup({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 }
             });
      
      jQuery.ajax({
                url: 'ho-create-invoice-multiple', 
                method: 'post',
                data:{out_str:out_str},

                   success: function(result){
                       $('#disp_inv_no').append(result);
                   }});  
        
    }

     function task(out_id,i) {
  setTimeout(function() {
      
      $.ajaxSetup({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 }
             });
      
      jQuery.ajax({
                url: 'ho-create-invoice', 
                method: 'post',
                data:{out_id:out_id},

                   success: function(result){
                       $('#disp_inv_no').append(result);
                   }});  
  }, 500*i );
}   
 
 
</script>

@endsection
