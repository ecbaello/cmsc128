var app = angular.module("app", ['ngMaterial'])
.config(function($mdThemingProvider) {
  $mdThemingProvider.theme('default')
    .primaryPalette('deep-purple')
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

app.controller('student_add',function($scope,$rootScope,$http){
	
	$scope.currCategoryKey = 0;
	$scope.currCategory = {};
	$scope.tableData = {};
	
	$scope.init = function(){
		$http.get($rootScope.baseURL+'/studentinfo/add/get/tables')
		.then(function(response){
			$scope.tableData = response.data;
			$scope.currCategory = response.data[$scope.currCategoryKey];
		});
	}
	
	$scope.alert = function(msg){
		alert(msg);
	}
	
	$scope.changeCategory = function(categoryKey){
		$scope.currCategoryKey = categoryKey;
		$scope.currCategory = $scope.tableData[categoryKey];
	}
	
});