<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserAnswers extends TestsController {

	public function __construct(){
		parent::__construct();
		$this->load->model('student_information');
	}

	public function body()
	{
		$this->load->view('tests_user_answers');
	}
	
}