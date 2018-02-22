<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends TestsController {

	public function body()
	{
		$this->load->view('tests_edit');
	}
	
}