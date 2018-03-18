<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage extends StudentInfoController {

	public function body()
	{
		$this->load->view('student_info_manage');
		//$this->load->view('student_info_form');
	}
	
}