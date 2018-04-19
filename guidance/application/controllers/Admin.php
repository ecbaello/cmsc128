<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends BaseController {

	public function body()
	{
		$this->load->view($this->ion_auth->logged_in()?'admin':'login');
	}

	
}