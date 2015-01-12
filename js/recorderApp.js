
var recorderApp = angular.module( 'recorderApp', [ 'ngRoute' ] );

recorderApp.config( function( $routeProvider ) {
	// record partial
	$routeProvider.when( '/', {
		controller: 'RecordController',
		templateUrl: 'partials/record.html',
	} );
	
	// login partial
	$routeProvider.when( '/login', {
		controller: 'LoginController',
		templateUrl: 'partials/login.html',
	} );
	
	// logout partial
	$routeProvider.when( '/logout', {
		controller: 'LogoutController',
		templateUrl: 'partials/login.html',
	} );
} );

recorderApp.factory( 'userInfo', function( $http ) {
	
	return {
		getUserData: function() {
			return $http.get( 'rest.php', {} );
		},
		
		userRecordings: function() {
			return $http.get( 'rest.php?action=user_recordings', {} );
		},
		
		attemptLogin: function( user, pass ) {
			return $http.get( 'rest.php?action=login&username=' + user + '&password=' + pass, {} );
		},
		
		logout: function( user, pass ) {
			return $http.get( 'rest.php?action=logout', {} );
		},
	};
	
} );

recorderApp.factory( 'genetalInfo', function( $http ) {
	
	return {
		getPhrases: function() {
			return $http.get( 'rest.php?action=phrases', {} );
		},
	};
	
} );

recorderApp.run( function( $rootScope, $location, userInfo ) {
	$rootScope.$on( '$routeChangeStart', function( event ) {
		// Get the user's info
		userInfo.getUserData().success( function( data ) {
			if( data.authed == false ) {
				$location.path( '/login' );
			}
		} );
	} );
} );

recorderApp.controller( 'RecordController', function( $scope, $window, $http, userInfo, genetalInfo ) {
	$scope.blob = false; // Where the recording will go
	$scope.curRecording = false; // Pointer to clip being recorded
	$scope.reallyRecording = false; // Is the mic on?
	$scope.uploadSuccess = false;
	
	// pass user information to the scope
	userInfo.getUserData().success( function( data ) {
		$scope.userData = data;
	} );

	// pass user information to the scope
	userInfo.userRecordings().success( function( data ) {
		$scope.user_recordings = data.user_recordings;
		$scope.completion = (data.completion * 100);
	} );
	
	// pass user information to the scope
	genetalInfo.getPhrases().success( function( data ) {
		$scope.phrases = data.phrases;
	} );
	
	$scope.doRecord = function( phraseID ) {
		$window.curRecording = phraseID;
		$scope.curRecording = phraseID;
	};
	
	$scope.startRecording = function() {
		$scope.reallyRecording = true;
		toggleRecording( true );
	};
	
	$scope.uploadAudio = function() {
		var fd = new FormData();
		fd.append( 'phrase', $scope.curRecording );
		fd.append( 'data', $window.blob );
		
		$.ajax( {
			type: 'POST',
			url: 'rest.php?action=upload',
			data: fd,
			processData: false,
			contentType: false
		} ).done( function( data ) {
			if( data.status == "success" ) {
				// pass user information to the scope
				userInfo.userRecordings().success( function( data ) {
					$scope.user_recordings = data.user_recordings;
					$scope.completion = (data.completion * 100);
				} );
				
				$scope.uploadSuccess = true;
				
				setTimeout( function() {
					$scope.uploadSuccess = false;
				}, 1000 );
			}
		} );
	};
	
	$scope.stopRecording = function() {
		$scope.blob = $window.blob;
		$scope.reallyRecording = false;
		toggleRecording( false );
	};
	
	$scope.doPlayback = function( id ) {
		window.open( "http://alteredrp.com/ai/record/_temp/" + $scope.userData.user.username + "/" + id + ".wav", '_blank' );
	}
} );

recorderApp.controller( 'LoginController', function( $scope, $location, userInfo ) {
	// pass user information to the scope
	userInfo.getUserData().success( function( data ) {
		$scope.userData = data;
		
		$scope.userLogin = function( user, pass ) {
			userInfo.attemptLogin( user, pass ).success( function( data ) {
				if( data.status == "fail" ) {
					alert( data.status_msg );
				} else {
					$location.path( '/' );
				}
			} );
		};
	} );
} );

recorderApp.controller( 'LogoutController', function( $scope, $location, userInfo ) {
	userInfo.logout().success( function( data ) {
		$location.path( '/login' );
	} );
} );
