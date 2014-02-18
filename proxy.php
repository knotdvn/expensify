<?php
//This page is our proxy between the expressify api server and our web server
//it prevenets cross-domain policy issues
// the basic flow is: command - > auth -> execute -> return

//"App-Specific" Details
$partnerName = 'applicant';
$partnerPassword = 'd7c3119c6cdab02d68d9';



//given the command and an array of paramaters
// it will build the get url
function createRequestUrl($command, $paramaters){
	$url = 'https://api.expensify.com/?command=' . $command;
	//parse the paramaters
	foreach($paramaters as $key => $value){
		$url .= '&' . $key . '=' . $value;
	}
	return $url;
}//end createRequestUrl



//is valid command coming in?
if( isset($_POST["command"]) ){
	if($_POST["command"]==="Authenticate" ){
		//do we have correct paramaters
		if( isset($_POST["partnerUserID"]) && isset($_POST["partnerUserSecret"]) ){
			$partnerUserID = $_POST["partnerUserID"];
			$partnerUserSecret = $_POST["partnerUserSecret"];
			$paramaters = array(
				"partnerName" => $partnerName,
				"partnerPassword" => $partnerPassword,
				"partnerUserID" => $partnerUserID,
				"partnerUserSecret" => $partnerUserSecret);
			$url = createRequestUrl("Authenticate", $paramaters);
			
			//api request
			$request =  file_get_contents($url);
			$phpData = json_decode($request);

			if($phpData->jsonCode == 200){
				//all good
				$task = $phpData->authToken;
				setcookie("ExpensifyAUTH",$task, time()+3500);
			
				$response = array('error' => 'false', 'msg' => 'Successful Auth'); 
				$msg = json_encode($response);
				die($msg);
			}elseif ($phpData->jsonCode == 401){
				//password
				$response = array('error' => 'true', 'msg' => 'Incorrect Password'); 
				$msg = json_encode($response);
				die($msg);
			}elseif( $phpData->jsonCode == 404){
				//account
				$response = array('error' => 'true', 'msg' => 'Incorrect Account'); 
				$msg = json_encode($response);
				die($msg);
			}elseif ($phpData->jsonCode == 405){
				$response = array('error' => 'true', 'msg' => 'Incorrect Email'); 
				$msg = json_encode($response);
				die($msg);
			}else{
				//other code or something
				$response = array('error' => 'true', 'msg' => 'Authorization Unsuccessful Please Try Again'); 
				$msg = json_encode($response);
				die($msg);
			}//end if else status


		}else{
		//lacking correct paramaters
			$response = array('error' => 'true', 'msg' => 'User Credentials were not provided.'); 
			$msg = json_encode($response);
			die($msg);
		}//end if else credentials present
	//end if Authenticate command

	}else if($_POST["command"]==="Get"){
		//are we authed
		if( isset($_POST["authToken"])){
			//good token lets request
			$paramaters = array(
				"authToken" => $_POST["authToken"],
				"returnValueList" => "transactionList"
				);
			$url = createRequestUrl("Get", $paramaters);

			//api returns json
			$jsonData =  file_get_contents($url);
			//php is like this
			$phpData = json_decode($jsonData);
			
			//check codes
			if($phpData->jsonCode == 200){
				//all good send the data	reset cookie 
				$task = $phpData->authToken;
				setcookie("ExpensifyAUTH",$task, time()+3500);
				$response = array('error' => 'false', 'msg' => json_encode($phpData) ); 
				$msg = json_encode($response);
				die($msg);

			}else if($phpData->jsonCode == 404){
				$response = array('error' => 'true', 'msg' => 'Resource Not Found'); 
				$msg = json_encode($response);
				die($msg);

			}else if($phpData->jsonCode == 408){
				$response = array('error' => 'Auth', 'msg' => 'Bad Token'); 
				$msg = json_encode($response);
				die($msg);
			}//end else if request codes


		}else{
			//no auth
			$response = array('error' => 'Auth', 'msg' => 'Bad Token'); 
			$msg = json_encode($response);
			die($msg);
		}//end if token not set

	//end Get command	
	}else if($_POST["command"]==="CreateTransaction"){
		//check auth
		if( isset($_POST["authToken"])){
			//input check
			if( isset($_POST["merchant"]) && isset($_POST["amount"]) && isset($_POST["date"]) ){
				//we have everything lets make the request
				$paramaters = array(
				"authToken" => $_POST["authToken"],
				"created" => $_POST["date"],
				"amount" => $_POST["amount"],
				"merchant" => $_POST["merchant"],
				);
				$url = createRequestUrl("CreateTransaction", $paramaters);

				//api returns json
				$jsonData =  file_get_contents($url);
				//php is like this
				$phpData = json_decode($jsonData);

				//check codes
				if($phpData->jsonCode == 200){
					//all good send the data
					//create transaction does NOT reset the auth
					$response = array('error' => 'false', 'msg' => json_encode($phpData) ); 
					$msg = json_encode($response);
					die($msg);
					
				}else if($phpData->jsonCode == 404){
					$response = array('error' => 'true', 'msg' => 'Resource Not Found'); 
					$msg = json_encode($response);
					die($msg);

				}else if($phpData->jsonCode == 408){
					$response = array('error' => 'Auth', 'msg' => 'Bad Token'); 
					$msg = json_encode($response);
					die($msg);
				}//end else if request codes

			}else{
				//no input	
				$response = array('error' => 'true', 'msg' => 'Please Provide Valid Transaction Data'); 
				$msg = json_encode($response);
				die($msg);
			}//end if we have input
		}else{
			//no auth
			$response = array('error' => 'Auth', 'msg' => 'Bad Token'); 
			$msg = json_encode($response);
			die($msg);
		}//end if authed
	//end CreateTransaction
	}else{
	//not valid command: auth, get, or create

		$response = array('error' => 'true', 'msg' => 'Valid Command Not Issued'); 
		$msg = json_encode($response);
		die($msg);
	}//end if command type


}else{ 
	//no POST commandset
	$response = array('error' => 'true', 'msg' => 'No Command Issued'); 
	$msg = json_encode($response);
	die($msg);

}//end if else command set



?>