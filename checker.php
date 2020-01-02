<?php
	//Please change the following variables to their appropriate values
	$db_host = "localhost"; //MySQL Host name
	$db_username = "root"; //MySQL User name
	$db_password =""; //MySQL password
	$db_name = "online_users"; //Database name
	
	$con = mysql_connect($db_host, $db_username, $db_password);
	

	//For number of online users
	$num_of_users_online = 0;
	$num_online_today = 0;
	if(isset($_GET[num_online]))
	{
		$query = "SELECT * FROM ". $db_name .".online";
		$result = mysql_query($query, $con);
		if(mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_assoc($result)) 
			{	//If last seen is less than 1 mins, user is assumed online
				if(abs((strtotime($row['time'])-strtotime(date('Y-m-d H:i:s', strtotime("now"))))) < 60)
				{
					$num_of_users_online++;
				}
				
				//Number online today
				if(((strtotime($row['time'])-strtotime(date('Y-m-d H:i:s', strtotime("midnight now")))) < 86400) && ((strtotime($row['time'])-strtotime(date('Y-m-d H:i:s', strtotime("midnight now")))) > 0))
				{
					$num_online_today++;
				}
			}
		}
		echo "{'users':'" . $num_of_users_online . "', 'today':'" . $num_online_today . "'}";//Returns JSON
	}
	
	//Last seen for users
	if(isset($_GET[last_seen]))
	{
		$query = "SELECT time FROM ". $db_name .".online WHERE id = '%s'";
		$query = sprintf($query, mysql_real_escape_string(stripslashes($_GET[id])));
		$result = mysql_query($query, $con);
		if(mysql_num_rows($result) > 0)
		{
			
			$row = mysql_fetch_assoc($result); 
			$sec_pass = abs(strtotime($row['time'])-strtotime(date('Y-m-d H:i:s', strtotime("now"))));
			if($sec_pass < 60)// If time is less than 60 seconds, user is online
			{
				$timepast = "online";
				echo "Last Seen for user " . $_GET[id] . ":" . $timepast;
				return 0;
			}
				
			$days = floor($sec_pass/86400);	
			if ($days > 0)
				$timepast = $days . " days";
     		if ($days == 1)
				$timepast = $days . " day";
   			
			$sec_pass = $sec_pass - ($days * 86400);
			$hours = floor($sec_pass/3600);
     		if ($hours > 1)
				$timepast = $timepast . " " . $hours . " hours";
     		if ($hours == 1)
				$timepast = $timepast . " " . $hours . " hour";
   			
			$sec_pass = $sec_pass - ($hours * 3600);
			$minutes = floor($sec_pass/60);
     		if ($minutes > 1)
				$timepast = $timepast . " " . $minutes . " minutes";
     		if ($minutes == 1)
				$timepast = $timepast . " " . $minutes . " minute";
				
			$sec_pass = $sec_pass - ($minutes * 60);
     		if ($sec_pass > 0)
				$timepast = $timepast . " " . $sec_pass . " seconds";
		}
		echo "Last Seen for user " . $_GET[id] . ":" . $timepast . " ago"; 
	}
	
	//Report your presence
	if(isset($_GET[report]))
	{
		$query = "SELECT * FROM ". $db_name .".online WHERE id = '%s'";
		$query = sprintf($query, mysql_real_escape_string(stripslashes($_GET[id])));
		if(mysql_num_rows(mysql_query($query, $con)) > 0)
		{
			$query = "UPDATE ". $db_name .".online SET time = '%s' WHERE id = '%s'";
			$now = date('Y-m-d H:i:s', strtotime('now'));
			$query = sprintf($query, $now, mysql_real_escape_string(stripslashes($_GET[id])));
			mysql_query($query, $con);echo $query;
		}
		else
		{
			$query = "INSERT INTO ". $db_name .".online (id, time) VALUES ('%s', '%s')";
			$now = date('Y-m-d H:i:s', strtotime('now'));
			$query = sprintf($query, mysql_real_escape_string(stripslashes($_GET[id])), $now);
			mysql_query($query, $con);
		}
		//Set highest online info
		setHighestOnline($con, $db_name);
	}
	
	//Get the highest online ever
	if(isset($_GET[highest]))
	{
		$query = "SELECT * FROM ". $db_name .".highest";
		$result = mysql_query($query, $con);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_assoc($result); 
			echo "Highest Online Ever: There were " . $row['num'] . " users online on " . date("F d Y H:i:s A", strtotime($row['time'])) . ".";
		}
		else
		{
			setHighestOnline($con, $db_name);
		}
	}
	
	//Set the highest online at all time
	$highest_online = 0;
	function setHighestOnline($con, $db_name)
	{
		//Get number of user currently online
		mysql_select_db($db_name, $con);
		$query = "SELECT * FROM ". $db_name .".online";
		$result = mysql_query($query, $con);

		if(mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_assoc($result)) 
			{	//If last seen is less than 1 minute, user is assumed online
				if(abs((strtotime($row['time'])-strtotime(date('Y-m-d H:i:s', strtotime("now"))))) < 60)
				{
					$highest_online++;
				}
			}
		}
		
		//Get highest online previous record
		$query = "SELECT * FROM ". $db_name .".highest";
		$result = mysql_query($query, $con);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_assoc($result);
			if($highest_online >= $row['num'])
			{
				//The currently online is greater than or equals to the previous record so update
				$query = "UPDATE ". $db_name .".highest SET time = NOW(), num = '" . $highest_online . "'";
				mysql_query($query, $con);
			}
		}
		else
		{
			//Highest online never set before so set the record
			$query = "INSERT INTO ". $db_name .".highest(time, num) VALUES(NOW(), '" . $highest_online . "')"; 
			mysql_query($query, $con);
		}
	}

	//Report your presence
if(isset($_GET[report]))
{
    $query = "SELECT * FROM ". $db_name .".online WHERE id = '%s'";
    $query = sprintf($query, mysql_real_escape_string(stripslashes($_GET[id])));
    if(mysql_num_rows(mysql_query($query, $con)) > 0)
    {
        $query = "UPDATE ". $db_name .".online SET time = '%s' WHERE id = '%s'";
        $now = date('Y-m-d H:i:s', strtotime('now'));
        $query = sprintf($query, $now, mysql_real_escape_string(stripslashes($_GET[id])));
        mysql_query($query, $con);echo $query;
    }
    else
    {
        $query = "INSERT INTO ". $db_name .".online (id, time) VALUES ('%s', '%s')";
        $now = date('Y-m-d H:i:s', strtotime('now'));
        $query = sprintf($query, mysql_real_escape_string(stripslashes($_GET[id])), $now);
        mysql_query($query, $con);
    }
    //Set highest online info
    setHighestOnline($con, $db_name);
}
?>