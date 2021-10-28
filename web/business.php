<?php 

$alert_prefix = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error! </strong>';
$alert_suffix = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

function getAllOwnTasks($userID)
{
    try {
        global $db;
        $query = "SELECT * FROM task T1, (SELECT taskID FROM own_task WHERE userID=:userID) T2 WHERE T1.taskID=T2.taskID";
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
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

function getAllShareTasks($userID)
{
    try{
        global $db;
        $query = "SELECT * FROM task T1, (SELECT taskID FROM share_task WHERE userID=:userID) T2 WHERE T1.taskID=T2.taskID";
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
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

function getAllTasks($userID)
{
    try{
        $ownedTasks = getAllOwnTasks($userID);
        $sharedTasks = getAllShareTasks($userID);
    
        return array_merge($ownedTasks,$sharedTasks);
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}


function getAllEvents($userID)
{
    try {
        global $db;
        $query = "SELECT * FROM event T1  WHERE T1.userID=:userID";
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
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

function getTasksByEventID($eventID)
{
    try {
        global $db;
        $query = "SELECT * FROM task T1, (SELECT taskID FROM event_task WHERE eventID=:eventID) T2 WHERE T1.taskID=T2.taskID";
        $statement = $db->prepare($query);
        $statement->bindValue(':eventID', $eventID);
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



// function getAllOwnLists($userID)
// {
//     try {
//         global $db;
//         $query = "SELECT * FROM list T1, (SELECT listID FROM own_list WHERE userID=:userID) T2 WHERE T1.listID=T2.listID";
//         $statement = $db->prepare($query);
//         $statement->bindValue(':userID', $userID);
//         $statement->execute();		// run query
//         $results = $statement->fetchAll(); //returns an arrays of rows
//         $statement->closeCursor();
    
//         return $results;
//     }
// 	catch (PDOException $e) {
//         global $alert_prefix;
//         global $alert_suffix;
//         echo $alert_prefix . $e->getMessage(). $alert_suffix;
//     }
// }

function addTask($task_name, $due_date, $due_time, $description, $priority, $location, $completed)
{
    try {	
        global $db;
        $query = "INSERT INTO `task` (`taskID`,`task_name`, `date_created`, `time_created`, `due_date`, `due_time`, `description`, `priority`, `location`, `completed`) VALUES (:taskID, :task_name, :date_created, :time_created, :due_date, :due_time, :description, :priority, :location, :completed);";
        
        // $timezone = date_default_timezone_get();
        date_default_timezone_set('America/New_York');
        $date_created = date('Y-m-d');
        $time_created = date('H:i:s');

        $taskID = getCurrMaxID_task()+1;
        
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
        $statement->bindValue(':task_name', $task_name);
        $statement->bindValue(':date_created', $date_created);
        $statement->bindValue(':time_created', $time_created);
        $statement->bindValue(':due_date', $due_date);
        $statement->bindValue(':due_time', $due_time);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':priority', $priority);
        $statement->bindValue(':location', $location);
        $statement->bindValue(':completed', $completed);
        $statement->execute();		// run query
        // $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    
    try {
        $add_ownership_query = "INSERT INTO `own_task` (`userID`, `taskID`) VALUES (:userID, :taskID)";
        $statement2 = $db->prepare($add_ownership_query);
        $statement2->bindValue(':userID', $_SESSION['sess_user_id']);
        $statement2->bindValue(':taskID', $taskID);
        $statement2->execute();		// run query
        $statement2->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function updateTask($taskID, $task_name, $due_date, $due_time, $description, $priority, $location, $completed)
{
    try {	
        global $db;
        $query = "UPDATE `task` SET `task_name`=:task_name, `description`=:description, `due_date`=:due_date, `due_time`=:due_time, `priority`=:priority, `location`=:location, `completed`=:completed WHERE `taskID`=:taskID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':task_name', $task_name);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':due_date', $due_date);
        $statement->bindValue(':due_time', $due_time);
        $statement->bindValue(':priority', $priority);
        $statement->bindValue(':location', $location);
        $statement->bindValue(':completed', $completed);
        $statement->bindValue(':taskID', $taskID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function deleteTask($taskID, $userID)
{
    try {	
        global $db;
        $query = "DELETE FROM `task` WHERE `taskID`=:taskID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    try {	
        $query = "DELETE FROM `own_task` WHERE `taskID`=:taskID AND `userID`=:userID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
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
        $query = "DELETE FROM `share_task` WHERE `taskID`=:taskID AND `userID`=:userID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
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
        $query = "DELETE FROM `event_task` WHERE `taskID`=:taskID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    try {	
        $query = "DELETE FROM `task_list` WHERE `taskID`=:taskID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function addShareTask($userID, $taskID, $permission, $sharerID)
{
    try {	
        global $db;

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

        $all_tasks = getAllTasks($sharerID);
        $validTaskFlag = False;
        foreach ($all_tasks as $item) {
            if ($item['taskID']==$taskID) {
                $validTaskFlag = True;
                break;
            }
        }
        if(!$validTaskFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this task (Task ID: '.$taskID.").");
        }


        $query = "UPDATE `share_task` SET `permission`=:permission WHERE `userID`=:userID AND `taskID`=:taskID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':taskID', $taskID);
        $statement->bindValue(':permission', $permission);
        $statement->execute();		// run query
        // $statement->closeCursor(); // release hold on this connection

        $query = "INSERT INTO `share_task` (`userID`, `taskID`, `permission`) VALUES (:userID, :taskID, :permission);";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':taskID', $taskID);
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

function getAllSharingByTaskID($taskID)
{
    try {
        global $db;
        $query = "SELECT * FROM share_task T1 WHERE T1.taskID=:taskID";
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
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


function deleteShareTask($userID, $taskID, $sharerID)
{
    try {	
        global $db;

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

        $all_tasks = getAllTasks($sharerID);
        $validTaskFlag = False;
        foreach ($all_tasks as $item) {
            if ($item['taskID']==$taskID) {
                $validTaskFlag = True;
                break;
            }
        }
        if(!$validTaskFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this task (Task ID: '.$taskID.").");
        }

        $query = "DELETE FROM `share_task` WHERE `userID`=:userID AND `taskID`=:taskID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':taskID', $taskID);
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

function getTaskByID($taskID)
{
    try {
        global $db;
        $query = "SELECT * FROM task T1 WHERE T1.taskID=:taskID";
        $statement = $db->prepare($query);
        $statement->bindValue(':taskID', $taskID);
        $statement->execute();		// run query
        $results = $statement->fetch(); //returns an arrays of rows
        $statement->closeCursor();
    
        return $results;
    }
	catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}


function addEvent($userID, $event_name, $start_date, $start_time, $end_date, $end_time)
{
    try {
        global $db;
        $d_start=strtotime($start_date." ".$start_time);
        $d_end=strtotime($end_date." ".$end_time)."<br>";

        if ($d_start>$d_end) {
            throw new InvalidArgumentException('Event start time is greater than end time.');
        }
        $query = "INSERT INTO `event` (`eventID`, `userID`, `event_name`, `start_date`, `start_time`, `end_date`, `end_time`) VALUES (:eventID, :userID, :event_name, :start_date, :start_time, :end_date, :end_time)";
        
        date_default_timezone_set('America/New_York');

        $eventID = getCurrMaxID_event()+1;
        
        $statement = $db->prepare($query);
        $statement->bindValue(':eventID', $eventID);
        $statement->bindValue(':userID', $userID);
        $statement->bindValue(':event_name', $event_name);
        $statement->bindValue(':start_date', $start_date);
        $statement->bindValue(':start_time', $start_time);
        $statement->bindValue(':end_date', $end_date);
        $statement->bindValue(':end_time', $end_time);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (InvalidArgumentException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function getEventByID($eventID)
{
    try {
        global $db;
        $query = "SELECT * FROM event T1 WHERE T1.eventID=:eventID";
        $statement = $db->prepare($query);
        $statement->bindValue(':eventID', $eventID);
        $statement->execute();		// run query
        $results = $statement->fetch(); //returns an arrays of rows
        $statement->closeCursor();
    
        return $results;
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
	
}

function updateEvent($eventID, $event_name, $start_date, $start_time, $end_date, $end_time)
{
    try {	
        global $db;
        $query = "UPDATE `event` SET `event_name`=:event_name, `start_date`=:start_date, `start_time`=:start_time, `end_date`=:end_date, `end_time`=:end_time WHERE `eventID`=:eventID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':event_name', $event_name);
        $statement->bindValue(':start_date', $start_date);
        $statement->bindValue(':start_time', $start_time);
        $statement->bindValue(':end_date', $end_date);
        $statement->bindValue(':end_time', $end_time);
        $statement->bindValue(':eventID', $eventID);

        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function deleteEvent($eventID)
{
    try {	
        global $db;
        $query = "DELETE FROM `event` WHERE `eventID`=:eventID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':eventID', $eventID);
        $statement->execute();		// run query
        $statement->closeCursor(); // release hold on this connection
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function getAllAssociatedTasksByEventID($eventID)
{
    try {
        global $db;
        $query = "SELECT * FROM event_task T1 WHERE T1.eventID=:eventID";
        $statement = $db->prepare($query);
        $statement->bindValue(':eventID', $eventID);
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

function addEventTask($eventID, $taskID, $userID)
{
    try {
        global $db;	

        $all_events = getAllEvents($userID);
        $validEventFlag = False;
        foreach ($all_events as $item) {
            if ($item['eventID']==$eventID) {
                $validEventFlag = True;
                break;
            }
        }
        if(!$validEventFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this event (Event ID: '.$eventID.").");
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


        $query = "INSERT INTO `event_task` (`eventID`, `taskID`) VALUES (:eventID, :taskID);";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':eventID', $eventID);
        $statement->bindValue(':taskID', $taskID);
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

function deleteEventTask($eventID, $taskID, $userID)
{
    try {	
        global $db;

        $all_events = getAllEvents($userID);
        $validEventFlag = False;
        foreach ($all_events as $item) {
            if ($item['eventID']==$eventID) {
                $validEventFlag = True;
                break;
            }
        }
        if(!$validEventFlag) {
            throw new InvalidArgumentException('You do not have permission to edit this event (Event ID: '.$eventID.").");
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



        $query = "DELETE FROM `event_task` WHERE `eventID`=:eventID AND `taskID`=:taskID";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':eventID', $eventID);
        $statement->bindValue(':taskID', $taskID);
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




function getCurrMaxID_task() 
{
    try {
        global $db;
        $query = "SELECT MAX(taskID) AS maxID FROM task";
        $statement = $db->prepare($query);
        $statement->execute();		// run query
        $result = $statement->fetch();
        $statement->closeCursor(); // release hold on this connection
        
        // $statement->debugDumpParams();
        return $result['maxID'];
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}

function getCurrMaxID_event() 
{
    try {
        global $db;

        $query = "SELECT MAX(eventID) AS maxID FROM event";
        $statement = $db->prepare($query);
        $statement->execute();		// run query
        $result = $statement->fetch();
        $statement->closeCursor(); // release hold on this connection

        // $statement->debugDumpParams();  
        return $result['maxID'];
    }
    catch (PDOException $e) {
        global $alert_prefix;
        global $alert_suffix;
        echo $alert_prefix . $e->getMessage(). $alert_suffix;
    }
}
?>