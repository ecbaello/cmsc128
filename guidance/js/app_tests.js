

app.controller('tests_take',function($scope,$rootScope,$window){
	
	$scope.test = {};
	
	$scope.init = function(test){
		$scope.test = test;
		console.log($scope.test);
	}
	
	$scope.submit = function(){
		$rootScope.busy = true;
		action =function(){
			success = function(response) {
				$rootScope.customConfirm('Success',response.msg,function(){
					$window.location.href=$rootScope.baseURL+'tests';
				},
				function(){
					$window.location.href=$rootScope.baseURL+'tests';
				});
			}
			error = function(response){
				$rootScope.customAlert('Error',response.msg);
				$rootScope.busy=false;
			}
			$rootScope.post($rootScope.baseURL+'tests/take/post',$scope.test,success,error);
		}
		$rootScope.customConfirm('Warning','Are you sure you want to continue?',action,function(){});
	}
	
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
	
	$scope.getNumber = function(num) {
		return new Array(num);   
	}
		
	$scope.submit = function(){
		$rootScope.busy = true;
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
			$rootScope.busy = false;
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

app.controller('tests_passwords',function($scope,$rootScope,$window,$mdDialog,$http){
	$scope.disp={};
	$scope.gen={};
	$scope.passwords={};
	$scope.search = function(){
		if($scope.disp.option == '' || $scope.disp.value == '')
			$rootScope.customAlert('Error','Please fill-up the required fields');
		$rootScope.busy =true;
		$http.get($rootScope.baseURL+'tests/passwords/getPasswords/'+$scope.disp.option+'/'+$scope.disp.value)
		.then(function(response){
			if(response.data.hasOwnProperty('success')){
				if(!response.data.success){
					$rootScope.customAlert('Error',response.data.msg);
					$rootScope.busy = false;
					return;
				}
			}
			$rootScope.busy = false;
			$scope.passwords = response.data;
		});
	}
	$scope.submit = function(){
		if($scope.gen.option == '' || $scope.gen.value == '')
			$rootScope.customAlert('Error','Please fill-up the required fields');
		$rootScope.customConfirm('Warning','Generating passwords will overwrite previous passwords. Do you want to continue?',function(){
			$rootScope.busy = true;
			$http.get($rootScope.baseURL+'tests/passwords/generatePasswords/'+$scope.gen.option+'/'+$scope.gen.value)
			.then(function(response){
				if(response.data.hasOwnProperty('success')){
					if(!response.data.success){
						$rootScope.customAlert('Error',response.data.msg);
						$rootScope.busy = false;
						return;
					}
				}else{
					$rootScope.customAlert('Success','Passwords generated successfully.');
					$rootScope.busy = false;
				}
			});
		},
		function(){
			$rootScope.busy = false;
		});
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
	
	$scope.take = function(testTitle){
		$window.location.href=$scope.baseURL+'tests/take/test/'+encodeURIComponent(testTitle);
	}
	
});