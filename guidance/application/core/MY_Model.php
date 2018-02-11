<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
}

class BaseModel extends CI_Controller{
	
	const ModelRegistryTableName = "upbguidance_dbmeta_model_registry";
	const TableRegistryTableName = "upbguidance_dbmeta_table_registry";
	const FieldRegistryTableName = "upbguidance_dbmeta_field_registry";
	
	const ModelRegistryPKName = "model_id";
	const TableRegistryPKName = "table_id";
	const FieldRegistryPKName = "field_id";
	
	const ModelTitleFieldName = "model_title";
	const TableTitleFieldName = "table_title";
	const TableNameFieldName = "table_name";
	const FieldTitleFieldName = "field_title";
	const FieldNameFieldName = "field_name";
	const FieldInputTypeFieldName = "field_input_type";
	const FieldInputRequiredFieldName = "field_input_required";
	
	public $ModelName = "";
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		
		$this->createRegistry();
		$this->registerModel();
		$this->createModel();
	}
	
	public function createRegistry(){

		//Create model registry table
		$this->dbforge->add_field(self::ModelRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::ModelTitleFieldName.' varchar(50) not null unique');
		$this->dbforge->add_field('primary key ('.self::ModelRegistryPKName.')');

		$this->dbforge->create_table(self::ModelRegistryTableName,true);
		
		//Create table registry table
		$this->dbforge->add_field(self::ModelRegistryPKName.' int unsigned not null');
		$this->dbforge->add_field(self::TableRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::TableTitleFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::TableNameFieldName.' varchar(50) not null unique');
		$this->dbforge->add_field('primary key ('.self::TableRegistryPKName.')');
		$this->dbforge->add_field('foreign key ('.self::ModelRegistryPKName.') references '.self::ModelRegistryTableName.'('.self::ModelRegistryPKName.')');
		
		$this->dbforge->create_table(self::TableRegistryTableName,true);
		
		//Create field registry table
		$this->dbforge->add_field(self::TableRegistryPKName.' int unsigned not null');
		$this->dbforge->add_field(self::FieldRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::FieldTitleFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::FieldNameFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::FieldInputTypeFieldName.' varchar(50) not null default "TEXT"');
		$this->dbforge->add_field(self::FieldInputRequiredFieldName.' boolean not null');
		$this->dbforge->add_field('primary key ('.self::FieldRegistryPKName.')');
		$this->dbforge->add_field('foreign key ('.self::TableRegistryPKName.') references '.self::TableRegistryTableName.'('.self::TableRegistryPKName.')');

		$this->dbforge->create_table(self::FieldRegistryTableName,true);
		
	}
	
	private function registerModel(){
		
		//Check if function is called from BaseModel
		if($this->ModelName == "") return; 
		
		//Check if model exists
		$this->db->select(self::ModelTitleFieldName);
		$this->db->from(self::ModelRegistryTableName);
		$result = $this->db->get()->result_array();
		
		if(count($result)>0){
			return;
		}
		
		//Register model
		$data = array(
			self::ModelTitleFieldName => $this->ModelName
		);
		$this->db->insert(self::ModelRegistryTableName,$data);
	}
	
	private function registerTable($modelTitle,$tableTitle,$tableName){
			
		//Getting model ID
		$this->db->select(self::ModelRegistryPKName);
		$this->db->from(self::ModelRegistryTableName);
		$this->db->where(array(
			self::ModelTitleFieldName => $modelTitle
		));
		$result = $this->db->get()->result_array();
		
		if(count($result)!=1) {
			log_message('error','Register Table: Multiple/No model(s) found.');
			return;
		}
		
		$modelID = $result[0][self::ModelRegistryPKName];
		
		//Check if table exists
		$data = array(
			self::ModelRegistryPKName => $modelID,
			self::TableTitleFieldName => $tableTitle,
			self::TableNameFieldName => $tableName
		);
		
		$this->db->where($data);
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		
		if(count($result) > 0){
			log_message('error','Register Table: Table already registered.');
			return;
		}
		
		//Register table
		$this->db->insert(self::TableRegistryTableName,$data);
		
	}
	
	private function registerField($tableName,$fieldTitle,$fieldName,$fieldInputType,$fieldInputRequired=false){
		
		//Check if field exists
		$data = array(
			self::TableNameFieldName => $tableName,
			self::FieldNameFieldName => $fieldName
		);
		
		$this->db->where($data);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		if(count($result)>0){
			log_message('error','Register Field: Field already registered.');
			return;
		}
		
		//Getting table ID
		$this->db->select(self::TableRegistryPKName);
		$this->db->from(self::TableRegistryTableName);
		$this->db->where(array(
			self::TableNameFieldName => $tableName
		));
		$result = $this->db->get()->result_array();
		
		if(count($result)!=1) {
			log_message('error','Register Field: Multiple/No table(s) found.');
			return;
		}
		
		$tableID = $result[0][self::TableRegistryPKName];
		
		//Register field
		$data = array(
			self::TableRegistryPKName => $tableID,
			self::FieldTitleFieldName => $fieldTitle,
			self::FieldNameFieldName => $fieldName,
			self::FieldInputTypeFieldName => $fieldInputType,
			self::FieldInputRequiredFieldName => $fieldInputRequired
		);
		
		$this->db->insert(self::FieldRegistryTableName,$data);
	}
}