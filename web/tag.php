<?php

session_start();
require('connectdb.php');
require('functions.php');

$all_tags = getAllTags($_SESSION['sess_user_id']);
$tag_to_update = null;
$all_sharing = null;
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (!empty($_POST['action']) && ($_POST['action']=='Add Tag'))
  {
    if(empty($_POST['completed'])) {
      $_POST['completed']=0;
    }
    addTag($_POST['tag_name'], $_POST['description'], $_POST['completed']);
    $all_tags = getAllTags($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Details'))
  {
    $tag_to_update = getTagByID($_POST['tag_to_update']);
    $all_sharing = getAllSharingByTagID($_POST['tag_to_update']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Update Tag'))
  {
    if(empty($_POST['completed'])) {
      $_POST['completed']=0;
    }
    updateTag($_POST['tagID'], $_POST['tag_name'], $_POST['description'], $_POST['completed']);
    $all_tags = getAllTags($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Delete'))
  {
    deleteTag($_POST['tag_to_delete'], $_SESSION['sess_user_id']);
    $all_tags = getAllTags($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Share Task'))
  {
    addShareList($_POST['userID'], $_POST['tagID'],  $_POST['permission']);
    $all_tags = getAllTags($_SESSION['sess_user_id']);
  }
  elseif (!empty($_POST['action']) && ($_POST['action']=='Unshare List'))
  {
    deleteShareList($_POST['userID'], $_POST['tagID']);
    $all_tags = getAllTags($_SESSION['sess_user_id']);
  }
}

?>
	
	<h1>Create Tag</h1>

<!-- <form action="formprocessing.php" method="post">  -->
<form name="mainForm" action="tag.php" method="post">  
  <div class="form-group">
    List name:
    <input type="text" class="form-control" name="tag_name" required 
    value="" 
    />        
  </div>  
  <div class="form-group">
    Description:
    <input type="text" class="form-control" name="description"  
    value=""
    />        
  </div> 
     
  <input type="submit" value="Create" name="action" class="btn btn-dark" title="Create a tag" />
</form>  
	<html>
	<head>
	  <meta charset="UTF-8">  
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <meta name="author" content="your name">
	  <meta name="description" content="include some description about your page">      
	  <title>Tag</title>
	  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	  <link rel="shortcut icon" href="http://www.cs.virginia.edu/~up3f/cs4750/images/db-icon.png" type="image/ico" />  
	</head>

	<body>
	<div class="container">

	
	<!-- <form action="formprocessing.php" method="post">  -->
	
	<h2>Tags</h2>
	
	<table class="w3-table w3-bordered w3-card-4 center" style="width:70%">
	  <thead>
	  <tr style="background-color:#B0B0B0">
	    <th width="25%">List ID</th>  
	    <th width="25%">List Name</th>        
	    <th width="25%">Description</th>        
	    <th width="10%">Update</th>
	    <th width="10%">Delete</th> 
	  </tr>
	  </thead>
	  <?php foreach ($all_tags as $item): ?>
	  	<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
	  <tr>
	  	<td><input type="text" value = "<?php echo $item['listID']; ?>" name="listID"/ readonly></td>
	    <td><input type="text" value = "<?php echo $item['list_name']; ?>" name="list_name"/></td>
	    <td><input type="text" value = "<?php echo $item['description']; ?>" name="description"/></td>              
	    <td>
	      	
	        <input type="submit" value="Update" name="action" class="btn btn-primary" title="Update the record" />  
	        <input type="hidden" name="list_to_update" value="<?php echo $item['listID'] ?>" />
	        
	      
	    </td>                        
	    <td>
	      
	        <input type="submit" value="Delete" name="action" class="btn btn-danger" title="Permanently delete the record" />      
	        <input type="hidden" name="list_to_delete" value="<?php echo $item['listID'] ?>" />
	      
	    </td>                                               
	  </tr>
	  </form>
	  <?php endforeach; ?>
	</table>
	
	                                                   
	  </tr>
	  </form>
	</table>
	</div>    
	</body>
	</html>



<?php
echo '<h4><a href="home.php">Home</a></h4>';
echo '<h4><a href="logout.php">Logout</a></h4>';

?>