<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->load->view('header');
		$this->body();
		$this->load->view('footer');
	}
	
	public function body(){
	}
	
}
