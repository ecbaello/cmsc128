<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bin extends StudentInfoController {

	public function body()
	{
		$this->load->view('student_info_bin');
	}
	
	public function getDeleted(){
		$output = $this->student_information->getDeleted();
		echo json_encode($output,JSON_NUMERIC_CHECK);
		return;
	}
	
	public function action($mode=null,$type=null){
		if($mode == null || $type == null){
			$this->responseJSON(false,'Incomplete arguments.');
			return;
		}
		$data = $this->input->post('data');
		if($data==null){
			$this->responseJSON(false,'Empty input.');
			return;
		}
		
		switch($mode){
			case 'restore':
				switch($type){
					case 'Tables':
						$res = $this->student_information->restoreTable($data);
						if($res != null){
							$this->responseJSON(false,$res);
							return;
						}
						$this->responseJSON(true,'Table restored successfully.');
						return;
					case 'Fields':
						$res = $this->student_information->restoreField($data);
						if($res != null){
							$this->responseJSON(false,$res);
							return;
						}
						$this->responseJSON(true,'Field restored successfully.');
						return;
					case 'Records':
						$res = $this->student_information->restoreStudent($data);
						if($res != null){
							$this->responseJSON(false,$res);
							return;
						}
						$this->responseJSON(true,'Student record restored successfully.');
						return;
					default:
						show_404();
				}
				return;
			case 'delete':
				switch($type){
					case 'Tables':
						$res = $this->student_information->deleteTable($data,true);
						if($res != null){
							$this->responseJSON(false,$res);
							return;
						}
						$this->responseJSON(true,'Table permanently deleted.');
						return;
					case 'Fields':
						$res = $this->student_information->deleteField($data,true);
						if($res != null){
							$this->responseJSON(false,$res);
							return;
						}
						$this->responseJSON(true,'Field permanently deleted.');
						return;
					case 'Records':
						$res = $this->student_information->deleteStudent($data,true);
						if($res != null){
							$this->responseJSON(false,$res);
							return;
						}
						$this->responseJSON(true,'Student record permanently deleted.');
						return;
					default:
						show_404();
				}
				return;
			default:
				show_404();
		}
		
	}
	
}