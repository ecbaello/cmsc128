<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
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
	const TableEssentialFieldName = "is_essential";
	const TableFloatingFieldName = "is_visible";
	const FieldTitleFieldName = "field_title";
	const FieldNameFieldName = "field_name";
	const FieldInputTypeFieldName = "field_input_type";
	const FieldInputRequiredFieldName = "field_input_required";
	const FieldInputTipFieldName = "field_input_tip";
	const FieldInputOrderFieldName = "field_input_order";
	const FieldInputRegexFieldName = "field_input_regex";
	const FieldInputRegexErrMsgFieldName = "field_input_regex_error_msg";
	
	public $ModelTitle = "";
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		
		$this->createRegistry();
		$this->registerModel();
		$this->preModelCreation();
		$this->createModel();
	}
	
	public function addField($tableName,$fieldData = array(),$isPK = false, $isFK = false, $FKReference = array()){
		
		//FKReference: field_name*, table_name*
		//fieldData: name*,type*,title,constraints,input_type,input_required,input_regex,input_order,input_tip,input_regex,input_regex_error_msg
		
		//Error Checking
		if(!isset($fieldData['name']) || !isset($fieldData['type'])){
			log_message('error','Add Field: Name or type not defined');
			return;
		}
		
		//Default values
		if(!isset($fieldData['input_type'])){
			$fieldData['input_type'] = 'hidden';
		}
		if(!isset($fieldData['input_required'])){
			$fieldData['input_required'] = false;
		}
		if(!isset($fieldData['input_regex'])){
			$fieldData['input_regex'] = NULL;
		}
		if(!isset($fieldData['constraints'])){
			$fieldData['constraints'] = '';
		}
		if(!isset($fieldData['input_order'])){
			$fieldData['input_order'] = '';
		}
		if(!isset($fieldData['input_tip'])){
			$fieldData['input_tip'] = '';
		}
		if(!isset($fieldData['input_regex_error_msg'])){
			$fieldData['input_regex_error_msg'] = '';
		}
		if(!isset($fieldData['title'])){
			$fieldData['title']='';
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
		
		//Register field
		
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

		if($this->db->table_exists(self::ModelRegistryTableName) && $this->db->table_exists(self::TableRegistryTableName) && $this->db->table_exists(self::FieldRegistryTableName)) return;
	
		//Create model registry table
		$this->dbforge->add_field(self::ModelRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::ModelTitleFieldName.' varchar(50) not null unique');
		$this->dbforge->add_field('primary key ('.self::ModelRegistryPKName.')');

		$this->dbforge->create_table(self::ModelRegistryTableName,true);
		
		//Create table registry table
		$this->dbforge->add_field(self::ModelRegistryPKName.' int unsigned');
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
		$this->dbforge->add_field(self::FieldInputTypeFieldName.' varchar(15) not null default "hidden"');
		$this->dbforge->add_field(self::FieldInputRegexFieldName.' varchar(15)');
		$this->dbforge->add_field(self::FieldInputRequiredFieldName.' boolean not null default FALSE');
		$this->dbforge->add_field('primary key ('.self::FieldRegistryPKName.')');
		$this->dbforge->add_field('foreign key ('.self::TableRegistryPKName.') references '.self::TableRegistryTableName.'('.self::TableRegistryPKName.')');

		$this->dbforge->create_table(self::FieldRegistryTableName,true);
		
	}
	
	public function getFieldID ($tableName,$fieldName){
		$tableID = $this->getTableID($tableName);
		
		$this->db->select(self::FieldRegistryPKName);
		$this->db->where(self::TableRegistryPKName,$tableID);
		$this->db->where(self::FieldNameFieldName,$fieldName);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		return $result[0][self::FieldRegistryPKName];
	}
	
	public function getFieldTitle($tableName,$fieldName){
		$tableID = $this->getTableID($tableName);
		
		$this->db->select(self::FieldTitleFieldName);
		$this->db->where(self::TableRegistryPKName,$tableID);
		$this->db->where(self::FieldNameFieldName,$fieldName);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		return $result[0][self::FieldTitleFieldName];
	}
	
	public function getFields($tableName,$whereQuery=array()){
		$tableID = $this->getTableID($tableName);
		
		$this->db->select(self::FieldNameFieldName.','.self::FieldTitleFieldName.','.self::FieldInputTypeFieldName.','.self::FieldInputRequiredFieldName.','.self::FieldInputRegexFieldName);
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
			//log_message('error','Get Model ID: Multiple/No model(s) found.');
			return;
		}
		return($result[0][self::ModelRegistryPKName]);
	}
	
	public function getTableTitle($tableName){
		$this->db->select(self::TableTitleFieldName);
		$this->db->where(self::TableNameFieldName,$tableName);
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		
		return $result[0][self::TableTitleFieldName];
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
	
	public function insert($tableName,$fields = array()){
		//fields: field_name => field_data
		
		if(!$this->db->insert($tableName,$fields)){
			return $this->parseError($tableName,$this->db->error());
		}
		
	}
	
	private function parseError($tableName,$error=array()){
		$msg='';
		switch($error['code']){
			case '1062':
				preg_match('/for key \'(.+)\'/',$error['message'],$matches);
				$msg = $this->getTableTitle($tableName).'. Duplicate entry for '.$this->getFieldTitle($tableName,$matches[1]);
				break;
			default:
				$msg = $error['message'];
		}
		return $msg;
	}
	
	protected function preModelCreation(){
		return;
	}
	
	private function registerField($tableID,$fieldData = array()){
		
		// fieldData : title, name, input_type, input_required, regex
		
		$data = array(
			self::TableRegistryPKName => $tableID,
			self::FieldTitleFieldName => $fieldData['title'],
			self::FieldNameFieldName => $fieldData['name'],
			self::FieldInputTypeFieldName => $fieldData['input_type'],
			self::FieldInputRequiredFieldName => $fieldData['input_required'],
			self::FieldInputRegexFieldName => $fieldData['input_regex']
		);
		
		$this->db->insert(self::FieldRegistryTableName,$data);
	}
	
	private function registerModel(){
		
		//Check if function is called from BaseModel
		if($this->ModelTitle == "") return; 
		
		
		
		//Check if model exists
		$this->db->select(self::ModelTitleFieldName);
		$this->db->where(self::ModelTitleFieldName,$this->ModelTitle);
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
	
	protected function registerTable($modelID,$tableName,$tableTitle){
		
		$data = array(
			self::ModelRegistryPKName => $modelID,
			self::TableTitleFieldName => $tableTitle,
			self::TableNameFieldName => $tableName
		);
		
		$this->db->insert(self::TableRegistryTableName,$data);
		
	}
	
}

class FloatingEntityModel extends BaseModel{

	const FERegistryTableName = DB_PREFIX.'floating_entitiy_registry';
	
	const BaseTableIDFieldName = 'base_table_id';
	const FEIDFieldName = 'fe_table_id';
	const FECardinalityFieldIDFieldName = 'fe_cardinality_field_id';
	
	const FERegistryPKName = 'fe_id';
	const FEDefaultCardinalityFieldName = 'default_cardinality';
	
	public function addFE($tableName,$tableTitle){
		
		//Check first if table exists
		if($this->db->table_exists($tableName)){
			log_message('error','Add FE: Table already exists.');
			return;
		}
		
		//Create table
		$this->dbforge->add_field(self::PlaceholderField.' int');
		$this->dbforge->create_table($tableName);
		
		//Register table
		$this->registerTable(NULL,$tableName,$tableTitle);
		
	}
	
	public function addFEField($tableName,$FEName,$FECardinalityFieldName,$defaultCardinality=1){
		$baseTableID = $this->getTableID($tableName);
		$FEID = $this->getTableID($FEName);
		$cardinalityFieldID = $this->getFieldID($tableName,$FECardinalityFieldName);
		
		$this->registerFE($baseTableID,$FEID,$cardinalityFieldID,$defaultCardinality);

		$this->addField($tableName,array(
			'name'=>$FEName,
			'title'=>$this->getTableTitle($FEName),
			'type'=>'int',
			'input_type'=>'FE'
		));
	}
	
	public function createFERegistry(){
		if(!$this->db->table_exists(self::FERegistryTableName)){
			
			$this->dbforge->add_field(self::BaseTableIDFieldName.' int unsigned not null');
			$this->dbforge->add_field(self::FEIDFieldName.' int unsigned not null');
			$this->dbforge->add_field(self::FECardinalityFieldIDFieldName.' int unsigned not null');
			$this->dbforge->add_field(self::FEDefaultCardinalityFieldName.' int unsigned not null');
			
			$this->dbforge->add_field('foreign key ('.self::BaseTableIDFieldName.') references '.BaseModel::TableRegistryTableName.'('.BaseModel::TableRegistryPKName.')');
			
			$this->dbforge->add_field('foreign key ('.self::FEIDFieldName.') references '.BaseModel::TableRegistryTableName.'('.BaseModel::TableRegistryPKName.')');
			
			$this->dbforge->add_field('foreign key ('.self::FECardinalityFieldIDFieldName.') references '.BaseModel::FieldRegistryTableName.'('.BaseModel::FieldRegistryPKName.')');
			
			$this->dbforge->create_table(self::FERegistryTableName,TRUE);
			
		}
	}
	
	public function getFECardinalityFieldName($baseTableName,$FETableName){
		$baseTableID = $this->getTableID($baseTableName);
		$FEID = $this->getTableID($FETableName);
		
		$this->db->select(self::FECardinalityFieldIDFieldName);
		$this->db->where(self::BaseTableIDFieldName,$baseTableID);
		$this->db->where(self::FEIDFieldName,$FEID);
		$FEFieldID = $this->db->get(self::FERegistryTableName)->result_array()[0][self::FECardinalityFieldIDFieldName];
		
		$this->db->select(BaseModel::FieldNameFieldName);
		$this->db->where(BaseModel::FieldRegistryPKName,$FEFieldID);
		$result = $this->db->get(BaseModel::FieldRegistryTableName)->result_array();
		
		return $result[0][BaseModel::FieldNameFieldName];
	}
	
	public function getFEDefaultCardinality($baseTableName,$FETableName){
		$baseTableID = $this->getTableID($baseTableName);
		$FEID = $this->getTableID($FETableName);
		
		$this->db->select(self::FEDefaultCardinalityFieldName);
		$this->db->where(self::BaseTableIDFieldName,$baseTableID);
		$this->db->where(self::FEIDFieldName,$FEID);
		$result = $this->db->get(self::FERegistryTableName)->result_array();
		return $result[0][self::FEDefaultCardinalityFieldName];
	}
	
	protected function preModelCreation(){
		$this->createFERegistry();
	}
	
	private function registerFE($baseTableID,$FEID,$FECardinalityFieldID,$defaultCardinality){
		
		$data = array(
			self::BaseTableIDFieldName => $baseTableID,
			self::FEIDFieldName => $FEID,
			self::FECardinalityFieldIDFieldName => $FECardinalityFieldID,
			self::FEDefaultCardinalityFieldName => $defaultCardinality
		);
		
		$this->db->insert(self::FERegistryTableName,$data);
	}
	
}

