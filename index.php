<!DOCTYPE html>
<html lang="en" data-ng-app="recorderApp">
	<head>
		<title>Recorder</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="css/recorder.css" />
	</head>
	
	<body>
		<!-- angular render pane -->
		<div data-ng-view="" style="margin: 60px 30px 20px 30px;"></div>
		
		<!-- dependencies -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.0rc1/angular-route.min.js"></script>
		<script src="js/recorderApp.js"></script>
		
		<!-- the actual recorder dependencies -->
		<script src="js/audiodisplay.js"></script>
		<script src="js/recorderjs/recorder.js"></script>
		<script src="js/main.js"></script>
	</body>
</html>