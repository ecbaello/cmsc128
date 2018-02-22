<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends TestsController {

	public function body()
	{
		$this->load->view('tests_add');
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
	
	public function getTableData($returnOnly = false){
		
		$tables = $this->test_maker->getTables($this->test_maker->ModelTitle);
		
		$data = array();
		foreach($tables as $index=>$table){
			$fieldsTemp = $this->test_maker->getFields($table[BaseModel::TableNameFieldName]);
			$fields = array();
			
			foreach($fieldsTemp as $field){
				
				if($field[BaseModel::FieldInputTypeFieldName] == 'hidden') continue;
				
				array_push($fields,array(
					'Title' => $field[BaseModel::FieldTitleFieldName],
					'Name' => $field[BaseModel::FieldNameFieldName],
					'Input Type'=>$field[BaseModel::FieldInputTypeFieldName],
					'Input Required'=>$field[BaseModel::FieldInputRequiredFieldName],
					'Input Regex'=>$field[BaseModel::FieldInputRegexFieldName],
				));
			}
			
			array_push($data,array(
				'Table'=>array(
					'Name'=>$table[BaseModel::TableNameFieldName],
					'Title'=>$table[BaseModel::TableTitleFieldName]
				),
				'Fields' => $fields
			));
			
		}
		
		if(!$returnOnly){
			echo json_encode($data);
		}
		return $data;
	}
	
}