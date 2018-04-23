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
		$this->load->add_package_path(APPPATH.'third_party/ion_auth/');
		$this->load->library('ion_auth');
	}
	
	protected function permissionRestrict(){
		if(!$this->ion_auth->is_admin()){
			$this->permissionError();
			return;
		}
	}
	
	public function index(){
		$this->load->view('header');
		$this->body();
		$this->load->view('footer');
	}
	
	public function body(){
	}
	
	public function permissionError(){
		show_error('The user doesn\'t have the permission to perform this action.', 403, 'Forbidden');
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
		$this->load->model('test_maker');
		$this->load->model('student_information');
		$this->permissionRestrict();
	}
	
	public function get($data=null,$arg0=null){
		if($data == null)
			show_404();
		
		$data = urldecode($data);
		$arg0 = urldecode($arg0);
		
		switch($data){
			case 'form':
				$this->getForm(false,$arg0==null?true:false);
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
		$tableData = $this->getForm(true);
		foreach($tableData as $table){
			
			if(!isset($data[$table['Table']['Name']])){
				$this->responseJSON(false,'Incomplete Data. Please fill-up at least one field in the category: '.$table['Table']['Title']);
				return;
			}
			$toInsert = $this->prepareAndValidateInput($toInsert,$table,$data[$table['Table']['Name']]);
		}
		
		if($type=='add'){
			//print('<pre>');print_r($toInsert);print('</pre>');die();
			$studentNumber = '';
			foreach($toInsert as $index=>$value){
				if($value['Table_Name'] == Student_Information::BaseTableTableName){
					$studentNumber=$value['Fields'][Student_Information::StudentNumberFieldName];
					$result = $this->student_information->insert($value['Table_Name'],$value['Fields']);
					if($result !== null){
						$this->responseJSON(false,$result);
						return;
					}
				}
			}
			
			foreach($toInsert as $index=>$value){
				if($studentNumber == ''){
					$this->responseJSON(false,'Something went wrong.');
					return;
				}
				if($value['Table_Name'] != Student_Information::BaseTableTableName){
					$value['Fields'][Student_Information::BaseTablePKName]=$this->student_information->getBasePK($studentNumber);
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
	
	private function getForm($returnOnly = false,$hideFE = true){
		$tables = $this->student_information->getTables(array(
			StudentInfoBaseModel::FlagFieldName.'!=' => StudentInfoBaseModel::FlagFieldName.'|'.Flags::DELETED
		));
		$data = array();
		foreach($tables as $table){
			
			if($table[StudentInfoBaseModel::FlagFieldName] == Flags::FLOATING && $hideFE)
				continue;
			
			$fields = $this->getTableFields($table[StudentInfoBaseModel::TableNameFieldName]);
			array_push($data,array(
				'Table'=>array(
					'ID'=>$table[StudentInfoBaseModel::TableRegistryPKName],
					'Title'=>$table[StudentInfoBaseModel::TableTitleFieldName],
					'Name'=>$table[StudentInfoBaseModel::TableNameFieldName],
					'Essential'=>$table[StudentInfoBaseModel::EssentialFieldName],
					'Flag'=>$table[StudentInfoBaseModel::FlagFieldName]
				),
				'Fields'=>$fields
			));
		}
		if(!$returnOnly)
			echo json_encode($data,JSON_NUMERIC_CHECK);
		//print('<pre>');print_r($data);print('</pre>');die();
		return $data;
	}
	
	private function getParams(){
		$data = $this->student_information->getBaseTableFields();
		$output = array();
		foreach($data as $value){
			
			$inputType = $value[StudentInfoBaseModel::FieldInputTypeFieldName];
			if($inputType=='text'){
				array_push($output,array(
					'title'=>$value[StudentInfoBaseModel::FieldTitleFieldName],
					'name'=>$value[StudentInfoBaseModel::FieldNameFieldName]
				));
			}
			
		}
		echo json_encode($output);
		return $output;
	}
	
	private function getTableFields($tableName){
		
		$fieldsTemp = $this->student_information->getFields($tableName,array(
			StudentInfoBaseModel::FlagFieldName.'!=' => StudentInfoBaseModel::FlagFieldName.'|'.Flags::DELETED
		));
		$fields = array();
			
		foreach($fieldsTemp as $field){
				
			if($field[StudentInfoBaseModel::FieldInputTypeFieldName] == 'hidden') continue;
			
			$FEData = array();
			if($field[StudentInfoBaseModel::FieldInputTypeFieldName]=='FE'){
				
				$FEFields = $this->getTableFields(
					$this->student_information->getFETableName($tableName,$field[StudentInfoBaseModel::FieldNameFieldName])
				);
				
				$FEData = array(
					'Table'=>array(
						'Title'=>$this->student_information->getFETableTitle($tableName,$field[StudentInfoBaseModel::FieldNameFieldName]),
						'Name'=>$this->student_information->getFETableName($tableName,$field[StudentInfoBaseModel::FieldNameFieldName])
					),
					'Cardinality Field Name' => $this->student_information->getFECardinalityFieldName($tableName,$field[StudentInfoBaseModel::FieldNameFieldName]),
					'Default Cardinality'=>$this->student_information->getFEDefaultCardinality($tableName,$field[StudentInfoBaseModel::FieldNameFieldName]),
					'Fields' => $FEFields
				);
				
			}
			
			$MCData = array();
			if($field[StudentInfoBaseModel::FieldInputTypeFieldName]=='MC'){
				$MCData['Type'] = $this->student_information->getMCType($tableName,$field[StudentInfoBaseModel::FieldNameFieldName]);
				$choices = $this->student_information->getMCChoices($tableName,$field[StudentInfoBaseModel::FieldNameFieldName]);
				
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
				'ID' => $field[StudentInfoBaseModel::FieldRegistryPKName],
				'Title' => $field[StudentInfoBaseModel::FieldTitleFieldName],
				'Name' => $field[StudentInfoBaseModel::FieldNameFieldName],
				'Input Type'=>$field[StudentInfoBaseModel::FieldInputTypeFieldName],
				'Input Required'=>$field[StudentInfoBaseModel::FieldInputRequiredFieldName],
				'Input Regex'=>$field[StudentInfoBaseModel::FieldInputRegexFieldName],
				'Input Regex Error Message'=>$field[StudentInfoBaseModel::FieldInputRegexErrMsgFieldName],
				'Input Order'=>$field[StudentInfoBaseModel::FieldInputOrderFieldName],
				'Input Tip'=>$field[StudentInfoBaseModel::FieldInputTipFieldName],
				'Essential'=>$field[StudentInfoBaseModel::EssentialFieldName]
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
						
						$value = array(
							'Custom'=>array(),
							'Normal'=>array()
						);
						foreach($inputData[$field['Name']] as $key=>$choice){
							
							if($key==='Custom'){
								
								if(!isset($inputData[$field['Name']]['Custom'])) //This is needed for some reason
									continue;
								foreach($inputData[$field['Name']]['Custom'] as $choiceKey=>$customChoice){
									$value['Custom'][$choiceKey]=$customChoice;
								}
							}else if($choice!=false){ //If the choice is not chosen, it is equal to false.
								$value['Normal'][$key]=$choice;
							}
						}
						$inputData[$field['Name']] = json_encode($value,JSON_NUMERIC_CHECK|JSON_HEX_APOS|JSON_HEX_QUOT);
					}else{
					
						//if input required, and input found, add to insert
						if(!$this->isInputValid($inputData[$field['Name']],$field['Input Type'],$field['Input Regex'])){
							$this->responseJSON(false,'Invalid Data at '.$field['Title']);
							return;
						}
						
						if($field['Input Type']=='date'){
							$inputData[$field['Name']]= (new DateTime($inputData[$field['Name']]))->format('Y-m-d');
						}
					}
					$toInsertFields[$field['Name']]=$inputData[$field['Name']];
				}
			}else{
				
				if($field['FE']['Cardinality Field Name']!='' && isset($inputData[ $field['FE']['Cardinality Field Name'] ])){
					$cardinality = $inputData[ $field['FE']['Cardinality Field Name'] ];
				}else{
					$cardinality = $field['FE']['Default Cardinality'];
				}
				
				for($i = 0 ; $i<$cardinality ; $i++){
					if(!isset( $inputData[ $field['Name'] ][$i])){
						$this->responseJSON(false,'Incomplete Data. Please fill-in at least one field in '.$field['Title'].' #'.($i+1));
						return;
					}
					$toInsertArray = $this->prepareAndValidateInput($toInsertArray,$field['FE'],$inputData[ $field['Name'] ][$i]);
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
	
	protected function getTestData($testTitle){
		$testID = $this->test_maker->getTestID($testTitle);
		if($testID == null){
			return null;
		}
		
		$output = array();
		$output['Questions'] = $this->test_maker->getQuestions($testTitle);
		$output['Title']=$testTitle;
		$output['ID']=$this->test_maker->getTestID($testTitle);
		$output['Desc']=$this->test_maker->getTestDescription($testTitle);
		
		//echo json_encode($output,JSON_NUMERIC_CHECK);
		//return json_encode($output,JSON_NUMERIC_CHECK);
		return $output;
	}

}