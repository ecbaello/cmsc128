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
	
	public function getPasswords($mode=null,$arg=null){
		//$mode = 0 => get entire batch
		//		= 1 => for single student only
		if($mode===null || $arg===null)
			show_404();
		
		$result = '';
		switch($mode){
			case 0:
				if(!preg_match('/^\\d{4}$/',$arg)){
					$this->responseJSON(false,'Invalid Year');
					return;
				}
				$result = $this->test_maker->getPasswords(0,$arg);
				break;
			case 1:
				if(!preg_match('/^\\d{4}-\\d{5}$/',$arg)){
					$this->responseJSON(false,'Invalid Student Number');
					return;
				}
				$result = $this->test_maker->getPasswords(1,$arg);
				break;
			default:
				show_404();
				return;
		}
		
		if($result != ''){
			echo json_encode($result);
		}
	}
	
	public function generatePasswords($mode=null,$arg=null){
		if($mode===null || $arg===null)
			show_404();
		//print_r($mode);die();
		switch($mode){
			case 0:
				if(!preg_match('/^\\d{4}$/',$arg)){
					$this->responseJSON(false,'Invalid Year');
					return;
				}
				$this->test_maker->generatePasswords(0,$arg);
				break;
			case 1:
				if(!preg_match('/^\\d{4}-\\d{5}$/',$arg)){
					$this->responseJSON(false,'Invalid Student Number');
					return;
				}
				$this->test_maker->generatePasswords(1,$arg);
				break;
			default:
				show_404();
				return;
		}
	}
	
}