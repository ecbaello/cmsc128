<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Take extends TestsController {

	public function body()
	{
		$this->load->view('tests_take');
	}
	
}