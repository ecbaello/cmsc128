<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends BaseController {

	public function body()
	{
		$this->load->view('sampletest');
	}
	
}