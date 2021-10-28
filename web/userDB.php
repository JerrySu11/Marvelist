<?php
include("connectdb.php");
include("business.php");
include("functions.php");

function getUserInfo_by_ID($userID)
{
	global $db;
	
	// bad
	//$query = "SELECT * FROM friends WHERE name ='" . $name . "'";
	//$statement = $db->query($query);
	
	// good, use prepare statement to minimize chance of sql injection
	$query = "SELECT * FROM user WHERE userID = :ID";
	$statement = $db->prepare($query);
	$statement->bindValue(':ID', $userID);
	$statement->execute();
	
	// fetchAll() returns an array for all of the rows in the result set
	// fetch() return a row
	$results = $statement->fetch();
	
	// closes the cursor and frees the connection to the server so other SQL statements may be issued
	$statement->closecursor();
	
	return $results;
}

function updateUser($userID, $first_name, $last_name, $password, $email,$phone)
{
	global $db;
	try{
		$query = "UPDATE user SET first_name=:first_name, last_name=:last_name, password =:password, email=:email,phone=:phone WHERE userID=:userID";
		$statement = $db->prepare($query);
		$statement->bindValue(':userID', $userID);
		$statement->bindValue(':first_name', $first_name);
		$statement->bindValue(':last_name', $last_name);
		$statement->bindValue(':password', $password);
		$statement->bindValue(':email', $email);
		$statement->bindValue(':phone', $phone);
		$statement->execute();
		$statement->closeCursor();
	}
	catch (PDOException $e) {
      echo "Error : ".$e->getMessage();
    }
	
}

function exportUser($userID){

	$filename = $userID.'-'.date('d.m.Y').'.json';

    $data = fopen($filename, 'w');
    fwrite($data, json_encode(getUserInfo_by_ID($userID)));
    fwrite($data,json_encode(getAllOwnTasks($userID)));
    fwrite($data,json_encode(getAllOwnLists($userID)));
	fwrite($data,json_encode(getAllEvents($userID)));
    

    fclose($data);
    return $filename;
}

?>