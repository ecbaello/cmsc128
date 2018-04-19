<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestsFlag{
	const DELETED = 1;
}

class UTAFlag{
	const DELETED = 1;
}

class Test_Maker extends CI_Model{

	const TestsTableName = DB_PREFIX.'tests';
	const QuestionsTableName = DB_PREFIX.'tests_questions';
	const ChoicesTableName = DB_PREFIX.'tests_choices';
	const UTATableName = DB_PREFIX.'tests_user_tests_assoc'; //UTA = Users Tests Association
	const UAATableName = DB_PREFIX.'tests_user_answers_assoc'; //UAA = User Answers Association
	const UAAChoicesTableName = DB_PREFIX.'tests_user_answers_assoc_choices';
	
	const TestsPKName = 'test_id';
	const TestsTitleFieldName = 'test_title';
	const TestsDescFieldName = 'test_desc';
	const TestsFlagFieldName = 'test_flag';
	
	const QuestionsPKName = 'question_id';
	const QuestionsTitleFieldName = 'question_title';
	const QuestionsOrderFieldName = 'question_order';
	
	const ChoicesValueFieldName = 'choice_value';
	
	const UTAPKName = 'uta_id';
	const UTAUserIDFieldName = 'user_id';
	const UTATestTitleFieldName = 'uta_test_title';
	const UTAFlagFieldName = 'uta_flag';
	
	const UAAPKName = 'uaa_id';
	const UAAQuestionTitleFieldName = 'uaa_question_title';
	
	const UAAChoicesValueName = 'uaa_choice_value';
	const UAAChoicesIsAnswerName = 'uaa_choice_is_answer';
	
	public $ModelTitle = 'Tests';
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		$this->createModel();
	}
	
	public function createModel(){
		
		//Tests Table
		$this->dbforge->add_field(self::TestsPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::TestsTitleFieldName.' varchar(50) not null unique');
		$this->dbforge->add_field(self::TestsDescFieldName.' varchar(400)');
		$this->dbforge->add_field(self::TestsFlagFieldName.' int');
		$this->dbforge->add_field('primary key ('.self::TestsPKName.')');
		
		$this->dbforge->create_table(self::TestsTableName,true);
		
		//Questions Table
		$this->dbforge->add_field(self::TestsPKName.' int unsigned not null');
		$this->dbforge->add_field(self::QuestionsPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::QuestionsTitleFieldName.' varchar(500) not null');
		$this->dbforge->add_field(self::QuestionsOrderFieldName.' int');
		$this->dbforge->add_field('primary key ('.self::QuestionsPKName.')');
		$this->dbforge->add_field('foreign key ('.self::TestsPKName.') references '.self::TestsTableName.'('.self::TestsPKName.') on update cascade on delete cascade');
		
		$this->dbforge->create_table(self::QuestionsTableName,true);

		//Choices Table
		$this->dbforge->add_field(self::QuestionsPKName.' int unsigned not null');
		$this->dbforge->add_field(self::ChoicesValueFieldName.' varchar(50)');
		$this->dbforge->add_field('foreign key ('.self::QuestionsPKName.') references '.self::QuestionsTableName.'('.self::QuestionsPKName.') on update cascade on delete cascade');
		
		$this->dbforge->create_table(self::ChoicesTableName,true);
		
		// UTA Table
		$this->dbforge->add_field(self::UTAPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::UTATestTitleFieldName.' varchar(50) not null');
		$this->dbforge->add_field(self::UTAUserIDFieldName.' varchar(30) not null');
		$this->dbforge->add_field(self::UTAFlagFieldName.' int unsigned');
		$this->dbforge->add_field('primary key ('.self::UTAPKName.')');
		
		$this->dbforge->create_table(self::UTATableName,true);
		
		// UAA Table
		$this->dbforge->add_field(self::UTAPKName.' int unsigned not null');
		$this->dbforge->add_field(self::UAAPKName.' int unsigned not null auto_increment unique');
		$this->dbforge->add_field(self::UAAQuestionTitleFieldName.' varchar(500) not null');
		$this->dbforge->add_field('primary key ('.self::UAAPKName.')');
		$this->dbforge->add_field('foreign key ('.self::UTAPKName.') references '.self::UTATableName.'('.self::UTAPKName.') on update cascade on delete cascade');
		
		$this->dbforge->create_table(self::UAATableName,true);
		
		//UAA Choices
		$this->dbforge->add_field(self::UAAPKName.' int unsigned not null');
		$this->dbforge->add_field(self::UAAChoicesValueName.' varchar(500) not null');
		$this->dbforge->add_field(self::UAAChoicesIsAnswerName.' boolean not null default 0');
		$this->dbforge->add_field('foreign key ('.self::UAAPKName.') references '.self::UAATableName.'('.self::UAAPKName.') on update cascade on delete cascade');
		
		$this->dbforge->create_table(self::UAAChoicesTableName,true);
	}
	
	public function addTest($testTitle,$testDesc=''){
		$this->db->where(self::TestsTitleFieldName,$testTitle);
		$result = $this->db->get(self::TestsTableName)->result_array();
		
		if(count($result)>0){
			if($result[0][self::TestsFlagFieldName]==TestsFlag::DELETED){
				$this->db->where(self::TestsTitleFieldName,$result[0][self::TestsTitleFieldName]);
				$this->db->delete(self::TestsTableName);
			}else{
				return 'Test Title Must Be Unique';
			}
		}
		
		$this->db->insert(self::TestsTableName,array(
			self::TestsTitleFieldName => $testTitle,
			self::TestsDescFieldName => $testDesc
		));		
	}
	
	public function editTest($testID, $testData = array()){
		//$testData = ('Title','Desc','Flag')
		
		if($testData == array())
			return;
		
		$data = array();
		$data[self::TestsPKName]=$testID;
		
		if(isset($testData['Title']))
			$data[self::TestsTitleFieldName]=$testData['Title'];
		if(isset($testData['Desc']))
			$data[self::TestsDescFieldName]=$testData['Desc'];
		if(isset($testData['Flag']))
			$data[self::TestsFlagFieldName]=$testData['Flag'];
		$this->db->replace(self::TestsTableName,$data);
	}
	
	public function getTestFlag($testID){
		$this->db->select(self::TestsFlagFieldName);
		$this->db->where(self::TestsPKName,$testID);
		$result = $this->db->get(self::TestsTableName)->result_array();
		
		if(!isset($result[0]))
			return null;
		
		return $result[0][self::TestsFlagFieldName];
	}
	
	public function getTestByID($testID){
		$this->db->where(self::TestsPKName,$testID);
		$result = $this->db->get(self::TestsTableName)->result_array();
		
		if(!isset($result[0]))
			return null;
		
		return $result[0];
	}
	
	public function setQuestions($testTitle,$questions=array()){
		/*foreach questions as question
			question = array(
				'Title'=>title,
				'Order'=> order, 
				'Choices'=>array(
					'Value'=>value
				)
			)
		*/
		
		$testID = $this->getTestID($testTitle);
		
		$this->db->where(self::TestsPKName,$testID);
		$this->db->delete(self::QuestionsTableName);
		
		foreach($questions as $question){
			$data = array(
				self::TestsPKName => $testID,
				self::QuestionsTitleFieldName => $question['Title'],
				self::QuestionsOrderFieldName => $question['Order']
			);
			$this->db->insert(self::QuestionsTableName,$data);
			
			$this->db->where(self::TestsPKName,$testID);
			$this->db->where(self::QuestionsTitleFieldName,$question['Title']);
			$questionID = $this->db->get(self::QuestionsTableName)->result_array()[0][self::QuestionsPKName];
			
			foreach($question['Choices'] as $choice){
				$this->db->insert(self::ChoicesTableName,array(
					self::QuestionsPKName=>$questionID,
					self::ChoicesValueFieldName=>$choice['Value'])
				);
			}
		}
	}
	
	public function getTestID($testTitle){
		$this->db->select(self::TestsPKName);
		$this->db->where(self::TestsTitleFieldName,$testTitle);
		$result = $this->db->get(self::TestsTableName)->result_array();
		
		if(!isset($result[0][self::TestsPKName]))
			return null;
		
		return $result[0][self::TestsPKName];
	}
	
	public function getTestDescription($testTitle){
		$testID = $this->getTestID($testTitle);
		if($testID==null)
			return null;
		$this->db->select(self::TestsDescFieldName);
		$this->db->where(self::TestsPKName,$testID);
		$result = $this->db->get(self::TestsTableName)->result_array();
		
		if(!isset($result[0][self::TestsDescFieldName]))
			return null;
		
		return $result[0][self::TestsDescFieldName];
	}
	
	public function getTests(){
		$result = $this->db->get(self::TestsTableName)->result_array();
		return $result;
	}
	
	public function getQuestions($testTitle){
		/*foreach questions as question
			question = array(
				'Title'=>title,
				'Order'=> order, 
				'Choices'=>array(
					'Value'=>value
				)
			)
		*/
		$questions = array();
		$testID = $this->getTestID($testTitle);
		
		$this->db->where(self::TestsPKName,$testID);
		$result = $this->db->get(self::QuestionsTableName)->result_array();
		foreach($result as $r){
			$question = array(
				'Title'=>$r[self::QuestionsTitleFieldName],
				'Order'=>$r[self::QuestionsOrderFieldName]
			);
			
			$this->db->where(self::QuestionsPKName,$r[self::QuestionsPKName]);
			$choices = $this->db->get(self::ChoicesTableName)->result_array();
			
			$question['Choices']=array();
			foreach($choices as $choice){
				array_push($question['Choices'],array('Value'=>$choice[self::ChoicesValueFieldName]));
			}
			
			array_push($questions,$question);
		}
		return $questions;
	}
	
	private function getUAAID($UTAID,$questionTitle){
		$this->db->select(self::UAAPKName);
		$this->db->where(self::UTAPKName,$UTAID);
		$this->db->where(self::UAAQuestionTitleFieldName,$questionTitle);
		$result = $this->db->get(self::UAATableName)->result_array();
		if(!isset($result[0][self::UAAPKName]))
			return null;
		return $result[0][self::UAAPKName];
	}
	
	public function getUTAID($userID,$testTitle){
		$this->db->select(self::UTAPKName);
		$this->db->where(self::UTAUserIDFieldName,$userID);
		$this->db->where(self::UTATestTitleFieldName,$testTitle);
		$result = $this->db->get(self::UTATableName)->result_array();
		if(!isset($result[0][self::UTAPKName]))
			return null;
		return $result[0][self::UTAPKName];
	}
	
	public function submitAnswers($userID,$testData=array()){
		/*
		testData = array(
			Title,
			Questions = array(
				Title,
				Choices = array(
					Value
				),
				Answer
			)
		)
		*/
		//print_r($testData);die();
		$data = array(
			self::UTATestTitleFieldName => $testData['Title'],
			self::UTAUserIDFieldName => $userID
		);
		$this->db->delete(self::UTATableName,$data);
		$this->db->insert(self::UTATableName,$data);
		
		$UTAID = $this->getUTAID($userID,$testData['Title']);
		
		foreach($testData['Questions'] as $question){

			$data = array(
				self::UTAPKName => $UTAID,
				self::UAAQuestionTitleFieldName=>$question['Title']
			);
			
			$this->db->where($data);
			$this->db->delete(self::UAATableName);
			
			$this->db->insert(self::UAATableName,$data);
			$UAAID = $this->getUAAID($UTAID,$question['Title']);
			
			$this->db->where(self::UAAPKName,$UAAID);
			$this->db->delete(self::UAAChoicesTableName);
			
			foreach($question['Choices'] as $choice){
				$this->db->insert(self::UAAChoicesTableName,array(
					self::UAAPKName=>$UAAID,
					self::UAAChoicesValueName=>$choice['Value'],
					self::UAAChoicesIsAnswerName=>$choice['Value']==$question['Answer']
				));
			}
		}
	}
	
	public function getAnswers($userID){
		/*
		ouput = array(
			array(
				Title,
				Questions = array(
					Title,
					Choices = array(
						Value
					)
				),
				Answer
			)
		)*/
		$output = array();
		
		$this->db->select(self::UTATestTitleFieldName.','.self::UTAPKName);
		$this->db->where(self::UTAUserIDFieldName,$userID);
		$result = $this->db->get(self::UTATableName)->result_array();
		foreach($result as $r){
			$UTAID = $this->getUTAID($userID,$r[self::UTATestTitleFieldName]);
			$this->db->select(self::UAAQuestionTitleFieldName);
			$this->db->where(self::UTAPKName,$UTAID);
			$questionsTemp = $this->db->get(self::UAATableName)->result_array();
			$questions = array();
			foreach($questionsTemp as $question){
				$UAAID = $this->getUAAID($UTAID,$question[self::UAAQuestionTitleFieldName]);
				$answer = '';
				
				$this->db->select(self::UAAChoicesValueName.','.self::UAAChoicesIsAnswerName);
				$this->db->where(self::UAAPKName,$UAAID);
				$choicesTemp = $this->db->get(self::UAAChoicesTableName)->result_array();
				$choices = array();
				foreach($choicesTemp as $choice){
					array_push($choices,array(
						'Value'=>$choice[self::UAAChoicesValueName]
					));
					if($choice[self::UAAChoicesIsAnswerName]==true)
						$answer = $choice[self::UAAChoicesValueName];
				}
				
				array_push($questions,array(
					'Title'=>$question[self::UAAQuestionTitleFieldName],
					'Choices'=>$choices,
					'Answer'=>$answer
				));
			}
			array_push($output,array(
				'Title'=>$r[self::UTATestTitleFieldName],
				'Questions'=>$questions
			));
		}
		
		return $output;
	}
	
	public function getPasswords($mode,$arg){
		//$mode = 0 => batch, 1=>indiv
		$this->db->select('username,pword');
		$this->db->where('id!=',1);
		if($mode == 0){
			$this->db->like('username',$arg,'after');
		}else{
			$this->db->where('username',$arg);
		}
		//print($this->db->get_compiled_select(Ion_Auth_Init::UsersTableName));die();
		return $this->db->get(Ion_Auth_Init::UsersTableName)->result_array();
	}
	
	public function generatePasswords($mode,$arg){
		$this->db->select(StudentInfoBaseModel::StudentNumberFieldName);
		$this->db->where(StudentInfoBaseModel::FlagFieldName.' !=',Flags::DELETED);
		if($mode == 0){
			$this->db->like(StudentInfoBaseModel::StudentNumberFieldName,$arg,'after');
		}else{
			$this->db->where(StudentInfoBaseModel::StudentNumberFieldName,$arg);
		}
		$subquery = $this->db->get_compiled_select(StudentInfoBaseModel::BaseTableTableName);
		
		$this->db->select('id');
		$this->db->where("username in ($subquery)",null,false);
		$res = $this->db->get(Ion_Auth_Init::UsersTableName)->result_array();
		//print_r($res);die();
		foreach($res as $r){
			$this->ion_auth->delete_user($r['id']);
		}	
		
		$res = $this->db->query($subquery)->result_array();
		//print_r($res);die();
		foreach($res as $r){
			$password = bin2hex(openssl_random_pseudo_bytes(4));
			$this->ion_auth->register($r[StudentInfoBaseModel::StudentNumberFieldName],$password,'ex@ex.com',array(
				'pword'=>$password
			));
		}
	}
}

?>