<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_Maker extends BaseModel{

	const TestsTableName = DB_PREFIX.'tests';
	const QuestionsTableName = DB_PREFIX.'tests_questions';
	const AnswersTableName = DB_PREFIX.'tests_answers';
	const UserAnswersTableName = DB_PREFIX.'tests_user_answers';

	const TestsPKName = 'test_id';
	const QuestionsPKName = 'question_id';
	
	public $ModelTitle = 'Tests';
	
	public function createModel(){
		
		//Create Tests Table
		if(!$this->db->table_exists(self::TestsTableName)){
			$this->addTable($this->ModelTitle,self::TestsTableName,'Tests');
			$this->addField(self::TestsTableName,array(
				'name'=>self::TestsPKName,
				'type'=>'int',
				'constraints'=>'not null auto_increment unique'
			),true);
			
			$this->addField(self::TestsTableName,array(
				'name'=>'test_title',
				'title'=>'Test Title',
				'type'=>'varchar(20)',
				'constraints'=>'not null unique',
				'input_type'=>'text',
				'input_required'=>true
			));
		}
		
		//Create Questions Table
		if(!$this->db->table_exists(self::QuestionsTableName)){
			$this->addTable($this->ModelTitle,self::QuestionsTableName,'Questions');
			$this->addField(self::QuestionsTableName,array(
				'name'=>self::QuestionsPKName,
				'type'=>'int',
				'constraints'=>'not null auto_increment unique'
			),true);
			$this->addField(self::QuestionsTableName,array(
				'name'=>'question_body',
				'title'=>'Question Body',
				'type'=>'varchar(250)',
				'constraints'=>'not null',
				'input_type'=>'textarea',
				'input_required'=>true
			));
			$this->addField(self::QuestionsTableName,array(
				'name'=>self::TestsPKName,
				'type'=>'int',
				'constraints'=>'not null'
			),false,true,array(
				'table_name'=>self::TestsTableName,
				'field_name'=>self::TestsPKName
			));
		}
		
		//Answers
		if(!$this->db->table_exists(self::AnswersTableName)){
			$this->addTable($this->ModelTitle,self::AnswersTableName,'Answers');
			$this->addField(self::AnswersTableName,array(
				'name'=>self::QuestionsPKName,
				'title'=>'Question ID',
				'type'=>'int',
				'constraints'=>'not null'
			),false,false,array(
				'table_name'=>self::QuestionsTableName,
				'field_name'=>self::QuestionsPKName
			));
			$this->addField(self::AnswersTableName,array(
				'name'=>'answer',
				'title'=>'Answer',
				'type'=>'char(1)',
				'constraints'=>'not null',
				'input_type'=>'text',
				'input_required'=>true,
				'input_pattern'=>'\w',
			));
			$this->addField(self::AnswersTableName,array(
				'name'=>'answer_weight',
				'title'=>'Answer Weight',
				'type'=>'float',
				'constraints'=>'not null',
				'input_type'=>'number',
				'input_required'=>true,
				'input_pattern'=>'\d..\d.',
			));
			
		}
		
	}
	
}

?>