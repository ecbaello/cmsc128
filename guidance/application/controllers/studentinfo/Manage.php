<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage extends StudentInfoController {
	
	public function body()
	{
		$this->load->view('student_info_manage');
		//$this->load->view('student_info_form');
	}
	
	public function student($studentNumber = null){
		
		if($studentNumber == null){
			show_404();
		}
		$studentNumber = urldecode($studentNumber);
		
		$studentInfo = $this->getStudentData($studentNumber);
		
		$this->load->view('header');
		$this->load->view('student_info_form',array('mode'=>'manage','student_id'=>$this->student_information->getBasePK($studentNumber),'student_number'=>$studentNumber,'student_info'=>json_encode($studentInfo,JSON_HEX_APOS|JSON_NUMERIC_CHECK)));
		$this->load->view('footer');
		
	}
	
	public function printStudent($studentNumber=null){
		if($studentNumber == null){
			show_404();
		}
		$studentNumber = urldecode($studentNumber);
		
		$studentInfo = $this->getStudentData($studentNumber);
		
		$this->load->view('header');
		//$this->load->view('student_info_print',array('mode'=>'manage','student_id'=>$this->student_information->getBasePK($studentNumber),'student_number'=>$studentNumber,'student_info'=>json_encode($studentInfo,JSON_HEX_APOS|JSON_NUMERIC_CHECK)));
		$this->load->view('footer');
		
	}
	
	public function deleteStudent(){
		$data = $this->input->post('data');
		if($data == null){
			$this->responseJSON(false,'Missing data');
			return;
		}
		$res = $this->student_information->deleteStudent($data);
		if($res != null){
			$this->responseJSON(false,$res);
			return;
		}
		$this->responseJSON(true,'Student Record deleted.');
		return;
	}
	
	private function getStudentData($studentNumber){
		
		$tables = $this->student_information->getTables(array(
			StudentInfoBaseModel::FlagFieldName.'!=' => StudentInfoBaseModel::FlagFieldName.'|'.Flags::DELETED
		));
		$data = array();
		
		foreach($tables as $table){

			if($table[StudentInfoBaseModel::FlagFieldName] == Flags::FLOATING)
				continue;
			$studentData = $this->student_information->getStudentData($table[StudentInfoBaseModel::TableNameFieldName],$studentNumber);
				
			$fields = array();
			
			if($studentData==null)
				continue;
			
			foreach($studentData as $index=>$student){
				$fields[$index]=$student;
			}
			
			$fieldsTemp = $this->student_information->getFields($table[StudentInfoBaseModel::TableNameFieldName],array(
				StudentInfoBaseModel::FlagFieldName.'!=' => StudentInfoBaseModel::FlagFieldName.'|'.Flags::DELETED
			));
			foreach($fieldsTemp as $i=>$field){
				
				if($field[StudentInfoBaseModel::FieldInputTypeFieldName]=='FE'){
					$FETableName = $this->student_information->getFETableName($table[StudentInfoBaseModel::TableNameFieldName],$field[StudentInfoBaseModel::FieldNameFieldName]);
					$FEFields = $this->student_information->getStudentData($FETableName,$studentNumber,true);
					$FEData = array();
					foreach($FEFields as $index=>$FEField){
						foreach($FEField as $name=>$value){
							
							if($this->student_information->getMCType($FETableName,$name)==MCTypes::MULTIPLE){
								$FEData[$index][$name]=array();
								$MCData = json_decode($value,true);
								foreach($MCData['Custom'] as $i2=>$val){
									$FEData[$index][$name]['Custom'][$i2]=$val;
								}
								foreach($MCData['Normal'] as $i2=>$val){
									$FEData[$index][$name][$i2]=$val;
								}
							}else{
								$FEData[$index][$name]=$value;
							}
						}
					}
					$fields[$field[StudentInfoBaseModel::FieldNameFieldName]]=$FEData;
				}
				
				if($field[StudentInfoBaseModel::FieldInputTypeFieldName]=='MC' && $this->student_information->getMCType($table[StudentInfoBaseModel::TableNameFieldName],$field[StudentInfoBaseModel::FieldNameFieldName]) == MCTypes::MULTIPLE){
					$index = $field[StudentInfoBaseModel::FieldNameFieldName];
					$value = $fields[$field[StudentInfoBaseModel::FieldNameFieldName]];
					$fields[$index] = array();
					
					$result = json_decode($value,true);
					foreach($result['Custom'] as $i2=>$val){
						$fields[$index]['Custom'][$i2]=$val;
					}
					foreach($result['Normal'] as $i2=>$val){
						$fields[$index][$i2]=$val;
					}
					
				}
			}
			
			
			$data[$table[StudentInfoBaseModel::TableNameFieldName]]=$fields;
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