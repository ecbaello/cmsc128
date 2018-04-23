<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_Information extends AdvancedInputsModel{
	
	const FamilyDataTableName = DB_PREFIX.'student_family';
	const FamilyParentTableName = DB_PREFIX.'student_family_parent';
	const FamilyChildrenTableName = DB_PREFIX.'student_family_children';
	const FamilyGuardianTableName = DB_PREFIX.'student_family_guardian';
	const FamilyEmergencyContactTableName = DB_PREFIX.'student_family_emercon';
	
	const EducationalBGTableName = DB_PREFIX.'student_education';
	const FinancialInfoTableName = DB_PREFIX.'student_finance';
	const VocationalPlansTableName = DB_PREFIX.'student_vocation';
	const LeisureInfoTableName = DB_PREFIX.'student_leisure';
	
	public function __construct(){
		parent::__construct();
		$this->initDefaults();
	}
	
	public function initDefaults(){
		
		//Bacgkround Information 
		$this->addField(StudentInfoBaseModel::BaseTableTableName,array(
			'name'=>'course_block',
			'title'=>'Course/Block',
			'type'=>'varchar(30)',
			'input_type'=>'text',
			'input_required'=>FALSE,
		));
		
		$this->addField(StudentInfoBaseModel::BaseTableTableName,array(
			'name'=>'nickname',
			'title'=>'Nickname',
			'type'=>'varchar(30)',
			'constraints'=>'not null',
			'input_type'=>'text',
			'input_required'=>FALSE
		));
		
		$this->addField(StudentInfoBaseModel::BaseTableTableName,array(
			'name'=>'sex',
			'title'=>'Sex',
			'type'=>'varchar(15)',
			'constraints'=>'not null',
			'input_type'=>'text',
			'input_required'=>TRUE,
		));
		
		$this->addField(StudentInfoBaseModel::BaseTableTableName,array(
			'name'=>'birthdate',
			'title'=>'Date of Birth',
			'type'=>'date',
			'constraints'=>'not null',
			'input_type'=>'date',
			'input_required'=>TRUE,
		));
		
		$this->addField(StudentInfoBaseModel::BaseTableTableName,array(
			'name'=>'birthplace',
			'title'=>'Place of Birth',
			'type'=>'varchar(100)',
			'constraints'=>'not null',
			'input_type'=>'text',
			'input_required'=>FALSE,
		));
		
		$this->addField(StudentInfoBaseModel::BaseTableTableName,array(
			'name'=>'nationality',
			'title'=>'Nationality',
			'type'=>'varchar(30)',
			'constraints'=>'not null',
			'input_type'=>'text',
			'input_required'=>FALSE,
		));
		
		$this->addField(StudentInfoBaseModel::BaseTableTableName,array(
			'name'=>'citizenship',
			'title'=>'Citizenship',
			'type'=>'varchar(30)',
			'constraints'=>'not null',
			'input_type'=>'text',
			'input_required'=>FALSE,
		));

		//Family Data
		if(!$this->db->table_exists(self::FamilyDataTableName)){
			
			$this->addTable(self::FamilyDataTableName, 'Family Data');

			$this->addMCField(self::FamilyDataTableName,MCTypes::MULTIPLE,'parents_marital_status','Parent\'s Marital Status',true,'Check as many that applies');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Parents still married');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Parents separated');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Father re-married');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Mother re-married');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','',true,'Others (specify)');
			
			//Floating Entities
			
			//Guardian
			$this->addTable(self::FamilyGuardianTableName,'Guardian',Flags::FLOATING);

			$this->addField(self::FamilyGuardianTableName,array(
				'name'=>'guardian_id',
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
			),true);
			$this->addField(self::FamilyGuardianTableName,array(
				'name'=>'name',
				'title'=>'Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			$this->addField(self::FamilyGuardianTableName,array(
				'name'=>'address',
				'title'=>'Address',
				'type'=>'varchar(100)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			$this->addField(self::FamilyGuardianTableName,array(
				'name'=>'contactno',
				'title'=>'Contact No.',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyGuardianTableName,array(
				'title'=>'Guardian',
				'name'=>'guardian'
			),null,1);
			
			//Emergency Contact
			$this->addTable(self::FamilyEmergencyContactTableName,'Emergency Contact',Flags::FLOATING);
			$this->addField(self::FamilyEmergencyContactTableName,array(
				'name'=>'emergency_contact_id',
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
			),true);
			$this->addField(self::FamilyEmergencyContactTableName,array(
				'name'=>'name',
				'title'=>'Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			$this->addField(self::FamilyEmergencyContactTableName,array(
				'name'=>'address',
				'title'=>'Address',
				'type'=>'varchar(100)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			$this->addField(self::FamilyEmergencyContactTableName,array(
				'name'=>'contactno',
				'title'=>'Contact No.',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyEmergencyContactTableName,array(
				'title'=>'Emergency Contact',
				'name'=>'emergency_contact'
			),null,1);
			
			//Parent
			
			$this->addTable(self::FamilyParentTableName,'Parents',Flags::FLOATING);
			
			$this->addField(self::FamilyParentTableName,array(
				'name'=>'parent_id',
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
			),true);
			
			$this->addField(self::FamilyParentTableName,array(
				'name'=>'parent_student_relationship',
				'title'=>'Relationship With Student',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text'
			));
			
			$this->addField(self::FamilyParentTableName,array(
				'name'=>'name',
				'title'=>'Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyParentTableName,array(
				'title'=>'Parents',
				'name'=>'parents'
			),null,2);
			
			//Children
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'family_children_cardinality',
				'title'=>'Number of Children in Family',
				'type'=>'int',
				'constraints'=>'not null default 1',
				'input_type'=>'number'
			));
			
			$this->addTable(self::FamilyChildrenTableName,'Children In Family',Flags::FLOATING);
			
			$this->addField(self::FamilyChildrenTableName,array(
				'name'=>'child_id',
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
			),true);
			
			$this->addField(self::FamilyChildrenTableName,array(
				'name'=>'name',
				'title'=>'Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE
			));
			$this->addField(self::FamilyChildrenTableName,array(
				'name'=>'age',
				'title'=>'Age',
				'type'=>'int',
				'constraints'=>'not null',
				'input_type'=>'number'
			));
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyChildrenTableName,array(
				'title'=>'Children In Family',
				'name'=>'children_in_family',
				'input_tip'=>'Enter student details first.'
			),'family_children_cardinality',1);
			
		}
		
		//Educational Background
		if(!$this->db->table_exists(self::EducationalBGTableName)){
			$this->addTable(self::EducationalBGTableName,'Educational Background');

			$this->addField(self::EducationalBGTableName,array(
				'name'=>'elem_school',
				'title'=>'Elementary School Graduated From',
				'type'=>'varchar(50)',
				'input_type'=>'text'
			));
			$this->addField(self::EducationalBGTableName,array(
				'name'=>'elem_school_location',
				'title'=>'Elementary School Location',
				'type'=>'varchar(100)',
				'input_type'=>'text'
			));
			$this->addField(self::EducationalBGTableName,array(
				'name'=>'high_school',
				'title'=>'High School Graduated From',
				'type'=>'varchar(50)',
				'input_type'=>'text'
			));
			$this->addField(self::EducationalBGTableName,array(
				'name'=>'high_school_location',
				'title'=>'High School Location',
				'type'=>'varchar(100)',
				'input_type'=>'text'
			));
			$this->addMCField(self::EducationalBGTableName,MCTypes::MULTIPLE,'high_school_type','Type of High School',true,'Check as many as appropriate');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Private) Exclusive');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Private) Sectarian');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Private) Vocational/Technical');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Private) Co-ed');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Private) Non-Sectarian');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Public) City');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Public) Provincial');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Public) National');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Public) Barangay');
			$this->addChoice(self::EducationalBGTableName,'high_school_type','(Public) Tech/Voc');
			
			$this->addMCField(self::EducationalBGTableName,MCTypes::SINGLE,'high_school_gradnum','High School Number of Graduating Students ',true);
			$this->addChoice(self::EducationalBGTableName,'high_school_gradnum','less than 25');
			$this->addChoice(self::EducationalBGTableName,'high_school_gradnum','25-99');
			$this->addChoice(self::EducationalBGTableName,'high_school_gradnum','100-199');
			$this->addChoice(self::EducationalBGTableName,'high_school_gradnum','200-399');
			$this->addChoice(self::EducationalBGTableName,'high_school_gradnum','400-499');
			$this->addChoice(self::EducationalBGTableName,'high_school_gradnum','600 or more');
		}
		
		//Financial Information
		if(!$this->db->table_exists(self::FinancialInfoTableName)){
			$this->addTable(self::FinancialInfoTableName,'Financial Information');
			
			$this->addMCField(self::FinancialInfoTableName,MCTypes::SINGLE,'family_annual_income','Family\'s Annual Income',true);
			$this->addChoice(self::FinancialInfoTableName,'family_annual_income','0-40,500');
			$this->addChoice(self::FinancialInfoTableName,'family_annual_income','40,501-49,500');
			$this->addChoice(self::FinancialInfoTableName,'family_annual_income','49,501-58,500');
			
			$this->addMCField(self::FinancialInfoTableName,MCTypes::MULTIPLE,'family_income_sources','Family\'s Income Sources',true,'Check as many that applies');
			$this->addChoice(self::FinancialInfoTableName,'family_income_sources','Salaries/Wages/Commission');
			$this->addChoice(self::FinancialInfoTableName,'family_income_sources','Farming/Fishing');
			$this->addChoice(self::FinancialInfoTableName,'family_income_sources','Own Business');
			$this->addChoice(self::FinancialInfoTableName,'family_income_sources','Pension');
			$this->addChoice(self::FinancialInfoTableName,'family_income_sources','',true,'Others (Pls. specify)');
			$this->addChoice(self::FinancialInfoTableName,'family_income_sources','None');
			
		}
	}
	
	public function insert($tableName,$fields = array()){
		//fields: field_name => field_data
		
		if(isset($fields[StudentInfoBaseModel::StudentNumberFieldName])){
			$this->db->where(StudentInfoBaseModel::StudentNumberFieldName,$fields[StudentInfoBaseModel::StudentNumberFieldName]);
			$result = $this->db->get(StudentInfoBaseModel::BaseTableTableName)->result_array();
			
			if(count($result)>0)
				return 'Student Number already registered.';
		}
		
		$this->db->insert($tableName,$fields);
		return null;
		
	}
	
	public function getBasePK($studentNumber){
		$this->db->select(self::BaseTablePKName);
		$this->db->where(self::StudentNumberFieldName,$studentNumber);
		$result = $this->db->get(self::BaseTableTableName)->result_array();
		return isset($result[0][self::BaseTablePKName])?$result[0][self::BaseTablePKName]:null;
	}
	
	public function getBaseTableFields(){
		return $this->getFields(self::BaseTableTableName);
	}
	
	public function getStudentData($tableName,$studentNumber,$isFE=false){
		$pk = $this->getBasePK($studentNumber);
		if($pk == null)
			return;
		$fields = $this->getFields($tableName);
		$select= array();
		foreach($fields as $field){
			if($field[StudentInfoBaseModel::FieldInputTypeFieldName] == 'hidden' || $field[StudentInfoBaseModel::FieldInputTypeFieldName] == 'FE')
				continue;
			array_push($select,$field[StudentInfoBaseModel::FieldNameFieldName]);
		}
		$this->db->select(implode(' , ',$select));
		$this->db->where(self::BaseTablePKName,$pk);
		$result = $this->db->get($tableName)->result_array();
		if($isFE){
			return $result;
		}
		return isset($result[0])?$result[0]:null;
	}
	
	public function searchStudents($whereQuery = array()){
	
		//whereQuery: type=>and/or,query=> (field=>data)
		$fieldsTemp = $this->getFields(self::BaseTableTableName);
		
		$fields = array();
		foreach($fieldsTemp as $field){
			if($field[StudentInfoBaseModel::FieldInputTypeFieldName]=='text'){
				array_push($fields,$field[StudentInfoBaseModel::FieldNameFieldName]);
			}
		}
		
		$this->db->select(implode(' , ',$fields));
		foreach($whereQuery as $query){
			if($query['type']=='and')
				$this->db->like($query['query']);
			if($query['type']=='or')
				$this->db->or_like($query['query']);
		}
		//print_r($this->db->get_compiled_select(self::BaseTableTableName));die();
		$result = $this->db->get(self::BaseTableTableName)->result_array();
		
		return $result;
		
	}
	
	public function update($tableName,$fields = array()){
		$this->db->replace($tableName,$fields);
	}
	
	
}