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
                        <div class="card-body"><h5 class="card-title">Edit Product Category</h5>
                            <form method="post" action="update-product-category">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                

                                

                                <div class="form-row">
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                            <select name="brand_id" id="brand_id" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'"';
                                                            if($data['brand_id']==$brand['brand_id'])
                                                            { echo 'selected';}
                                                            echo '>'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Category</label>
                                            <input name="category_name" id="category_name" placeholder="Category" type="text" value="<?php echo $data['category_name']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                


                                
                                 <div class="form-row">
                                     <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="category_status" name="category_status" class="form-control" required="">
                                            <option value="1" <?php if($data['category_status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['category_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                 </div>
                                 <input type="hidden" id="product_category_id" name="product_category_id" value="<?php echo $data['product_category_id']; ?>" />
                                 <a href="add-product-category" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 


@endsection
