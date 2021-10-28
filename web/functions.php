 
<?php

$alert_prefix = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error! </strong>';
$alert_suffix = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';


include("connectdb.php");
function getAllOwnLists($userID)
{
    try {
        global $db;
        $query = "SELECT * FROM tdlist T1, (SELECT listID FROM own_list WHERE userID=:userID) T2 WHERE T1.listID=T2.listID";
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->execute();		//run the query
        $results = $statement->fetchAll(); //return an arrays of rows of result set
        $statement->closeCursor();
    
        return $results;
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
	
}

function getAllShareLists($userID)  //CHECKKKKKKKKKKK
{
	global $db;
	$query = "SELECT * FROM tdlist T1, (SELECT listID FROM share_list WHERE userID=:userID) T2 WHERE T1.listID=T2.listID";
    $statement = $db->prepare($query);
    $statement->bindValue(':userID', $userID);
	$statement->execute();		// run the query
	$results = $statement->fetchAll(); //return an arrays of rows of result set
	$statement->closeCursor();

	return $results;
}

function getAllLists($userID)
{
    $ownedLists = getAllOwnLists($userID);
	$sharedLists = getAllShareLists($userID);

	return array_merge($ownedLists,$sharedLists);
}

function getAllTags($userID)
{
    global $db;
	$query = "SELECT * FROM tag T1  WHERE T1.userID=:userID";
    $statement = $db->prepare($query);
    $statement->bindValue(':userID', $userID);
	$statement->execute();		// run the query
	$results = $statement->fetchAll(); //return an arrays of rows of result set
	$statement->closeCursor();

	return $results;
}

function getListsByTagID($tagID)  //CHECKKKKKKKKKKKKKKKKKKKKKK
{
    global $db;
	$query = "SELECT * FROM tdlist T1, (SELECT listID FROM tag_list WHERE tagID=:tagID) T2 WHERE T1.listID=T2.listID";
    $statement = $db->prepare($query);
    $statement->bindValue(':tagID', $tagID);
	$statement->execute();		// run query
	$results = $statement->fetchAll(); //returns an arrays of rows
	$statement->closeCursor();

	return $results;
}

function addList($list_name, $description, $completed)
{
	global $db;
    try {	
        $query = "INSERT INTO `tdlist` (`listID`,`list_name`, `description`, `completed`) VALUES (:listID, :list_name, :description, :completed);";
        
        $listID = getCurrMaxID_list()+1;
        
        $statement = $db->prepare($query);
        $statement->bindValue(':listID', $listID);
        $statement->bindValue(':list_name', $list_name);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':completed', $completed);
        $statement->execute();		// run query
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    
    try {
        $add_ownership_query = "INSERT INTO `own_list` (`userID`, `listID`) VALUES (:userID, :listID)";
        $statement2 = $db->prepare($add_ownership_query);
        $statement2->bindValue(':userID', $_SESSION['sess_user_id']);
        $statement2->bindValue(':listID', $listID);
        $statement2->execute();		// run query
        $statement2->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function updateList($listID, $list_name, $description, $completed)
{
	global $db;
    try {	
        $query = "UPDATE `tdlist` SET `list_name`=:list_name, `description`=:description, `completed`=:completed WHERE `listID`=:listID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':list_name', $list_name);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':completed', $completed);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function deleteList($listID, $userID)
{
	global $db;
    try {	
        $query = "DELETE FROM `tdlist` WHERE `listID`=:listID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    try {	
        $query = "DELETE FROM `own_list` WHERE `listID`=:listID AND `userID`=:userID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':listID', $listID);
        $statement->bindValue(':userID', $userID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    try {	
        $query = "DELETE FROM `share_list` WHERE `listID`=:listID AND `userID`=:userID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':listID', $listID);
        $statement->bindValue(':userID', $userID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function addShareList($userID, $listID, $permission, $sharerID)
{
    try {	
        global $db;

        // Validate userID 

        if($userID==$sharerID) {
            throw new InvalidArgumentException('You cannot share with yourself.');
        }

        $query = "SELECT * FROM `user` WHERE userID=:userID";
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->execute();
        $all_users = $statement->fetchAll();

        if (empty($all_users)) {
            throw new InvalidArgumentException('User does not exist! (User ID: '.$userID.").");
        }

        // Validate listID 

        $all_lists = getAllLists($sharerID);

        $validListFlag = False;
        foreach ($all_lists as $item) {
            if ($item['listID']==$listID) {
                $validListFlag = True;
                break;
            }
        }
        if(!$validListFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this list (List ID: '.$listID.").");
        }        


        $query = "UPDATE `share_list` SET `permission`=:permission WHERE `userID`=:userID AND `listID`=:listID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':listID', $listID);
        $statement->bindValue(':permission', $permission);
        $statement->execute();		// run query
        // $statement->closeCursor(); // release hold on this connection

        $query = "INSERT INTO `share_list` (`userID`, `listID`, `permission`) VALUES (:userID, :listID, :permission);";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':listID', $listID);
        $statement->bindValue(':permission', $permission);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    catch (InvalidArgumentException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function getListDueDate($listID)
{
    try {
        global $db;
        
        $query ="CALL `computeListDueDate`(?, @p1);";

        $statement = $db->prepare($query);
        $statement->bindParam(1, $listID, PDO::PARAM_INT, 100000 );
        $statement->execute();	// run query

        $query ="SELECT @p1 AS date;";

        $statement = $db->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        $statement->closeCursor();

        return $result[0]['date'];
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}


function getAllSharingByListID($listID)
{
    try {
        global $db;

        $query = "SELECT * FROM share_list T1 WHERE T1.listID=:listID";
        $statement = $db->prepare($query);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        $results = $statement->fetchAll(); //returns an arrays of rows
        $statement->closeCursor();

        return $results;
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function getAllAssociatedTasksByListID($listID)
{
    try {
        global $db;
        $query = "SELECT * FROM task_list T1 WHERE T1.listID=:listID";
        $statement = $db->prepare($query);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        $results = $statement->fetchAll(); //returns an arrays of rows
        $statement->closeCursor();
    
        return $results;
    }
	catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function deleteShareList($userID, $listID, $sharerID)
{
	global $db;
    try {	
        
        // Validate userID 

        if($userID==$sharerID) {
            throw new InvalidArgumentException('You cannot unshare with yourself.');
        }

        $query = "SELECT * FROM `user` WHERE userID=:userID";
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->execute();
        $all_users = $statement->fetchAll();

        if (empty($all_users)) {
            throw new InvalidArgumentException('User does not exist! (User ID: '.$userID.").");
        }

        // Validate listID 

        $all_lists = getAllLists($sharerID);

        $validListFlag = False;
        foreach ($all_lists as $item) {
            if ($item['listID']==$listID) {
                $validListFlag = True;
                break;
            }
        }
        if(!$validListFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this list (List ID: '.$listID.").");
        }   

        $query = "DELETE FROM `share_list` WHERE `userID`=:userID AND `listID`=:listID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
    catch (InvalidArgumentException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function getListByID($listID)
{
	global $db;
	$query = "SELECT * FROM tdlist T1 WHERE T1.listID=:listID";
    $statement = $db->prepare($query);
    $statement->bindValue(':listID', $listID);
	$statement->execute();		// run query
	$results = $statement->fetch(); //returns an arrays of rows
	$statement->closeCursor();

	return $results;
}

function addTag($userID, $tag_name, $description)
{
	global $db;
    try {	
        $query = "INSERT INTO `tag` (`tagID`, `userID`, `tag_name`, `description`) VALUES (:tagID, :userID, :tag_name, :description)";

        $tagID = getCurrMaxID_tag()+1;
        
        $statement = $db->prepare($query);
        $statement->bindValue(':tagID', $tagID);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':tag_name', $tag_name);
        $statement->bindValue(':description', $description);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function getTagByID($tagID)
{
	global $db;
	$query = "SELECT * FROM tag T1 WHERE T1.tagID=:tagID";
    $statement = $db->prepare($query);
    $statement->bindValue(':tagID', $tagID);
	$statement->execute();		// run query
	$results = $statement->fetch(); //returns an arrays of rows
	$statement->closeCursor();

	return $results;
}

function updateTag($tagID, $tag_name, $description)
{
	global $db;
    try {	
        $query = "UPDATE `tag` SET `tag_name`=:tag_name, `description`=:description WHERE `tagID`=:tagID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':tag_name', $tag_name);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':tagID', $tagID);

        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}


function deleteTag($tagID)
{
	global $db;
    try {	
        $query = "DELETE FROM `tag` WHERE `tagID`=:tagID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':tagID', $tagID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function getAllAssociatedListsByTagID($tagID)  //CHECKKKKKKKKKKK
{
	global $db;
	$query = "SELECT * FROM tag_list T1 WHERE T1.tagID=:tagID";
    $statement = $db->prepare($query);
    $statement->bindValue(':tagID', $tagID);
	$statement->execute();		// run query
	$results = $statement->fetchAll(); //returns an arrays of rows
	$statement->closeCursor();

	return $results;
}

function addTagList($tagID, $listID)
{
    global $db;
    try {	
        $query = "INSERT INTO `tag_list` (`tagID`, `listID`) VALUES (:tagID, :listID);";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':tagID', $tagID);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function deleteTagList($tagID, $listID)
{
	global $db;
    try {	
        $query = "DELETE FROM `tag_list` WHERE `tagID`=:tagID AND `listID`=:listID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':tagID', $tagID);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        echo "Error : " . $e->getMessage();
    }
}

function getCurrMaxID_list() {
    global $db;

    $query = "SELECT MAX(listID) AS maxID FROM tdlist";
    $statement = $db->prepare($query);
    $statement->execute();		// run query
    $result = $statement->fetch();
    $statement->closeCursor(); // release hold on this connection
    
    return $result['maxID'];
}

function getCurrMaxID_tag() {
    global $db;

    $query = "SELECT MAX(tagID) AS maxID FROM tag";
    $statement = $db->prepare($query);
    $statement->execute();		// run query
    $result = $statement->fetch();
    $statement->closeCursor(); // release hold on this connection

    return $result['maxID'];
}

// function getTaskByID($taskID)
// {
//     try {
//         global $db;
//         $query = "SELECT * FROM task T1 WHERE T1.taskID=:taskID";
//         $statement = $db->prepare($query);
//         $statement->bindValue(':taskID', $taskID);
//         $statement->execute();		// run query
//         $results = $statement->fetch(); //returns an arrays of rows
//         $statement->closeCursor();
    
//         return $results;
//     }
// 	catch (PDOException $e) {
//         global $alert_prefix;
//         global $alert_suffix;
//         echo $alert_prefix . $e->getMessage(). $alert_suffix;
//     }
// }

function addTaskList($taskID, $listID, $userID)
{
    try {
        global $db;

        $all_lists = getAllLists($userID);

        $validListFlag = False;
        foreach ($all_lists as $item) {
            if ($item['listID']==$listID) {
                $validListFlag = True;
                break;
            }
        }
        if(!$validListFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this list (List ID: '.$listID.").");
        }      

        $all_tasks = getAllTasks($userID);
        $validTaskFlag = False;
        foreach ($all_tasks as $item) {
            if ($item['taskID']==$taskID) {
                $validTaskFlag = True;
                break;
            }
        }
        if(!$validTaskFlag) {
            throw new InvalidArgumentException('You do not have permission to view this task (Task ID: '.$taskID.").");
        }

        $query = "INSERT INTO `task_list` (`taskID`, `listID`) VALUES (:taskID, :listID)";
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        // $results = $statement->fetch(); //returns an arrays of rows
        $statement->closeCursor();
    
    }
	catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    catch (InvalidArgumentException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function deleteTaskList($taskID, $listID, $userID)
{
    try {
        global $db;

        $all_lists = getAllLists($userID);

        $validListFlag = False;
        foreach ($all_lists as $item) {
            if ($item['listID']==$listID) {
                $validListFlag = True;
                break;
            }
        }
        if(!$validListFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this list (List ID: '.$listID.").");
        }      

        $all_tasks = getAllTasks($userID);
        $validTaskFlag = False;
        foreach ($all_tasks as $item) {
            if ($item['taskID']==$taskID) {
                $validTaskFlag = True;
                break;
            }
        }
        if(!$validTaskFlag) {
            throw new InvalidArgumentException('You do not have permission to view this task (Task ID: '.$taskID.").");
        }

        $query = "DELETE FROM `task_list` WHERE taskID=:taskID AND listID=:listID";
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
        $statement->bindValue(':listID', $listID);
        $statement->execute();		// run query
        // $results = $statement->fetch(); //returns an arrays of rows
        $statement->closeCursor();
    
    }
	catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    catch (InvalidArgumentException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}
?>


