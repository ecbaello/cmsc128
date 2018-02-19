<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends StudentInfoController {

	public function body()
	{
		$this->load->view('student_info_add');
	}
	
	public function get($data){
		
		switch($data){
			case 'tables':
				$this->getTableData();
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
		
		//Check for data completeness
		$tableData = $this->getTableData(true);
		foreach($tableData as $table){
			
			if(!isset($data[$table['Table']['Name']])){
				$this->responseJSON(false,'Incomplete Data. Please fill-up at least one field in the category: '.$table['Table']['Title']);
				return;
			}
			foreach($table['Fields'] as $field){
				if($field['Input Type'] != 'AET'){
					if($field['Input Required'] == false)
						continue;
					
					if(!isset( $data[$table['Table']['Name']][$field['Name']] )){
						$this->responseJSON(false,'Incomplete Data. Please fill-in the required field: '.$field['Title']);
						return;
					}
				}else{
					foreach($field['AET']['Fields'] as $AETField){
						if($AETField['Input Required'] == false)
							continue;
					
						if(!isset( $data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ] )){
							$this->responseJSON(false,'Incomplete Data. Please fill-in the required fields');
							return;
						}
						
						if(!isset( $data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ][ $AETField['Name'] ] )){
							$this->responseJSON(false,'Incomplete Data. Please fill-in the required field: '.$AETField['Title']);
							return;
						}
					}
				}
			}
		}
		
		$this->responseJSON(true,'Added Student');
		return;
	}
	
	private function getTableData($returnOnly = false){
		$tables = $this->student_information->getTables($this->student_information->ModelTitle);
		$data = array();
		foreach($tables as $table){
			
			$fieldsTemp = $this->student_information->getFields($table[BaseModel::TableNameFieldName]);
			$fields = array();
			
			foreach($fieldsTemp as $field){
				
				if($field[BaseModel::FieldInputTypeFieldName] == 'hidden') continue;
				
				$AETData = array();
				if($field[BaseModel::FieldInputTypeFieldName]=='AET'){
					$AETFieldsTemp = $this->student_information->getFields($field[BaseModel::FieldNameFieldName]);
					$AETFields = array();
					foreach($AETFieldsTemp as $AETField){
						if($AETField[BaseModel::FieldInputTypeFieldName] == 'hidden') continue;
						array_push($AETFields,array(
							'Title' => $AETField[BaseModel::FieldTitleFieldName],
							'Name' => $AETField[BaseModel::FieldNameFieldName],
							'Input Type'=>$AETField[BaseModel::FieldInputTypeFieldName],
							'Input Required'=>$AETField[BaseModel::FieldInputRequiredFieldName],
							'Input Regex'=>$AETField[BaseModel::FieldInputRegexFieldName]
						));
					}
					
					$AETData = array(
						'Table'=>array(
							'Title'=>$field[BaseModel::FieldTitleFieldName],
							'Name'=>$field[BaseModel::FieldNameFieldName]
						),
						'Cardinality Field Name' => $this->student_information->getAETCardinalityFieldName($table[BaseModel::TableNameFieldName],$field[BaseModel::FieldNameFieldName]),
						'Default Cardinality'=>$this->student_information->getAETDefaultCardinality($table[BaseModel::TableNameFieldName],$field[BaseModel::FieldNameFieldName]),
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
		return $data;
	}
	
}