<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends StudentInfoController {

	public function body()
	{
		$this->load->view('student_info_add');
	}
	
	public function get($data){
		
		switch($data){
			case 'form':
				$this->getModelForm();
				break;
			default:
				show_404();
				break;
		}
		
	}
	
	public function post($input=null){
		if($input == null)
			return;
		
		$input = urldecode($input);
		$input = json_decode($input,true);
		
		//print('<pre>');print_r($input);print('</pre>');die();
		
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		$csrfTokenName = $this->security->get_csrf_token_name();
		$csrfHash = $this->security->get_csrf_hash();
		
		if(!isset($input[$csrfTokenName])){
			$this->responseJSON(false,'Missing CSRF Validation');
			return;
		}
		if($input[$csrfTokenName]!=$csrfHash){
			$this->responseJSON(false,'Invalid CSRF Token');
			return;
		}
		
		$data= $input['data'];
		
		/*
		To Insert
		{
			Table_Name,
			Fields
			{
				Field Name => Data
			}
		}
		*/
		
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
	
	private function getModelForm($returnOnly = false){
		$tables = $this->student_information->getTables($this->student_information->ModelTitle);
		$data = array();
		foreach($tables as $table){
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
	
	private function getTableFields($tableName){
		
		$fieldsTemp = $this->student_information->getFields($tableName);
		$fields = array();
			
		foreach($fieldsTemp as $field){
				
			if($field[BaseModel::FieldInputTypeFieldName] == 'hidden') continue;
			
			$AETData = array();
			if($field[BaseModel::FieldInputTypeFieldName]=='AET'){
				
				$AETFields = $this->getTableFields($field[BaseModel::FieldNameFieldName]);
				
				$AETData = array(
					'Table'=>array(
						'Title'=>$field[BaseModel::FieldTitleFieldName],
						'Name'=>$field[BaseModel::FieldNameFieldName]
					),
					'Cardinality Field Name' => $this->student_information->getAETCardinalityFieldName($tableName,$field[BaseModel::FieldNameFieldName]),
					'Default Cardinality'=>$this->student_information->getAETDefaultCardinality($tableName,$field[BaseModel::FieldNameFieldName]),
					'Fields' => $AETFields
				);
				
			}
			array_push($fields,array(
				'Title' => $field[BaseModel::FieldTitleFieldName],
				'Name' => $field[BaseModel::FieldNameFieldName],
				'Input Type'=>$field[BaseModel::FieldInputTypeFieldName],
				'Input Required'=>$field[BaseModel::FieldInputRequiredFieldName],
				'Input Regex'=>$field[BaseModel::FieldInputRegexFieldName],
				'AET'=>$AETData
			));
			
		}
		
		return $fields;
		
	}
	
	//validates per table
	private function prepareAndValidateInput($toInsertArray,$formData,$inputData){

		$toInsertFields = array();
		foreach($formData['Fields'] as $field){
			
			if($field['Input Type']!='AET'){
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
				
				if(isset($inputData[ $field['AET']['Cardinality Field Name'] ])){
					$cardinality = $inputData[ $field['AET']['Cardinality Field Name'] ];
				}else{
					$cardinality = $field['AET']['Default Cardinality'];
				}
				
				for($i = 0 ; $i<$cardinality ; $i++){
					if(!isset( $inputData[ $field['AET']['Table']['Name'] ][$i])){
						$this->responseJSON(false,'Incomplete Data. Please fill-in at least one field in '.$field['AET']['Table']['Title'].' #'.($i+1));
						return;
					}
					$toInsertArray = $this->prepareAndValidateInput($toInsertArray,$field['AET'],$inputData[ $field['AET']['Table']['Name'] ][$i]);
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