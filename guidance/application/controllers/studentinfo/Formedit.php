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
		
		switch($mode){
			case 'addfield':
				$this->addField($data);
				break;
			case 'editfield':
				if($arg===null){
					$this->responseJSON(false,'Empty input.');
					return;
				}
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
	
	
	private function addField($data){
		/*
			$data = array(
			)
		*/
		
		$fieldData = array();
		
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
	}
	
	private function addTable($data){
		/*
			$data = array(
				Title=>title,
				Name=>name,
				Floating=>floating
			)
		*/
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
}
