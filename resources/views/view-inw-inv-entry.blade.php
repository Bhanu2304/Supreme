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
        
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         
        <div class="tab-pane tabs-animation fade show active" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
                <form  >
                    <div class="card-body">                                        
                        <h5 class="card-title">View Inward Stock</h5> 
                                 <h5 class="card-title">SR. NO.: <?php echo $inw_det->inw_no; ?></h5>
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Supplier Name (Party Name) <font color="red">*</font></label>
                                            <input type="text" id="supplier_name" name="supplier_name" value="<?php echo $inw_det->supplier_name; ?>" class="form-control"  readonly="" />
                                        </div>
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Voucher / Invoice No. <font color="red">*</font></label>
                                            <input type="text" id="voucher_no" name="voucher_no"  value="<?php echo $inw_det->voucher_no; ?>" class="form-control" readonly="" />
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Invoice Date <font color="red">*</font></label>
                                            <input type="text" id="invoice_date" readonly="" name="invoice_date" value="<?php echo $inw_det->invoice_date; ?>" class="form-control" readonly="" />
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">No. of cases/boxes <font color="red">*</font></label>
                                            <input type="text" id="no_of_case" name="no_of_case" onkeypress="return checkNumber(this.value,event)" value="<?php echo $inw_det->no_of_case; ?>" class="form-control" readonly="" />
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Vehicle / Docket No. <font color="red">*</font></label>
                                            <input type="text" id="veh_doc_no" name="veh_doc_no" value="<?php echo $inw_det->veh_doc_no; ?>" class="form-control" readonly="" />
                                        </div>
                                    </div>
                            </div>
                        
  
                        <table  border="1"  style="font-size:13px;">
                                     <thead>
                                         <tr>
                                             <th>Sr. No.</th>
                                             <th>Brand </th>
                                             <th>Model </th>
                                             <th>Part Code </th>
                                             <th>Part Name </th>
                                             <th>Item Color </th>
                                             <th>HSN Code </th>
                                             <th>GST </th>
                                             <th>Item Qty. </th>
                                             <th>Bin or Rack No. </th>
                                             <th>Purchase Amount </th>
                                             <th>ASC Amount </th>
                                             <th>Customer Amount </th>
                                             <th>Remarks </th>
                                             
                                         </tr>
                                     </thead>
                                     <tbody id="part_arr">
                                        <?php $a=0; foreach($data_part_arr as $part) { $a++;   ?> 
                                         <tr style="text-align:center;">
                                                     <th><?php echo $a;?></th>
                                                     <td><?php echo $part->brand; ?></td>
                                                    <td><?php echo $part->model; ?></td>
                                                    <td><?php echo $part->part_no; ?></td>
                                                    <td><?php echo $part->part_name; ?></td>
                                                    <td><?php echo $part->item_color; ?></td>
                                                    
                                                    <td><?php echo $part->hsn_code; ?> </td>
                                                    <td ><?php echo $part->gst; ?></td>
                                                    <td ><?php echo $part->item_qty; ?></td>
                                                    <td><?php echo $part->bin_no; ?></td>
                                                    <td ><?php echo $part->purchase_amt; ?></td>
                                                    <td ><?php echo $part->asc_amount; ?></td>
                                                    <td ><?php echo $part->customer_amount;; ?></td>
                                                    <td ><?php echo $part->remarks; ?></td>
                                         </tr>
                                        <?php } ?> 
                                         
                                         
                                     </tbody>
                                 </table>
      

      
                                 <br>
                                 <br>

                            

                        <div class="form-row">
                            <div class="col-md-5">
                                <div class="position-relative form-group">
                                    <a href="inward-inv-entry" class="mt-2 btn btn-primary">Back</a>     
                                </div>
                            </div>
                        </div>       
                    </div>                                      



                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                </form>
        </div>
     </div>
         
    </div>
        </div>
    </div>  
</div> 

<script>
    
    
    
 


    
    
 
 
 
</script>

@endsection
