<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Formedit extends StudentInfoController {
	 
	public function body()
	{
		$this->load->view("student_info_form_edit");
	}
	
	public function action($mode=null,$arg=null){
		if($mode===null){
			$this->responseJSON(false,'Incomplete arguments.');
			return;
		}
		$data = $this->input->post('data');
		$data= json_decode($data,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		};
		
		if($data==null){
			$this->responseJSON(false,'Empty input.');
			return;
		}
		
		$arg = urldecode($arg);
		
		switch($mode){
			case 'addfield':
				if($arg==null){
					$this->responseJSON(false,'Incomplete arguments.');
					return;
				}
				$this->addField($arg,$data);
				break;
			case 'editfield':
				if($arg===null){
					$this->responseJSON(false,'Empty input.');
					return;
				}
				$this->editField($arg,$data);
				break;
			case 'deletefield':
				if($arg===null){
					$this->responseJSON(false,'Incomplete arguments.');
					return;
				}
				$this->deleteField($arg);
				break;
			case 'addtable':
				$this->addTable($data);
				break;
			case 'edittabletitle':
				if($arg===null){
					$this->responseJSON(false,'Incomplete arguments.');
					return;
				}
				$this->editTableTitle($arg,$data);
				break;
			case 'deletetable':
				if($arg===null){
					$this->responseJSON(false,'Incomplete arguments.');
					return;
				}
				$this->deleteTable($arg);
				break;
			case 'updateorder':
				$this->updateOrder($data);
			default:
				return;
		}
	}
	
	private function updateOrder($data){
		/*
			$data = array(
				field id => order
			)	
		*/
		foreach($data as $id=>$o){
			$this->student_information->editInputOrder($id,$o);
		}
		$this->responseJSON(true,'Success');
		return;
	}
	
	
	private function addField($tableName,$data){
		
		$res = $this->student_information->getTableID($tableName);
		if($res == null){
			$this->responseJSON(false,'Table not found.');
			return;
		}
		
		if(!isset($data['Title'])||!isset($data['Input Type'])||!isset($data['Name'])){
			$this->responseJSON(false,'Title, Name, and Input Type must be defined');
			return;
		}
		
		$field= array();
		$field['title'] = $data['Title'];
		$field['input_type'] = $data['Input Type'];
		$field['name'] = $data['Name'];
		$field['input_tip']=isset($data['Input Tip'])?$data['Input Tip']:null;
		$field['input_required']=isset($data['Input Required'])?$data['Input Required']:null;
		$field['input_regex']=isset($data['Input Regex'])?$data['Input Regex']:null;
		$field['input_order']=isset($data['Input Order'])?$data['Input Order']:null;
		$field['input_regex_error_msg']=isset($data['Input Regex Error Message'])?$data['Input Regex Error Message']:null;
		
		switch($data['Input Type']){
			case 'text':
			case 'number':
			case 'date':
				$res = $this->student_information->addField($tableName,$field);
				if($res !== null){
					$this->responseJSON(false,$res);
					return;
				}
				break;
			case 'FE':
				if(!isset($data['FE'])){
					$this->responseJSON(false,'Missing floating entity data.');
					return;
				}
				if(!isset($data['FE']['Table']['Name'])){
					$this->responseJSON(false,'Missing floating entity table data.');
					return;
				}
				if(!isset($data['FE']['Default Cardinality'])){
					$defaultCardinality = 1;
				}else{
					$defaultCardinality = $data['FE']['Default Cardinality'];
				}
				
				$cardinalityField = isset($data['FE']['Cardinality Field Name'])?$data['FE']['Cardinality Field Name']:null;
				
				$res = $this->student_information->addFEField($tableName,$data['FE']['Table']['Name'],$field,$cardinalityField,$defaultCardinality);
				
				if($res !== null){
					$this->responseJSON(false,$res);
					return;
				}
				break;
			case 'MC':
				if(!isset($data['MC'])){
					$this->responseJSON(false,'Missing multiple choice data.');
					return;
				}
				if(!isset($data['MC']['Type'])){
					$data['MC']['Type']=MCTypes::SINGLE;
				}
				
				$data['MC']['Type'] = $data['MC']['Type']==MCTypes::SINGLE?MCTypes::SINGLE:MCTypes::MULTIPLE;
				
				$res = $this->student_information->addMCField($tableName,$data['MC']['Type'],$field);
				if($res !=null){
					$this->responseJSON(false,$res);
					return;
				}
				foreach($data['MC'] as $key=>$choices){
					if($key != 'Choices' && $key != 'Custom')
						continue;
					
					foreach($data['MC'][$key] as $choice){
						$res = $this->student_information->addChoice($tableName,$field['name'],$choice,$key=='Choices'?false:true);
						if($res !=null){
							$this->responseJSON(false,$res);
							return;
						}
					}
				}
				
				break;
			default:
				$this->responseJSON(false,'Invalid Input Type');
				return;
		}
		$this->responseJSON(true,'Successfully added field');
		return;
		
	}
	
	private function deleteField($fieldID){
		$res = $this->student_information->deleteField($fieldID);
		if($res !== null){
			$this->responseJSON(false,$res);
			return;
		}
		$this->responseJSON(true,'Field deleted.');
		return;
	}
	
	private function editField($fieldID,$fieldData){
		if(!isset($fieldID)){
			$this->responseJSON(false,'Missing field id.');
			return;		
		}
		$baseData = array(
			'title'=>$fieldData['Title'],
			'input_required'=>$fieldData['Input Required'],
			'input_tip'=>$fieldData['Input Tip'],
			'input_regex'=>$fieldData['Input Regex'],
			'input_regex_error_msg'=>$fieldData['Input Regex Error Message']
		);
		
		$res = $this->student_information->editField($fieldID,$baseData);
		if($res != null){
			$this->responseJSON(false,$res);
			return;
		}
		
		if(isset($fieldData['Input Type'])){
			if($fieldData['Input Type']=='MC'){
				if(!isset($fieldData['Name'])){
					$this->responseJSON(false,'Name not found.');
					return;
				}
				if(!isset($fieldData['MC'])){
					$this->responseJSON(false,'Multiple choice data not found.');
					return;
				}
				if(!isset($fieldData['MC']['Type'])){
					$this->responseJSON(false,'Multiple choice type not found.');
					return;
				}
				
				$tableName = $this->student_information->getFieldTableName($fieldID);
				if($tableName == null){
					$this->responseJSON(false,'No such field.');
					return;
				}
				
				$MCType = $fieldData['MC']['Type']==MCTypes::SINGLE ? MCTypes::SINGLE:MCTypes::MULTIPLE;
				$this->student_information->editMCFieldType($fieldID,$MCType);
				$this->student_information->deleteMCChoices($fieldID);
				foreach($fieldData['MC'] as $key=>$choices){
					if($key != 'Choices' && $key != 'Custom')
						continue;
					
					foreach($fieldData['MC'][$key] as $choice){
						$res = $this->student_information->addChoice($tableName,$fieldData['Name'],$choice,$key=='Choices'?false:true);
						if($res !=null){
							$this->responseJSON(false,$res);
							return;
						}
					}
				}
				
			}
			if($fieldData['Input Type']=='FE'){
				if(!isset($fieldData['FE'])){
					$this->responseJSON(false,'Floating entity data not found.');
					return;
				}
				$this->student_information->editFE($fieldID,array(
					'fe_name'=>isset($fieldData['FE']['Table']['Name'])?$fieldData['FE']['Table']['Name']:null,
					'cardinality_field_name'=>isset($fieldData['FE']['Cardinality Field Name'])?$fieldData['FE']['Cardinality Field Name']:null,
					'default_cardinality'=>isset($fieldData['FE']['Default Cardinality'])?$fieldData['FE']['Default Cardinality']:null
				));
			}
		}
		
		$this->responseJSON(true,'Edited field successfully.');
		return;
	}
	
	private function addTable($data){
		$res = $this->student_information->addTable($data['Name'],$data['Title'],$data['Floating']?Flags::FLOATING:Flags::DEF);
		if($res !=null){
			$this->responseJSON(false,$res);
			return;
		}else{
			$this->responseJSON(true,'Successfully added table.');
			return;
		}
	}
	
	private function deleteTable($tableID){
		
		$res = $this->student_information->deleteTable($tableID);
		if($res !== null){
			$this->responseJSON(false,$res);
			return;
		}
		$this->responseJSON(true,'Table deleted.');
		return;
	}
	
	private function editTableTitle($tableID,$data){
		if(!isset($data['title'])){
			$this->responseJSON(false,'Incomplete data.');
			return;
		}
		
		$res = $this->student_information->editTableTitle($tableID,$data['title']);
		if($res!==null){
			$this->responseJSON(false,$res);
			return;
		}
		$this->responseJSON(true,'Table title edited.');
		return;
	}
}
