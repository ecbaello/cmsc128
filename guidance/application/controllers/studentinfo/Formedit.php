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
		
		switch($mode){
			case 'addfield':
				if($data==null){
					$this->responseJSON(false,'Empty input.');
					return;
				}
				break;
			case 'editfield':
				if($data==null||$arg===null){
					$this->responseJSON(false,'Empty input.');
					return;
				}
				break;
			case 'deletefield':
				if($arg===null){
					$this->responseJSON(false,'Incomplete arguments.');
					return;
				}
				break;
			case 'addtable':
				if($data==null){
					$this->responseJSON(false,'Empty input.');
					return;
				}
				break;
			case 'deletetable':
				if($arg===null){
					$this->responseJSON(false,'Incomplete arguments.');
					return;
				}
				break;
			case 'updateorder':
				if($data ==null){
					$this->responseJSON(false,'Empty input.');
					return;
				}
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
	
}
