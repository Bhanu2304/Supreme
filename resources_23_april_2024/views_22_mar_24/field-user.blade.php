@extends('layouts.app')

@section('content')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#" class="tip-bottom">User</a> <a href="#" class="current">View User</a> </div>
   
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
             <span class="icon"><i class="icon-th"></i></span> 
            <h5>Field User List</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
					<th>Sno.</th>
    				<th>User Name</th> 
    				<th>Contact No</th>
    				<th>Name</th> 
    				<th>State</th> 
					<th>City</th>
					<th>AccNo</th>
					<th>Bank</th>
					<th>Ifsc</th>
					<th>Date</th>
                </tr>
              </thead>
              <tbody>
			 <?php 
			 $i=1; 
			  

			  foreach($DataArr as $Data)
			  {
			 ?> 
				<tr> 
				   
					<td><?php echo $i;?></td>
    				<td><?php echo $Data['UserId'];?></td> 
    				<td><?php echo $Data['ContactNo'];?></td> 
    				<td><?php echo $Data['DisplayName'];?></td> 
					<td><?php echo $Data['State'];?></td> 
					<td><?php echo $Data['City'];?></td> 
					<td><?php echo $Data['BankAccountNumber'];?></td> 
					<td><?php echo $Data['BankName'];?></td> 
					<td><?php echo $Data['IFSCCode'];?></td> 
					<td><?php echo $Data['CreateDate'];?></td> 
				</tr> 
			  <?php $i++; } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

 
@endsection
