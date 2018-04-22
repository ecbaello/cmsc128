var app = angular.module("app", ['ngMaterial','ngMessages','ngTableToCsv'])
.config(function($mdThemingProvider) {
  $mdThemingProvider.theme('default')
    .primaryPalette('grey')
    .accentPalette('green');
});

app.run(function($rootScope,$http,$httpParamSerializer,$mdDialog){
	$rootScope.busy = false;
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

app.controller('login',function($scope,$rootScope,$http,$mdDialog,$window){
	$scope.account = {};
	$scope.login = function(){
		if($scope.username=='' || $scope.password==''){
			$rootScope.customAlert('Error','Please fill-up the required fields.','Ok');
		}

		$rootScope.post($rootScope.baseURL+'main/login',$scope.account
		,function(response){
			$window.location.reload();
		},function(response){
			$rootScope.customAlert('Error',response.msg,'Ok');
		})
	}
});

app.controller('admin',function($scope,$rootScope,$http,$mdDialog,$window){
	$scope.account={};
	$scope.init = function(){
		$http.get($rootScope.baseURL+'admin/getaccount').then(function(response){
		
		});
	}
});

app.controller('loading',function($rootScope){
});