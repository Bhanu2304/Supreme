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
                        <div class="card-body"><h5 class="card-title">Edit Brand</h5>
                            <form method="post" action="update-brand">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                

                                

                                <div class="form-row">
                                    
                                    
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                            <input name="brand_name" id="brand_name" placeholder="Model" type="text" value="<?php echo $data['brand_name']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                


                                
                                 <div class="form-row">
                                     <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="brand_status" name="brand_status" class="form-control" required="">
                                            <option value="1" <?php if($data['brand_status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['brand_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                 </div>
                                 <input type="hidden" id="brand_id" name="brand_id" value="<?php echo $data['brand_id']; ?>" />
                                 <a href="add-brand" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
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
