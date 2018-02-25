var app = angular.module("app", ['ngMaterial'])
.config(function($mdThemingProvider) {
  $mdThemingProvider.theme('default')
    .primaryPalette('grey')
    .accentPalette('green');
});


app.run(function($rootScope,$http){

	$rootScope.post = function(url,inputData,onSuccess,onFailure){
	
		var data = {
			'data':inputData,
			[$rootScope.csrf.tokenName]:$rootScope.csrf.hash
		};
		data = angular.toJson(data);
		
		success = function(response) {
			var responseData = {};
			responseData = response.data;
			console.log(responseData);
			if(!('success' in responseData) || !('msg' in responseData)){
				alert('Something is missing');
				return;
			}
			if(responseData.success){
				onSuccess(responseData);
			}else{
				onFailure(responseData);
			}
		}
		
		error = function(response){
			alert('Something went wrong');
		}
		
		$http({
			method: 'GET',
			url: url+encodeURIComponent(data)
		}).then(success,error);
		
		console.log(url+encodeURIComponent(data));
		
	}
});

app.controller('initializer',function($scope,$rootScope){
	$scope.init = function(baseURL,csrfTokenName,csrfHash){
		$rootScope.baseURL = baseURL;
		$rootScope.csrf = {};
		$rootScope.csrf.tokenName = csrfTokenName;
		$rootScope.csrf.hash = csrfHash;
	}
});

app.controller('student_form',function($scope,$rootScope,$http,$window){
	
	$scope.currCategoryKey = 0;
	$scope.currCategory = {};
	$scope.tableData = {};
	$scope.searchInput = ''; //manage
	$scope.input = {};
	
	$scope.init = function(){
		$http.get($rootScope.baseURL+'studentinfo/add/get/form')
		.then(function(response){
			$scope.tableData = response.data;
			$scope.currCategory = response.data[$scope.currCategoryKey];
			console.log($scope.tableData);
		});
	}

	$scope.getCardinality = function(baseTableName,FEName){
		var number = 1;
		for(key in $scope.tableData){
			if($scope.tableData[key].Table.Name == baseTableName){
				var table = $scope.tableData[key];
				for(key2 in table.Fields){
					if(table.Fields[key2].Name == FEName){
						var field = table.Fields[key2];
						var cardinalityField = field.FE['Cardinality Field Name'];
						
						if(!(table.Table.Name in $scope.input)){
							number = field.FE['Default Cardinality'];
							$scope.input[table.Table.Name] = {};
							$scope.input[table.Table.Name][cardinalityField] = parseInt(number);
							break;
						}
						if(!(cardinalityField in $scope.input[table.Table.Name])){
							number = field.FE['Default Cardinality'];
							$scope.input[table.Table.Name][cardinalityField] = parseInt(number);
							break;
						}else{
							number = $scope.input[table.Table.Name][cardinalityField];
							
							var validNumber = true;
							validNumber = validNumber && !isNaN(parseInt(number));
							validNumber = validNumber && !( (parseFloat(number)%1 != 0) || parseFloat(number)<1);
							
							$scope.input[table.Table.Name][cardinalityField] = number = validNumber?number:1;
							
							break;
						}
					}
				}
			}
		}
		return new Array(parseInt(number));
	}

	
	$scope.changeCategory = function(categoryKey){
		var object = $scope.tableData[0];
		$scope.currCategoryKey = categoryKey;
		$scope.currCategory = $scope.tableData[categoryKey];
	}
	
	$scope.search = function(){
		if($scope.searchInput == '')
			return;
		success = function(response) {
			var responseData = {};
			responseData = response.data;
			$scope.input = {};
			$scope.input = responseData.data;
		}
		error = function(response){
			alert('Error: '+response.msg);
		}
		$rootScope.post($rootScope.baseURL+'studentinfo/manage/getstudentdata/',$scope.searchInput,success,error);
	}
	
	$scope.submit = function(){
		success = function(response) {
			alert('Success: '+response.msg);
			$window.location.reload();
		}
		error = function(response){
			alert('Error: '+response.msg);
		}
		$rootScope.post($rootScope.baseURL+'studentinfo/add/post/',$scope.input,success,error);
	}
});



app.controller('student_search',function($scope,$rootScope,$window){


});
