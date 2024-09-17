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
                            <h5 class="card-title"> Inventory Details </h5>
                            <a href="allocate-inv" class="btn btn-primary" style="float:right;">Back</a>
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                
                                <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                       <tr>
                                          <th>Sr.No</th>
                                          <th>Brand</th>
                                          <th>Product Detail</th>
                                          <th>Product</th>
                                          <th>Model</th>
                                          <th>Spare Part Name</th>
                                          <th>Part No</th>
                                          <th>HSN Code</th>
                                          <th>Raw No.</th>
                                          
                                          
                                          <th>Stock Quantity</th>
                                          <th>Create Date</th>
                                          
                                       </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 0; @endphp
                                          @foreach($data_arr as $Data)


                                       <tr>
                                          <td>{{++$i}}</td>
                                          <td>{{$Data->brand_name}}</td>
                                          <td>{{$Data->category_name}}</td>
                                          <td>{{$Data->product_name}}</td>
                                          <td>{{$Data->model_name}}</td>
                                          <td>{{$Data->part_name}}</td>
                                          <td class="Officer">{{$Data->part_no}}</td>
                                          <td class="Officer">{{$Data->hsn_code}}</td>
                                          <td class="Officer">{{$Data->raw_no}}</td>
                                          
                                          <td class="Officer">{{$Data->stock_qty}}</td>
                                          <td class="Officer">{{$Data->created_at}}</td>
                                       </tr>
                                       @endforeach

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
    
    function get_mol(balance)
    {
        var mol = parseFloat(balance*1.5);
        document.getElementById('mol').value = mol;
    }
    
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
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

function get_partno(part_name)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-no',
              method: 'post',
              data: {
                 part_name: part_name 
              },
              success: function(result){
                  $('#part_no').html(result)
              }});
 }

function get_hsn_code(part_no)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-hsn-code',
              method: 'post',
              data: {
                 part_no: part_no 
              },
              success: function(result){
                  $('#hsn_code').html(result)
              }});
 }
 
 
 $('#table_id').DataTable( );
</script>

@endsection
