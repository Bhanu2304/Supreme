@extends('layouts.app')

@section('content')


<script>
    
    menu_select('{{$url}}'); 
    
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
                        <div class="card-body"><h5 class="card-title"> Product Category</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('state_form','view');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('view','state_form');" style="cursor: pointer;">View</a>
                            </h5> 
                             @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="state_form" method="post" action="save-product-category" style="display:none;">
                               
                                

                                

                                <div class="form-row">
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                            <select name="brand_id" id="brand_id" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Category</label>
                                            <input name="category_name" id="category_name" placeholder="Category Name" type="text" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                


                                
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" onclick="form_toggle('view','state_form');" class="mt-2 btn btn-danger"  title="back">Exit</a>
                            </form>
                            
                                 <div id="view">   
                                     <form action="add-product-category" method="get">
                                         <div class="form-row">
                                             <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                         <select name="brand_search" onchange="this.form.submit();" class="form-control">
                                             <option value="All">All</option>
                                             <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'" ';
                                                            if($brand['brand_id']==$brand_search)
                                                            {
                                                                echo 'selected';
                                                            }
                                                            echo '>'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                         </select>
                                         </div>
                                             </div>
                                         </div>
                                     </form>
                                     
                                    <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Brand</th>
                                    <th>Product Category</th>
                                    <th>Creation Date</th>
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
                                    <td>{{$Data->category_name}}</td>
                                    <td class="Officer">{{$Data->created_at}}</td>
                                    <td class="Status">@if($Data->category_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-product-category?product_category_id=<?php echo base64_encode($Data->product_category_id); ?>" >Edit</a></td>
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
</div>
 
<script>
    $('#table_id').DataTable( );
    </script>

@endsection
