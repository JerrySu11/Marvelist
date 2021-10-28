<?php
include("connectdb.php");
function getGroupsByID($userID)
{
	global $db;
	
	// bad
	//$query = "SELECT * FROM friends WHERE name ='" . $name . "'";
	//$statement = $db->query($query);
	
	// good, use prepare statement to minimize chance of sql injection
	$query = "SELECT * FROM `group_user` NATURAL JOIN `group` WHERE userID = :ID";
	$statement = $db->prepare($query);
	$statement->bindValue(':ID', $userID);
	$statement->execute();
	
	// fetchAll() returns an array for all of the rows in the result set
	// fetch() return a row
	$results = $statement->fetchAll();
	
	// closes the cursor and frees the connection to the server so other SQL statements may be issued
	$statement->closecursor();
	
	return $results;
}

function updateGroup($groupID, $group_name, $description, $location)
{
	global $db;
	try{
		$query = "UPDATE `group` SET group_name=:group_name, description = :description, location=:location WHERE groupID=:groupID";
		$statement = $db->prepare($query);
		$statement->bindValue(':groupID', $groupID);
		$statement->bindValue(':group_name', $group_name);
		$statement->bindValue(':description', $description);
		$statement->bindValue(':location', $location);
		$statement->execute();
		$statement->closeCursor();
	}
	catch (PDOException $e) {
      echo "Error : ".$e->getMessage();
    }
	
}

function createGroup($userID,$group_name, $description, $location,$parent_groupID){
	$groupID = getMaxGroupID();
	global $db;
	$query = "INSERT INTO `group` VALUES (:groupID,:group_name,:description,:location)";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':groupID', $groupID);
	$stmt->bindValue(':group_name', $group_name);
	$stmt->bindValue(':description', $description);
	$stmt->bindValue(':location', $location);

	$stmt->execute();
	$stmt->closeCursor();
	
	$query = "INSERT INTO `group_user` VALUES (:groupID,:userID,:permission)";
	$stmt = $db->prepare($query);
	$stmt->bindValue(':groupID', $groupID);
	$stmt->bindValue(':userID', $userID);
	//1 is read and write. 2 is read only
	$stmt->bindValue(':permission', 1);
	$stmt->execute();

	$stmt->closeCursor();

	if (!empty($parent_groupID)){
		$queryTest = "SELECT * FROM `group` WHERE groupID = :groupID";
		$stmt = $db->prepare($queryTest);
		$stmt->bindValue(':groupID', $parent_groupID);
		$stmt->execute();
		$results = $stmt->fetch();
		if (!empty($results)){
			$query = "INSERT INTO `subgroup_of` VALUES (:parent_groupID,:sub_groupID)";
			$stmt = $db->prepare($query);
			$stmt->bindValue(':parent_groupID', $parent_groupID);
			$stmt->bindValue(':sub_groupID', $groupID);
			$stmt->execute();

			$stmt->closeCursor();
		}
	}
}
function getMaxGroupID(){
	global $db;
	$query = "SELECT MAX(groupID) AS maxID FROM `group`";
	$statement = $db->prepare($query);
	$statement->execute();
	$results = $statement->fetch();
	$statement->closeCursor();
	return $results['maxID']+1;
}
function deleteGroup($groupID){
	global $db;
	$query = "DELETE FROM `group` WHERE groupID = :groupID";
	$statement = $db->prepare($query);
	$statement->bindValue(':groupID', $groupID);
	$statement->execute();
	$statement->closeCursor();

	$query = "DELETE FROM `group_user` WHERE groupID = :groupID";
	$statement = $db->prepare($query);
	$statement->bindValue(':groupID', $groupID);
	$statement->execute();
	$statement->closeCursor();

	$query = "DELETE FROM `subgroup_of` WHERE parent_groupID = :groupID OR sub_groupID = :groupID";
	$statement = $db->prepare($query);
	$statement->bindValue(':groupID', $groupID);
	$statement->execute();
	$statement->closeCursor();
}
function leaveGroup($userID,$groupID){
	global $db;
	$query = "DELETE FROM `group_user` WHERE groupID = :groupID AND userID = :userID";
	$statement = $db->prepare($query);
	$statement->bindValue(':groupID', $groupID);
	$statement->bindValue(':userID', $userID);
	$statement->execute();
	$statement->closeCursor();
}
function getSubGroupsByID($groupID){
	global $db;
	$query = "SELECT * FROM `subgroup_of`, `group` WHERE `subgroup_of`.parent_groupID = :groupID AND `subgroup_of`.sub_groupID = `group`.groupID";
	$statement = $db->prepare($query);
	$statement->bindValue(':groupID', $groupID);
	$statement->execute();
	
	// fetchAll() returns an array for all of the rows in the result set
	// fetch() return a row
	$results = $statement->fetchAll();
	
	// closes the cursor and frees the connection to the server so other SQL statements may be issued
	$statement->closecursor();
	
	return $results;
}
function deleteSubGroupsByID($parentgroup,$subgroup){
	global $db;
	$query = "DELETE FROM `subgroup_of` WHERE parent_groupID = :parentgroupID AND sub_groupID = :subgroupID";
	$statement = $db->prepare($query);
	$statement->bindValue(':parentgroupID', $parentgroup);
	$statement->bindValue(':subgroupID', $subgroup);
	$statement->execute();
	$statement->closeCursor();
}

function hasGroupWritePermission($userID,$groupID){
	global $db;
	$query = "SELECT permission FROM group_user WHERE userID = :userID AND groupID = :groupID";
	$statement = $db->prepare($query);
	$statement->bindValue(':groupID', $groupID);
	$statement->bindValue(':userID', $userID);
	$statement->execute();
	$results = $statement->fetch();
	return $results['permission']=='1';
}
function joinGroup($userID, $groupID){
	global $db;

	$queryTest = "SELECT * FROM `group` WHERE groupID = :groupID";
	$stmt = $db->prepare($queryTest);
	$stmt->bindValue(':groupID', $groupID);
	$stmt->execute();
	$results = $stmt->fetch();
	if (!empty($results)){
		$query = "INSERT INTO `group_user` VALUES (:groupID,:userID,:permission)";
		$stmt = $db->prepare($query);
		$stmt->bindValue(':groupID', $groupID);
		$stmt->bindValue(':userID', $userID);
		//1 is read and write. 2 is read only
		$stmt->bindValue(':permission', 2);
		$stmt->execute();

		$stmt->closeCursor();
	}
	
}
function getMemberByID($groupID){
	global $db;
	$query = "SELECT userID,first_name,last_name,email,phone FROM group_user NATURAL JOIN user WHERE groupID = :groupID";
	$statement = $db->prepare($query);
	$statement->bindValue(':groupID', $groupID);
	$statement->execute();
	$results = $statement->fetchAll();
	return $results;
}

?>