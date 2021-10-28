
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
require("userDB.php");

$userID = $_SESSION['sess_user_id'];
$userInfo = getUserInfo_by_ID($userID);
if(isset($_POST['submitBtnProfile'])){
  
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $password = $password==$userInfo['password'] ? $userInfo['password']:password_hash(trim($_POST['password']),PASSWORD_DEFAULT);
  $first_name = trim($_POST['firstname']);
  $last_name = trim($_POST['lastname']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);

  if(preg_match( '/^(\d{3})-?(\d{3})-?(\d{4})$/', $phone,  $phoneMatches )){
    
    $phone = sprintf("%s-%s-%s",$phoneMatches[1],$phoneMatches[2], $phoneMatches[3]);
    updateUser($username, $first_name, $last_name, $password, $email,$phone);

      $userInfo = getUserInfo_by_ID($userID);
  }
  else{
    $msg = "Phone number needs to be 10 digit US number or be empty.";
    echo $msg;
  }

   
}
if (isset($_POST['exportData'])){
  $filename = exportUser($userID);
  header('location:'.$filename);
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
    <h1 class="h2">Profile</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
          
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-6">
            <div class="card">
              <div class="card-header">
                Profile
              </div>
              <div class="card-body">
                  <form method="post"> 
                      <div class="form-group">
                        <label class="firstLabel">Username:</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?php echo $userID?>" autocomplete="off" readonly />
                      </div> 
                      <div class="form-group">
                        <label>Password:</label>
                        <input type="password"  class="form-control" name="password" id="password" value="<?php if ($userInfo!=null) echo $userInfo['password'] ?>" autocomplete="off" required/></td>
                      </div> 
                      <div class="form-group">
                        <label>First name:</label>
                        <input type="text"  class="form-control" name="firstname" id="firstname" value="<?php if ($userInfo!=null) echo $userInfo['first_name'] ?>" autocomplete="off" required/>
                      </div> 
                      <div class="form-group">
                        <label>Last name:</label>
                        <input type="text"  class="form-control" name="lastname" id="lastname" value="<?php if ($userInfo!=null) echo $userInfo['last_name'] ?>" autocomplete="off" required/>
                      </div> 
                      <div class="form-group">
                        <label>Email:</label>
                        <input type="email"  class="form-control" name="email" id="email" value="<?php if ($userInfo!=null) echo $userInfo['email'] ?>" autocomplete="off" required/>
                      </div> 
                      <div class="form-group">
                        <label>Phone number:</label>
                        <input type="text"  class="form-control" name="phone" id="phone" value="<?php if ($userInfo!=null) echo $userInfo['phone'] ?>" autocomplete="off" />
                      </div> 
                      <div class="form-group">
                        <input type="submit"  class="btn btn-primary" name="submitBtnProfile" id="submitBtnProfile" value="Update" />
                        <span ><?php echo @$msg;?></span>
                        <input type="submit"  class="btn btn-primary" name="exportData" id="exportData" value="Export Data" />
                        <span ><?php echo @$msg;?></span>
                      </div> 
                  </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      
<br><br>

</main>










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
  
