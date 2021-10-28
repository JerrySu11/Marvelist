<form method="post">
  <table class="registerTable">
     <tr>
      <th>Register</th>
     </tr>
     <tr>
      <td>
        <label class="firstLabel">Username:</label>
        <input type="text" name="username" id="username" value="" autocomplete="off" required/>
      </td>
     </tr>
     <tr>
      <td><label>Password:</label>
        <input type="password" name="password" id="password" value="" autocomplete="off" required/></td>
     </tr>
     <tr>
      <td>
        <label>First name:</label>
        <input type="text" name="firstname" id="firstname" value="" autocomplete="off" required/>
      </td>
     </tr>
     <tr>
      <td>
        <label>Last name:</label>
        <input type="text" name="lastname" id="lastname" value="" autocomplete="off" required/>
      </td>
     </tr>
     
     <tr>
      <td>
        <label>Email:</label>
        <input type="email" name="email" id="email" value="" autocomplete="off" required/>
      </td>
     </tr>
     <tr>
      <td>
        <label>Phone number:</label>
        <input type="text" name="phone" id="phone" value="" autocomplete="off" />
      </td>
     </tr>
     <tr>
      <td>
         <input type="submit" name="submitBtnRegister" id="submitBtnRegister" value="Register" />
         <span ><?php echo @$msg;?></span>
      </td>
     </tr>
  </table>
</form>

<?php 
session_start();
include("connectdb.php");

?>
<?php

$msg = ""; 
if(isset($_POST['submitBtnRegister'])) {
  $username = trim($_POST['username']);
  $password = password_hash(trim($_POST['password']),PASSWORD_DEFAULT);
  $first_name = trim($_POST['firstname']);
  $last_name = trim($_POST['lastname']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);

  $phoneMatches = "";
  
  
  if(preg_match( '/^\d{10}$/', $phone,  $phoneMatches )) {
    try {
      $formatPhone = sprintf("%s-%s-%s",substr($phone, 0, 3),substr($phone, 3, 3), substr($phone, 6));
      $query = "INSERT INTO user VALUES (:username,:firstname,:lastname,:password,:email,:phone)";
      $stmt = $db->prepare($query);
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':firstname', $first_name);
      $stmt->bindValue(':lastname', $last_name);
      $stmt->bindValue(':password', $password);
      $stmt->bindValue(':email', $email);
      $stmt->bindValue(':phone', $formatPhone);

      $stmt->execute();

      $stmt->closeCursor();
      $row   = $stmt->fetch(PDO::FETCH_ASSOC);
      
      echo '<script>alert("Registered successfully")</script>';
      echo "<script>location='index.php'</script>";

      
    } catch (PDOException $e) {
      echo "Error : ".$e->getMessage();
    }
  } else if (empty($phone)){
    try {
      

      $query = "INSERT INTO user (`userID`, `first_name`, `last_name`, `password`, `email`) VALUES (:username,:firstname,:lastname,:password,:email)";
      $stmt = $db->prepare($query);
      $stmt->bindValue(':username', $username);
      $stmt->bindValue(':firstname', $first_name);
      $stmt->bindValue(':lastname', $last_name);
      $stmt->bindValue(':password', $password);
      $stmt->bindValue(':email', $email);
      $stmt->execute();

      $stmt->closeCursor();
      $row   = $stmt->fetch(PDO::FETCH_ASSOC);
      echo '<script>alert("Registered successfully")</script>';
      echo "<script>location='index.php'</script>";

      
    } catch (PDOException $e) {
      echo "Error : ".$e->getMessage();
    }
  } else{
    $msg = "Phone number needs to be 10 digit US number or be empty.";
    echo $msg;
  }
}
?>