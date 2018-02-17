var app = angular.module("app", ['ngMaterial'])
.config(function($mdThemingProvider) {
  $mdThemingProvider.theme('default')
    .primaryPalette('red')
    .accentPalette('green');
});

app.controller('initializer',function($scope,$rootScope){
	$scope.init = function(baseURL){
		$rootScope.baseURL = baseURL;
	}
});

app.controller('tests',function($scope,$rootScope){
	$scope.csrf = {};
	
	$scope.init = function(csrfTokenName,csrfHash){
		$scope.csrf.tokenName = csrfTokenName;
		$scope.csrf.hash = csrfHash;
	}	
	$scope.test = function(){
		alert($scope.csrf.tokenName+' '+$scope.csrf.hash);
	}
});

app.controller('student',function($scope,$rootScope,$http){
	
	$scope.tableData = {};
	
	$scope.init = function(){
		$http.get($rootScope.baseURL+'/studentinfo/data/tables')
		.then(function(response){
			$scope.tableData = response.data;
		});
	}
	
	$scope.bump = function(){
		alert($scope.tableData);
		console.log($scope.tableData);
	}
	
});