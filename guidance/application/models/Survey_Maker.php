<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Survey_Maker extends CI_Model{
	
	const CategoryTableName = DB_PREFIX.'survey_category';
	const QuestionTableName = DB_PREFIX.'survey_question';
	const AnswerTableName = DB_PREFIX.'survey_answer';
	const StudentAnswerTableName = DB_PREFIX.'survey_student_answer';
	const StudentResultTableName = DB_PREFIX.'survey_student_result';
	
	const CategoryID = 'category_id';
	const CategoryTitle = 'category_title';
	const CategoryTip = 'category_tip';
	const CategoryAC = 'is_auto_compute'; 
	
	const QuestionID = 'question_id';
	const Question = 'question';
	const QuestionDep = 'dependent_on'; //dependent on question id
	const QuestionDepAID = 'dependent_on_aid'; //above question id must be answered with answer id (aid) in order for this question to appear
	const QuestionCustom = 'is_custom';
	
	const AnswerID = 'answer_id';
	const AnswerWeight = 'answer_weight';
	const AnswerValue = 'answer_value';
	
	const SAnswerCustom = 'answer_custom';
	
	const SRRawResult = 'raw_result';
	const SRInterpretation = 'interpretation';
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		$this->createSurveyModel();
	}
	
	public function createSurveyModel(){
		
		if(!$this->db->table_exists(self::CategoryTableName) || !$this->db->table_exists(self::QuestionTableName) || !$this->db->table_exists(self::AnswerTableName) || !$this->db->table_exists(self::StudentAnswerTableName)){
			$this->dbforge->add_field(self::CategoryID.' int unsigned not null unique auto_increment');
			$this->dbforge->add_field(self::CategoryAC.' boolean not null default true');
			$this->dbforge->add_field(self::CategoryTitle.' varchar(100) not null unique');
			$this->dbforge->add_field(self::CategoryTip.' varchar(300)');
			$this->dbforge->create_table(self::CategoryTableName,true);
			
			$this->dbforge->add_field(self::CategoryID.' int unsigned not null');
			$this->dbforge->add_field(self::AnswerID.' int unsigned not null unique auto_increment');
			$this->dbforge->add_field(self::AnswerValue.' varchar(50)');
			$this->dbforge->add_field(self::AnswerWeight.' int');
			$this->dbforge->add_field('primary key ('.self::AnswerID.')');
			$this->dbforge->add_field('foreign key ('.self::CategoryID.') references '.self::CategoryTableName.'('.self::CategoryID.') on delete cascade on update cascade');
			$this->dbforge->create_table(self::AnswerTableName,true);
			
			$this->dbforge->add_field(self::CategoryID.' int unsigned not null');
			$this->dbforge->add_field(self::QuestionID.' int unsigned not null unique auto_increment');
			$this->dbforge->add_field(self::Question.' varchar(500) not null');
			$this->dbforge->add_field(self::QuestionCustom.' boolean not null default 0');
			$this->dbforge->add_field(self::QuestionDep.' int unsigned');
			$this->dbforge->add_field(self::QuestionDepAID.' int unsigned');
			$this->dbforge->add_field('primary key ('.self::QuestionID.')');
			$this->dbforge->add_field('foreign key ('.self::CategoryID.') references '.self::CategoryTableName.'('.self::CategoryID.') on delete cascade on update cascade');
			$this->dbforge->add_field('foreign key ('.self::QuestionDep.') references '.self::QuestionTableName.'('.self::QuestionID.') on delete cascade on update cascade');
			$this->dbforge->add_field('foreign key ('.self::QuestionDepAID.') references '.self::AnswerTableName.'('.self::AnswerID.') on delete cascade on update cascade');
			$this->dbforge->create_table(self::QuestionTableName,true);
			
			$this->dbforge->add_field(StudentInfoBaseModel::BaseTablePKName.' int unsigned not null');
			$this->dbforge->add_field(self::QuestionID.' int unsigned not null');
			$this->dbforge->add_field(self::AnswerID.' int unsigned');
			$this->dbforge->add_field(self::SAnswerCustom.' varchar(150)');
			$this->dbforge->add_field('foreign key ('.StudentInfoBaseModel::BaseTablePKName.') references '.StudentInfoBaseModel::BaseTableTableName.'('.StudentInfoBaseModel::BaseTablePKName.') on delete cascade on update cascade');
			$this->dbforge->add_field('foreign key ('.self::QuestionID.') references '.self::QuestionTableName.'('.self::QuestionID.') on delete cascade on update cascade');
			$this->dbforge->add_field('foreign key ('.self::AnswerID.') references '.self::AnswerTableName.'('.self::AnswerID.') on delete cascade on update cascade');
			$this->dbforge->create_table(self::StudentAnswerTableName,true);
			
			$this->dbforge->add_field(StudentInfoBaseModel::BaseTablePKName.' int unsigned not null');
			$this->dbforge->add_field(self::CategoryID.' int unsigned not null');
			$this->dbforge->add_field(self::SRRawResult.' int unsigned');
			$this->dbforge->add_field(self::SRInterpretation.' varchar(150)');
			$this->dbforge->add_field('foreign key ('.StudentInfoBaseModel::BaseTablePKName.') references '.StudentInfoBaseModel::BaseTableTableName.'('.StudentInfoBaseModel::BaseTablePKName.') on delete cascade on update cascade');
			$this->dbforge->add_field('foreign key ('.self::CategoryID.') references '.self::CategoryTableName.'('.self::CategoryID.') on delete cascade on update cascade');
			$this->dbforge->create_table(self::StudentResultTableName,true);
			
			$this->initDefaultSurvey();
		}
	}
	
	public function initDefaultSurvey(){
		
		$this->db->insert_batch(self::CategoryTableName,array(
			array(
				self::CategoryTitle => 'Demographic Factors: A. Risk Factors',
				self::CategoryTip => 'In the past six months, I have:',
				self::CategoryAC => true
			),
			array(
				self::CategoryTitle => 'Demographic Factors: B. Protective Factors',
				self::CategoryTip => 'I believe that I have:',
				self::CategoryAC => true
			),
			array(
				self::CategoryTitle => 'Ideation',
				self::CategoryTip=>null,
				self::CategoryAC => true
			),
			array(
				self::CategoryTitle => 'Attempt',
				self::CategoryTip=>null,
				self::CategoryAC => false
			),
			array(
				self::CategoryTitle => 'Validation: Reasons For Living',
				self::CategoryTip => 'Please select the option that corresponds to indicate the importance of each statement for NOT killing yourself.',
				self::CategoryAC => true
			)
		));
		
		$this->db->insert_batch(self::AnswerTableName,array(
			array(
				self::CategoryID=>1,
				self::AnswerID=>1,
				self::AnswerValue=>'Yes',
				self::AnswerWeight=>1
			),array(
				self::CategoryID=>1,
				self::AnswerID=>2,
				self::AnswerValue=>'No',
				self::AnswerWeight=>0
			),
			array(
				self::CategoryID=>2,
				self::AnswerID=>3,
				self::AnswerValue=>'Not true of me',
				self::AnswerWeight=>0
			),
			array(
				self::CategoryID=>2,
				self::AnswerID=>4,
				self::AnswerValue=>'Sometimes true of me',
				self::AnswerWeight=>1
			),
			array(
				self::CategoryID=>2,
				self::AnswerID=>5,
				self::AnswerValue=>'Always true of me',
				self::AnswerWeight=>2
			),
			array(
				self::CategoryID=>3,
				self::AnswerID=>6,
				self::AnswerValue=>'Not true',
				self::AnswerWeight=>0
			),
			array(
				self::CategoryID=>3,
				self::AnswerID=>7,
				self::AnswerValue=>'Sometimes true',
				self::AnswerWeight=>1
			),
			array(
				self::CategoryID=>3,
				self::AnswerID=>8,
				self::AnswerValue=>'Often true',
				self::AnswerWeight=>2
			),
			array(
				self::CategoryID=>3,
				self::AnswerID=>9,
				self::AnswerValue=>'Always true',
				self::AnswerWeight=>2
			),
			array(
				self::CategoryID=>4,
				self::AnswerID=>10,
				self::AnswerValue=>'Yes',
				self::AnswerWeight=>1
			),
			array(
				self::CategoryID=>4,
				self::AnswerID=>11,
				self::AnswerValue=>'No',
				self::AnswerWeight=>0
			),
			array(
				self::CategoryID=>5,
				self::AnswerID=>12,
				self::AnswerValue=>'Not at all important',
				self::AnswerWeight=>0
			),
			array(
				self::CategoryID=>5,
				self::AnswerID=>13,
				self::AnswerValue=>'Somewhat unimportant',
				self::AnswerWeight=>1
			),
			array(
				self::CategoryID=>5,
				self::AnswerID=>14,
				self::AnswerValue=>'Somewhat important',
				self::AnswerWeight=>2
			),
			array(
				self::CategoryID=>5,
				self::AnswerID=>15,
				self::AnswerValue=>'Extremely important',
				self::AnswerWeight=>3
			)
		));
		
		$this->db->insert_batch(self::QuestionTableName,array(
			array(
				self::CategoryID=>1,
				self::QuestionID=>1,
				self::Question=>'Felt so hopeless that there are no solution to my problems',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>2,
				self::Question=>'Felt so alone that there is no one to help me',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>3,
				self::Question=>'Experienced financial difficulties',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>4,
				self::Question=>'Experienced personal and/or family health challenges',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>5,
				self::Question=>'Experienced death in the family',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>6,
				self::Question=>'Thought of suicide',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>7,
				self::Question=>'Experienced parental disengagement',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>8,
				self::Question=>'Unresolved family issues',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>9,
				self::Question=>'Experienced poor school/academic performance',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>10,
				self::Question=>'Poor peer/social relationship',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>1,
				self::QuestionID=>11,
				self::Question=>'Romantic relationship problems',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			
			array(
				self::CategoryID=>2,
				self::QuestionID=>12,
				self::Question=>'Strong family connectedness and support',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>2,
				self::QuestionID=>13,
				self::Question=>'Enhanced social support',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>2,
				self::QuestionID=>14,
				self::Question=>'Positive coping skills',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>2,
				self::QuestionID=>15,
				self::Question=>'Positive problem-solving skills',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>2,
				self::QuestionID=>16,
				self::Question=>'Excellent conflict resolution and non-violent handling of disputes',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>2,
				self::QuestionID=>17,
				self::Question=>'Personal, social, cultural and religious beliefs that support life preservation',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>2,
				self::QuestionID=>18,
				self::Question=>'Confidence in the importance of help-seeking behavior',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			
			array(
				self::CategoryID=>3,
				self::QuestionID=>19,
				self::Question=>'Thoughts of dying',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>20,
				self::Question=>'Wishing I am dead',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>21,
				self::Question=>'Thinking about the chances of committing suicide',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>22,
				self::Question=>'Thinking about how I would be gone',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>23,
				self::Question=>'Thinking about writing down my last wishes before I die',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>24,
				self::Question=>'Thinking of giving away my possessions',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>25,
				self::Question=>'Thinking about how to prepare the things I need to carry out plans of dying',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>26,
				self::Question=>'Thinking about the best time and day to die',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>27,
				self::Question=>'Wishing that I have the courage to be gone',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>3,
				self::QuestionID=>28,
				self::Question=>'Thinking of how I can be successful in carrying-out my plans of dying',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>29,
				self::Question=>'Have you ever tried inflicting injury upon yourself?',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>30,
				self::Question=>'What was the method/s used?',
				self::QuestionDep=>29,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>true
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>31,
				self::Question=>'How many times have you attempted suicide?',
				self::QuestionDep=>29,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>true
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>32,
				self::Question=>'When was the most recent attempt? (Enter "n/a" if not applicable)',
				self::QuestionDep=>29,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>true
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>33,
				self::Question=>'Did you require medical attention after the attempt?',
				self::QuestionDep=>29,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>34,
				self::Question=>'Did you tell anyone about the attempt?',
				self::QuestionDep=>29,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>35,
				self::Question=>'To whom?',
				self::QuestionDep=>34,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>true
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>36,
				self::Question=>'Did you talk to a councelor or some other person after your attempt?',
				self::QuestionDep=>29,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>4,
				self::QuestionID=>37,
				self::Question=>'To whom?',
				self::QuestionDep=>36,
				self::QuestionDepAID=>10,
				self::QuestionCustom=>true
			),
			
			array(
				self::CategoryID=>5,
				self::QuestionID=>38,
				self::Question=>'I am afraid of the actual "act" of killing myself (the pain, the blood, the violence)',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>39,
				self::Question=>'I believe I can cope with my problems',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>40,
				self::Question=>'I believe I am completely worthy of love',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>41,
				self::Question=>'I believe suicide is not the only way to solve my problems',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>42,
				self::Question=>'I believe only God has the right to end a life',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>43,
				self::Question=>'I believe I can endure the pain and life changes',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>44,
				self::Question=>'I value my family too much and could not bear to leave them',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>45,
				self::Question=>'I believe I am not a burden to my family',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
			array(
				self::CategoryID=>5,
				self::QuestionID=>46,
				self::Question=>'Other people would think I am weak and selfish',
				self::QuestionDep=>null,
				self::QuestionDepAID=>null,
				self::QuestionCustom=>false
			),
		));
	}
	
	public function getResults($studentNumber){
		
		$this->db->select(StudentInfoBaseModel::BaseTablePKName);
		$this->db->where(StudentInfoBaseModel::StudentNumberFieldName,$studentNumber);
		$res = $this->db->get(StudentInfoBaseModel::BaseTableTableName)->result_array();
		if(count($res)!=1){
			return null;
		}
		$studentID = $res[0][StudentInfoBaseModel::BaseTablePKName];
		
		$this->db->from(self::StudentAnswerTableName);
		$this->db->order_by(self::QuestionID,'asc');
		$this->db->where(StudentInfoBaseModel::BaseTablePKName,$studentID);
		$res = $this->db->get()->result_array();
		if(count($res)<1){
			return null;
		}
		
		$surveyForm = $this->getSurvey();
		$output = array();
		foreach($surveyForm as $section){
			$this->db->where(StudentInfoBaseModel::BaseTablePKName,$studentID);
			$this->db->where(self::CategoryID,$section['Category']['ID']);
			$res2 = $this->db->get(self::StudentResultTableName)->result_array();
			if(count($res)!=1)
				return null;
			$output[$section['Category']['Title']]['Raw Result'] = $res2[0][self::SRRawResult];
			$output[$section['Category']['Title']]['Interpretation'] = $res2[0][self::SRInterpretation];
			$output[$section['Category']['Title']]['Answers']=array();
			foreach($section['Questions'] as $question){
				foreach($res as $saIndex=>$studentAnswer){
					if($studentAnswer[self::QuestionID] == $question['Question ID']){
						$answer = 'error';
						if($question['Custom']){
							$answer = $studentAnswer[self::SAnswerCustom];
						}else{
							foreach($section['Answers'] as $ans){
								if($studentAnswer[self::AnswerID]==$ans['Answer ID']){
									$answer = $ans['Value'];
								}
							}
						}
						array_push($output[$section['Category']['Title']]['Answers'],array(
							'Question'=>$question['Question'],
							'Answer' => $answer
						));
					}
				}
			}
		}
		
		return $output;
		
	}
	
	public function getSurvey(){
		$categories = $this->db->get(self::CategoryTableName)->result_array();
		
		$survey = array();
		foreach($categories as $category){
			$this->db->where(self::CategoryID,$category[self::CategoryID]);
			
			$answers = $this->db->get(self::AnswerTableName)->result_array();
			$ans = array();
			foreach($answers as $answer){
				array_push($ans,array(
					'Category ID'=>$answer[self::CategoryID],
					'Answer ID'=>$answer[self::AnswerID],
					'Value'=>$answer[self::AnswerValue]
				));
			}
			
			$this->db->where(self::CategoryID,$category[self::CategoryID]);
			$questions = $this->db->get(self::QuestionTableName)->result_array();
			$ques = array();
			foreach($questions as $question){
				array_push($ques,array(
					'Category ID'=>$question[self::CategoryID],
					'Question ID'=>$question[self::QuestionID],
					'Question'=>$question[self::Question],
					'Custom'=>$question[self::QuestionCustom],
					'Dependent'=>$question[self::QuestionDep],
					'Dependent AID'=>$question[self::QuestionDepAID]
				));
			}
			
			array_push($survey,array(
				'Category'=>array(
					'ID' => $category[self::CategoryID],
					'Title'=>$category[self::CategoryTitle],
					'Tip'=>$category[self::CategoryTip]
				),
				'Answers'=>$ans,
				'Questions'=>$ques
			));
		}
		
		return $survey;
	}
	
	public function hasAnswered($sn){
		$this->db->select(StudentInfoBaseModel::BaseTablePKName);
		$this->db->where(StudentInfoBaseModel::StudentNumberFieldName,$sn);
		$res = $this->db->get(StudentInfoBaseModel::BaseTableTableName)->result_array();
		if(count($res)!=1){
			return false;
		}
		$this->db->where(StudentInfoBaseModel::BaseTablePKName,$res[0][StudentInfoBaseModel::BaseTablePKName]);
		$res = $this->db->get(self::StudentAnswerTableName)->result_array();
		return count($res)>0 ? true:false;
	}
	
	public function setInterpretation($studentID, $categoryID, $interpretation){
		$this->db->where(StudentInfoBaseModel::BaseTablePKName,$studentID);
		$this->db->where(self::CategoryID,$categoryID);
		$this->db->update(self::StudentResultTableName,array(
			self::SRInterpretation=>$interpretation
		));
	}
	
	public function submitAnswers($sn,$answers){
		/*
		answers = array(
			Normal=array(
				question id => answer id
			)
			Custom = array(
				question id => answer
			)
		)
		*/
		
		//check if student number is registered
		$this->db->select(StudentInfoBaseModel::BaseTablePKName);
		$this->db->where(StudentInfoBaseModel::StudentNumberFieldName,$sn);
		$res = $this->db->get(StudentInfoBaseModel::BaseTableTableName)->result_array();
		if(count($res)!=1){
			return 'Student Number not registered.';
		}
		$studentID = $res[0][StudentInfoBaseModel::BaseTablePKName];
		
		$categories = $this->db->get(self::CategoryTableName)->result_array();
		foreach($categories as $category){
			
			if($category[self::CategoryAC]){
				$this->db->where(self::CategoryID,$category[self::CategoryID]);
				$questions = $this->db->get(self::QuestionTableName)->result_array();
				$rawResult = 0;
				$interpretation = null;
				foreach($questions as $question){
					if(isset($answers['Normal'][$question[self::QuestionID]])){
						$this->db->select(self::AnswerWeight);
						$this->db->where(self::AnswerID,$answers['Normal'][$question[self::QuestionID]]);
						$res = $this->db->get(self::AnswerTableName)->result_array();
						if(count($res)!=1){
							return 'No such answer';
						}
						$rawResult = $rawResult+$res[0][self::AnswerWeight];
					}
				}
				
				switch($category[self::CategoryID]){
					case 1:
						if($rawResult>=0 && $rawResult<=3){
							$interpretation='Low Risk Factors';
						}
						if($rawResult>=4 && $rawResult<=7){
							$interpretation='Moderate Risk Factors';
						}else{
							$interpretation='High Risk Factors';
						}
						break;
					case 2:
						if($rawResult>=0 && $rawResult<=4){
							$interpretation='Low Protective Factors';
						}
						if($rawResult>=5 && $rawResult<=9){
							$interpretation='Moderate Protective Factors';
						}else{
							$interpretation='High Protective Factors';
						}
						break;
					case 3:
						if($rawResult>=0 && $rawResult<=6){
							$interpretation='Very Low Ideation';
						}
						if($rawResult>=7 && $rawResult<=14){
							$interpretation='Low Ideation';
						}
						if($rawResult>=15 && $rawResult<=22){
							$interpretation='Moderate Ideation';
						}else{
							$interpretation='High Ideation';
						}
						break;
					case 5:
						if($rawResult>=0 && $rawResult<=5){
							$interpretation='Very Low';
						}
						if($rawResult>=6 && $rawResult<=12){
							$interpretation='Low';
						}
						if($rawResult>=13 && $rawResult<=19){
							$interpretation='Moderate';
						}else{
							$interpretation='High';
						}
						break;
					default:
						break;
				}
				$this->db->insert(self::StudentResultTableName,array(
					StudentInfoBaseModel::BaseTablePKName=>$studentID,
					self::CategoryID=>$category[self::CategoryID],
					self::SRRawResult=>$rawResult,
					self::SRInterpretation=>$interpretation
				));
			}
		}
		
		foreach($answers['Normal'] as $qID=>$aID){
			$this->db->insert(self::StudentAnswerTableName,array(
				StudentInfoBaseModel::BaseTablePKName=>$studentID,
				self::QuestionID=>$qID,
				self::AnswerID=>$aID
			));
		}
		foreach($answers['Custom'] as $qID=>$answer){
			$this->db->insert(self::StudentAnswerTableName,array(
				StudentInfoBaseModel::BaseTablePKName=>$studentID,
				self::QuestionID=>$qID,
				self::SAnswerCustom=>$answer
			));
		}
	}
	
	public function unsetAnswers($sn){
		$this->db->select(StudentInfoBaseModel::BaseTablePKName);
		$this->db->where(StudentInfoBaseModel::StudentNumberFieldName,$sn);
		$res = $this->db->get(StudentInfoBaseModel::BaseTableTableName)->result_array();
		if(count($res)!=1){
			return 'No such student.';
		}
		$this->db->where(StudentInfoBaseModel::BaseTablePKName,$res[0][StudentInfoBaseModel::BaseTablePKName]);
		$this->db->delete(self::StudentAnswerTableName);
		$this->db->where(StudentInfoBaseModel::BaseTablePKName,$res[0][StudentInfoBaseModel::BaseTablePKName]);
		$this->db->delete(self::StudentResultTableName);
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
		$res = $this->db->get(Ion_Auth_Init::UsersTableName)->result_array();
		
		$passwords = array();
		foreach($res as $r){
			$this->db->select(StudentInfoBaseModel::LastNameFieldName.','.StudentInfoBaseModel::FirstNameFieldName.','.StudentInfoBaseModel::MiddleNameFieldName);
			$this->db->where(StudentInfoBaseModel::FlagFieldName.'|'.Flags::DELETED.' != ',StudentInfoBaseModel::FlagFieldName,false);
			$this->db->where(StudentInfoBaseModel::StudentNumberFieldName,$r['username']);
			$result=$this->db->get(StudentInfoBaseModel::BaseTableTableName)->result_array();
			if(count($result)==0){
				$this->db->where('username',$r['username']);
				$this->db->delete(Ion_Auth_Init::UsersTableName);
			}
			array_push($passwords,array(
				'username'=>$r['username'],
				'lastname'=>$result[0][StudentInfoBaseModel::LastNameFieldName],
				'firstname'=>$result[0][StudentInfoBaseModel::FirstNameFieldName],
				'middlename'=>$result[0][StudentInfoBaseModel::MiddleNameFieldName],
				'pword'=>$r['pword']
			));
		}
		return $passwords;
	}
	
	public function generatePasswords($mode,$arg){
		$this->db->select(StudentInfoBaseModel::StudentNumberFieldName);
		$this->db->where(StudentInfoBaseModel::FlagFieldName.'|'.Flags::DELETED.' != ',StudentInfoBaseModel::FlagFieldName,false);
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
			$this->ion_auth->register($r[StudentInfoBaseModel::StudentNumberFieldName],$password,'',array(
				'pword'=>$password
			));
		}
		
		return $this->getPasswords($mode,$arg);
	}
	
}

?>