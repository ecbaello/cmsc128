<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StudentInformationModel extends BaseModel{
	 
	const BaseTableName = 'upbguidance_student'; 
	
	public $ModelName = "Student Information";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function createModel(){
		
	}
	
}