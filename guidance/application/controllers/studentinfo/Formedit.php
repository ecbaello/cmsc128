<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Formedit extends StudentInfoController {
	 
	public function body()
	{
		$this->load->view("student_info_form_edit");
	}
	
}
