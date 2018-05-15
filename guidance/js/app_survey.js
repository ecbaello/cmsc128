app.controller('survey_form',function($scope,$rootScope,$mdDialog,$window,$http){
	
	$scope.ALLresult = {};
	$scope.DFRF = [
		'','','','','','','','','','',''
	];
	$scope.DFPF = [
		'','','','','','',''
	];
	$scope.IDTN = [
		'','','','','','','','','',''
	];
	$scope.ATMP = [
		'','','','','','','','',''
	];
	$scope.VRFL = [
		'','','','','','','','',''
	];
	
	DFRFresult = function(){
		var result = 0;
		var score = '';
		for(var i=0; i<$scope.DFRF.length; i++){
			result = result+$scope.DFRF[i];
		}
		if(result>=0 && result<=3){
			score='Low Risk Factors';
		}
		if(result>=4 && result<=7){
			score='Moderate Risk Factors';
		}else{
			score='High Risk Factors';
		}
		return score;
	}
	
	DFPFresult = function(){
		var result = 0;
		var score = '';
		for(var i=0; i<$scope.DFPF.length; i++){
			result = result+$scope.DFPF[i];
		}
		if(result>=0 && result<=4){
			score='Low Protective Factors';
		}
		if(result>=5 && result<=9){
			score='Moderate Protective Factors';
		}else{
			score='High Protective Factors';
		}
		return score;
	}
	
	IDTNresult = function(){
		var result = 0;
		var score = '';
		for(var i=0; i<$scope.IDTN.length; i++){
			result = result+$scope.IDTN[i];
		}
		if(result>=0 && result<=6){
			score='Very Low Ideation';
		}
		if(result>=7 && result<=14){
			score='Low Ideation';
		}
		if(result>=15 && result<=22){
			score='Moderate Ideation';
		}else{
			score='High Ideation';
		}
		return score;
	}
	
	
	VRFLresult = function(){
		var result = 0;
		var score = '';
		for(var i=0; i<$scope.VRFL.length; i++){
			result = result+$scope.VRFL[i];
		}
		if(result>=0 && result<=5){
			score='Very Low';
		}
		if(result>=6 && result<=12){
			score='Low';
		}
		if(result>=13 && result<=19){
			score='Moderate';
		}else{
			score='High';
		}
		return score;
	}
	
	$scope.survey = [];
	$scope.answers = {};
	
	$scope.init = function(){
		$rootScope.busy = true;
		$http.get($rootScope.baseURL+'survey/take/getsurveyform').then(function(response){
			$scope.survey = response.data;
			for(var i = 0 ; i<$scope.survey.length ; i++){
				$scope.answers[$scope.survey[i].Category.ID] = {};
				for(var j = 0 ; j<$scope.survey[i].Questions.length ; j++){
					$scope.answers[$scope.survey[i].Category.ID][$scope.survey[i].Questions[j]['Question ID']]='';
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
			for(var i = 0 ; i<$scope.survey[sectionIndex].Questions.length ; i++){
				var q = $scope.survey[sectionIndex].Questions[questionIndex];
				var a = $scope.answers[$scope.survey[sectionIndex].Category.ID];
				if(!a.hasOwnProperty(q.Dependent)){
					return false;
				}else{
					if(a[q.Dependent] == q['Dependent AID'])
						return true;
				}
			}
		}
		
		return false;
	}
	
	$scope.submit = function(sn){
		
		console.log($scope.answers);
		return;
		//1,3,6,8
		for(var i = 0 ; i<$scope.DFRF.length ; i++){
			if($scope.DFRF[i] == ''){
				$rootScope.customAlert('Error','Please answer Demographic Factor: Risk Factors #'+(i+1));
				return;
			}
		}
		for(var i = 0 ; i<$scope.DFPF.length ; i++){
			if($scope.DFPF[i] == ''){
				$rootScope.customAlert('Error','Please answer Demographic Factor: Protective Factors #'+(i+1));
				return;
			}
		}
		for(var i = 0 ; i<$scope.IDTN.length ; i++){
			if($scope.IDTN[i] == ''){
				$rootScope.customAlert('Error','Please answer Ideation #'+(i+1));
				return;
			}
		}
		for(var i = 0 ; i<$scope.ATMP.length ; i++){
			if($scope.ATMP[i] == ''){
				$rootScope.customAlert('Error','Please answer Attempt #'+(i+1));
				return;
			}
		}
		for(var i = 0 ; i<$scope.VRFL.length ; i++){
			if(i == 1 || i ==3 || i ==6 || i==8){
				continue;
			}
			if($scope.VRFL[i] == ''){
				$rootScope.customAlert('Error','Please answer Validation: Reasons for Living #'+(i+1));
				return;
			}
		}
		
		$scope.ALLresult.DFRF = DFRFresult();
		$scope.ALLresult.DFPF = DFPFresult();
		$scope.ALLresult.ATMP =	$scope.ATMP;
		$scope.ALLresult.IDTN = IDTNresult();
		$scope.ALLresult.VRFL = VRFLresult();
		
		console.log($scope.ALLresult);
		$rootScope.busy=true;
		$rootScope.post(
			$rootScope.baseURL+'survey/main/submit/'+sn,
			$scope.ALLresult,
			function(response){
				$rootScope.busy = false;
				$rootScope.customConfirm('Success',response.msg,function(){$window.location.reload();},function(){$window.location.reload();});
			},
			function(response){
				$rootScope.busy= false;
				$rootScope.customAlert('Error',response.msg);
			}
		);
	}
});

app.controller('survey_passwords',function($scope,$rootScope,$window,$mdDialog,$http){
	$scope.disp={};
	$scope.gen={};
	$scope.passwords={};

	$scope.submit = function(type){
		post = function(){
			$rootScope.busy = true;
			console.log($rootScope.baseURL+'survey/passwords/action/'+type);
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