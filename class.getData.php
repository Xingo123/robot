<?php
	/*
		Author  : Xing Woo
		Date 	: 02-04-2014
		TESTING XING
		Get details and import into the database.
	*/

	class getData()
	{
		public static function databaseConnection()
		{
			$sql = new Mysqli('localhost', 'website', '5vw4wqPr', 'website_nxtrobot');
		}

		public static function importDetails($responses, $groupMacadress)
		{
			foreach($responses as $response)
			{				
				$explode = explode(" ", $response);

					$battery = $explode[0];
					$degrees = $explode[1];
				
					$sql->query("INSERT INTO 
								NXTStorage
								(
									mac_adress,
									batteryLife,
									drivenMeter,
									timestamp

								)
								VALUES
								(
									'" . $groupMacadress . "',
									'" . $battery . "',
									'" . $degrees . "',
									'" . time() . "'
								)
								");
					echo $sql->error;	
				
			}
		}

		public static function getDetails($groupName, $groupMacadress)
		{
			$url = 'http://127.0.0.1:1337/';
		
			$data = array( 			"clientName" 	=> $groupName,
									"macAddress" 	=> $groupMacadress,
									"command" 		=> "alive",
									"parameter" 	=> "");

			$options = array(
		    	'http' => array(
		        'method'  => 'POST',
		        'content' => json_encode( $data ),
		        'header'=>  "Content-Type: application/json\r\n" .
		                    "Accept: application/json\r\n"
			      )
			);
			 
			$context     = stream_context_create($options);
			$result      = file_get_contents($url, false, $context);
			$responses   = json_decode($result);

			self::importDetails($responses, $groupMacadress);
		}

		public static function loopRefTable()
		{
			self::databaseConnection();
			$query = $sql->query("SELECT * FROM reftable");
			if($query->num_rows > 0)
			{
				while($row = $query->fetch_object())
				{
					$groupName = $row->name;
					$groupMacadress = $row->mac_adress;
					self::getDetails($groupName, $groupMacadress);
				}
			}
		}
	}

	getData::loopRefTable();
?>