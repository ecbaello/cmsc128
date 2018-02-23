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
		
		//Validate Data
		$tableData = $this->getTableData(true);
		foreach($tableData as $tableIndex=>$table){
			
			if(!isset($data[$table['Table']['Name']])){
				$this->responseJSON(false,'Incomplete Data. Please fill-up at least one field in the category: '.$table['Table']['Title']);
				return;
			}
			$toInsertFields = array();
			
			foreach($table['Fields'] as $field){
				if($field['Input Type'] != 'AET'){
					
					//If field is not an AET
						
					if($field['Input Required'] == true && !isset( $data[$table['Table']['Name']][$field['Name']] )){
						//if input is required but no input found, show error
						$this->responseJSON(false,'Incomplete Data. Please fill-in the required field: '.$field['Title']);
						return;
					}else if (isset( $data[$table['Table']['Name']][$field['Name']] )){
						//if input required, and input found, add to insert
						
						if(!$this->isInputValid($data[$table['Table']['Name']][$field['Name']],$field['Input Type'],$field['Input Regex'])){
							$this->responseJSON(false,'Invalid Data at '.$field['Title']);
							return;
						}
						$toInsertFields[$field['Name']]=$data[$table['Table']['Name']][$field['Name']];
					}
					
				}else{
					//If field is an AET
					//getting cardinality
					if(isset($data[ $table['Table']['Name'] ][ $field['AET']['Cardinality Field Name'] ])){
						$cardinality = $data[ $table['Table']['Name'] ][ $field['AET']['Cardinality Field Name'] ];
					}else{
						$cardinality = $field['AET']['Default Cardinality'];
					}
					
					for($i = 0 ; $i<$cardinality ; $i++){
						
						if(!isset( $data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ][$i])){
							$this->responseJSON(false,'Incomplete Data. Please fill-in at least one field in '.$field['AET']['Table']['Title']);
							return;
						}
						$toInsertAETFields = array();
						foreach($field['AET']['Fields'] as $AETField){
						
							if($AETField['Input Required']==true && !isset( $data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ] )){
								$this->responseJSON(false,'Incomplete Data. Please fill-in the required fields in '.$field['AET']['Table']['Title']);
								return;
							}else if(isset( $data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ] )){
									
								if(isset( $data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ][$i][ $AETField['Name'] ] )){
									if(!$this->isInputValid($data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ][$i][ $AETField['Name'] ],$AETField['Input Type'],$AETField['Input Regex'])){
										$this->responseJSON(false,'Invalid Data at '.$AETField['Title']);
										return;
									}
									$toInsertAETFields[$AETField['Name']]=$data[ $table['Table']['Name'] ][ $field['AET']['Table']['Name'] ][$i][ $AETField['Name'] ];
								}
							}					
						}
						if(count($toInsertAETFields)>0){
							array_push($toInsert, array(
								'Table_Name'=>$field['Name'],
								'Fields'=>$toInsertAETFields
							));
						}
					}
				}
			}
			
			if(count($toInsertFields)>0){
				array_push($toInsert,array(
					'Table_Name'=> $table['Table']['Name'],
					'Fields'=>$toInsertFields
				));
			}	
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