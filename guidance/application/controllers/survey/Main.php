<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends surveyController {

	public function body()
	{
		$this->load->view('survey_form');
	}
	
}