var app = angular.module("app", ['ngMaterial','ngMessages'])
.config(function($mdThemingProvider) {
  $mdThemingProvider.theme('default')
    .primaryPalette('grey')
    .accentPalette('green');
});


app.run(function($rootScope,$http,$httpParamSerializer,$mdDialog){

	$rootScope.post = function(url,inputData,onSuccess,onFailure){
	
		var data = {
			'data':inputData,
			[$rootScope.csrf.tokenName]:$rootScope.csrf.hash
		};
		data = $httpParamSerializer(data);
		
		success = function(response) {
			var responseData = {};
			responseData = response.data;
			console.log(responseData+"asd");
			if(!('success' in responseData) || !('msg' in responseData)){
				$rootScope.customAlert('ERROR','Something is missing');
				return;
			}
			if(responseData.success){
				onSuccess(responseData);
			}else{
				onFailure(responseData);
			}
		}
		
		error = function(response){
			$rootScope.customAlert('Error','Something went wrong');
		}
		
		$http({
			method: 'POST',
			url: url,
			data: data,
			headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
		}).then(success,error);
		
	}
	
	$rootScope.customAlert = function(title,content,ok='ok'){
		$mdDialog.show(
		    $mdDialog.alert()
				.clickOutsideToClose(true)
				.title(title)
				.textContent(content)
				.ok(ok)
		);
	}
	
	$rootScope.customConfirm = function(title,content,confirm,cancel,okButton='ok',cancelButton='cancel'){
		$mdDialog.show( $mdDialog.confirm()
			.clickOutsideToClose(true)
			.title(title)
			.textContent(content)
			.ok(okButton)
			.cancel(cancelButton)
		).then(confirm,cancel);
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
		//alert('test');
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
	
	$scope.submit = function(){
		success = function(response) {
			$rootScope.customConfirm('Success',response.msg,function(){
				$window.location.reload();
			},
			function(){
				$window.location.reload();
			});
		}
		error = function(response){
			$rootScope.customAlert('Error',response.msg);
		}
		$rootScope.post($rootScope.baseURL+'studentinfo/add/post/add',$scope.input,success,error);
	}
	
	$scope.test = function(index){
		
		return angular.toJson($scope.currCategory.Fields[index]);
	}
});



app.controller('student_search',function($scope,$rootScope,$window,$http){
	
	$scope.params = {};
	$scope.filters = [];
	
	$scope.init = function(){
		//alert('debug');
		console.log($rootScope.baseURL+'studentinfo/manage/get/params');
		
		$http.get($rootScope.baseURL+'studentinfo/manage/get/params')
		.then(function(response){
			$scope.params = response.data;
			console.log($scope.params);
		});
	}
	
	$scope.addFilter = function(type){
		
		var toAdd = JSON.parse($scope.toAddFilter);
		console.log(toAdd);
		var filter = {
			name:toAdd.name,
			title:toAdd.title,
			type:type
		};
		$scope.filters.push(filter);
		console.log($scope.filters);
	}
	
	$scope.removeFilter = function(index){
		$scope.filters.splice(index,1);
		console.log($scope.filters);
	}
	
	$scope.getFiltersLength = function(){
		return Object.keys($scope.filters).length;
	}
	
	$scope.search = function(){
		if($scope.getFiltersLength() == 0){
			$rootScope.customAlert('Error','At least one filter is required.');
		}
		
	}

});
