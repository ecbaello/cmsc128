<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
	}
	
}

class BaseController extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model("ion_auth_init");
	}
	
	public function index(){
		$this->load->view('header');
		$this->body();
		$this->load->view('footer');
	}
	
	public function body(){
	}
	
	protected function responseJSON($isSuccessful,$msg){
		echo json_encode(array(
			'success'=>$isSuccessful,
			'msg'=>$msg
		));
		die();
	}
	
	protected function isInputValid($input,$inputType,$regex){
		return true;
	}
}

class StudentInfoController extends BaseController {

	public function __construct(){
		parent::__construct();
		$this->load->model('student_information');
	}
	
	public function get($data,$arg0=null){
		switch($data){
			case 'form':
				$this->getModelForm();
				break;
			case 'params':
				$this->getParams();
				break;
			case 'search':
				$this->search($arg0);
				break;
			default:
				show_404();
				break;
		}
	}
	
	public function post($type,$arg0=null){
		switch($type){
			case 'add':
				$this->update('add');
				break;
			case 'edit':
				$this->update('edit',$arg0);
				break;
			default:
				show_404();
		}
	}
	
	private function update($type='add',$studentID=null){
		$input = $this->input->post('data');
		
		if($input == null){
			$this->responseJSON(false,'Empty Input');
			return;
		}
		//log_message('debug',$input);die();
		$data= json_decode($input,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		if($type=='edit'&&$studentID==null){
			$this->responseJSON(false,'Missing Student ID.');
			return;
		}
		
		$toInsert = array();
		$tableData = $this->getModelForm(true);
		foreach($tableData as $table){
			
			if(!isset($data[$table['Table']['Name']])){
				$this->responseJSON(false,'Incomplete Data. Please fill-up at least one field in the category: '.$table['Table']['Title']);
				return;
			}
			$toInsert = $this->prepareAndValidateInput($toInsert,$table,$data[$table['Table']['Name']]);
		}
		
		if($type=='add'){
			//print('<pre>');print_r($toInsert);print('</pre>');die();
			$referenceField = '';
			foreach($toInsert as $index=>$value){
				if($value['Table_Name'] == Student_Information::BaseTableTableName){
					$referenceField=$value['Fields'][Student_Information::ReferenceFieldFieldName];
					$result = $this->student_information->insert($value['Table_Name'],$value['Fields']);
					if($result !== null){
						$this->responseJSON(false,$result);
						return;
					}
				}
			}
			
			foreach($toInsert as $index=>$value){
				if($referenceField == ''){
					$this->responseJSON(false,'Something went wrong.');
					return;
				}
				if($value['Table_Name'] == Student_Information::BaseTableTableName){
					continue;
				}else{
					$value['Fields'][Student_Information::BaseTablePKName]=$this->student_information->getBasePK($referenceField);
					$result = $this->student_information->insert($value['Table_Name'],$value['Fields']);
					if($result !== null){
						$this->responseJSON(false,$result);
						return;
					}
				}
			}
			
			$this->responseJSON(true,'Added Student');
			return;
		}
		if($type=='edit'){
			
			foreach($toInsert as $index=>$value){
				$value['Fields'][Student_Information::BaseTablePKName] = $studentID;
				$result = $this->student_information->update($value['Table_Name'],$value['Fields']);
				if($result !== null){
					$this->responseJSON(false,$result);
					return;
				}
			}
			
			$this->responseJSON(true,'Edited Student Record');
			return;
		}
	}
	
	private function getModelForm($returnOnly = false){
		$tables = $this->student_information->getTables($this->student_information->ModelTitle);
		$data = array();
		foreach($tables as $table){
			
			if($table[BaseModel::TableFlagFieldName] == TableFlags::FLOATING)
				continue;
			
			$fields = $this->getTableFields($table[BaseModel::TableNameFieldName]);
			array_push($data,array(
				'Table'=>array(
					'Title'=>$table[BaseModel::TableTitleFieldName],
					'Name'=>$table[BaseModel::TableNameFieldName]
				),
				'Fields'=>$fields
			));
		}
		if(!$returnOnly)
			echo json_encode($data);
		//print('<pre>');print_r($data);print('</pre>');die();
		return $data;
	}
	
	private function getParams(){
		$data = $this->student_information->getBaseTableFields();
		$output = array();
		foreach($data as $value){
			
			$inputType = $value[BaseModel::FieldInputTypeFieldName];
			if($inputType=='text'){
				array_push($output,array(
					'title'=>$value[BaseModel::FieldTitleFieldName],
					'name'=>$value[BaseModel::FieldNameFieldName]
				));
			}
			
		}
		echo json_encode($output);
		return $output;
	}
	
	private function getTableFields($tableName){
		
		$fieldsTemp = $this->student_information->getFields($tableName);
		$fields = array();
			
		foreach($fieldsTemp as $field){
				
			if($field[BaseModel::FieldInputTypeFieldName] == 'hidden') continue;
			
			$FEData = array();
			if($field[BaseModel::FieldInputTypeFieldName]=='FE'){
				
				$FEFields = $this->getTableFields($field[BaseModel::FieldNameFieldName]);
				
				$FEData = array(
					'Table'=>array(
						'Title'=>$field[BaseModel::FieldTitleFieldName],
						'Name'=>$field[BaseModel::FieldNameFieldName]
					),
					'Cardinality Field Name' => $this->student_information->getFECardinalityFieldName($tableName,$field[BaseModel::FieldNameFieldName]),
					'Default Cardinality'=>$this->student_information->getFEDefaultCardinality($tableName,$field[BaseModel::FieldNameFieldName]),
					'Fields' => $FEFields
				);
				
			}
			
			$MCData = array();
			if($field[BaseModel::FieldInputTypeFieldName]=='MC'){
				$MCData['Type'] = $this->student_information->getMCType($tableName,$field[BaseModel::FieldNameFieldName]);
				$choices = $this->student_information->getMCChoices($tableName,$field[BaseModel::FieldNameFieldName]);
				
				$MCChoices = array();
				$MCCustom = array();
				foreach($choices as $choice){
					if(isset($choice[AdvancedInputsModel::ChoiceCustomFieldName]) && $choice[AdvancedInputsModel::ChoiceCustomFieldName]==true){
						array_push($MCCustom,$choice[AdvancedInputsModel::ChoiceTitleFieldName]);
					}else{
						array_push($MCChoices,$choice[AdvancedInputsModel::ChoiceValueFieldName]);
					}
				}
				$MCData['Choices'] = $MCChoices;
				$MCData['Custom'] = $MCCustom;
			}
			
			$toPush = array(
				'Title' => $field[BaseModel::FieldTitleFieldName],
				'Name' => $field[BaseModel::FieldNameFieldName],
				'Input Type'=>$field[BaseModel::FieldInputTypeFieldName],
				'Input Required'=>$field[BaseModel::FieldInputRequiredFieldName],
				'Input Regex'=>$field[BaseModel::FieldInputRegexFieldName],
				'Input Regex Error Message'=>$field[BaseModel::FieldInputRegexErrMsgFieldName],
				'Input Order'=>$field[BaseModel::FieldInputOrderFieldName],
				'Input Tip'=>$field[BaseModel::FieldInputTipFieldName]
			);
			
			if(count($FEData)>0)
				$toPush['FE']=$FEData;
			if(count($MCData)>0)
				$toPush['MC']=$MCData;
			
			array_push($fields,$toPush);
			
		}
		
		return $fields;
		
	}
	
	//validates per table
	private function prepareAndValidateInput($toInsertArray,$formData,$inputData){

		$toInsertFields = array();
		foreach($formData['Fields'] as $field){
			
			if($field['Input Type']!='FE'){
				if($field['Input Required'] == true && !isset( $inputData[$field['Name']] )){
					//if input is required but no input found, show error
					$this->responseJSON(false,'Incomplete Data. Please fill-in the required field: '.$field['Title']);
					return;
				}else if (isset( $inputData[$field['Name']] )){
					
					if($field['Input Type'] == 'MC' && $field['MC']['Type'] == MCTypes::MULTIPLE){
						
						if(!is_array($inputData[$field['Name']])){
							$this->responseJSON(false,'Invalid Input at: '.$field['Title']);
							return;
						}
						
						$value = '';
						foreach($inputData[$field['Name']] as $key=>$choice){
							
							if($key == 'Custom'){
								if(!isset($inputData[$field['Name']]['Custom']))
									continue;
								foreach($inputData[$field['Name']]['Custom'] as $choiceKey=>$customChoice){
									$customChoice = preg_replace('[\{}]','',$customChoice);
									$value= $value.' c{\\'.$choiceKey.'\\'.$customChoice.'}';
								}
							}else if($choice != false){
								$value= $value.' {\\'.$key.'\\'.$choice.'}';
							}
						}
						$inputData[$field['Name']] = $value;
						
					}else{
					
						//if input required, and input found, add to insert
						if(!$this->isInputValid($inputData[$field['Name']],$field['Input Type'],$field['Input Regex'])){
							$this->responseJSON(false,'Invalid Data at '.$field['Title']);
							return;
						}
					}
					$toInsertFields[$field['Name']]=$inputData[$field['Name']];
				}
			}else{
				
				if(isset($inputData[ $field['FE']['Cardinality Field Name'] ])){
					$cardinality = $inputData[ $field['FE']['Cardinality Field Name'] ];
				}else{
					$cardinality = $field['FE']['Default Cardinality'];
				}
				
				for($i = 0 ; $i<$cardinality ; $i++){
					if(!isset( $inputData[ $field['FE']['Table']['Name'] ][$i])){
						$this->responseJSON(false,'Incomplete Data. Please fill-in at least one field in '.$field['FE']['Table']['Title'].' #'.($i+1));
						return;
					}
					$toInsertArray = $this->prepareAndValidateInput($toInsertArray,$field['FE'],$inputData[ $field['FE']['Table']['Name'] ][$i]);
				}	
			}
		}
		
		if(count($toInsertFields)>0){
			array_push($toInsertArray,array(
				'Table_Name'=>$formData['Table']['Name'],
				'Fields'=>$toInsertFields
			));
		}
		
		
		return $toInsertArray;
		
	}
	
	private function search($filters){
		if($filters == null){
			$this->responseJSON(false,'No Filters Found');
			return;
		}
		$filters = json_decode(urldecode($filters),true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		$whereQuery=array();
		foreach($filters as $filter){
			if(!isset($filter['value']) || $filter['value']=='')
				continue;
			array_push($whereQuery,array(
				'type'=>$filter['type'],
				'query'=>array(
					$filter['name']=>$filter['value']
				)
			));
		}
		$result = $this->student_information->searchStudents($whereQuery);
		
		echo json_encode($result,JSON_NUMERIC_CHECK);
		return $result;
		
	}

}

class TestsController extends BaseController {

	public function __construct(){
		parent::__construct();
		$this->load->model('test_maker');
	}

}