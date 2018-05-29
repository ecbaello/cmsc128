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
	const LastNameFieldName = "last_name";
	const FirstNameFieldName = "first_name";
	const MiddleNameFieldName = "middle_name";
	const SexFieldName = "sex";
	
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
	}
	
	public function addField($tableName,$fieldData = array()){
		
		//fieldData: name*,type*,title,constraints,input_type,input_required,input_regex,input_order,input_tip,input_regex_error_msg,flag,essential
		
		//Error Checking
		if(!isset($fieldData['name'])){
			log_message('error','Add Field: Name not defined');
			return 'Name not defined.';
		}
		
		if(!isset($fieldData['type'])){
			
			if(isset($fieldData['input_type'])){
				switch($fieldData['input_type']){
					case 'text':
						$fieldData['type'] = 'varchar(300)';
						break;
					case 'number':
						$fieldData['type'] = 'int';
						break;
					case 'date':
						$fieldData['type'] = 'date';
						break;
					default:
						return 'Invalid input type.';
				}
			}else{
				return 'Type not defined.';
			}
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
		if($tableID==null){
			return 'Table does not exist.';
		}
		
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
		$this->dbforge->add_field(self::FieldTitleFieldName.' varchar(100) not null');
		$this->dbforge->add_field(self::FieldNameFieldName.' varchar(100) not null');
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
	
	public function deleteField($fieldID,$permanent=false){
		
		$this->db->select(self::TableRegistryPKName.','.self::FieldNameFieldName.','.self::EssentialFieldName);
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$res = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		if(count($res)<1){
			return 'No such field found.';
		}
		
		if($res[0][self::EssentialFieldName]){
			return 'Field is essential.';
		}
		
		$this->db->select(self::TableNameFieldName);
		$this->db->where(self::TableRegistryPKName,$res[0][self::TableRegistryPKName]);
		$tableName = $this->db->get(self::TableRegistryTableName)->result_array()[0][self::TableNameFieldName];
		
		if(!$permanent){
			$this->db->where(self::FieldRegistryPKName,$fieldID);
			$this->db->set(self::FlagFieldName,self::FlagFieldName.'|'.Flags::DELETED,false);
			$this->db->update(self::FieldRegistryTableName);
		}else{
			if(!$this->dbforge->drop_column($tableName,$res[0][self::FieldNameFieldName])){
				return 'Database error.';
			}
			$this->db->where(self::FieldRegistryPKName,$fieldID);
			$this->db->delete(self::FieldRegistryTableName);
		}
		return null;
	}
	
	public function deleteTable($tableID,$permanent=false){
		
		$this->db->select(self::TableNameFieldName.','.self::EssentialFieldName);
		$this->db->where(self::TableRegistryPKName,$tableID);
		$res = $this->db->get(self::TableRegistryTableName)->result_array();
		
		if(count($res)<1){
			return 'No such table found.';
		}
		
		if($res[0][self::EssentialFieldName]){
			return 'Table is essential.';
		}
		
		if(!$permanent){
			$this->db->where(self::TableRegistryPKName,$tableID);
			$this->db->set(self::FlagFieldName,self::FlagFieldName.'|'.Flags::DELETED,false);
			$this->db->update(self::TableRegistryTableName);
		}else{
			if(!$this->dbforge->drop_table($res[0][self::TableNameFieldName])){
				return 'Database error.';
			}
			$this->db->where(self::TableRegistryPKName,$tableID);
			$this->db->delete(self::TableRegistryTableName);
		}
		return null;
	}
	
	public function editField($fieldID,$fieldData){
		
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$res = $this->db->get(self::FieldRegistryTableName)->result_array();
		if(count($res)!=1){
			return 'No such field found.';
		}
		
		$data = array();
		
		if(!$res[0][self::EssentialFieldName]&&isset($fieldData['input_required'])){
			$data[self::FieldInputRequiredFieldName]=$fieldData['input_required'];
		}
		if(isset($fieldData['input_regex'])){
			$data[self::FieldInputRegexFieldName]=$fieldData['input_regex'];
		}
		if(isset($fieldData['input_tip'])){
			$data[self::FieldInputTipFieldName]=$fieldData['input_tip'];
		}
		if(isset($fieldData['input_regex_error_msg'])){
			$data[self::FieldInputRegexErrMsgFieldName]=$fieldData['input_regex_error_msg'];
		}
		if(isset($fieldData['title'])){
			$data[self::FieldTitleFieldName]=$fieldData['title'];
		}
		
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$this->db->update(self::FieldRegistryTableName,$data);
		return null;
	}
	
	public function editInputOrder($fieldID,$order){
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$this->db->update(self::FieldRegistryTableName,array(
			self::FieldInputOrderFieldName=>$order
		));
	}
	
	public function editTableTitle($tableID,$newTableTitle){
		//Check for uniqueness
		$this->db->where(self::TableTitleFieldName,$newTableTitle);
		$res = $this->db->get(self::TableRegistryTableName)->result_array();
		if(count($res)>0){
			return 'Table with that title already exists.';
		}
		//Check for existence
		$this->db->where(self::TableRegistryPKName,$tableID);
		$res = $this->db->get(self::TableRegistryTableName)->result_array();
		if(count($res)<1){
			return 'No such table found.';
		}
		
		$this->db->where(self::TableRegistryPKName,$tableID);
		$this->db->update(self::TableRegistryTableName,array(
			self::TableTitleFieldName=>$newTableTitle
		));
		return null;
	}
	
	public function getDeleted(){
		$output = array();
		
		$this->db->where(self::FlagFieldName.'|'.Flags::DELETED,self::FlagFieldName,false);
		$res = $this->db->get(self::TableRegistryTableName)->result_array();
		$tables = array();
		foreach($res as $r){
			array_push($tables,array(
				'ID'=>$r[self::TableRegistryPKName],
				'Title'=>$r[self::TableTitleFieldName],
				'Name'=>$r[self::TableNameFieldName]
			));
		}
		$output['Tables']=$tables;
		
		$this->db->where(self::FlagFieldName.'|'.Flags::DELETED,self::FlagFieldName,false);
		$res = $this->db->get(self::FieldRegistryTableName)->result_array();
		$fields = array();
		foreach($res as $r){
			$this->db->select(self::TableTitleFieldName);
			$this->db->where(self::TableRegistryPKName,$res[0][self::TableRegistryPKName]);
			$tableTitle = $this->db->get(self::TableRegistryTableName)->result_array()[0][self::TableTitleFieldName];
			array_push($fields,array(
				'ID'=>$r[self::FieldRegistryPKName],
				'Table Title'=>$tableTitle,
				'Title'=>$r[self::FieldTitleFieldName],
				'Name'=>$r[self::FieldNameFieldName],
				'Input Type'=>$r[self::FieldInputTypeFieldName]
			));
		}
		$output['Fields']=$fields;
		
		$this->db->where(self::FlagFieldName.'|'.Flags::DELETED,self::FlagFieldName,false);
		$res = $this->db->get(self::BaseTableTableName)->result_array();
		$records = array();
		foreach($res as $r){
			array_push($records,array(
				'ID'=>$r[self::BaseTablePKName],
				'Student Number'=>$r[self::StudentNumberFieldName],
				'Last Name'=>$r[self::LastNameFieldName],
				'First Name'=>$r[self::FirstNameFieldName],
				'Middle Name'=>$r[self::MiddleNameFieldName]
			));
		}
		$output['Records']=$records;
		return $output;
	}
	
	protected function getFieldID ($tableName,$fieldName){
		$tableID = $this->getTableID($tableName);
		
		$this->db->select(self::FieldRegistryPKName);
		$this->db->where(self::TableRegistryPKName,$tableID);
		$this->db->where(self::FieldNameFieldName,$fieldName);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		if(count($result)!=1) {
			return;
		}
		
		return $result[0][self::FieldRegistryPKName];
	}
	
	public function getFieldTableName($fieldID){
		$this->db->select(self::TableRegistryPKName);
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$res = $this->db->get(self::FieldRegistryTableName)->result_array();
		if(count($res)!=1){
			return;
		}
		return $this->db->get_where(self::TableRegistryTableName,array(self::TableRegistryPKName=>$res[0][self::TableRegistryPKName]))->result_array()[0][self::TableNameFieldName];
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
		
		$this->db->where(self::TableRegistryPKName,$tableID);
		foreach($whereQuery as $index=>$key){
			$this->db->where($index,$key,false);
		}
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		return $result;
	}
	
	public function getTableTitle($tableName){
		$this->db->select(self::TableTitleFieldName);
		$this->db->where(self::TableNameFieldName,$tableName);
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		
		if(count($result)!=1)
			return;
		
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
			return;
		}
		
		return($result[0][self::TableRegistryPKName]);
		
	}
	
	public function getTables($whereQuery=array()){	
		foreach($whereQuery as $index=>$key){
			$this->db->where($index,$key,false);
		}
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		return $result;
	}
	
	protected function registerField($tableID,$fieldData = array()){
		
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
	
	public function restoreField($fieldID){
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$this->db->set(self::FlagFieldName,self::FlagFieldName.'&'.Flags::DEF,false);
		$this->db->update(self::FieldRegistryTableName);
	}
	
	public function restoreTable($tableID){
		$this->db->where(self::TableRegistryPKName,$tableID);
		$this->db->set(self::FlagFieldName,self::FlagFieldName.'&'.Flags::DEF,false);
		$this->db->update(self::TableRegistryTableName);
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
	
	public function __construct(){
		parent::__construct();
		$this->createFERegistry();
		$this->createMCRegistry();
		$this->createChoiceRegistry();
	}
	
	public function addChoice($tableName, $fieldName, $choiceValue, $isCustom = false){
		$MCID = $this->getMCID($tableName,$fieldName);
		
		//Check for existence first
		if($MCID==null)
			return 'Multiple choice field does not exist.';
		$this->db->where(self::MCRegistryPKName,$MCID);
		$this->db->where(self::ChoiceValueFieldName,$choiceValue);
		$res = $this->db->get(self::ChoiceRegistryTableName)->result_array();
		if(count($res)!=0)
			return 'Choice already exists.';
		
		$fields =array(
			self::MCRegistryPKName => $MCID,
			self::ChoiceValueFieldName => $choiceValue,
			self::ChoiceCustomFieldName => $isCustom
		);
		$this->db->insert(self::ChoiceRegistryTableName,$fields);
		return;
	}
	
	public function addFEField($tableName,$FEName,$fieldData = array(),$FECardinalityFieldName=null,$defaultCardinality=1){
		/*
		$tableName = base table name
		$FEName = floating entity table name
		$fieldData = array(
			'title'=>title
			'name'=>name
			'input_tip'=>input tip
		)
		*/
		
		if(!is_int($defaultCardinality)||$defaultCardinality<1){
			return 'Cardinality must be a positive integer';
		}
		
		//Check for existence
		$fieldID = $this->getFieldID($tableName,$fieldData['name']);
		$this->db->where(self::FEIDFieldName,$fieldID);
		$res = $this->db->get(self::FERegistryTableName)->result_array();
		if(count($res)!=0)
			return 'Field already registered.';
		
		if(!isset($fieldData['name'])||!isset($fieldData['title'])){
			return 'Name or title not set';
		}
		
		$FEID = $this->getTableID($FEName);
		$cardinalityFieldID = $this->getFieldID($tableName,$FECardinalityFieldName);
		
		if($FEID==null){
			return 'Table does not exist.';
		}
		
		$res = $this->addField($tableName,array(
			'name'=>$fieldData['name'],
			'title'=>$fieldData['title'],
			'type'=>'int',
			'input_type'=>'FE',
			'input_tip'=>isset($fieldData['input_tip'])?$fieldData['input_tip']:'',
			'input_order'=>isset($fieldData['input_order'])?$fieldData['input_order']:null
		));
		
		if($res !== null)
			return $res;
		
		$fieldID = $this->getFieldID($tableName,$fieldData['name']);
		if($fieldID==null)
			return 'FE field does not exist.';
		
		$this->registerFE($fieldID,$FEID,$cardinalityFieldID,$defaultCardinality);
		
	}
	
	public function addMCField($tableName,$choiceType,$fieldData =array()){
		$tableID = $this->getTableID($tableName);
		if($tableID==null)
			return 'Table does not exist.';
		
		if(!isset($fieldData['name'])||!isset($fieldData['title']))
			return 'Missing field name or title';
		
		//Check for existence
		$fieldID = $this->getFieldID($tableName,$fieldData['name']);
		$this->db->where(self::MCFieldIDFieldName,$fieldID);
		$res = $this->db->get(self::MCRegistryTableName)->result_array();
		if(count($res)!=0)
			return 'Field already registered';
		
		switch($choiceType){
			case MCTypes::SINGLE:
				break;
			case MCTypes::MULTIPLE:
				break;
			default:
				return 'Invalid Choice Type';
		}
		
		$res = $this->addField($tableName, array(
			'name'=>$fieldData['name'],
			'title'=>$fieldData['title'],
			'type'=>'varchar(1200)',
			'input_type'=>'MC',
			'input_required'=>isset($fieldData['input_required'])?$fieldData['input_required']:false,
			'input_tip'=>isset($fieldData['input_tip'])?$fieldData['input_tip']:'',
			'input_order'=>isset($fieldData['input_order'])?$fieldData['input_order']:null
		));
		if($res!=null){
			return $res;
		}
		
		$fieldID = $this->getFieldID($tableName,$fieldData['name']);
		$this->registerMC($fieldID,$choiceType);
		
	}
	
	public function createFERegistry(){
		if(!$this->db->table_exists(self::FERegistryTableName)){
			
			$this->dbforge->add_field(self::FieldRegistryPKName.' int unsigned not null unique');
			$this->dbforge->add_field(self::FEIDFieldName.' int unsigned not null');
			$this->dbforge->add_field(self::FECardinalityFieldIDFieldName.' int unsigned');
			$this->dbforge->add_field(self::FEDefaultCardinalityFieldName.' int unsigned not null');
			
			$this->dbforge->add_field('foreign key ('.self::FieldRegistryPKName.') references '.self::FieldRegistryTableName.'('.self::FieldRegistryPKName.') on update cascade on delete cascade');
			$this->dbforge->add_field('foreign key ('.self::FEIDFieldName.') references '.self::TableRegistryTableName.'('.self::TableRegistryPKName.') on update cascade on delete cascade');
			$this->dbforge->add_field('foreign key ('.self::FECardinalityFieldIDFieldName.') references '.self::FieldRegistryTableName.'('.self::FieldRegistryPKName.') on update cascade on delete cascade');
			
			$this->dbforge->create_table(self::FERegistryTableName,TRUE);
			
		}
	}
	
	//for association of tables and choices
	private function createMCRegistry(){
		if(!$this->db->table_exists(self::MCRegistryTableName)){
			
			$this->dbforge->add_field(self::MCRegistryPKName.' int unsigned not null auto_increment');
			$this->dbforge->add_field(self::MCFieldIDFieldName.' int unsigned not null unique');
			$this->dbforge->add_field(self::MCTypeFieldName.' int unsigned not null default 1');
			
			$this->dbforge->add_field('primary key ('.self::MCRegistryPKName.')');
			$this->dbforge->add_field('foreign key ('.self::MCFieldIDFieldName.') references '.self::FieldRegistryTableName.'('.self::FieldRegistryPKName.') on update cascade on delete cascade');
			
			$this->dbforge->create_table(self::MCRegistryTableName,TRUE);
			
		}
	}
	
	//for choices themselves
	private function createChoiceRegistry(){
		if(!$this->db->table_exists(self::ChoiceRegistryTableName)){
			
			$this->dbforge->add_field(self::MCRegistryPKName.' int unsigned not null');
			$this->dbforge->add_field(self::ChoiceValueFieldName.' varchar(100) not null');
			$this->dbforge->add_field(self::ChoiceCustomFieldName.' boolean not null default 0');
			
			$this->dbforge->add_field('foreign key ('.self::MCRegistryPKName.') references '.self::MCRegistryTableName.'('.self::MCRegistryPKName.') on update cascade on delete cascade');
			
			$this->dbforge->create_table(self::ChoiceRegistryTableName,TRUE);
		}
	}
	
	public function deleteMCChoices($MCFieldID){
		$this->db->select(self::MCRegistryPKName);
		$this->db->where(self::MCFieldIDFieldName,$MCFieldID);
		$res = $this->db->get(self::MCRegistryTableName)->result_array();
		if(count($res)!=1){
			return 'Field not registered as multiple choice.';
		}
		$MCID = $res[0][self::MCRegistryPKName];
		$this->db->where(self::MCRegistryPKName,$MCID);
		$this->db->delete(self::ChoiceRegistryTableName);
	}
	
	public function editFE($FEFieldID,$data){
		//data => fe_name,cardinality_field_name,default_cardinality
		$update = array();
		if(!isset($data['fe_name'])){
			return 'Floating Entity name not set.';
		}
		
		$baseTableName = $this->getFieldTableName($FEFieldID);
		if($baseTableName == null){
			return 'No such field';
		}
		
		$update[self::FEIDFieldName]=$this->getTableID($data['fe_name']);
		
		if(isset($data['cardinality_field_name'])){
			$update[self::FECardinalityFieldIDFieldName]=$this->getFieldID($baseTableName,$data['cardinality_field_name']);
		}
		if(isset($data['default_cardinality'])){
			$update[self::FEDefaultCardinalityFieldName]=$data['default_cardinality'];
		}	
		
		$this->db->where(self::FieldRegistryPKName,$FEFieldID);
		$this->db->update(self::FERegistryTableName,$update);
	}
	
	public function editMCFieldType($MCFieldID,$MCType){
		
		switch($MCType){
			case MCTypes::SINGLE:
				break;
			case MCTypes::MULTIPLE:
				break;
			default:
				return 'Invalid Choice Type';
		}
		
		$this->db->select(self::MCRegistryPKName);
		$this->db->where(self::MCFieldIDFieldName,$MCFieldID);
		$res = $this->db->get(self::MCRegistryTableName)->result_array();
		if(count($res)!=1){
			return 'Field not registered as multiple choice.';
		}
		$MCID = $res[0][self::MCRegistryPKName];
		$this->db->where(self::MCRegistryPKName,$MCID);
		$this->db->update(self::MCRegistryTableName,array(
			self::MCTypeFieldName=>$MCType
		));
	}
	
	public function editFEDefaultCardinality($FEFieldID,$cardinality){
		if(!is_int($cardinality)||$cardinality<1){
			return 'Cardinality must be a positive integer';
		}
		$this->db->where(self::FieldRegistryPKName,$FEFieldID);
		$this->db->update(self::FERegistryTableName,array(
			self::FEDefaultCardinalityFieldName=>$cardinality
		));
	}
	
	public function getFECardinalityFieldName($tableName,$fieldName){
		$fieldID = $this->getFieldID($tableName,$fieldName);
		
		$this->db->select(self::FECardinalityFieldIDFieldName);
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$FEFieldID = $this->db->get(self::FERegistryTableName)->result_array()[0][self::FECardinalityFieldIDFieldName];
		
		if($FEFieldID==null)
			return '';
		
		$this->db->select(self::FieldNameFieldName);
		$this->db->where(self::FieldRegistryPKName,$FEFieldID);
		$result = $this->db->get(self::FieldRegistryTableName)->result_array();
		
		return $result[0][self::FieldNameFieldName];
	}
	
	public function getFEDefaultCardinality($tableName,$fieldName){
		$fieldID = $this->getFieldID($tableName,$fieldName);
		
		$this->db->select(self::FEDefaultCardinalityFieldName);
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$result = $this->db->get(self::FERegistryTableName)->result_array();
		if(count($result)!=1){
			return 1;
		}
		return $result[0][self::FEDefaultCardinalityFieldName];
	}
	
	public function getFETableName($tableName,$fieldName){
		$fieldID = $this->getFieldID($tableName,$fieldName);
		$this->db->select(self::FEIDFieldName);
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$res = $this->db->get(self::FERegistryTableName)->result_array();
		if(count($res)!=1)
			return;
		
		$FEID = $res[0][self::FEIDFieldName];
		
		$this->db->select(self::TableNameFieldName);
		$this->db->where(self::TableRegistryPKName,$FEID);
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		if(count($result)!=1)
			return;
		return $result[0][self::TableNameFieldName];
	}
	
	public function getFETableTitle($tableName,$fieldName){
		$fieldID = $this->getFieldID($tableName,$fieldName);
		$this->db->select(self::FEIDFieldName);
		$this->db->where(self::FieldRegistryPKName,$fieldID);
		$res = $this->db->get(self::FERegistryTableName)->result_array();
		if(count($res)!=1)
			return;
		$FEID = $res[0][self::FEIDFieldName];
		
		$this->db->select(self::TableTitleFieldName);
		$this->db->where(self::TableRegistryPKName,$FEID);
		$result = $this->db->get(self::TableRegistryTableName)->result_array();
		if(count($result)!=1)
			return;
		return $result[0][self::TableTitleFieldName];
	}
	
	public function getMCChoices($tableName,$fieldName){
		$MCID = $this->getMCID($tableName,$fieldName);
		$this->db->select(self::ChoiceValueFieldName.','.self::ChoiceCustomFieldName);
		$this->db->where(self::MCRegistryPKName,$MCID);
		$result = $this->db->get(self::ChoiceRegistryTableName)->result_array();
		return $result;
	}
	
	public function getMCType($tableName,$fieldName){
		$MCID = $this->getMCID($tableName,$fieldName);
		$this->db->select(self::MCTypeFieldName);
		$this->db->where(self::MCRegistryPKName,$MCID);
		$result = $this->db->get(self::MCRegistryTableName)->result_array();
		if(count($result)!=1)
			return;
		return $result[0][self::MCTypeFieldName];
	}
	
	public function getMCID($tableName,$fieldName){
		$fieldID = $this->getFieldID($tableName,$fieldName);
		
		$this->db->select(self::MCRegistryPKName);
		$this->db->where(self::MCFieldIDFieldName,$fieldID);
		$result = $this->db->get(self::MCRegistryTableName)->result_array();
		if(count($result)!=1)
			return;
		return $result[0][self::MCRegistryPKName];
	}
	
	protected function registerFE($fieldID,$FEID,$FECardinalityFieldID,$defaultCardinality){
		$data = array(
			self::FieldRegistryPKName => $fieldID,
			self::FEIDFieldName => $FEID,
			self::FECardinalityFieldIDFieldName => $FECardinalityFieldID,
			self::FEDefaultCardinalityFieldName => $defaultCardinality
		);
		
		$this->db->insert(self::FERegistryTableName,$data);
	}
	
	protected function registerMC($fieldID, $choiceType){
		$data = array(
			self::MCFieldIDFieldName => $fieldID,
			self::MCTypeFieldName => $choiceType
		);
		
		$this->db->insert(self::MCRegistryTableName,$data);
	}
	
}

