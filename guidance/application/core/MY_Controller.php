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
	}
	
	public function index(){
		$this->load->view('header');
		$this->body();
		$this->load->view('footer');
	}
	
	public function body(){
	}
	
	protected function responseJSON($isSuccessful,$msg){
		echo json_encode(array(
			'success'=>$isSuccessful,
			'msg'=>$msg
		));
	}
	
	protected function isInputValid($input,$inputType){
		return true;
	}
}

class StudentInfoController extends BaseController {

	public function __construct(){
		parent::__construct();
		$this->load->model('student_information');
	}

}