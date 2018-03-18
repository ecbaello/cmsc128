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
	
	public function get($data){
		switch($data){
			case 'form':
				$this->getModelForm();
				break;
			case 'params':
				$this->getParams();
				break;
			default:
				show_404();
				break;
		}
	}
	
	public function post($type){
		switch($type){
			case 'add':
				$this->add();
				break;
			case 'edit':
				$this->edit();
				break;
			default:
				show_404();
		}
	}
	
	private function add(){
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
		
		$toInsert = array();
		$tableData = $this->getModelForm(true);
		foreach($tableData as $table){
			
			if(!isset($data[$table['Table']['Name']])){
				$this->responseJSON(false,'Incomplete Data. Please fill-up at least one field in the category: '.$table['Table']['Title']);
				return;
			}
			$toInsert = $this->prepareAndValidateInput($toInsert,$table,$data[$table['Table']['Name']]);
		}
		
		$referenceField = '';
		//print('<pre>');print_r($toInsert);print('</pre>');die();
		foreach($toInsert as $index=>$value){
			if($index == 0){
				$referenceField=$value['Fields'][Student_Information::ReferenceFieldFieldName];
				$result = $this->student_information->insert($value['Table_Name'],$value['Fields']);
				if($result !== null){
					$this->responseJSON(false,$result);
					return;
				}
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
	
	private function edit(){
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
			if($inputType=='hidden' || $inputType== 'FE' || $inputType=='MC')
				continue;
			
			array_push($output,array(
				'title'=>$value[BaseModel::FieldTitleFieldName],
				'name'=>$value[BaseModel::FieldNameFieldName]
			));
			
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
			array_push($fields,array(
				'Title' => $field[BaseModel::FieldTitleFieldName],
				'Name' => $field[BaseModel::FieldNameFieldName],
				'Input Type'=>$field[BaseModel::FieldInputTypeFieldName],
				'Input Required'=>$field[BaseModel::FieldInputRequiredFieldName],
				'Input Regex'=>$field[BaseModel::FieldInputRegexFieldName],
				'Input Regex Error Message'=>$field[BaseModel::FieldInputRegexErrMsgFieldName],
				'Input Order'=>$field[BaseModel::FieldInputOrderFieldName],
				'Input Tip'=>$field[BaseModel::FieldInputTipFieldName],
				'FE'=>$FEData
			));
			
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
					//if input required, and input found, add to insert
					if(!$this->isInputValid($inputData[$field['Name']],$field['Input Type'],$field['Input Regex'])){
						$this->responseJSON(false,'Invalid Data at '.$field['Title']);
						return;
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

}

class TestsController extends BaseController {

	public function __construct(){
		parent::__construct();
		$this->load->model('test_maker');
	}

}