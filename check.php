<html>
	<head>
		<title>Checking for online users...</title>
		
	</head>

	<body>
		UserID for the user whose "Last Seen" is to be checked: <input type=text value=0 id=lastseen /> (Simply change the ID here and wait to see the effect)<br><br>
		<div id="lastseen_report_pan"></div>
		<div id="numonline_report_pan"></div>
		<div id="highestonline_report_pan"></div>
		<div id="online_today"></div>
		
		<?php
			echo "<input type=hidden id=userID value=" . rand(0, 5) . ">";
		?>
		
		<script>
			function lastSeen()
			{
				i = getXMLHttpRequestObject();
				if(i != false)
				{
					url = "checker.php?last_seen&id=" + document.getElementById("lastseen").value;//id is the UserID for the user whose last seen is to be checked
					i.open("POST", url, true);
					i.onreadystatechange=function()
									{
										if(i.readyState==4)
										{
											document.getElementById("lastseen_report_pan").innerHTML = i.responseText;
										}
									}										
					i.send();
				}
				else
				{
					alert("Cant create XMLHttpRequest");
				}
			}
			
			function numOnline()
			{
				j = getXMLHttpRequestObject();
				if(j != false)
				{
					url = "checker.php?num_online";
					j.open("POST", url, true);
					j.onreadystatechange=function()
									{
										if(j.readyState==4)
										{
											var jsonResp = eval("(" + j.responseText + ")");//This is a JSON response from the server
											document.getElementById("numonline_report_pan").innerHTML = jsonResp.users + " user(s) currently online";
											document.getElementById("online_today").innerHTML = jsonResp.today + " user(s) online today";
										}
									}										
					j.send();
				}
				else
				{
					alert("Cant create XMLHttpRequest");
				}
			}
			
			function highestOnlineEver()
			{
				m = getXMLHttpRequestObject();
				if(m != false)
				{
					url = "checker.php?highest";
					m.open("POST", url, true);
					m.onreadystatechange=function()
									{
										if(m.readyState==4)
										{
											document.getElementById("highestonline_report_pan").innerHTML = m.responseText;
										}
									}										
					m.send();
				}
				else
				{
					alert("Cant create XMLHttpRequest");
				}
			}
			
			function doReport()
			{
				k = getXMLHttpRequestObject();
				if(k != false)
				{
					url = "checker.php?report&id=" + document.getElementById("userID").value;
					k.open("POST", url, true);
					k.onreadystatechange=function()
									{
										if(k.readyState==4)
										{
											//...Do nothing...
										}
									}										
					k.send();
				}
				else
				{
					alert("Cant create XMLHttpRequest");
				}
			}
			
			
			//Getting the right XMLHttpRequest object 
			function getXMLHttpRequestObject()
			{
				xmlhttp = 0;
				try 
				{
					// Try to create object for Chrome, Firefox, Safari, IE7+, etc.      
					xmlhttp = new XMLHttpRequest();
				}
				catch(e)
				{      
					try
					{
						// Try to create object for later versions of IE.
						xmlhttp = new ActiveXObject('MSXML2.XMLHTTP');
					}      
					catch(e)
					{        
						try
						{          
							// Try to create object for early versions of IE.
							xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
						}        
						catch(e)
						{
							// Could not create an XMLHttpRequest object.
							return false;        
						}      
					}    
				}
				return xmlhttp;
			} 
			
			//Call the functions
			doReport();
			numOnline();
			lastSeen();
			highestOnlineEver();
			
			//... then set the interval
			setInterval(doReport, 30000);// Report user presence every 30sec
			setInterval(numOnline, 30000);//Get number of user online every 30sec
			setInterval(lastSeen, 30000);//Get the last seen time of a user every 30sec
			setInterval(highestOnlineEver, 30000);//Get the highest online every 30sec
		</script>
	</body>
</html>