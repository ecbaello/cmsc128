<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_Information extends AssociativeEntityModel{
	 
	const BaseTableTableName = DB_PREFIX.'student'; 
	
	const FamilyDataTableName = DB_PREFIX.'student_family';
	const FamilyParentTableName = DB_PREFIX.'student_family_parent';
	const FamilyChildrenTableName = DB_PREFIX.'student_family_children';
	const FamilyGuardianTableName = DB_PREFIX.'student_family_guardian';
	
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
			$this->addTable($this->ModelTitle,self::BaseTableTableName,'Background Information');
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null auto_increment unique',
			),true);
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>self::ReferenceFieldFieldName,
				'title'=>'Student Number',
				'type'=>'varchar(15)',
				'constraints'=>'not null unique',
				'input_type'=>'text',
				'input_required'=>TRUE,
				'input_regex'=>'^\d{4}-\d{5}$'
			));
			
			$this->addField(self::BaseTableTableName,array(
				'name'=>'last_name',
				'title'=>'Last Name',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>TRUE,
			));
		}
		
		//Family Data
		if(!$this->db->table_exists(self::FamilyDataTableName)){
			
			$this->addTable($this->ModelTitle,self::FamilyDataTableName, 'Family Data');
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'parents_marital_status',
				'title'=>'Parent\'s Marital Status',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text'
			));
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'guardian_cardinality',
				'title'=>'',
				'type'=>'int',
				'constraints'=>'not null default 1',
				'input_type'=>'hidden'
			));
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'emergency_contact_cardinality',
				'title'=>'',
				'type'=>'int',
				'constraints'=>'not null default 1',
				'input_type'=>'hidden'
			));
			
			//Associated Entities
			
			//Parent
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'family_parent_cardinality',
				'title'=>'',
				'type'=>'int',
				'constraints'=>'not null default 2',
				'input_type'=>'hidden'
			));
			
			$this->addAET(self::FamilyParentTableName,'Parents');
			
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
			
			$this->addAETField(self::FamilyDataTableName,self::FamilyParentTableName,'family_parent_cardinality',2);
			
			//Children
			
			$this->addField(self::FamilyDataTableName,array(
				'name'=>'family_children_cardinality',
				'title'=>'Number of Children in Family',
				'type'=>'int',
				'constraints'=>'not null default 1',
				'input_type'=>'number'
			));
			
			$this->addAET(self::FamilyChildrenTableName,'Children In Family');
			
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
			
			$this->addAETField(self::FamilyDataTableName,self::FamilyChildrenTableName,'family_children_cardinality',1);
			
		}
		
		//Financial Information
		if(!$this->db->table_exists(self::FinancialInfoTableName)){
			$this->addTable($this->ModelTitle,self::FinancialInfoTableName,'Financial Information');
			
			$this->addField(self::FinancialInfoTableName,array(
				'name'=>self::BaseTablePKName,
				'type'=>'int',
				'constraints'=>'not null',
			),false,true,array(
				'field_name'=>self::BaseTablePKName,
				'table_name'=>self::BaseTableTableName
			));
			
			$this->addField(self::FinancialInfoTableName,array(
				'name'=>'family_annual_income',
				'title'=>'Family\'s Annual Income',
				'type'=>'varchar(30)',
				'constraints'=>'not null',
				'input_type'=>'text'
			));
			
			$this->addField(self::FinancialInfoTableName,array(
				'name'=>'family_income_sources',
				'title'=>'Family\'s Income Sources',
				'type'=>'varchar(100)',
				'constraints'=>'not null',
				'input_type'=>'text'
			));
		}
		
	}
	
	public function getBasePK($studentNumber){
		$this->db->select(self::BaseTablePKName);
		$this->db->where(self::ReferenceFieldFieldName,$studentNumber);
		$result = $this->db->get(self::BaseTableTableName)->result_array();
		return isset($result[0][self::BaseTablePKName])?$result[0][self::BaseTablePKName]:null;
	}
	
	public function getStudentData($tableName,$studentNumber,$isAET=false){
		$pk = $this->getBasePK($studentNumber);
		if($pk == null)
			return;
		$fields = $this->getFields($tableName);
		$select= array();
		foreach($fields as $field){
			if($field[BaseModel::FieldInputTypeFieldName] == 'hidden' || $field[BaseModel::FieldInputTypeFieldName] == 'AET')
				continue;
			array_push($select,$field[BaseModel::FieldNameFieldName]);
		}
		$this->db->select(implode(' , ',$select));
		$this->db->where(self::BaseTablePKName,$pk);
		$result = $this->db->get($tableName)->result_array();
		if($isAET){
			return $result;
		}
		return isset($result[0])?$result[0]:null;
	}
	
}