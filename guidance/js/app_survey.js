app.controller('survey_form',function($scope,$rootScope,$mdDialog,$window){
	
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
	
	$scope.submit = function(sn){
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