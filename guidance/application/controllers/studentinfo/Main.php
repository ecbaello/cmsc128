<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends StudentInfoController {
	
	public function body()
	{
		$this->load->view('student_info_nav');
	}
	
}