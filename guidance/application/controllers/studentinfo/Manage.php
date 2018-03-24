<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage extends StudentInfoController {
	
	public function body()
	{
		$this->load->view('student_info_manage');
		//$this->load->view('student_info_form');
	}
	
	public function student($studentNumber){
		
		$studentNumber = urldecode($studentNumber);
		
		$studentInfo = $this->getStudentData($studentNumber);
		
		$this->load->view('header');
		$this->load->view('student_info_form',array('mode'=>'manage','student_id'=>$this->student_information->getBasePK($studentNumber),'student_number'=>$studentNumber,'student_info'=>json_encode($studentInfo,JSON_HEX_APOS|JSON_NUMERIC_CHECK)));
		$this->load->view('footer');
		
	}
	
	private function getStudentData($studentNumber){
		
		$tables = $this->student_information->getTables($this->student_information->ModelTitle);
		$data = array();
		
		foreach($tables as $table){

			if($table[BaseModel::TableFlagFieldName] == TableFlags::FLOATING)
				continue;
			$studentData = $this->student_information->getStudentData($table[BaseModel::TableNameFieldName],$studentNumber);
			if($studentData == null){
				show_404();
			}
				
			$fields = array();
			
			if($studentData==null)
				continue;
			
			foreach($studentData as $index=>$student){
				$fields[$index]=$student;
			}
			
			$fieldsTemp = $this->student_information->getFields($table[BaseModel::TableNameFieldName]);
			foreach($fieldsTemp as $i=>$field){
				
				if($field[BaseModel::FieldInputTypeFieldName]=='FE'){
					$FEFields = $this->student_information->getStudentData($field[BaseModel::FieldNameFieldName],$studentNumber,true);
					$FEData = array();
					foreach($FEFields as $index=>$FEField){
						foreach($FEField as $name=>$value){
							$FEData[$index][$name]=$value;
						}
					}
					$fields[$field[BaseModel::FieldNameFieldName]]=$FEData;
				}
				
				if($field[BaseModel::FieldInputTypeFieldName]=='MC' && $this->student_information->getMCType($table[BaseModel::TableNameFieldName],$field[BaseModel::FieldNameFieldName]) == MCTypes::MULTIPLE){
					$index = $field[BaseModel::FieldNameFieldName];
					$value = $fields[$field[BaseModel::FieldNameFieldName]];
					$fields[$index] = array();
					
					$result = array();
					preg_match_all('/c?{[^{}]+}+/',$value,$result);
					foreach($result[0] as $r){
						if(preg_match('/^c.+/',$r)){
							preg_match('/c{\\\\(.+)\\\\(.+)}/',$r,$choice);
							$fields[$index]['Custom'][$choice[1]] = $choice[2];
						}else{
							preg_match('/{\\\\(.+)\\\\(.+)}/',$r,$choice);
							$fields[$index][$choice[1]] = $choice[2];
						}
					}
					
				}
			}
			
			
			$data[$table[BaseModel::TableNameFieldName]]=$fields;
		}
		
		$data['Test Answers'] = array();
		
		$testAnswers = $this->test_maker->getAnswers($studentNumber);
		if($testAnswers !== null)
			$data['Test Answers']=$testAnswers;
		
		//return json_encode($data,JSON_NUMERIC_CHECK);
		//print('<pre>');print_r($data);print('</pre>');
		return $data;
		
	}
	
}