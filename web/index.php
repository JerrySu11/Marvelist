<form method="post">
  <table class="loginTable">
     <tr>
      <th>LOGIN</th>
     </tr>
     <tr>
      <td>
        <label class="firstLabel">Username:</label>
        <input type="text" name="username" id="username" value="" autocomplete="off" required />
      </td>
     </tr>
     <tr>
      <td><label>Password:</label>
        <input type="password" name="password" id="password" value="" autocomplete="off" required/></td>
     </tr>
     <tr>
      <td>
         <input type="submit" name="submitBtnLogin" id="submitBtnLogin" value="Login" />
         <span class="loginMsg"><?php echo @$msg;?></span>
      </td>
     </tr>
  </table>
</form>

<?php 
session_start();
include("connectdb.php");
?>
<?php
echo '<h4><a href="register.php">Register</a></h4>';
$msg = ""; 
if(isset($_POST['submitBtnLogin'])) {
  
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  if($username != "" && $password != "") {
   
    try {
      $query = "select * from `user` where `userID`=:username";
      $stmt = $db->prepare($query);
      $stmt->bindParam('username', $username, PDO::PARAM_STR);
      $stmt->execute();
      
      $row   = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if(!empty($row) && password_verify($password, $row['password'])) {
        /******************** Your code ***********************/

        $_SESSION['sess_user_id']   = $row['userID'];
        $_SESSION['sess_name'] = $row['first_name'];

        echo "<script>location='home.php'</script>";
      } else {
        $msg = "Invalid username and password!";

        echo $msg;
      }
    } catch (PDOException $e) {
      
      echo "Error : ".$e->getMessage();
    }
  } else {
    $msg = "Both fields are required!";
    echo $msg;
  }
}
?>