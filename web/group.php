
<?php 
session_start();
$welcomeElement;
$logoutElement;
if(isset($_SESSION['sess_user_id']) && $_SESSION['sess_user_id'] != "") {
  $welcomeElement = '<h1>Welcome '.$_SESSION['sess_name'].'</h1>';
  $logoutElement = '<a class="nav-link" href="logout.php">Logout</a>';
} else { 
  header('location:index.php');
}?>
<?php 
require('connectdb.php');
require('business.php');
require('groupDB.php');

$userID = $_SESSION['sess_user_id'];
$group_to_view = "";
$member_to_view = "";
$groupMember = [];
$groups = getGroupsByID($userID);
$subgroups = [];
if (!empty($_POST['action']) && ($_POST['action'] == 'Create')){
	createGroup($userID,$_POST['group_name'], $_POST['description'], $_POST['location'],$_POST['parent_groupID']);
	$groups = getGroupsByID($userID);
}
else if (!empty($_POST['action']) && ($_POST['action'] == 'Update')){
	if (hasGroupWritePermission($userID,$_POST['group_to_delete'])){
		updateGroup($_POST['group_to_update'], $_POST['group_name'], $_POST['description'],$_POST['location']);
		$groups = getGroupsByID($userID);
	}
	else{
		echo '<script>alert("You do not have permissions to modify this group.")</script>';
	}
	
}
else if (!empty($_POST['action']) && ($_POST['action'] == 'Delete')){
	if (hasGroupWritePermission($userID,$_POST['group_to_delete'])){
		deleteGroup($_POST['group_to_delete']);
		$groups = getGroupsByID($userID);
	}
	else{
		echo '<script>alert("You do not have permissions to delete this group.")</script>';
	}
	
	

}
else if (!empty($_POST['action']) && ($_POST['action'] == 'Leave')){
	echo "Here";
	leaveGroup($userID,$_POST['group_to_leave']);
	$groups = getGroupsByID($userID);
}
else if (!empty($_POST['action']) && ($_POST['action'] == 'ViewSub')){
	$subgroups = getSubGroupsByID($_POST['group_to_view']);
	$group_to_view = $_POST['group_name'];
}
else if (!empty($_POST['action']) && ($_POST['action'] == 'DeleteSub')){
	deleteSubGroupsByID($_POST['parentgroup_to_delete'],$_POST['subgroup_to_delete']);
	$subgroups = getSubGroupsByID($_POST['parentgroup_to_delete']);
	$group_to_view = $_POST['parentgroup_to_delete'];
}
else if (!empty($_POST['action']) && ($_POST['action'] == 'Join')){
	joinGroup($userID,$_POST['groupID']);
	$groups = getGroupsByID($userID);
}
else if (!empty($_POST['action']) && ($_POST['action'] == 'ViewMember')){
	$groupMember = getMemberByID($_POST['group_to_view']);
	$member_to_view = $_POST['group_name'];
}
?>



<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Welcome to Marvelist - Task Organization Every Day</title>
  </head>
  
<body>

<nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
<a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">Welcome, <?php echo $_SESSION['sess_name'] ?></a>
<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<!-- <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"> -->
  <ul class="navbar-nav px-3">
    <li class="nav-item text-nowrap">
      <?php echo $logoutElement ?>
    </li>
  </ul>
</nav>

<div class="container-fluid">
<div class="row">
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
  <div class="sidebar-sticky pt-3">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link active" href="home.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
          Dashboard <span class="sr-only">(current)</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="tasks.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
          Tasks
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="list.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
          Lists
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="events.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
          Events
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="group.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
          Groups
        </a>
      </li>
    </ul>

    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
      <span>User Info</span>
      <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
      </a>
    </h6>
    <ul class="nav flex-column mb-2">
      <li class="nav-item">
        <a class="nav-link" href="profile.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>

          <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg> -->
          My Profile
        </a>
      </li>
      
    </ul>
  </div>
</nav>


<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4"><div style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;" class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Groups</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
          
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-3">
            <div class="card">
              <div class="card-header">
			  	Create Group
              </div>
              <div class="card-body">
				<form name="mainForm" action="group.php" method="post">  
					<div class="form-group">
						Group name:
						<input type="text" class="form-control" name="group_name" required 
						value="" 
						/>        
					</div>  
					<div class="form-group">
						Description:
						<input type="text" class="form-control" name="description"  
						value=""
						/> 
					</div>  
					<div class="form-group">
						Location:
						<input type="text" class="form-control" name="location"
						value="" 
						/>        
					</div> 
					<div class="form-group">
						Parent Group ID :
						<input type="number" class="form-control" name="parent_groupID"
						value="" 
						/>        
					</div> 
						
					<input type="submit" value="Create" name="action" class="btn btn-dark" title="Create a group" />
				</form>  
              </div>
            </div>
				<br>
				<br>
			<div class="card">
			  <div class="card-header">
			  	Join Group
              </div>
              <div class="card-body">
			  	<form name="mainForm" action="group.php" method="post">  
					<div class="form-group">
						Group ID:
						<input type="text" class="form-control" name="groupID" required 
						value="" 
						/>        
					</div>  
						
					<input type="submit" value="Join" name="action" class="btn btn-dark" title="Join a group" />
				</form>
              </div>
            </div>
          
		  </div>
		  

          <div class="col-9">
            
			
			
			<div class="card">
				<div class="modal-header">
					<h2 class="h3">List of Subgroups for <?php echo $group_to_view ?></h2>
              	</div>
				<div class="card-body">
				<table class="table center" style="width:100%">
					<thead class="thead-dark">
					<tr>
						<th width="10%">Group ID</th>  
						<th width="25%">Group Name</th>        
						<th width="25%">Description</th>        
						<th width="25%">Location</th> 
						<th width="10%">Delete subGroup Relation</th> 
					</tr>
					</thead>
					<?php foreach ($subgroups as $item): ?>
						<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
					<tr>
						<td><input type="text" class="form-control" value = "<?php echo $item['groupID']; ?>" name="groupID" readonly/></td>
						<td><input type="text" class="form-control" value = "<?php echo $item['group_name']; ?>" name="group_name"/></td>
						<td><input type="text" class="form-control" value = "<?php echo $item['description']; ?>" name="description"/></td>        
						<td><input type="text" class="form-control" value = "<?php echo $item['location']; ?>" name="location"/></td>       
											
						<td>
						
							<input type="submit" value="DeleteSub" name="action" class="btn btn-danger" title="Permanently delete the subgroup" />      
							<input type="hidden" name="subgroup_to_delete" value="<?php echo $item['sub_groupID'] ?>" />
							<input type="hidden" name="parentgroup_to_delete" value="<?php echo $item['parent_groupID'] ?>" />
						</td>
																	
					</tr>
					</form>
					<?php endforeach; ?>
				</table>
				</div>
            </div>
				<br>
				<br>

			<div class="card">
				<div class="modal-header">
					<h2 class="h3">List of Group Members of <?php echo $member_to_view ?></h2>
              	</div>
				<div class="card-body">
				
					<table class="table center" style="width:100%">
						<thead class="thead-dark">
							<th width="10%">User ID</th>  
							<th width="20%">First Name</th>        
							<th width="20%">Last Name</th>        
							<th width="20%">Email</th> 
							<th width="25%">Phone</th> 
						</thead>
						<?php foreach ($groupMember as $item): ?>
							
						<tr>
							<td><?php echo $item['userID']; ?></td>
							<td><?php echo $item['first_name']; ?></td>
							<td><?php echo $item['last_name']; ?></td>
							<td><?php echo $item['email']; ?></td>
							<td><?php echo $item['phone']; ?></td>
																		
						</tr>
						<?php endforeach; ?>
						</table>
					
				</div>
            </div>
            
          </div>
        
        </div>
		<br>
				<br>
		<div class="row">
			<div class="card">
					<div class="modal-header">
						<h2 class="h3">List of Groups That You Are Currently In</h2>
					</div>
					<div class="card-body">
					
						<table class="table center" style="width:100%">
							<thead class="thead-dark" >
								<th width="7%">Group ID</th>  
								<th width="15%">Group Name</th>        
								<th width="15%">Description</th>        
								<th width="10%">Location</th> 
								<th width="5%">Update</th>
								<th width="5%">Delete Group</th> 
								<th width="5%">Leave Group</th> 
								<th width="5%">View subGroup</th> 
								<th width="5%">View group member</th> 
							</thead>
							<?php foreach ($groups as $item): ?>
								<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
							<tr>
								<td><input type="text" class="form-control" value = "<?php echo $item['groupID']; ?>" name="groupID" readonly/></td>
								<td><input type="text" class="form-control" value = "<?php echo $item['group_name']; ?>" name="group_name"/></td>
								<td><input type="text" class="form-control" value = "<?php echo $item['description']; ?>" name="description"/></td>        
								<td><input type="text" class="form-control" value = "<?php echo $item['location']; ?>" name="location"/></td>       
								<td>
									
									<input type="submit" value="Update" name="action" class="btn btn-primary" title="Update the record" />  
									<input type="hidden" name="group_to_update" value="<?php echo $item['groupID'] ?>" />
									
								
								</td>                        
								<td>
								
									<input type="submit" value="Delete" name="action" class="btn btn-danger" title="Permanently delete the record" />      
									<input type="hidden" name="group_to_delete" value="<?php echo $item['groupID'] ?>" />
								
								</td>
								<td>
								
									<input type="submit" value="Leave" name="action" class="btn btn-danger" title="Permanently leave the group" />      
									<input type="hidden" name="group_to_leave" value="<?php echo $item['groupID'] ?>" />
								
								</td>
								<td>
								
									<input type="submit" value="ViewSub" name="action" class="btn btn-danger" title="View subgroup" />      
									<input type="hidden" name="group_to_view" value="<?php echo $item['groupID'] ?>" />
								
								</td> 
								<td>
								
									<input type="submit" value="ViewMember" name="action" class="btn btn-danger" title="View Member" />      
									<input type="hidden" name="group_to_view" value="<?php echo $item['groupID'] ?>" />
								
								</td>                                                  
							</tr>
							</form>
							<?php endforeach; ?>
						</table>
					</div>
				</div>

			
		</div>
      </div>

      

    </div>


</main>





</div>
</div>













<script type="text/javascript">

$('#myModal').on('shown.bs.modal', function () {
  $('#myInput').trigger('focus')
})
</script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
  



