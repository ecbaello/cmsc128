<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_Information extends BaseModel{
	 
	const BaseTableName = DB_PREFIX.'student'; 
	const FamilyDataTableName = DB_PREFIX.'student_family';
	const EducationalBGTableName = DB_PREFIX.'student_education';
	const FinancialInfoTableName = DB_PREFIX.'student_finance';
	const VocationalPlansTableName = DB_PREFIX.'student_vocation';
	const LeisureInfoTableName = DB_PREFIX.'student_leisure';
	
	const BasePKName = "student_id";
	
	public $ModelTitle = "Student Information";
	
	public function __construct(){
		parent::__construct();
	}
	
	public function createModel(){
		
		/*$this->dbforge->add_field(self::BasePKName.' int not null auto_increment unique');
		$this->dbforge->add_field('student_number varchar(20) not null');
		$this->dbforge->add_field('course varchar(30)');
		$this->dbforge->add_field('block varchar(30)');
		$this->dbforge->add_field('last_name varchar(30) not null');
		$this->dbforge->add_field('first_name varchar(30) not null');
		$this->dbforge->add_field('middle_name varchar(30)');
		$this->dbforge->add_field('enrollment_status varchar(20)');
		$this->dbforge->add_field('nickname varchar(30)');
		$this->dbforge->add_field('sex varchar(20) not null');
		$this->dbforge->add_field('age int');
		$this->dbforge->add_field('date_of_birth date not null');
		$this->dbforge->add_field('place_of_birth varchar(50)');
		$this->dbforge->add_field('nationality varchar(20)');
		$this->dbforge->add_field('citizenship varchar(20)');
		$this->dbforge->add_field('religion varchar(20)');
		$this->dbforge->add_field('address_upb varchar(50)');
		$this->dbforge->add_field('telno_upb varchar(20)');
		$this->dbforge->add_field('mobile_number int');
		$this->dbforge->add_field('email varchar(30)');
		$this->dbforge->add_field('address_perma varchar(30)');
		$this->dbforge->add_field('telno_perma varchar(30)');
		*/
		if(!$this->db->table_exists(self::BaseTableName)){
			$this->addTable($this->ModelTitle,self::BaseTableName,'Background Information');
			
			$this->addField(self::BaseTableName,array(
				'name'=>'student_id',
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
			),true);
			
			$this->addField(self::BaseTableName,array(
				'name'=>'student_number',
				'title'=>'Student Number',
				'type'=>'varchar(20)',
				'constraints'=>'not null',
				'input_type'=>InputType::TEXT
			));
		}
		if(!$this->db->table_exists(self::FinancialInfoTableName)){
			$this->addTable($this->ModelTitle,self::FinancialInfoTableName,'Financial Information');
			
			$this->addField(self::FinancialInfoTableName,array(
				'name'=>'student_id',
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
			),false,true,array(
				'field_name'=>'student_id',
				'table_name'=>self::BaseTableName
			));
			
			$this->addField(self::FinancialInfoTableName,array(
				'name'=>'family_annual_income',
				'title'=>'Family\'s Annual Income',
				'type'=>'varchar(20)',
				'constraints'=>'not null',
				'input_type'=>InputType::TEXT
			));
		}
		
	}
	
}