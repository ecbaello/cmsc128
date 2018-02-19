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
	
	var csrfTokenName = '';
	var csrfHash = '';
	
	$scope.init = function(){
		$http.get($rootScope.baseURL+'studentinfo/add/get/tables')
		.then(function(response){
			$scope.tableData = response.data;
			$scope.currCategory = response.data[$scope.currCategoryKey];
			console.log($scope.tableData);
		});
	}
	
	$scope.setCSRF = function(name,hash){
		csrfTokenName = name;
		csrfHash = hash;
	}
	
	$scope.getCardinality = function(baseTableName,AETName){
		var number = 1;
		for(key in $scope.tableData){
			if($scope.tableData[key].Table.Name == baseTableName){
				var table = $scope.tableData[key];
				for(key2 in table.Fields){
					if(table.Fields[key2].Name == AETName){
						var field = table.Fields[key2];
						var cardinalityField = field.AET['Cardinality Field Name'];
						
						if(!(table.Table.Name in $scope.input)){
							number = field.AET['Default Cardinality'];
							$scope.input[table.Table.Name] = {};
							$scope.input[table.Table.Name][cardinalityField] = parseInt(number);
							break;
						}
						if(!(cardinalityField in $scope.input[table.Table.Name])){
							number = field.AET['Default Cardinality'];
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
		/*if(!(object.Table.Name in $scope.input)){
			alert(object.Table.Title+' should be filled up first.');
			return;
		}
		for(var field in object.Table.Field){
			if(!(field in $scope.input[object.Table.Name])){
				alert(object.Table.Title+' should be filled up first.');
				return;
			}
		}*/
		$scope.currCategoryKey = categoryKey;
		$scope.currCategory = $scope.tableData[categoryKey];
	}
	
	$scope.submit = function(){
		
		var data = {
			'data':$scope.input,
			[csrfTokenName]:csrfHash
		};
		data = JSON.stringify(data);
		console.log($scope.input);
		success = function(response) {
			var responseData = {};
			responseData = response.data;
			console.log(responseData);
			if(!('success' in responseData) || !('msg' in responseData)){
				alert('Something is missing');
				return;
			}
			
			alert((responseData.success ? 'Success: ':'Error: ')+responseData.msg);
		}
		
		error = function(response){
			alert('Something went wrong');
		}
		
		$http({
			method: 'GET',
			url: $rootScope.baseURL+'studentinfo/add/post/'+encodeURIComponent(data)
		}).then(success,error);
		
		console.log($rootScope.baseURL+'studentinfo/add/post/'+encodeURIComponent(data));
	}
	
});