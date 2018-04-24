<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Passwords extends TestsController {

	public function __construct(){
		parent::__construct();
		$this->permissionRestrict();
	}
	
	public function body(){
		$this->load->view('tests_password');
	}
	
	public function action($mode=null){
		//$mode: 0=>get, 1=>generate
		if($mode===null){
			$this->responseJSON(false,'Missing arguments.');
			return;
		}
		$mode=urldecode($mode);
		if($mode!=0&&$mode!=1){
			$this->responseJSON(false,'Invalid action.');
			return;
		}
		$data = $this->input->post('data');
		$data= json_decode($data,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		if(!isset($data['mode'])|| !isset($data['value'])){
			$this->responseJSON(false,'Missing input.');
			return;
		}		

		if($data['mode']==0){
			if(!preg_match('/^\\d{4}$/',$data['value'])){
				$this->responseJSON(false,'Invalid Year');
				return;
			}
		}else if($data['mode']==1){
			if(!preg_match('/^\\d{4}-\\d{5}$/',$data['value'])){
				$this->responseJSON(false,'Invalid Student Number');
				return;
			}
		}else{
			$this->responseJSON(false,'Invalid mode.');
			return;
		}
				
		$result = $mode==0 ? $this->test_maker->getPasswords($data['mode'],$data['value']) : $this->test_maker->generatePasswords($data['mode'],$data['value']);
		
		echo json_encode(array(
			'success'=>true,
			'msg'=>'',
			'data'=>$result
		));
		return;
		
	}
	
}