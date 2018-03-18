<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends StudentInfoController {

	public function body()
	{
		$this->load->view('student_info_form',array('mode'=>'add'));
	}
	
}