
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

$all_tasks = getAllTasks($_SESSION['sess_user_id']);
$task_to_update = null;
$all_sharing = null;
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (!empty($_POST['action']) && ($_POST['action']=='Add Task'))
  {
    if(empty($_POST['completed'])) {
      $_POST['completed']=0;
    }
    addTask($_POST['task_name'], $_POST['due_date'], $_POST['due_time'],$_POST['description'], $_POST['priority'], $_POST['location'],$_POST['completed']);
    $all_tasks = getAllTasks($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Details'))
  {
    $task_to_update = getTaskByID($_POST['task_to_update']);
    $all_sharing = getAllSharingByTaskID($_POST['task_to_update']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Update Task'))
  {
    if(empty($_POST['completed'])) {
      $_POST['completed']=0;
    }
    updateTask($_POST['taskID'], $_POST['task_name'], $_POST['due_date'], $_POST['due_time'],$_POST['description'], $_POST['priority'], $_POST['location'],$_POST['completed']);
    $all_tasks = getAllTasks($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Delete'))
  {
    deleteTask($_POST['task_to_delete'], $_SESSION['sess_user_id']);
    $all_tasks = getAllTasks($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Share Task'))
  {
    addShareTask($_POST['userID'], $_POST['taskID'],  $_POST['permission'], $_SESSION['sess_user_id']);
    $all_tasks = getAllTasks($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Unshare Task'))
  {
    deleteShareTask($_POST['userID'], $_POST['taskID'], $_SESSION['sess_user_id']);
    $all_tasks = getAllTasks($_SESSION['sess_user_id']);
  }
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
Dashboard 
</a>
</li>
<li class="nav-item">
<a class="nav-link" href="tasks.php#">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg>
Tasks<span class="sr-only">(current)</span>
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

My Profile
</a>
</li>


</ul>
</div>
</nav>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4"><div style="position: absolute; inset: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;" class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tasks</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#shareTaskModal">Share/Unshare</button>

        </div>
          
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-4">
            <div class="card">
              <div class="card-header">
                      <?php 
                        if ($task_to_update!=null) {
                          echo 'Task Details';
                        }
                        else {
                          echo 'New Task';
                        }
                      ?>
              </div>
              <div class="card-body">
                <form name="addTask" action="tasks.php" method="post">
                      <?php 
                        if ($task_to_update!=null) {
                          echo '<div class="form-group">Task ID <input type="text" class="form-control" name="taskID" required readonly value="'.$task_to_update['taskID'].'"/> </div>';
                        }
                      ?>
                      <div class="form-group">
                        Task Name
                        <input type="text" class="form-control" name="task_name" required 
                        value="<?php if ($task_to_update!=null) echo $task_to_update['task_name'] ?>"/>
                      </div>  
                      <div class="form-group">
                        Due Date
                        <input type="date" class="form-control" name="due_date" required 
                        value="<?php if ($task_to_update!=null) echo $task_to_update['due_date'] ?>" /> 
                      </div>  
                      <div class="form-group">
                        Due Time
                        <input type="time" class="form-control" name="due_time" required 
                        value="<?php if ($task_to_update!=null) echo $task_to_update['due_time'] ?>" />        
                      </div> 
                      <div class="form-group">
                        Description    
                        <textarea id="task_description" class="form-control" name="description" rows="4" >
                        <?php if ($task_to_update!=null) echo $task_to_update['description'] ?>
                        </textarea>  
                      </div> 
                      <div class="form-group">
                        Priority
                        <select name="priority" required class="form-control">
                          <option value="5" <?php if ($task_to_update!=null && $task_to_update['priority']==5) echo 'selected' ?> >Highest</option>
                          <option value="4" <?php if ($task_to_update!=null && $task_to_update['priority']==4) echo 'selected' ?>>High</option>
                          <option value="3" <?php if ($task_to_update!=null && $task_to_update['priority']==3) echo 'selected' ?>>Medium</option>
                          <option value="2" <?php if ($task_to_update!=null && $task_to_update['priority']==2) echo 'selected' ?>>Low</option>
                          <option value="1" <?php if ($task_to_update!=null && $task_to_update['priority']==1) echo 'selected' ?>>Lowest</option>
                        </select>      
                      </div> 
                      <div class="form-group">
                        Location
                        <input type="text" class="form-control" name="location" 
                        value="<?php if ($task_to_update!=null) echo $task_to_update['location'] ?>" />        
                      </div> 
                      <div class="form-group">
                        <input type="checkbox" id="completed" name="completed" value="1" 
                        <?php if ($task_to_update!=null && $task_to_update['completed']==1) echo 'checked' ?>>
                        <label for="completed"> Completed</label><br>       
                      </div>
                      <?php 
                        if ($task_to_update!=null){
                          echo '<div class="card" style="width: 100%;"> <div class="card-header">Other Users Who Have Access</div><ul class="list-group list-group-flush">';
                          if ($all_sharing!=null) {
                            foreach ($all_sharing as $item) {
                              $permission = null;
                              if ($item['permission']==2) {
                                $permission='Edit';
                              }
                              else {
                                $permission='Read Only';
                              }
                              echo '<li class="list-group-item"><strong>';
                              echo $item['userID'];
                              echo "</strong><br>".$permission.'</li>';
                            }
                          }else {
                            echo '<li class="list-group-item">No other user can currently access this.</li>';
                          }
                          
                          echo '</ul></div>';
                        }
                      ?>
                  </div>
                  <div class="modal-footer">
                        <?php 
                        if ($task_to_update!=null) {
                          echo '<input type="submit" value="Update Task" name="action" class="btn btn-primary" title="Confirm updating a task" />';
                        }
                        else {
                          echo '<input type="submit" value="Add Task" name="action" class="btn btn-primary" title="Confirm adding a task" />';
                        }?>
                      </form>
        
              </div>
            </div>




          </div>
          <div class="col-8">
            <div class="card">
              <div class="card-body">
                <table class="table center" style="width:100%">
                  <thead class="thead-dark">
                    <tr >
                      <th width="25%">Task Name</th>        
                      <th width="25%">Due Date</th>    
                      <th width="10%">Completed ?</th>
                      <th width="10%">View Detail</th>
                      <th width="10%">Delete</th> 
                    </tr>
                  </thead>

                  <?php foreach ($all_tasks as $item): ?>
                  <tr>
                    <td> <?php echo $item['task_name']; ?> </td>
                    <td> <?php echo $item['due_date']; ?> </td>              
                    <td>
                        <?php 
                        if ($item['completed']==1) {
                          echo "Yes";
                        }
                        else {
                          echo "No";
                        }
                        ?>
                    </td>                        
                    <td>
                    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                        <input type="submit" value="Details" class="btn btn-primary" name="action" title="Update the record" />        
                        <input type="hidden" name="task_to_update" value="<?php echo $item['taskID']; ?>" />
                      </form> 
                    </td>   
                    <td>
                      <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                        <input type="submit" value="Delete" name="action" class="btn btn-danger" title="Permanently delete the record" />      
                        <input type="hidden" name="task_to_delete" value="<?php echo $item['taskID']; ?>" />
                      </form>
                    </td>                                             
                  </tr>
                  <?php endforeach; ?>  
                </table>
              </div>
            </div>
            
          </div>
        
        </div>
      </div>

      

    </div>

    <!-- Modal -->
    <div class="modal fade" id="shareTaskModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Share Task</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            
            <form name="addTask" action="tasks.php" method="post">
                <div class="form-group">
                  User ID
                  <input type="text" class="form-control" name="userID" required value=""/>
                </div>  
                <div class="form-group">
                  Task ID
                  <input type="number" class="form-control" name="taskID" required value="">
                </div>  
                <div class="form-group">
                  Permission
                  <select name="permission" required class="form-control">
                    <option value="2" >Edit</option>
                    <option value="1" >Read Only</option>
                  </select> 
                </div>  
          </div>
          <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <input type="submit" value="Share Task" name="action" class="btn btn-primary" title="Confirm sharing a task" /> 
                <input type="submit" value="Unshare Task" name="action" class="btn btn-primary" title="Confirm unsharing a task" /> 
                </form>
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
  

