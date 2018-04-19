<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Passwords extends TestsController {

	public function __construct(){
		parent::__construct();
		
	}
	
	public function body(){
		$this->load->view('tests_password');
	}
	
	public function getPasswords($mode=null,$arg=null){
		//$mode = 0 => get entire batch
		//		= 1 => for single student only
		if($mode===null || $arg===null)
			show_404();
		
		switch($mode){
			case 0:
				if(!preg_match('^\\d{4}$',$arg))
					$this->responseJSON(false,'Invalid Year');
				break;
			case 1:
				if(!preg_match('^\\d{4}-{5}$',$arg))
					$this->responseJSON(false,'Invalid Student Number');
				break;
			default:
				show_404();
		}
	}
	
	public function generatePasswords($mode=null,$arg=null){
		if($mode===null || $arg===null)
			show_404();
		
		switch($mode){
			case 0:
				break;
			case 1:
				break;
			default:
				show_404();
		}
	}
	
}