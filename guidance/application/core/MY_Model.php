<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	
	public function __construct(){
		parent::__construct();
	}
	
}

class Flags{
	const DEF = 0;
	const DELETED = 1;
	const FLOATING = 2;
}

class MCTypes{
	const SINGLE = 1;
	const MULTIPLE = 2;
}

class StudentInfoBaseModel extends CI_Model{
	
	const TableRegistryTableName = DB_PREFIX."studentform_table_registry";
	const FieldRegistryTableName = DB_PREFIX."studentform_field_registry";
	const BaseTableTableName = DB_PREFIX.'student'; 
	
	const TableRegistryPKName = "table_id";
	const FieldRegistryPKName = "field_id";
	const BaseTablePKName = "student_id";
	
	const StudentNumberFieldName = "student_number";
	
	const TableTitleFieldName = "table_title";
	const TableNameFieldName = "table_name";
	
	const FieldTitleFieldName = "field_title";
	const FieldNameFieldName = "field_name";
	const FieldInputTypeFieldName = "field_input_type";
	const FieldInputRequiredFieldName = "field_input_required";
	const FieldInputTipFieldName = "field_input_tip";
	const FieldInputOrderFieldName = "field_input_order";
	const FieldInputRegexFieldName = "field_input_regex";
	const FieldInputRegexErrMsgFieldName = "field_input_regex_error_msg";
	
	const EssentialFieldName = 'is_essential';
	const FlagFieldName = "flag";
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		
		$this->createRegistry();
		$this->createBaseTable();
	}
	
	public function addField($tableName,$fieldData = array()){
		
		//fieldData: name*,type*,title,constraints,input_type,input_required,input_regex,input_order,input_tip,input_regex_error_msg,flag,essential
		
		//Error Checking
		if(!isset($fieldData['name']) || !isset($fieldData['type'])){
			log_message('error','Add Field: Name or type not defined');
			return 'Name or type not defined.';
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
			$fieldData['flag']=Flags::DEF;
		}
		if(!isset($fieldData['essential'])){
			$fieldData['essential']=false;
		}
		
		$tableID = $this->getTableID($tableName);
		
		//Check first if column exists
		$this->db->where(self::FieldNameFieldName,$fieldData['name']);
		$this->db->where(self::TableRegistryPKName,$tableID);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		if(count($result)>0 || $fieldData['name']==self::BaseTablePKName){
			//log_message('error','Add Field: Field already exists');
			return 'Field already exists';
		}

		//Add Column
		$this->dbforge->add_column($tableName, array(
			$fieldData['name'] => array('type'=>$fieldData['type'].' '.$fieldData['constraints'])
		));
		
		//Register field
		$this->registerField($tableID,$fieldData);
		return null;
	}
	
	public function addTable($tableName,$tableTitle,$flag = Flags::DEF,$essential = FALSE){
		
		//Check first if table exists
		if($this->db->table_exists($tableName)){
			log_message('error','Add Table: Table already exists.');
			return 'Table already exists.';
		}
		
		//Check for uniqueness of title
		$data = array(
			self::TableTitleFieldName => $tableTitle,
		);
		$result = $this->db->get_where(self::TableRegistryTableName,$data)->result_array();
		if(count($result)>0){
			log_message('error','Add Table: Table title must be unique');
			return 'Table title must be unique.';
		}
		
		//Create table
		$this->dbforge->add_field(self::BaseTablePKName.' int unsigned not null');
		$this->dbforge->add_field(self::FlagFieldName.' int unsigned not null default '.Flags::DEF);
		$this->dbforge->add_field('foreign key ('.self::BaseTablePKName.') references '.self::BaseTableTableName.'('.self::BaseTablePKName.') on delete cascade on update cascade');
		$this->dbforge->create_table($tableName);
		
		//Register table
		$this->registerTable($tableName,$tableTitle,$flag,$essential);
		return null;
		
	}
	
	private function createBaseTable(){
		if(!$this->db->table_exists(self::BaseTableTableName)){
			$this->dbforge->add_field(self::BaseTablePKName.' int unsigned not null auto_increment unique');
			$this->dbforge->add_field(self::FlagFieldName.' int unsigned not null default '.Flags::DEF);
			$this->dbforge->add_field('primary key ('.self::BaseTablePKName.')');
			$this->dbforge->create_table(self::BaseTableTableName,true);
			$this->registerTable(self::BaseTableTableName,'Background Information',Flags::DEF,TRUE);
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>self::StudentNumberFieldName,
				'title'=>'Student Number',
				'type'=>'varchar(11)',
				'constraints'=>'not null unique',
				'input_type'=>'text',
				'input_required'=>TRUE,
				'input_regex'=>'^\d{4}-\d{5}$',
				'input_regex_error_msg'=>'Must follow the format xxxx-xxxxx',
				'input_tip'=>'Must be unique',
				'essential'=>TRUE
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'last_name',
				'title'=>'Last Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE,
				'essential'=>TRUE
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'first_name',
				'title'=>'First Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE,
				'essential'=>TRUE
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'middle_name',
				'title'=>'Middle Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'essential'=>TRUE
			));
		}
	}
	
	private function createRegistry(){

		if($this->db->table_exists(self::TableRegistryTableName) && $this->db->table_exists(self::FieldRegistryTableName)) return;
		
		//Create table registry table
		$this->dbforge->add_field(self::TableRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::TableTitleFieldName.' varchar(75) not null');
		$this->dbforge->add_field(self::TableNameFieldName.' varchar(75) not null unique');
		$this->dbforge->add_field(self::EssentialFieldName.' boolean not null default FALSE');
		$this->dbforge->add_field(self::FlagFieldName.' int unsigned not null default '.Flags::DEF);
		$this->dbforge->add_field('primary key ('.self::TableRegistryPKName.')');
		
		$this->dbforge->create_table(self::TableRegistryTableName,true);
		
		//Create field registry table
		$this->dbforge->add_field(self::TableRegistryPKName.' int unsigned not null');
		$this->dbforge->add_field(self::FieldRegistryPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::FieldTitleFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::FieldNameFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::FlagFieldName.' int unsigned not null default '.Flags::DEF);
		$this->dbforge->add_field(self::FieldInputTypeFieldName.' varchar(15) not null default "hidden"');
		$this->dbforge->add_field(self::FieldInputRegexFieldName.' varchar(30)');
		$this->dbforge->add_field(self::FieldInputRequiredFieldName.' boolean not null default FALSE');
		$this->dbforge->add_field(self::FieldInputOrderFieldName.' int not null default 0');
		$this->dbforge->add_field(self::FieldInputTipFieldName.' varchar(100)');
		$this->dbforge->add_field(self::FieldInputRegexErrMsgFieldName.' varchar(100)');
		$this->dbforge->add_field(self::EssentialFieldName.' boolean not null default FALSE');
		$this->dbforge->add_field('primary key ('.self::FieldRegistryPKName.')');
		$this->dbforge->add_field('foreign key ('.self::TableRegistryPKName.') references '.self::TableRegistryTableName.'('.self::TableRegistryPKName.') on delete cascade on update cascade');

		$this->dbforge->create_table(self::FieldRegistryTableName,true);
		
	}
	
	protected function getFieldID ($tableName,$fieldName){
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
		
		//$this->db->select(self::FieldNameFieldName.','.self::FieldTitleFieldName.','.self::FieldInputTypeFieldName.','.self::FieldInputRequiredFieldName.','.self::FieldInputRegexFieldName.','.self::FieldInputOrderFieldName.','.self::FieldInputRegexErrMsgFieldName.','.self::FieldInputTipFieldName.','.self::FlagFieldName.','.self::EssentialFieldName);
		$this->db->where(self::TableRegistryPKName,$tableID);
		foreach($whereQuery as $index=>$key){
			$this->db->where($index,$key);
		}
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		return $result;
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
	
	public function getTables($whereQuery=array()){
		
		//$this->db->select(self::TableTitleFieldName.','.self::TableNameFieldName.','.self::FlagFieldName.','.self::EssentialFieldName);
		foreach($whereQuery as $index=>$key){
			$this->db->where($index,$key);
		}
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		return $result;
	}
	
	
	
	private function registerField($tableID,$fieldData = array()){
		
		// fieldData : title, name, input_type, input_required, ...
		
		$data = array(
			self::TableRegistryPKName => $tableID,
			self::FieldTitleFieldName => $fieldData['title'],
			self::FieldNameFieldName => $fieldData['name'],
			self::FieldInputTypeFieldName => $fieldData['input_type'],
			self::FieldInputRequiredFieldName => $fieldData['input_required'],
			self::FieldInputRegexFieldName => $fieldData['input_regex'],
			self::FieldInputOrderFieldName => $fieldData['input_order'],
			self::FlagFieldName => $fieldData['flag'],
			self::EssentialFieldName => $fieldData['essential'],
			self::FieldInputRegexErrMsgFieldName => $fieldData['input_regex_error_msg'],
			self::FieldInputTipFieldName => $fieldData['input_tip']
		);
		
		$this->db->insert(self::FieldRegistryTableName,$data);
	}
	
	protected function registerTable($tableName,$tableTitle,$tableFlag,$tableEssential){

		$data = array(
			self::TableTitleFieldName => $tableTitle,
			self::TableNameFieldName => $tableName,
			self::EssentialFieldName => $tableEssential,
			self::FlagFieldName => $tableFlag
		);
		
		$this->db->insert(self::TableRegistryTableName,$data);
		
	}
	
	
	
}



//FE = floating entity
//MC = Multiple Choice (singular,multiple)
class AdvancedInputsModel extends StudentInfoBaseModel{

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
	
	public function __construct(){
		parent::__construct();
		$this->createFERegistry();
		$this->createMCRegistry();
		$this->createChoiceRegistry();
	}
	
	public function addChoice($tableName, $fieldName, $choiceValue, $isCustom = false, $customTitle = ''){
		$MCID = $this->getMCID($tableName,$fieldName);
		$fields =array(
			self::MCRegistryPKName => $MCID,
			self::ChoiceValueFieldName => $choiceValue,
			self::ChoiceCustomFieldName => $isCustom,
			self::ChoiceTitleFieldName => $customTitle
		);
		return $this->db->insert(self::ChoiceRegistryTableName,$fields);
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
			'type'=>'varchar(1200)',
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
			
			$this->dbforge->add_field('foreign key ('.self::BaseTableIDFieldName.') references '.StudentInfoBaseModel::TableRegistryTableName.'('.StudentInfoBaseModel::TableRegistryPKName.')');
			$this->dbforge->add_field('foreign key ('.self::FEIDFieldName.') references '.StudentInfoBaseModel::TableRegistryTableName.'('.StudentInfoBaseModel::TableRegistryPKName.')');
			$this->dbforge->add_field('foreign key ('.self::FECardinalityFieldIDFieldName.') references '.StudentInfoBaseModel::FieldRegistryTableName.'('.StudentInfoBaseModel::FieldRegistryPKName.')');
			
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
			$this->dbforge->add_field('foreign key ('.self::BaseTableIDFieldName.') references '.StudentInfoBaseModel::TableRegistryTableName.'('.StudentInfoBaseModel::TableRegistryPKName.')');
			$this->dbforge->add_field('foreign key ('.self::MCFieldIDFieldName.') references '.StudentInfoBaseModel::FieldRegistryTableName.'('.StudentInfoBaseModel::FieldRegistryPKName.')');
			
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
		
		$this->db->select(StudentInfoBaseModel::FieldNameFieldName);
		$this->db->where(StudentInfoBaseModel::FieldRegistryPKName,$FEFieldID);
		$result = $this->db->get(StudentInfoBaseModel::FieldRegistryTableName)->result_array();
		
		return $result[0][StudentInfoBaseModel::FieldNameFieldName];
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

