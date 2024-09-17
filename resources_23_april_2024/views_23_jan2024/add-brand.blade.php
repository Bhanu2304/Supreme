@extends('layouts.app')

@section('content')

<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<script>
    function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
</script>


<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title"> Brand</h5>
                            
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('brand_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','brand_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="brand_form" method="post" action="save-brand" style="display:none;">
                                
                                
                                 
                                

                                <div class="form-row">
                                    
                                    
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                            <input name="brand_name" id="brand_name" placeholder="Brand" type="text" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                


                                
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" onclick="form_toggle('table_id','brand_form');" class="mt-2 btn btn-danger"  title="view">Exit</a>
                            </form>
                            <table id="table_id" class="table  " style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    
                                    <th>Brand Name</th>
                                    <th>Create Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @php $i = 0; @endphp
                                    @foreach($DataArr as $Data)
                                  
                                  
                                 <tr>
                                    <td>{{++$i}}</td>
                                    
                                    <td>{{$Data->brand_name}}</td>
                                    <td class="Officer">{{$Data->created_at}}</td>
                                    <td class="Status">@if($Data->brand_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-brand?brand_id=<?php echo base64_encode($Data->brand_id); ?>" >Edit</a></td>
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
    $('#table_id').DataTable( );
    </script>

@endsection
