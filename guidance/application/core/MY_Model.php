<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
}

class BaseModel extends CI_Controller{
	
	const ModelRegistryTableName = "upbguidance_model_registry";
	const ModelFieldsTableName = "upbguidance_model_fields_association";
	const ModelRegistryPKName = "model_id";
	const ModelFieldsPKName = "field_id";
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		
		$this->createModelRegistry();
		$this->createTable();
	}
	
	public function createModelRegistry(){
		
		$this->dbforge->add_field(self::ModelRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field('model_title varchar(50) not null');
		$this->dbforge->add_field('model_table varchar(50) not null unique');
		$this->dbforge->add_field('primary key ('.self::ModelRegistryPKName.')');

		$this->dbforge->create_table(self::ModelRegistryTableName,true);
		
		$this->dbforge->add_field(self::ModelRegistryPKName.' int unsigned not null');
		$this->dbforge->add_field(self::ModelFieldsPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field('field_title varchar(50) not null');
		$this->dbforge->add_field('field_input_type varchar(50) not null default "TEXT"');
		$this->dbforge->add_field('field_input_required boolean not null');
		$this->dbforge->add_field('primary key ('.self::ModelFieldsPKName.')');
		$this->dbforge->add_field('foreign key ('.self::ModelRegistryPKName.') references '.self::ModelRegistryTableName.'('.self::ModelRegistryPKName.')');

		$this->dbforge->create_table(self::ModelFieldsTableName,true);
		
	}
	
	public function registerField($tableName,$fieldName){
		$this->db->insert();
	}
}