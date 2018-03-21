<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_Information extends AdvancedInputsModel{
	 
	const BaseTableTableName = DB_PREFIX.'student'; 
	
	const FamilyDataTableName = DB_PREFIX.'student_family';
	const FamilyParentTableName = DB_PREFIX.'student_family_parent';
	const FamilyChildrenTableName = DB_PREFIX.'student_family_children';
	const FamilyGuardianTableName = DB_PREFIX.'student_family_guardian';
	const FamilyEmergencyContactTableName = DB_PREFIX.'student_family_emercon';
	
	const EducationalBGTableName = DB_PREFIX.'student_education';
	const FinancialInfoTableName = DB_PREFIX.'student_finance';
	const VocationalPlansTableName = DB_PREFIX.'student_vocation';
	const LeisureInfoTableName = DB_PREFIX.'student_leisure';

	const BaseTablePKName = "student_id";
	const ReferenceFieldFieldName = "student_number";
		
	public $ModelTitle = "Student Information";
	
	public function createModel(){
		
		//Bacgkround Information 
		if(!$this->db->table_exists(self::BaseTableTableName)){
			$this->addTable(self::BaseTableTableName,'Background Information',true);
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
				'essential'=>TRUE
			),true);
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>self::ReferenceFieldFieldName,
				'title'=>'Student Number',
				'type'=>'varchar(15)',
				'constraints'=>'not null unique',
				'input_type'=>'text',
				'input_required'=>TRUE,
				'input_regex'=>'^\d{4}-\d{5}$',
				'input_regex_error_msg'=>'Must follow the format xxxx-xxxxx',
				'input_tip'=>'Must be unique',
				'essential'=>TRUE
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'course_block',
				'title'=>'Course/Block',
				'type'=>'varchar(30)',
				'input_type'=>'text',
				'input_required'=>FALSE,
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'last_name',
				'title'=>'Last Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE,
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'first_name',
				'title'=>'First Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE,
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'middle_name',
				'title'=>'Middle Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text'
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'nickname',
				'title'=>'Nickname',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>FALSE
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'sex',
				'title'=>'Sex',
				'type'=>'varchar(15)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE,
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'birthdate',
				'title'=>'Date of Birth',
				'type'=>'varchar(40)',
				'constraints'=>'not null',
				'input_type'=>'date',
				'input_required'=>TRUE,
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'birthplace',
				'title'=>'Place of Birth',
				'type'=>'varchar(100)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>FALSE,
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'nationality',
				'title'=>'Nationality',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>FALSE,
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'citizenship',
				'title'=>'Citizenship',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>FALSE,
			));
			
		}
		
		//Family Data
		if(!$this->db->table_exists(self::FamilyDataTableName)){
			
			$this->addTable(self::FamilyDataTableName, 'Family Data');
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
			
			$this->addMCField(self::FamilyDataTableName,MCTypes::MULTIPLE,'parents_marital_status','Parent\'s Marital Status',true,'Check as many that applies');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Parents still married');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Parents separated');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Father re-married');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','Mother re-married');
			$this->addChoice(self::FamilyDataTableName,'parents_marital_status','',true,'Others (specify)');
			
			//Floating Entities
			
			//Guardian
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'family_guardian_cardinality',
				'title'=>'',
				'type'=>'int',
				'constraints'=>'not null default 1',
				'input_type'=>'hidden'
			));
			$this->addTable(self::FamilyGuardianTableName,'Guardian',FALSE,TableFlags::FLOATING);
			$this->addField(self::FamilyGuardianTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
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
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyGuardianTableName,'family_guardian_cardinality',1);
			
			//Emergency Contact
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'family_emercon_cardinality',
				'title'=>'',
				'type'=>'int',
				'constraints'=>'not null default 1',
				'input_type'=>'hidden'
			));
			$this->addTable(self::FamilyEmergencyContactTableName,'Emergency Contact',FALSE,TableFlags::FLOATING);
			$this->addField(self::FamilyEmergencyContactTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
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
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyEmergencyContactTableName,'family_emercon_cardinality',1);
			
			//Parent
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'family_parent_cardinality',
				'title'=>'',
				'type'=>'int',
				'constraints'=>'not null default 2',
				'input_type'=>'hidden'
			));
			
			$this->addTable(self::FamilyParentTableName,'Parents',FALSE,TableFlags::FLOATING);
			
			$this->addField(self::FamilyParentTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
			
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
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyParentTableName,'family_parent_cardinality',2);
			
			//Children
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'family_children_cardinality',
				'title'=>'Number of Children in Family',
				'type'=>'int',
				'constraints'=>'not null default 1',
				'input_type'=>'number'
			));
			
			$this->addTable(self::FamilyChildrenTableName,'Children In Family',FALSE,TableFlags::FLOATING);
			
			$this->addField(self::FamilyChildrenTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
			
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
			
			$this->addFEField(self::FamilyDataTableName,self::FamilyChildrenTableName,'family_children_cardinality',1,'Enter student details first.');
			
		}
		
		//Educational Background
		if(!$this->db->table_exists(self::EducationalBGTableName)){
			$this->addTable(self::EducationalBGTableName,'Educational Background');
			
			$this->addField(self::EducationalBGTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
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
			
			$this->addField(self::FinancialInfoTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
			
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
	
	public function getBasePK($studentNumber){
		$this->db->select(self::BaseTablePKName);
		$this->db->where(self::ReferenceFieldFieldName,$studentNumber);
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
			if($field[BaseModel::FieldInputTypeFieldName] == 'hidden' || $field[BaseModel::FieldInputTypeFieldName] == 'FE')
				continue;
			array_push($select,$field[BaseModel::FieldNameFieldName]);
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
			if($field[BaseModel::FieldInputTypeFieldName]=='text'){
				array_push($fields,$field[BaseModel::FieldNameFieldName]);
			}
		}
		
		$this->db->select(implode(' , ',$fields));
		foreach($whereQuery as $query){
			if($query['type']=='and')
				$this->db->where($query['query']);
			if($query['type']=='or')
				$this->db->or_where($query['query']);
		}
		//print_r($this->db->get_compiled_select(self::BaseTableTableName));die();
		$result = $this->db->get(self::BaseTableTableName)->result_array();
		
		return $result;
		
	}
	
	
}