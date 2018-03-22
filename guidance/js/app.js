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
	$scope.input = {};
	
	$scope.init = function(info=''){
		$http.get($rootScope.baseURL+'studentinfo/add/get/form')
		.then(function(response){
			$scope.tableData = response.data;
			$scope.currCategory = response.data[$scope.currCategoryKey];
			console.log($scope.tableData);
		});
		
		if(info!=''){
			$scope.input = info;
		}
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
	
	$scope.getLength = function(object){
		return Object.keys(object).length;
	}
	
	$scope.submit = function(type,studentid){
		
		var url = '';
		
		if(type=='add'){
			url=$rootScope.baseURL+'studentinfo/add/post/add';
		}else if(type=='manage'){
			if(studentid == ''){
				return;
			}
			url=$rootScope.baseURL+'studentinfo/manage/post/edit/'+studentid;
		}else{
			return;
		}
		
		action =function(){
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
			$rootScope.post(url,$scope.input,success,error);
		}
		
		if(type=='manage'){
			$rootScope.customConfirm('Warning','Are you sure you want to continue?',action,function(){})
		}else{
			action();
		}
	}
	
	$scope.categoryNav = function(direction){
		var index = $scope.currCategoryKey;
		if(direction=='left'){
			index=index-1<0?0:index-1;
		}else if(direction=='right'){
			index=index+1>$scope.getLength($scope.tableData)-1?$scope.getLength($scope.tableData)-1:index+1;
		}else{
			return;
		}
		$scope.currCategoryKey = index;
		$scope.currCategory = $scope.tableData[index];
		console.log($scope.tableData);
	}
	
	$scope.test = function(index){
		
		console.log($scope.input);
	}
});



app.controller('student_search',function($scope,$rootScope,$window,$http){
	
	$scope.params = {};
	$scope.filters = [];
	$scope.results = [];
	
	$scope.init = function(){
		//alert('debug');
		//console.log($rootScope.baseURL+'studentinfo/manage/get/params');
		
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
		//console.log($scope.filters);
	}
	
	$scope.removeFilter = function(index){
		$scope.filters.splice(index,1);
		console.log($scope.filters);
	}
	
	$scope.getLength = function(object){
		return Object.keys(object).length;
	}
	
	$scope.search = function(){
		if($scope.getLength($scope.filters) == 0){
			$rootScope.customAlert('Error','At least one filter is required.');
			return;
		}
		console.log($rootScope.baseURL+'studentinfo/manage/get/search/'+encodeURIComponent(angular.toJson($scope.filters)));
		$http.get($rootScope.baseURL+'studentinfo/manage/get/search/'+encodeURIComponent(angular.toJson($scope.filters)))
		.then(function(response){
			$scope.results = response.data;
			console.log($scope.results);
		});
	}

});

app.controller('tests_form',function($scope,$rootScope,$window){
	
});

app.controller('tests_edit',function($scope,$rootScope,$window){
	
	$scope.test = {};
	
	$scope.init = function(test){
		//console.log(test);
		$scope.test = test;
		console.log($scope.test);
	}
	
	$scope.addChoice = function(qIndex){
		$scope.test.Questions[qIndex].Choices.push({
			'Value':''
		});
	}
	
	$scope.deleteChoice = function(qIndex,cIndex){
		$scope.test.Questions[qIndex].Choices.splice(cIndex,1);
	}
	
	$scope.addQuestion = function(){
		$scope.test.Questions.push({
			'Title':'',
			'Choices':[],
			'Order':$scope.test.Questions.length+1
		});
		
	}
	
	$scope.changeOrder = function(currIndex,desIndex){
		if(currIndex == desIndex)
			return;
		var currValue = $scope.test.Questions[currIndex];
		$scope.test.Questions.splice(currIndex,1);
		$scope.test.Questions.splice(desIndex,0,currValue);
		
		updateOrder();
	}
	
	$scope.deleteQuestion = function(qIndex){
		yes = function(){
			$scope.test.Questions.splice(qIndex,1);
			updateOrder();
		}
		no = function(){
			//
		}
		$rootScope.customConfirm('Warning','Are you sure you want to do this?',yes,no);
	}
	
	$scope.ping = function(){
		alert('pong');
		console.log($scope.test.Questions);
	}
	
	$scope.getNumber = function(num) {
		return new Array(num);   
	}
		
	$scope.submit = function(){
		
		success = function(msg){
			cont = function(){
				$window.location.reload();
			}
			canc = function(){
				$window.location.reload();
			}
			$rootScope.customConfirm('Success',msg.msg,cont,canc);
		}
		
		fail = function(msg){
			$rootScope.customAlert('Error',msg.msg);
		}
		$rootScope.post($rootScope.baseURL+'tests/edit/post/',$scope.test,success,fail);
	}
		
	function updateOrder(){
		for(var i = 0 ; i<$scope.test.Questions.length ; i++){
			$scope.test.Questions[i].Order = i+1;
		}
		console.log($scope.test.Questions);
	}
	
});

app.controller('tests_nav',function($scope,$rootScope,$window,$mdDialog,$http){
	
	$scope.newTest = {};
	$scope.tests = {};
	
	$scope.init = function(){
		$scope.getTests();
	}
	
	$scope.add = function(){
		console.log($scope.newTest);
		
		success = function(response) {
			$rootScope.customConfirm('Success',response.msg,function(){
				//$window.location.reload();
				$scope.getTests();
			},
			function(){
				//$window.location.reload();
				$scope.getTests();
			});
		}
		error = function(response){
			$rootScope.customAlert('Error',response.msg);
		}
		$rootScope.post($rootScope.baseURL+'tests/main/post/add',$scope.newTest,success,error);
		
	}
	
	$scope.addDialog = function(){
		$mdDialog.show({
			contentElement: '#addDialog',
			clickOutsideToClose: true
		});
	}
	
	$scope.closeDialog = function(){
		$mdDialog.hide();
	}
	
	$scope.test = function(){
		alert('bbom');
	}
	
	$scope.getTests = function(){
		$http.get($rootScope.baseURL+'tests/main/get/tests/')
		.then(function(response){
			$scope.tests = response.data;
			console.log($scope.tests);
		});
	}
	
	$scope.edit = function(testTitle){
		$window.location.href=$scope.baseURL+'tests/edit/test/'+encodeURIComponent(testTitle);
	}
	
});
