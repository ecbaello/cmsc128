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
			$rootScope.busy = false;
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
	$scope.resPas = {};
	$scope.login = function(){
		
		if($scope.account.username=='forgot'  && $scope.account.password=='password'){
			$scope.showForgotPass();
			return;
		}
		
		$rootScope.post($rootScope.baseURL+'main/login',$scope.account
		,function(response){
			$window.location.reload();
		},function(response){
			$rootScope.customAlert('Error',response.msg,'Ok');
		})
	}
	
	$scope.showForgotPass = function(){
		$rootScope.busy = true;
		$http.get($rootScope.baseURL+'main/sendresetpassword').then(function(res){
			$mdDialog.show({
				contentElement: '#forgotPass',
				clickOutsideToClose: true
			});
			$rootScope.busy=false;
		});
		
	}
	
	$scope.forgotPass = function(){
		$rootScope.busy = true;
		if($scope.resPas.Code=='' || $scope.resPas.Password1=='' || $scope.resPas.Password2==''){
			$rootScope.customAlert('Error','Please fill-up the required fields.');
		}
		$rootScope.post(
			$rootScope.baseURL+'main/resetpassword',
			$scope.resPas,
			function(res){
				$rootScope.customAlert('Success',res.msg);
				$scope.closeDialog();
				$rootScope.busy = false;
			},
			function(res){
				$rootScope.customAlert('Error',res.msg);
				$rootScope.busy = false;
			}
		);
		$rootScope.busy=false;
	}
	
	$scope.closeDialog = function(){
		$mdDialog.hide();
	}
});

app.controller('admin',function($scope,$rootScope,$http,$window){
	$scope.account={};
	$scope.init = function(){
		$http.get($rootScope.baseURL+'admin/getaccount').then(function(response){
			$scope.account = response.data;
		});
	}
	
	$scope.change = function(mode){
		var url='';
		switch(mode){
			case 0:
				url=$rootScope.baseURL+'admin/action/changeuser/';
				break;
			case 1:
				url = $rootScope.baseURL+'admin/action/changeemail';
				break;
			case 2:
				url = $rootScope.baseURL+'admin/action/changepass';
				break;
			default:
				return;
		}
		
		$rootScope.customConfirm('Warning','Are you sure about this?',function (){
			$rootScope.post(
				url,
				$scope.account,
				function(response){
					$rootScope.customConfirm('Success',response.msg,function(){
						$window.location.reload();
					},function(){
						$window.location.reload();
					});
				},function(response){
					$rootScope.customAlert('Error',response.msg);
				}
			)
		},function(){});
	}
	
	$scope.initDB = function(){
		$rootScope.busy=true;
		$rootScope.post(
			$rootScope.baseURL+'admin/initDB',
			$scope.initdbpassword,
			function(res){
				$rootScope.customAlert('Success',res.msg);
				$rootScope.busy=false;
				$scope.initdbpassword='';
				return;
			},
			function(res){
				$rootScope.customAlert('Fail',res.msg);
				$rootScope.busy=false;
				return;
			}
		);
	}
	
});