<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
}

abstract class InputType{
	const HIDDEN = 0;
	const TEXT = 1;
}

class BaseModel extends CI_Controller{
	
	const ModelRegistryTableName = DB_PREFIX."dbmeta_model_registry";
	const TableRegistryTableName = DB_PREFIX."dbmeta_table_registry";
	const FieldRegistryTableName = DB_PREFIX."dbmeta_field_registry";
	
	const PlaceholderField = DB_PREFIX."placeholder_field";
	
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
	
	public $ModelTitle = "";
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		
		$this->createRegistry();
		$this->registerModel();
		$this->createModel();
	}
	
	public function addField($tableName,$fieldData = array(),$isPK = false, $isFK = false, $FKReference = array()){
		
		//FKReference: field_name, table_name
		//fieldData: name,type,title,constraints,input_type,input_required
		
		//Error Checking
		if(!isset($fieldData['name']) || !isset($fieldData['type'])){
			log_message('error','Add Field: Name not defined');
			return;
		}
		
		//Default values
		if(!isset($fieldData['input_type'])){
			$fieldData['input_type'] = InputType::HIDDEN;
		}
		if(!isset($fieldData['input_required'])){
			$fieldData['input_required'] = false;
		}
		if(!isset($fieldData['constraints'])){
			$fieldData['constraints'] = '';
		}

		$tableID = $this->getTableID($tableName);
		
		//Check first if column exists
		$this->db->where(self::FieldNameFieldName,$fieldData['name']);
		$this->db->where(self::TableRegistryPKName,$tableID);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		if(count($result)>0){
			log_message('error','Add Field: Field already exists');
			return;
		}
		
		//Add Column
		$this->dbforge->add_column($tableName, array(
			$fieldData['name'] => array('type'=>$fieldData['type'].' '.$fieldData['constraints'])
		));
		
		if($isPK){
			$this->db->query('alter table '.$tableName.' add primary key ('.$fieldData['name'].')');
		}
		
		if($isFK){
			$this->db->query('alter table '.$tableName.' add foreign key ('.$fieldData['name'].') references '.$FKReference['table_name'].'('.$FKReference['field_name'].')');
		}
		
		//Remove placeholder field
		/*$this->db->where(self::TableRegistryPKName,$tableID);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		if(count($result)== 0){
			$this->dbforge->drop_column($tableName,self::PlaceholderField);
		}*/
		
		//Register field
		//Check if title is set
		if(!isset($fieldData['title']))
			$fieldData['title']='';
		$this->registerField($tableID,$fieldData);
	}
	
	public function addTable($modelTitle,$tableName,$tableTitle){
		
		//Check first if table exists
		if($this->db->table_exists($tableName)){
			log_message('error','Add Table: Table already exists.');
			return;
		}
		
		$modelID = $this->getModelID($modelTitle);
		
		//Check for uniqueness of title
		$data = array(
			self::TableTitleFieldName => $tableTitle,
			self::ModelRegistryPKName => $modelID
		);
		$result = $this->db->get_where(self::TableRegistryTableName,$data)->result_array();
		if(count($result)>0){
			log_message('error','Add Table: Table title must be unique');
			return;
		}
		
		//Create table
		// but first add placeholder field
		$this->dbforge->add_field(self::PlaceholderField.' int');
		$this->dbforge->create_table($tableName);
		
		//Register table
		$this->registerTable($modelID,$tableName,$tableTitle);
		
	}
	
	public function createRegistry(){

		if($this->db->table_exists(self::ModelRegistryPKName) && $this->db->table_exists(self::TableRegistryTableName) && $this->db->table_exists(self::FieldRegistryTableName)) return;
	
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
		$this->dbforge->add_field(self::FieldInputTypeFieldName.' int not null default '.InputType::HIDDEN);
		$this->dbforge->add_field(self::FieldInputRequiredFieldName.' boolean not null');
		$this->dbforge->add_field('primary key ('.self::FieldRegistryPKName.')');
		$this->dbforge->add_field('foreign key ('.self::TableRegistryPKName.') references '.self::TableRegistryTableName.'('.self::TableRegistryPKName.')');

		$this->dbforge->create_table(self::FieldRegistryTableName,true);
		
	}
	
	public function getFields($tableName,$whereQuery=array()){
		$tableID = $this->getTableID($tableName);
		
		$this->db->select(self::FieldNameFieldName.','.self::FieldTitleFieldName.','.self::FieldInputTypeFieldName.','.self::FieldInputRequiredFieldName);
		$this->db->where(self::TableRegistryPKName,$tableID);
		foreach($whereQuery as $index=>$key){
			$this->db->where($index,$key);
		}
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		return $result;
	}
	
	public function getModelID($modelTitle){
		$this->db->select(self::ModelRegistryPKName);
		$this->db->from(self::ModelRegistryTableName);
		$this->db->where(array(
			self::ModelTitleFieldName => $modelTitle
		));
		$result = $this->db->get()->result_array();
		if(count($result)!=1) {
			log_message('error','Get Model ID: Multiple/No model(s) found.');
			return;
		}
		return($result[0][self::ModelRegistryPKName]);
	}
	
	public function getTableID($tableName){
		
		$this->db->select(self::TableRegistryPKName);
		$this->db->from(self::TableRegistryTableName);
		$this->db->where(array(
			self::TableNameFieldName => $tableName
		));
		$result = $this->db->get()->result_array();
		
		if(count($result)!=1) {
			log_message('error','Get Table ID: Multiple/No table(s) found.');
			return;
		}
		
		return($result[0][self::TableRegistryPKName]);
		
	}
	
	public function getTables($modelTitle,$whereQuery=array()){
		
		$modelID = $this->getModelID($modelTitle);
		
		$this->db->select(self::TableTitleFieldName.','.self::TableNameFieldName);
		$this->db->where(self::ModelRegistryPKName,$modelID);
		foreach($whereQuery as $index=>$key){
			$this->db->where($index,$key);
		}
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		return $result;
	}
	
	private function registerModel(){
		
		//Check if function is called from BaseModel
		if($this->ModelTitle == "") return; 
		
		//Check if model exists
		$this->db->select(self::ModelTitleFieldName);
		$this->db->from(self::ModelRegistryTableName);
		$result = $this->db->get()->result_array();
		
		if(count($result)>0){
			return;
		}
		
		//Register model
		$data = array(
			self::ModelTitleFieldName => $this->ModelTitle
		);
		$this->db->insert(self::ModelRegistryTableName,$data);
	}
	
	private function registerTable($modelID,$tableName,$tableTitle){
		
		$data = array(
			self::ModelRegistryPKName => $modelID,
			self::TableTitleFieldName => $tableTitle,
			self::TableNameFieldName => $tableName
		);
		
		$this->db->insert(self::TableRegistryTableName,$data);
		
	}
	
	private function registerField($tableID,$fieldData = array()){
		
		// fieldData : title, name, input_type, input_required
		
		$data = array(
			self::TableRegistryPKName => $tableID,
			self::FieldTitleFieldName => $fieldData['title'],
			self::FieldNameFieldName => $fieldData['name'],
			self::FieldInputTypeFieldName => $fieldData['input_type'],
			self::FieldInputRequiredFieldName => $fieldData['input_required']
		);
		
		$this->db->insert(self::FieldRegistryTableName,$data);
	}
}

