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
	
	private function getTableData(){
		$tables = $this->student_information->getTables($this->student_information->ModelTitle);
		$data = array();
		foreach($tables as $table){
			
			$fieldsTemp = $this->student_information->getFields($table[BaseModel::TableNameFieldName]);
			$fields = array();
			
			foreach($fieldsTemp as $field){
				array_push($fields,array(
					'Title' => $field[BaseModel::FieldTitleFieldName],
					'Name' => $field[BaseModel::FieldNameFieldName],
					'Input Type'=>$field[BaseModel::FieldInputTypeFieldName],
					'Input Required'=>$field[BaseModel::FieldInputRequiredFieldName],
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
		echo json_encode($data);
	}
	
}