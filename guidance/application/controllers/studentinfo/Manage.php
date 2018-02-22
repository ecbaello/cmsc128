<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage extends StudentInfoController {

	public function body()
	{
		$this->load->view('student_info_manage');
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
	
	public function getStudentData($studentNumber){
		$tables = $this->student_information->getTables($this->student_information->ModelTitle);
		$data = array();
		foreach($tables as $table){

			$studentData = $this->student_information->getStudentData($table[BaseModel::TableNameFieldName],$studentNumber);
			if($studentData == null){
				$this->responseJSON(false,'No Student Found');
				return;
			}
				
			$fields = array();
			
			if($studentData==null)
				continue;
			
			foreach($studentData as $index=>$student){
				$fields[$index]=$student;
			}
			
			$fieldsTemp = $this->student_information->getFields($table[BaseModel::TableNameFieldName]);
			foreach($fieldsTemp as $i=>$field){
				
				if($field[BaseModel::FieldInputTypeFieldName]=='AET'){
					$AETFields = $this->student_information->getStudentData($field[BaseModel::FieldNameFieldName],$studentNumber,true);
					$AETData = array();
					foreach($AETFields as $index=>$AETField){
						foreach($AETField as $name=>$value){
							$AETData[$index][$name]=$value;
						}
					}
					$fields[$field[BaseModel::FieldNameFieldName]]=$AETData;
				}
			}
			
			$data[$table[BaseModel::TableNameFieldName]]=$fields;
		}
		//print('<pre>');print_r($data);print('</pre>');die();
		echo json_encode(array(
		'success'=>true,
		'msg'=>'',
		'data'=>$data
		),JSON_NUMERIC_CHECK);
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