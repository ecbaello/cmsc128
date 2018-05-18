app.controller('survey_form',function($scope,$rootScope,$mdDialog,$window,$http){
	$scope.survey = [];
	$scope.answers = {};
	
	$scope.init = function(){
		$rootScope.busy = true;
		$http.get($rootScope.baseURL+'survey/take/getsurveyform').then(function(response){
			$scope.survey = response.data;
			for(var i = 0 ; i<$scope.survey.length ; i++){
				$scope.answers[$scope.survey[i].Category.ID] = {};
				for(var j = 0 ; j<$scope.survey[i].Questions.length ; j++){
					$scope.answers[$scope.survey[i].Questions[j]['Question ID']]='';
				}
			}
			$rootScope.busy = false;
		});
	}
	
	$scope.checkDependency = function(sectionIndex,questionIndex){
		//false if dependency is not satisfied
		//true otherwise
		if($scope.survey[sectionIndex].Questions[questionIndex].Dependent == null){
			return true;
		}else{
			var q = $scope.survey[sectionIndex].Questions[questionIndex];
			var a = $scope.answers;
			if(!a.hasOwnProperty(q.Dependent)){
				return false;
			}else{
				var dependentIndex = -1;
				for(var i = 0; i<$scope.survey[sectionIndex].Questions.length ; i++){
					if($scope.survey[sectionIndex].Questions[i]['Question ID'] == q.Dependent)
						dependentIndex = i;
				}
				if(dependentIndex==-1)
					return false;
				if(a[q.Dependent] == q['Dependent AID'] && $scope.checkDependency(sectionIndex,dependentIndex))
					return true;
			}
		}
		
		return false;
	}
	
	$scope.submit = function(sn){

		cont = function(){
			$rootScope.busy = true;
			
			for(var i = 0 ; i<$scope.survey.length ; i++){
				var section = $scope.survey[i];
				for(var j = 0 ; j<section.Questions.length ; j++){
					var question = section.Questions[j];
					if($scope.checkDependency(i,j)){
						if(!$scope.answers.hasOwnProperty(question['Question ID']) || $scope.answers[question['Question ID']]==''|| typeof $scope.answers[question['Question ID']] === 'undefined'){
							$rootScope.customAlert('Warning','Please answer the question: "'+question.Question+'" in '+section.Category.Title);
							$rootScope.busy = false;
							return;
						}
					}
				}
			}
			
			$rootScope.post(
				$rootScope.baseURL+'survey/take/submit/'+sn,
				$scope.answers,
				function(response){
					$rootScope.customConfirm('Success','Answers successfully submitted',function(){
						$window.location.reload();
					},function(){
						$window.location.reload();
					});
				},
				function(response){
					$rootScope.customAlert('Error',response.msg);
					$rootScope.busy = false;
				}
			);
		}
		
		$rootScope.customConfirm('Warning','Please ensure that all your answers are accurate. Once submitted, this survey can no longer be answered again.', cont,function(){});
		
		return;
	}
});

app.controller('survey_passwords',function($scope,$rootScope,$window,$mdDialog,$http){
	$scope.disp={};
	$scope.gen={};
	$scope.passwords={};

	$scope.submit = function(type){
		post = function(){
			$rootScope.busy = true;
			$rootScope.post(
				$rootScope.baseURL+'survey/passwords/action/'+type
				,{
					'mode':type==0 ? $scope.disp.option : $scope.gen.option,
					'value':type==0 ? $scope.disp.value : $scope.gen.value
				}
				,function(response){
					$rootScope.busy = false;
					if(type==1)
						$rootScope.customAlert('Success','Passwords generated successfully');
					$scope.passwords = response.data;
				}
				,function(response){
					$rootScope.customAlert('Error',response.msg);
					$rootScope.busy = false;
				}
			)
		}
		
		if(type==0){
			if($scope.disp.option == '' || $scope.disp.value == '')
				$rootScope.customAlert('Error','Please fill-up the required fields');
			post();
		}else if(type==1){
			if($scope.gen.option == '' || $scope.gen.value == '')
				$rootScope.customAlert('Error','Please fill-up the required fields');
			$rootScope.customConfirm('Warning','Generating passwords will overwrite previous passwords. Do you want to continue?',post,function(){});
		}else{
			return;
		}
		
	}
});