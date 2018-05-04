<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class survey extends CI_Model{
	
	const surveytablename = DB_PREFIX.'survey_ans';
	const StudentNumber = 'stdnum';
	const IA = 'demog_factor_risk';
	const IB = 'demog_factor_protective';
	const II = 'ideation';
	const III = 'attempt';
	const III1 = 'attempt1';
	const III2 = 'attempt2';
	const III3 = 'attempt3';
	const III4 = 'attempt4';
	const III5 = 'attempt5';
	const III6 = 'attempt6';
	const III6_5 = 'attempt6_5';
	const III7 = 'attempt7';
	const III7_5 = 'attempt7_5';
	const IV = 'validation';
	
	public $ModelTitle = 'survey';
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		$this->createModel();
	}
	
	public function createModel(){
		
		$this->dbforge->add_field(self::StudentNumber.' varchar(11)');
		$this->dbforge->add_field(self::IA.' int not null default 0');
		$this->dbforge->add_field(self::IB.' int not null default 0');
		$this->dbforge->add_field(self::II.' int not null default 0');
		$this->dbforge->add_field(self::III1.' varchar(3)');
		$this->dbforge->add_field(self::III2.' varchar(50)');
		$this->dbforge->add_field(self::III3.' varchar(50)');
		$this->dbforge->add_field(self::III4.' varchar(50)');
		$this->dbforge->add_field(self::III5.' varchar(3)');
		$this->dbforge->add_field(self::III6.' varchar(3)');
		$this->dbforge->add_field(self::III6_5.' varchar(50)');
		$this->dbforge->add_field(self::III7.' varchar(3)');
		$this->dbforge->add_field(self::III7_5.' varchar(50)');
		$this->dbforge->add_field(self::III.' int not null default 0');
		$this->dbforge->add_field(self::IV.' int not null default 0');
		$this->dbforge->add_field('foreign key ('.self::StudentNumber.') references upbguidance_student (student_number) on update cascade on delete cascade');
		
		$this->dbforge->create_table(self::surveytablename,true);
		
	}
	
}

?>