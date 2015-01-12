<?php

	/*-------------------------------------------------------------------------
	|	Key Application Points
	|	* Utilize AngularJS to create a fluid user experience
	|	* Create a RESTful API for dynamic and robust integration
	-------------------------------------------------------------------------*/

	require_once( "data.php" );
	
	// Declare the header
	header('Content-Type: application/json');
	
	// Initialize database
	$DB = new mysqli( "", "", "", "" );
	
	// Running RESTful response
	global $resp;
	$resp = array(
		"error" => array( 
			"code" => -1,
			"message" => "no error",
		),
		
		"user" => array(
			"id" => -1,
			"username" => "n/a",
		),
		
		"status" => "fail",
		"status_msg" => "N/A",
		"authed" => false,
	);
	
	// Terminate the connection_aborted
	function terminate() {
		global $resp;
		die( json_encode( $resp ) );
	}
	
	// Terminate the connection_aborted
	function error( $code, $msg ) {
		global $resp;
		$resp[ "error" ] = array( "code" => $code, "message" => $msg );
		terminate();
	}
	
	// Make sure DB is active
	if( !$DB ) {
		error( 1, "could not connect to database" );
	}
		
	// User session details
	$curUser = array();
	
	// Authenticate them
	if( isset( $_COOKIE[ "session_key" ] ) && strlen( $_COOKIE[ "session_key" ] ) >= 10 ) {
		$sKey = $DB->escape_string( $_COOKIE[ "session_key" ] );
		$res = $DB->query( "SELECT * FROM `users` WHERE `SessionKey` = '{$sKey}';" );
		$row = $res->fetch_assoc();
		
		if( $row ) {
			$resp[ "authed" ] = true;
			$resp[ "user" ] = array(
				"id" => $row[ "ID" ],
				"username" => $row[ "Username" ],
			);
		}
		
		$curUser = $row;
	}
	
	// Action
	$action = "";
	
	// Make sure there is an action
	if( !isset( $_GET[ "action" ] ) ) {
		error( 2, "No action given. An action must be defined in HTTP/GET @ key:action" );
	} else {
		$action = $_GET[ "action" ];
	}

	// Get phrases
	if( $action == "phrases" ) {
		$resp[ "status" ] = "success";
		$resp[ "phrases" ] = $recordings;
		terminate();
	}
	
	// User recordings
	if( $action == "user_recordings" ) {
		if( $resp[ "authed" ] == false ) {
			$resp[ "status" ] = "fail";
			$resp[ "status_msg" ] = "Cannot get user recordings when not logged in";
		} else {
			$queryRes = $DB->query( "SELECT * FROM `recordings` WHERE `User` = " . $curUser[ "ID" ] . ";" );
			$resp[ "status" ] = "success";
			$arr = array();
			
			while( $row = $queryRes->fetch_assoc() ) {
				$arr[ $row[ "Phrase" ] ] = $row;
			}
			
			$resp[ "completion" ] = ( count( $arr ) / numPhrases() );
			$resp[ "user_recordings" ] = $arr;
			terminate();
		}
	}
	
	// Upload recording
	if( $action == "upload" ) {
		if( $resp[ "authed" ] == false ) {
			$resp[ "status_msg" ] = "Cannot upload recordings without logging in";
			terminate();
		}
		
		if( !isset( $_FILES[ "data" ] ) ) {
			$resp[ "status_msg" ] = "No recording given!";
			terminate();
		}
		
		if ( !file_exists( '_temp/' . $curUser[ "Username" ] ) ) {
			@mkdir(  '_temp/' . $curUser[ "Username" ] );
		}
		
		$phrase = $_POST[ "phrase" ]; // todo: validate
		$path = "./_temp/" . $curUser[ "Username" ] . "/" . $phrase . ".wav";
		
		if ( file_exists( $path ) ) {
			$resp[ "replaced" ] = true;
			unlink( $path );
		}
		
		@move_uploaded_file( $_FILES["data"]["tmp_name"], $path );
		
		// puts
		@$DB->query( "DELETE FROM `recordings` WHERE `User` = " . $curUser[ "ID" ] . " AND `Phrase` = '{$phrase}';" );
		@$DB->query( "INSERT INTO `recordings` VALUES ( NULL, " . $curUser[ "ID" ] . ", '{$phrase}', " . time() . " );" );
		
		$resp[ "status" ] = "success";
		$resp[ "status_msg" ] = "Recording successfully uploaded";
		terminate();
	}
	
	// General action
	if( $action == "general" ) {
		$resp[ "status" ] = "pass";
		terminate();
	}
	
	// Login
	if( $action == "login" ) {
		// Username and password
		$username = isset( $_GET[ "username" ] ) ? $DB->escape_string( $_GET[ "username" ] ) : "";
		$password = isset( $_GET[ "password" ] ) ? $DB->escape_string( $_GET[ "password" ] ) : "";
		
		// Attempt authentication
		$res = $DB->query( "SELECT * FROM `users` WHERE `username` = '{$username}' AND `password` = '{$password}';" ); // no hash, dev purposes
		
		if( $res->num_rows == 0 ) {
			// The login failed..
			$resp[ "status" ] = "fail";
			$resp[ "status_msg" ] = "Incorrect username or password";
		} else {
			// The login passed..
			$row = $res->fetch_assoc();
			$uid = $row[ "ID" ];
			$newSessionKey = md5( rand() ) . md5( rand() ); // blah.
			$DB->query( "UPDATE `users` SET `SessionKey` = '{$newSessionKey}' WHERE `ID` = {$uid};" );
			setcookie( "session_key", $newSessionKey, time() + (60 * 30) );
			$resp[ "status" ] = "success";
		}
		
		terminate();
	}
	
	// Logout
	if( $action == "logout" ) {
		if( $resp[ "authed" ] == false ) {
			$resp[ "status" ] = "fail";
			$resp[ "status_msg" ] = "Cannot logout when not logged in";
		} else {
			setcookie( "session_key", "", 0 );
			$resp[ "status" ] = "success";
			$resp[ "status_msg" ] = "Logged out";
		}
		
		terminate();
	}
	
	// Catch all
	error( 0, "Action not found key:" . $action );
	
?>