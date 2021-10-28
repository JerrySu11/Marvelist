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
require('functions.php');
require('business.php');
$all_lists = getAllLists($_SESSION['sess_user_id']);
$list_to_update = null;
$all_sharing = null;
$all_associated_tasks=null;
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (!empty($_POST['action']) && ($_POST['action']=='Add List'))
  {
    if(empty($_POST['completed'])) {
      $_POST['completed']=0;
    }
    addList($_POST['list_name'],$_POST['description'], $_POST['completed']);
    $all_lists = getAllLists($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Details'))
  {
    $list_to_update = getListByID($_POST['list_to_update']);
    $all_sharing = getAllSharingByListID($_POST['list_to_update']);
    $all_associated_tasks = getAllAssociatedTasksByListID($_POST['list_to_update']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Update List'))
  {
    if(empty($_POST['completed'])) {
      $_POST['completed']=0;
    }
    updateList($_POST['listID'], $_POST['list_name'], $_POST['description'], $_POST['completed']);
    $all_lists = getAllLists($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Delete'))
  {
    deleteList($_POST['list_to_delete'], $_SESSION['sess_user_id']);
    $all_lists = getAllLists($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Share List'))
  {
    // echo "I was here";
    addShareList($_POST['userID'], $_POST['listID'],  $_POST['permission'], $_SESSION['sess_user_id']);
    $all_lists = getAllLists($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Unshare List'))
  {
    deleteShareList($_POST['userID'], $_POST['listID'], $_SESSION['sess_user_id']);
    $all_lists = getAllLists($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Add Task to List'))
  {
    addTaskList($_POST['taskID'], $_POST['listID'], $_SESSION['sess_user_id']);
    $all_lists = getAllLists($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Remove Task from List'))
  {
    deleteTaskList($_POST['taskID'], $_POST['listID'], $_SESSION['sess_user_id']);
    $all_lists = getAllLists($_SESSION['sess_user_id']);
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
    <h1 class="h2">Lists</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
      <div class="btn-group mr-2">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#shareListModal">Share/Unshare</button>
        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#taskListModal">Add/Remove Task to List</button>


        </div>
          
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-4">
            <div class="card">
              <div class="card-header">
                      <?php 
                        if ($list_to_update!=null) {
                          echo 'List Details';
                        }
                        else {
						  echo 'New List';
                        }
                      ?>
              </div>

              <div class="card-body">
                <form name="addList" action="list.php" method="post">
                      <?php 
                        if ($list_to_update!=null) {
                          echo '<div class="form-group">List ID <input type="text" class="form-control" name="listID" required readonly value="'.$list_to_update['listID'].'"/> </div>';
                        }
                      ?>
                      <div class="form-group">
                        List Name
                        <input type="text" class="form-control" name="list_name" required 
                        value="<?php if ($list_to_update!=null) echo $list_to_update['list_name'] ?>"/>
                      </div>  

                      
                    
                      <div class="form-group">
                        Description    
                        <textarea id="list_description" class="form-control" name="description" rows="4" >
                        <?php if ($list_to_update!=null) echo $list_to_update['description'] ?>
                        </textarea>  
                      </div> 

                      <div class="form-group">
                        <input type="checkbox" id="completed" name="completed" value="1" 
                        <?php if ($list_to_update!=null && $list_to_update['completed']==1) echo 'checked' ?>>
                        <label for="completed"> Completed</label><br>       
                      </div>
                      <?php 
                        if ($list_to_update!=null){
                          echo '<div class="card" style="width: 100%;"> <div class="card-header">Tasks Associated With This List </div><ul class="list-group list-group-flush">';
                          if ($all_associated_tasks!=null) {
                            foreach ($all_associated_tasks as $item) {
                                $task_detail = getTaskByID($item['taskID']);
                              echo '<li class="list-group-item"><strong>';
                              echo $task_detail['task_name'];
                              echo "</strong><br>Task ID: ".$item['taskID'].'</li>';
                            }
                          }else {
                            echo '<li class="list-group-item">There are no tasks currently associated with this list.</li>';
                          }
                          
                          echo '</ul></div>';
                        }
                ?>
                <br>
                      <?php 
                        if ($list_to_update!=null){
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
                        if ($list_to_update!=null) {
                          echo '<input type="submit" value="Update List" name="action" class="btn btn-primary" title="Confirm updating a list" />';
                        }
                        else {
                          echo '<input type="submit" value="Add List" name="action" class="btn btn-primary" title="Confirm adding a list" />';
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
                      <th width="25%">List Name</th>  
                      <th width="25%">Due Date</th>         
                      <th width="10%">Completed ?</th>
                      <th width="10%">View Detail</th>
                      <th width="10%">Delete</th> 
                    </tr>
                  </thead>

                  <?php foreach ($all_lists as $item): ?>
                  <tr>
                    <td> <?php echo $item['list_name']; ?> </td> 
                    <td>   
                      <?php 
                        $date = getListDueDate($item['listID']); 
                        if ($date=='') {
                          echo 'No Due Date Yet';
                        } else {
                          echo $date;
                        }
                        
                      ?>  
                    </td>         
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
                        <input type="hidden" name="list_to_update" value="<?php echo $item['listID']; ?>" />
                      </form> 
                    </td>   
                    <td>
                      <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                        <input type="submit" value="Delete" name="action" class="btn btn-danger" title="Permanently delete the record" />      
                        <input type="hidden" name="list_to_delete" value="<?php echo $item['listID']; ?>" />
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
    <div class="modal fade" id="shareListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Share List</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            
            <form name="addList" action="list.php" method="post">
                <div class="form-group">
                  User ID
                  <input type="text" class="form-control" name="userID" required value=""/>
                </div>  
                <div class="form-group">
                  List ID
                  <input type="number" class="form-control" name="listID" required value="">
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
                <input type="submit" value="Share List" name="action" class="btn btn-primary" title="Confirm sharing a list" /> 
                <input type="submit" value="Unshare List" name="action" class="btn btn-primary" title="Confirm unsharing a list" /> 
                </form>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="taskListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Share List</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            
            <form name="addList" action="list.php" method="post">
                <div class="form-group">
                  Task ID
                  <input type="text" class="form-control" name="taskID" required value=""/>
                </div>  
                <div class="form-group">
                  List ID
                  <input type="number" class="form-control" name="listID" required value="">
                </div>  
                
          </div>
          <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <input type="submit" value="Add Task to List" name="action" class="btn btn-primary" title="Confirm sharing a list" /> 
                <input type="submit" value="Remove Task from List" name="action" class="btn btn-primary" title="Confirm unsharing a list" /> 
                </form>
          </div>
        </div>
      </div>
    </div>




</main>

</div>
</div>


<br><br>

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