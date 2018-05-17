<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends BaseController {

	public function body()
	{
		if($this->ion_auth->logged_in()){
			if($this->ion_auth->is_admin()){
				$this->load->view('survey_nav');
			}else{
				$this->load->view('survey_form',array('answered'=>$this->survey_maker->hasAnswered($this->ion_auth->user()->row()->username)));
			}
		}else{
			$this->load->view('login');
		}
	}
}