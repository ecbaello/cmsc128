<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
}

class TableFlags{
	const FLOATING = 1;
}

class FieldFlags{
}

class MCTypes{
	const SINGLE = 1;
	const MULTIPLE = 2;
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
	const TableFlagFieldName = "table_flag";
	const FieldTitleFieldName = "field_title";
	const FieldNameFieldName = "field_name";
	const FieldInputTypeFieldName = "field_input_type";
	const FieldInputRequiredFieldName = "field_input_required";
	const FieldInputTipFieldName = "field_input_tip";
	const FieldInputOrderFieldName = "field_input_order";
	const FieldInputRegexFieldName = "field_input_regex";
	const FieldInputRegexErrMsgFieldName = "field_input_regex_error_msg";
	const FieldFlagFieldName = "field_flag";
	const FieldEssentialFieldName = "is_essential";
	
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
		//fieldData: name*,type*,title,constraints,input_type,input_required,input_regex,input_order,input_tip,input_regex,input_regex_error_msg,flag,essential
		
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
		if(!isset($fieldData['flag'])){
			$fieldData['flag']='';
		}
		if(!isset($fieldData['essential'])){
			$fieldData['essential']=0;
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
			$this->db->query('alter table '.$tableName.' add foreign key ('.$fieldData['name'].') references '.$FKReference['table_name'].'('.$FKReference['field_name'].') on update cascade on delete cascade');
		}
		
		//Register field
		
		$this->registerField($tableID,$fieldData);
	}
	
	public function addTable($tableName,$tableTitle,$essential = FALSE,$flag = null){
		
		//Check first if table exists
		if($this->db->table_exists($tableName)){
			log_message('error','Add Table: Table already exists.');
			return;
		}
		
		$modelID = $this->getModelID($this->ModelTitle);
		
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
		$this->registerTable($tableName,$tableTitle,$essential,$flag);
		
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
		$this->dbforge->add_field(self::TableTitleFieldName.' varchar(75) not null');
		$this->dbforge->add_field(self::TableNameFieldName.' varchar(75) not null unique');
		$this->dbforge->add_field(self::TableEssentialFieldName.' boolean not null default FALSE');
		$this->dbforge->add_field(self::TableFlagFieldName.' int');
		$this->dbforge->add_field('primary key ('.self::TableRegistryPKName.')');
		$this->dbforge->add_field('foreign key ('.self::ModelRegistryPKName.') references '.self::ModelRegistryTableName.'('.self::ModelRegistryPKName.')');
		
		$this->dbforge->create_table(self::TableRegistryTableName,true);
		
		//Create field registry table
		$this->dbforge->add_field(self::TableRegistryPKName.' int unsigned not null');
		$this->dbforge->add_field(self::FieldRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::FieldTitleFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::FieldNameFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::FieldFlagFieldName.' int');
		$this->dbforge->add_field(self::FieldInputTypeFieldName.' varchar(15) not null default "hidden"');
		$this->dbforge->add_field(self::FieldInputRegexFieldName.' varchar(30)');
		$this->dbforge->add_field(self::FieldInputRequiredFieldName.' boolean not null default FALSE');
		$this->dbforge->add_field(self::FieldInputOrderFieldName.' int not null default 0');
		$this->dbforge->add_field(self::FieldInputTipFieldName.' varchar(100)');
		$this->dbforge->add_field(self::FieldInputRegexErrMsgFieldName.' varchar(100)');
		$this->dbforge->add_field(self::FieldEssentialFieldName.' boolean not null default FALSE');
		$this->dbforge->add_field('primary key ('.self::FieldRegistryPKName.')');
		$this->dbforge->add_field('foreign key ('.self::TableRegistryPKName.') references '.self::TableRegistryTableName.'('.self::TableRegistryPKName.')');

		$this->dbforge->create_table(self::FieldRegistryTableName,true);
		
	}
	
	public function createModel(){
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
		
		$this->db->select(self::FieldNameFieldName.','.self::FieldTitleFieldName.','.self::FieldInputTypeFieldName.','.self::FieldInputRequiredFieldName.','.self::FieldInputRegexFieldName.','.self::FieldInputOrderFieldName.','.self::FieldInputRegexErrMsgFieldName.','.self::FieldInputTipFieldName.','.self::FieldFlagFieldName.','.self::FieldEssentialFieldName);
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
		
		$this->db->select(self::TableTitleFieldName.','.self::TableNameFieldName.','.self::TableFlagFieldName.','.self::TableEssentialFieldName);
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
	
	protected function removePlaceholderField($tableName){
		$this->dbforge->drop_column($tableName,self::PlaceholderField);
	}
	
	private function registerField($tableID,$fieldData = array()){
		
		// fieldData : title, name, input_type, input_required, regex
		
		$data = array(
			self::TableRegistryPKName => $tableID,
			self::FieldTitleFieldName => $fieldData['title'],
			self::FieldNameFieldName => $fieldData['name'],
			self::FieldInputTypeFieldName => $fieldData['input_type'],
			self::FieldInputRequiredFieldName => $fieldData['input_required'],
			self::FieldInputRegexFieldName => $fieldData['input_regex'],
			self::FieldInputOrderFieldName => $fieldData['input_order'],
			self::FieldFlagFieldName => $fieldData['flag'],
			self::FieldEssentialFieldName => $fieldData['essential'],
			self::FieldInputRegexErrMsgFieldName => $fieldData['input_regex_error_msg'],
			self::FieldInputTipFieldName => $fieldData['input_tip']
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
	
	protected function registerTable($tableName,$tableTitle,$tableEssential,$tableFlag){
		
		$modelID = $this->getModelID($this->ModelTitle);
		
		$data = array(
			self::ModelRegistryPKName => $modelID,
			self::TableTitleFieldName => $tableTitle,
			self::TableNameFieldName => $tableName,
			self::TableEssentialFieldName => $tableEssential,
			self::TableFlagFieldName => $tableFlag
		);
		
		$this->db->insert(self::TableRegistryTableName,$data);
		
	}
	
	public function update($tableName,$fields = array()){
		if(!$this->db->replace($tableName,$fields)){
			return $this->parseError($tableName,$this->db->error());
		}
	}
	
}



//FE = floating entity
//MC = Multiple Choice (singular,multiple)
class AdvancedInputsModel extends BaseModel{

	const FERegistryTableName = DB_PREFIX.'floating_entitiy_registry';
	const MCRegistryTableName = DB_PREFIX.'multiple_choice_registry';
	const ChoiceRegistryTableName = DB_PREFIX.'choice_registry';
	
	const BaseTableIDFieldName = 'base_table_id';
	
	const MCFieldIDFieldName = 'mc_field_id';
	const MCTypeFieldName = 'mc_type';
	const MCRegistryPKName = 'mc_id';
	
	const FEIDFieldName = 'fe_table_id';
	const FECardinalityFieldIDFieldName = 'fe_cardinality_field_id';
	const FEDefaultCardinalityFieldName = 'default_cardinality';
	
	const ChoiceValueFieldName = 'choice_value';
	const ChoiceCustomFieldName = 'is_custom';
	const ChoiceTitleFieldName = 'choice_title';
	
	public function addChoice($tableName, $fieldName, $choiceValue, $isCustom = false, $customTitle = ''){
		$MCID = $this->getMCID($tableName,$fieldName);
		$fields =array(
			self::MCRegistryPKName => $MCID,
			self::ChoiceValueFieldName => $choiceValue,
			self::ChoiceCustomFieldName => $isCustom,
			self::ChoiceTitleFieldName => $customTitle
		);
		return $this->insert(self::ChoiceRegistryTableName,$fields);
	}
	
	public function addFEField($tableName,$FEName,$FECardinalityFieldName,$defaultCardinality=1,$input_tip=''){
		$baseTableID = $this->getTableID($tableName);
		$FEID = $this->getTableID($FEName);
		$cardinalityFieldID = $this->getFieldID($tableName,$FECardinalityFieldName);
		
		$this->registerFE($baseTableID,$FEID,$cardinalityFieldID,$defaultCardinality);

		$this->addField($tableName,array(
			'name'=>$FEName,
			'title'=>$this->getTableTitle($FEName),
			'type'=>'int',
			'input_type'=>'FE',
			'input_tip'=>$input_tip
		));
	}
	
	public function addMCField($tableName,$choiceType,$fieldName,$fieldTitle,$fieldRequired=false,$fieldTip=''){
		$tableID = $this->getTableID($tableName);
		
		$this->addField($tableName, array(
			'name'=>$fieldName,
			'title'=>$fieldTitle,
			'type'=>'varchar(100)',
			'input_type'=>'MC',
			'input_required'=>$fieldRequired,
			'input_tip'=>$fieldTip
		));
		
		$fieldID = $this->getFieldID($tableName,$fieldName);
		$this->registerMC($tableID,$fieldID,$choiceType);
		
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
	
	//for association of tables and choices
	public function createMCRegistry(){
		if(!$this->db->table_exists(self::MCRegistryTableName)){
			
			$this->dbforge->add_field(self::MCRegistryPKName.' int unsigned not null auto_increment unique');
			$this->dbforge->add_field(self::BaseTableIDFieldName.' int unsigned not null');
			$this->dbforge->add_field(self::MCFieldIDFieldName.' int unsigned not null');
			$this->dbforge->add_field(self::MCTypeFieldName.' int unsigned not null default 1');
			
			$this->dbforge->add_field('primary key ('.self::MCRegistryPKName.')');
			$this->dbforge->add_field('foreign key ('.self::BaseTableIDFieldName.') references '.BaseModel::TableRegistryTableName.'('.BaseModel::TableRegistryPKName.')');
			$this->dbforge->add_field('foreign key ('.self::MCFieldIDFieldName.') references '.BaseModel::FieldRegistryTableName.'('.BaseModel::FieldRegistryPKName.')');
			
			$this->dbforge->create_table(self::MCRegistryTableName,TRUE);
			
		}
	}
	
	//for choices themselves
	public function createChoiceRegistry(){
		if(!$this->db->table_exists(self::ChoiceRegistryTableName)){
			
			$this->dbforge->add_field(self::MCRegistryPKName.' int unsigned not null');
			$this->dbforge->add_field(self::ChoiceValueFieldName.' varchar(100) not null');
			$this->dbforge->add_field(self::ChoiceCustomFieldName.' boolean not null default 0');
			$this->dbforge->add_field(self::ChoiceTitleFieldName.' varchar(100) not null default ""');
			
			$this->dbforge->add_field('foreign key ('.self::MCRegistryPKName.') references '.self::MCRegistryTableName.'('.self::MCRegistryPKName.')');
			
			$this->dbforge->create_table(self::ChoiceRegistryTableName,TRUE);
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
	
	public function getMCChoices($tableName,$fieldName){
		$MCID = $this->getMCID($tableName,$fieldName);
		$this->db->select(self::ChoiceValueFieldName.','.self::ChoiceCustomFieldName.','.self::ChoiceTitleFieldName);
		$this->db->where(self::MCRegistryPKName,$MCID);
		$result = $this->db->get(self::ChoiceRegistryTableName)->result_array();
		return $result;
	}
	
	public function getMCType($tableName,$fieldName){
		$MCID = $this->getMCID($tableName,$fieldName);
		$this->db->select(self::MCTypeFieldName);
		$this->db->where(self::MCRegistryPKName,$MCID);
		$result = $this->db->get(self::MCRegistryTableName)->result_array();
		return $result[0][self::MCTypeFieldName];
	}
	
	public function getMCID($tableName,$fieldName){
		
		$tableID = $this->getTableID($tableName);
		$fieldID = $this->getFieldID($tableName,$fieldName);
		
		$this->db->select(self::MCRegistryPKName);
		$this->db->where(self::BaseTableIDFieldName,$tableID);
		$this->db->where(self::MCFieldIDFieldName,$fieldID);
		$result = $this->db->get(self::MCRegistryTableName)->result_array();
		
		return $result[0][self::MCRegistryPKName];
		
	}
	
	protected function preModelCreation(){
		$this->createFERegistry();
		$this->createMCRegistry();
		$this->createChoiceRegistry();
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
	
	private function registerMC($tableID, $fieldID, $choiceType){
		
		switch($choiceType){
			case MCTypes::SINGLE:
				break;
			case MCTypes::MULTIPLE:
				break;
			default:
				log_message('error','Register MC: Invalid Choice Type');
				return;
				break;
		}
		
		$data = array(
			self::BaseTableIDFieldName => $tableID,
			self::MCFieldIDFieldName => $fieldID,
			self::MCTypeFieldName => $choiceType
		);
		
		$this->db->insert(self::MCRegistryTableName,$data);
	}
	
}

