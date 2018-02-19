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
	
	$scope.input = {};
	
	$scope.init = function(){
		$http.get($rootScope.baseURL+'/studentinfo/add/get/tables')
		.then(function(response){
			$scope.tableData = response.data;
			$scope.currCategory = response.data[$scope.currCategoryKey];
			console.log($scope.tableData);
		});
	}
	
	$scope.alert = function(msg){
		alert(msg);
		console.log($scope.input);
	}
	
	$scope.validate = function(){
		console.log($scope.input[$scope.tableData[0].name]['student_number']);
	}
	
	$scope.changeCategory = function(categoryKey){
		var object = $scope.tableData[0];
		if(!(object.Table.Name in $scope.input)){
			alert(object.Table.Title+' should be filled up first.');
			return;
		}
		for(var field in object.Table.Field){
			if(!(field in $scope.input[object.Table.Name])){
				alert(object.Table.Title+' should be filled up first.');
				return;
			}
		}
		$scope.currCategoryKey = categoryKey;
		$scope.currCategory = $scope.tableData[categoryKey];
	}
	
});