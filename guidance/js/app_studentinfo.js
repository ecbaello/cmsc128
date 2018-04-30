app.controller('student_form',function($scope,$rootScope,$http,$window){
	
	$scope.currCategoryKey = 0;
	$scope.currCategory = {};
	$scope.tableData = {};
	$scope.input = {};
	$scope.tests = {};
	$scope.isTests = false;
	$scope.currTest = {};
	$scope.currTestKey = 0;
	
	$scope.init = function(info=''){
		$rootScope.busy = true;
		$http.get($rootScope.baseURL+'studentinfo/add/get/form')
		.then(function(response){
			
			$scope.tableData = response.data;
			$scope.currCategory = response.data[$scope.currCategoryKey];
			$rootScope.busy= false;
		});
		
		if(info!=''){
			$scope.input = info;
			$scope.tests = info['Test Answers'];
		}
	}

	$scope.getCardinality = function(fieldID){
		var number = 1;
		for(var i = 0 ; i<$scope.currCategory.Fields.length ; i++){
			if($scope.currCategory.Fields[i].ID == fieldID){
				fieldIndex = i;
				break;
			}
		}
		console.log($scope.currCategory.Fields[fieldIndex]);
		var cardinalityField = $scope.currCategory.Fields[fieldIndex].FE['Cardinality Field Name'];
		var defaultCardinality = $scope.currCategory.Fields[fieldIndex].FE['Default Cardinality'];
		
		if(cardinalityField != ''){
			if(!$scope.input.hasOwnProperty($scope.currCategory.Table.Name)){
				$scope.input[$scope.currCategory.Table.Name]={
					cardinalityField:defaultCardinality
				};
			}
			if(!$scope.input[$scope.currCategory.Table.Name].hasOwnProperty(cardinalityField)){
				$scope.input[$scope.currCategory.Table.Name].cardinalityField = defaultCardinality;
			}
		
			if($scope.input[$scope.currCategory.Table.Name][cardinalityField]==''){
				$scope.input[$scope.currCategory.Table.Name][cardinalityField] = defaultCardinality;
			}else{
				$scope.input[$scope.currCategory.Table.Name][cardinalityField]= parseInt($scope.input[$scope.currCategory.Table.Name][cardinalityField],10);
				if(isNaN($scope.input[$scope.currCategory.Table.Name][cardinalityField])){
					$scope.input[$scope.currCategory.Table.Name][cardinalityField] = defaultCardinality;
				}
			}
			number = $scope.input[$scope.currCategory.Table.Name][cardinalityField];
		}else{
			number = defaultCardinality;
		}
		return new Array(parseInt(number,10));
	}
	
	$scope.changeCategory = function(categoryKey){
		$scope.currCategoryKey = categoryKey;
		$scope.currCategory = $scope.tableData[categoryKey];
		$scope.isTests = false;
	}
	
	$scope.changeTest = function(testKey){
		$scope.isTests =true;
		$scope.currTestKey = testKey;
		$scope.currTest = $scope.tests[testKey];
	}
	
	$scope.deleteRecord = function(studentID=''){
		if(!studentID)
			return;
		$rootScope.customConfirm('Warning','Are you sure you want to do this?',
			function(){
				$rootScope.post(
					$rootScope.baseURL+'studentinfo/manage/deleteStudent',
					studentID,
					function(res){
						$rootScope.customAlert('Success',res.msg);
					},
					function(res){
						$rootScope.customAlert('Fail',res.msg);
					}
				);
			},
			function(){}
		);
	}
	
	$scope.getLength = function(object){
		return Object.keys(object).length;
	}
	
	$scope.submit = function(type,studentid){
		console.log($scope.input);
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
		$rootScope.busy = true;
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
				$rootScope.busy = false;
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

app.controller('student_form_edit',function($scope,$rootScope,$window,$http,$mdDialog){
	
	$scope.tables = {};
	$scope.currCategoryKey = 0;
	$scope.currCategory = {};
	
	$scope.newTable = {};
	$scope.newField = {};
	
	$scope.newField.newChoice = '';
	$scope.newField.newCustom = '';
	$scope.newChoice = [];
	$scope.newCustom = [];
	
	$scope.fields = {};
	/*
	fields = {
		table key:{
			order: 
			expanded: boolean
			id: field id
		}
	}
	*/
	
	$scope.init = function(){
		$rootScope.busy=true;
		$http.get($rootScope.baseURL+'studentinfo/formedit/get/form/false').then(function(response){
			$scope.tables = response.data;
			$scope.currCategory = $scope.tables[$scope.currCategoryKey];
			console.log($scope.tables);
			
			for(key in $scope.tables){
				$scope.fields[key] = [];
				for(key2 in $scope.tables[key].Fields){
					field = {
						'order' : $scope.tables[key].Fields[key2]['Input Order'],
						'id' : $scope.tables[key].Fields[key2]['ID'],
						'expanded':false
					}
					$scope.fields[key].push(field);
				}
				$scope.fields[key].sort(function(a,b){
					res = parseInt(a.order,10)-parseInt(b.order,10);
					return res==0? parseInt(a.id,10)-parseInt(b.id,10):res;
				});
				updateOrder(false,key);
			}
			updateOrder(true);
			$scope.initNewInput();
			$rootScope.busy =false;
		});
	}
	
	$scope.initNewInput = function(){
		$scope.newTable = {
			'Title':'',
			'Name':'',
			'Floating':false,
		};
		$scope.newField = {
			'Title':'',
			'Name':'',
			'Input Type':'text',
			'Input Required':false,
			'Input Regex':'',
			'Input Regex Error Message':'',
			'Input Order':0,
			'Input Tip':'',
			'FE':{
				'Table':{
					'Title':'',
					'Name':''
				},
				'Cardinality Field Name':'',
				'Default Cardinality':1
			},
			'MC':{
				'Type':1,
				'Choices':[],
				'Custom':[]
			}
		};
	}
	
	$scope.changeCategory = function(categoryKey){
		$scope.currCategoryKey = categoryKey;
		$scope.currCategory = $scope.tables[categoryKey];
	}
	
	$scope.changeField = function(mode,id){
		var data = {};
		switch(mode){
			case 'delete':
				url = $rootScope.baseURL+'studentinfo/formedit/action/deletefield/'+id;
				break;
			case 'edit':
				url = $rootScope.baseURL+'studentinfo/formedit/action/editfield/'+id;
				break;
			default:
				return;
		}
		for(var i = 0 ; i<$scope.currCategory.Fields.length ; i++){
			if($scope.currCategory.Fields[i].ID == id){
				data = $scope.currCategory.Fields[i];
				break;
			}
		}
		success = function(response){
			$rootScope.busy = false;
			$rootScope.customConfirm('Success',response.msg,function(){
				$scope.init();
			},function(){
				$scope.init();
			});
		}
		failure = function(response){
			$rootScope.customAlert('Error',response.msg);
			$rootScope.busy = false;
		}
		
		$rootScope.customConfirm('Warning','Are you sure about this?',function(){
			$rootScope.busy = true;
			$rootScope.post(url,data,success,failure);
		},function(){});
	}
	
	$scope.showAddField = function(){
		$mdDialog.show({
			contentElement: '#addField',
			clickOutsideToClose: true
		});
	}
	$scope.showAddTable = function(){
		$mdDialog.show({
			contentElement: '#addTable',
			clickOutsideToClose: true
		});
	}
	$scope.showEditTableTitle=function(){
		$mdDialog.show({
			contentElement: '#editTableTitle',
			clickOutsideToClose: true
		});
	}
	$scope.closeDialog = function(){
		$mdDialog.hide();
	}
	
	$scope.addField = function(){
		$rootScope.busy = true;
		$scope.newField['Input Order'] = $scope.fields[$scope.currCategoryKey].length+1
		$rootScope.post(
			$rootScope.baseURL+'studentinfo/formedit/action/addfield/'+$scope.currCategory.Table.Name,
			$scope.newField,
			function(response){
				$rootScope.customAlert('Success',response.msg);
				$rootScope.busy = false;
				$scope.init();
			},
			function(response){
				$rootScope.customAlert('Error',response.msg);
				$rootScope.busy = false;
			}
		);
	}
	
	$scope.addTable = function(){
		$rootScope.busy = true;
		$rootScope.post(
			$rootScope.baseURL+'studentinfo/formedit/action/addtable',
			$scope.newTable,
			function(response){
				$rootScope.customAlert('Success',response.msg);
				$rootScope.busy = false;
				$scope.init();
			},
			function(response){
				$rootScope.customAlert('Error',response.msg);
				$rootScope.busy = false;
			}
		);
	}
	
	$scope.deleteTable = function(){
		$rootScope.customConfirm('Warning','Are you sure about this?',function(){
			$rootScope.busy = true;
			$rootScope.post(
				$rootScope.baseURL+'studentinfo/formedit/action/deletetable/'+$scope.tables[$scope.currCategoryKey].Table.ID,
				{placeholder:'test'},
				function(response){
					$rootScope.busy=false;
					$rootScope.customConfirm('Success',response.msg,function(){
						$window.location.reload();
					},function(){
						$window.location.reload();
					});
				},
				function(response){
					$rootScope.busy=false;
					$rootScope.customAlert('Error',response.msg);
				}
			);
			
		},function(){});
	}
	
	$scope.editTableTitle = function(){
		$rootScope.customConfirm('Warning','Are you sure about this?',function(){
			$rootScope.busy = true;
			$rootScope.post(
				$rootScope.baseURL+'studentinfo/formedit/action/edittabletitle/'+$scope.tables[$scope.currCategoryKey].Table.ID,
				{'title':$scope.currCategory.Table.Title},
				function(response){
					$rootScope.busy=false;
					$rootScope.customConfirm('Success',response.msg,function(){
						$scope.init();
					},function(){
						$scope.init();
					});
				},
				function(response){
					$rootScope.busy=false;
					$rootScope.customAlert('Error',response.msg);
				}
			);
			
		},function(){});
	}
	
	$scope.changeOrder = function(currIndex,desIndex){
		if(currIndex == desIndex)
			return;
		currIndex = currIndex-1;
		desIndex = desIndex-1;
		var currValue = $scope.fields[$scope.currCategoryKey][currIndex];
		$scope.fields[$scope.currCategoryKey].splice(currIndex,1);
		$scope.fields[$scope.currCategoryKey].splice(desIndex,0,currValue); 
		
		console.log('Orders: '+currIndex+' '+desIndex);
		updateOrder();
	}
	
	$scope.updateFieldName = function(){
		var re = /[^a-zA-Z0-9/_ -]+/g;
		var input = $scope.newField.Title;
		if(input == '') {
			$scope.newField.Name = '';
			return;
		}
		input = (input.replace(re, '')).toLowerCase();
		$scope.newField.Name = input.replace(/ /g,'_');
	}
	
	$scope.updateTableName = function(){
		var re = /[^a-zA-Z0-9/_ -]+/g;
		var input = $scope.newTable.Title;
		if(input == '') {
			$scope.newTable.Name ='';
			return;
		}
		input = (input.replace(re, '')).toLowerCase();
		$scope.newTable.Name = input.replace(/ /g,'_');
	}
	
	function updateOrder(toPost=true,tableKey=''){
		postData={};
		tableKey = tableKey=='' ? $scope.currCategoryKey:tableKey;
		for(var i= 0 ; i<$scope.fields[tableKey].length ; i++){
			for(var j = 0 ; j<$scope.tables[tableKey].Fields.length ; j++){
				if($scope.tables[tableKey].Fields[j].ID==$scope.fields[tableKey][i].id){
					$scope.tables[tableKey].Fields[j]['Input Order'] = i+1;
					$scope.fields[tableKey][i].order = i+1;
					postData[$scope.fields[tableKey][i].id]=i+1;
				}
			}
		}
		if(toPost){
			$rootScope.busy =true;
			url = $rootScope.baseURL+'studentinfo/formedit/action/updateorder/';
			success=function(){
				$rootScope.busy = false;
			}
			fail = function(){
				$rootScope.busy = false;
			}
			$rootScope.post(url,postData,success,fail);
		}
	}
	
	$scope.getNumber = function(num) {
		return new Array(num);   
	}
	
	$scope.toggleSettings = function(key){
		$scope.fields[$scope.currCategoryKey][key].expanded = !$scope.fields[$scope.currCategoryKey][key].expanded;
	}
	
	$scope.getCardinalityCandidates = function(){
		var candidates = [];
		for(var i=0 ; i<$scope.currCategory.Fields.length ; i++){
			if($scope.currCategory.Fields[i]['Input Type']=='number'){
				candidates.push($scope.currCategory.Fields[i]);
			}
		}
		return candidates;
	}
	
	$scope.addChoice = function(fieldID,isCustom){
		var fieldKey = fieldID;
		if(fieldID==-1){
			if( (isCustom && $scope.newField.newCustom=='') || (!isCustom && $scope.newField.newChoice=='') ){
				return;
			}
			if($scope.newField.MC[isCustom?'Custom':'Choices'].includes(isCustom?$scope.newField.newCustom:$scope.newField.newChoice)){
				$rootScope.customAlert('Error','Choices must be unique.');
				return;
			}
			$scope.newField.MC[isCustom?'Custom':'Choices'].push(isCustom?$scope.newField.newCustom:$scope.newField.newChoice);
			$scope.newField.newChoice = '';
			$scope.newField.newCustom = '';
		}else{
			
			for(var i = 0 ; i<$scope.currCategory.Fields.length ; i++){
				if($scope.currCategory.Fields[i].ID == fieldID){
					fieldKey = i;
					break;
				}
			}
			
			if( (isCustom && $scope.newCustom[fieldID]=='') || (!isCustom && $scope.newChoice[fieldID]==''))
				return;
			if($scope.currCategory.Fields[fieldKey].MC[isCustom?'Custom':'Choices'].includes(isCustom?$scope.newCustom[fieldID]:$scope.newChoice[fieldID])){
				$rootScope.customAlert('Error','Choices must be unique.');
				return;
			}
			$scope.currCategory.Fields[fieldKey].MC[isCustom?'Custom':'Choices'].push(isCustom?$scope.newCustom[fieldID]:$scope.newChoice[fieldID]);
			$scope.newCustom[fieldID] = '';
			$scope.newChoice[fieldID] = '';
		}
		
	}
	
	$scope.deleteChoice = function(fieldID,isCustom,choiceIndex){
		var fieldKey=fieldID;
		if(fieldID==-1){
			$scope.newField.MC[isCustom?'Custom':'Choices'].splice(choiceIndex,1);
		}else{
			for(var i = 0 ; i<$scope.currCategory.Fields.length ; i++){
				if($scope.currCategory.Fields[i].ID == fieldID){
					fieldKey = i;
					break;
				}
			}
			$scope.currCategory.Fields[fieldKey].MC[isCustom?'Custom':'Choices'].splice(choiceIndex,1);
		}
	}
	
	$scope.ping = function(){
		alert('pong');
	}
	
});

app.controller('student_recbin',function($scope,$rootScope,$window,$http,$mdDialog,$filter){
	$scope.bin = {};
	$scope.currType = 'Tables';
	$scope.filters = {
		Tables:{
			Reverse:false,
			Data:[],
			Index:1,
			Division:10,
			Headers:[]
		},
		Fields:{
			Reverse:false,
			Data:[],
			Index:1,
			Division:10,
			Headers:[],
		},
		Records:{
			Reverse:false,
			Data:[],
			Index:1,
			Division:10,
			Headers:[]
		}
	};
	
	$scope.init = function(){
		$rootScope.busy = true;
		$http.get($rootScope.baseURL+'studentinfo/bin/getdeleted').then(function(response){
			$scope.bin = response.data;
			console.log($scope.bin);
			$scope.filters.Tables.Data=$scope.bin.Tables;
			$scope.filters.Tables.Headers=$scope.bin.Tables.length>0?Object.getOwnPropertyNames($scope.bin.Tables[0]):[];
			$scope.filters.Fields.Data=$scope.bin.Fields;
			$scope.filters.Fields.Headers=$scope.bin.Fields.length>0?Object.getOwnPropertyNames($scope.bin.Fields[0]):[];
			$scope.filters.Records.Data=$scope.bin.Records;
			$scope.filters.Records.Headers=$scope.bin.Records.length>0?Object.getOwnPropertyNames($scope.bin.Records[0]):[];
			$rootScope.busy = false;
		});
	}
	
	$scope.action = function(mode,type,id){
		if(mode!='restore'&&mode!='delete')
			return;
		if(type!='Tables' && type!='Fields' && type!='Records')
			return;
		
		yes = function(){
			$rootScope.busy=true;
			$rootScope.post(
				$rootScope.baseURL+'studentinfo/bin/action/'+mode+'/'+type,
				id,
				function (response){
					$rootScope.busy = false;
					$rootScope.customConfirm('Success',response.msg,function(){
						$scope.init();
					},function(){});
				},
				function(response){
					$rootScope.customAlert('Error',response.msg);
					$rootScope.busy = false;
				}
			);
		}
		$rootScope.customConfirm('Warning','Are you sure you want to do this?',yes,function(){});
	}
	
	$scope.sort = function(type,header){
		if(type!='Tables' && type!='Fields' && type!='Records')
			return;
		if($scope.filters[type].Headers.indexOf(header)==-1)
			return;
		$scope.filters[type].Data = $filters('orderBy')($scope.bin[type],header,$scope.filters[type].Data.Reverse);
		$scope.filters[type].Data.Reverse = !$scope.filters[type].Data.Reverse;
	}
	
	$scope.showDialog = function(type,id){
		$scope.dialogID = id;
		$mdDialog.show({
			contentElement: '#action',
			clickOutsideToClose: true
		});
	}
	$scope.closeDialog = function(){
		$mdDialog.hide();
	}
})

app.controller('student_search',function($scope,$rootScope,$window,$http,$filter){
	
	var results = [];
	$scope.params = [];
	$scope.filters = [];
	$scope.results = [];
	$scope.reverse = [];
	$scope.currIndex = 1;
	$scope.division = 10;
	
	$scope.init = function(){
		$http.get($rootScope.baseURL+'studentinfo/manage/get/params')
		.then(function(response){
			$scope.params = response.data;
			console.log($scope.params);
		});
	}
	
	$scope.addFilter = function(type){
		
		var toAdd = JSON.parse($scope.toAddFilter);
		var filter = {
			name:toAdd.name,
			title:toAdd.title,
			type:type
		};
		$scope.filters.push(filter);
	}
	
	$scope.removeFilter = function(index){
		$scope.filters.splice(index,1);
	}
	
	$scope.getLength = function(object){
		return Object.keys(object).length;
	}
	
	$scope.search = function(){
		if($scope.getLength($scope.filters) == 0){
			$rootScope.customAlert('Error','At least one filter is required.');
			return;
		}
		$rootScope.busy = true;
		$http.get($rootScope.baseURL+'studentinfo/manage/get/search/'+encodeURIComponent(angular.toJson($scope.filters)))
		.then(function(response){
			results = response.data;
			$scope.results = results;
			console.log(results);
			$rootScope.busy = false;
		});
	}
	
	$scope.sort = function(key){
		if(typeof $scope.params[key] === 'undefined')
			return;
		if(typeof $scope.reverse[key]==='undefined')
			$scope.reverse[key] = false;
			
		$scope.results = $filter('orderBy')(results,$scope.params[key].name,$scope.reverse[key]);
		$scope.reverse[key] = !$scope.reverse[key];
	}
	
	$scope.getNumber = function(num) {
		num = parseInt(Math.ceil(num),10);
		return new Array(num);   
	}
	
	$scope.parseInt = function(num){
		return parseInt(Math.ceil(num),10);
	}
	
	$scope.nav = function(amt){
		$scope.currIndex+=amt;
	}

});