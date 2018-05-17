<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Take extends BaseController {
	
	public function body(){
		if($this->ion_auth->logged_in()){
			$this->load->view('survey_form',array(
				'answered'=>$this->survey_maker->hasAnswered($this->ion_auth->user()->row()->username)
			));
		}else{
			$this->load->view('login');
		}
	}
	
	public function getSurveyForm(){
		echo json_encode($this->survey_maker->getSurvey(),JSON_NUMERIC_CHECK|JSON_HEX_APOS);
	}
	
	public function submit($sn=null){
		if($sn==null){
			$this->responseJSON(false,'Empty Input');
			return;
		}
		$input = $this->input->post('data');
		
		if($input == null){
			$this->responseJSON(false,'Empty Input');
			return;
		}
		$data= json_decode($input,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		if($this->survey_maker->hasAnswered($sn)){
			$this->responseJSON(false,'Student has already answered the survey.');
			return;
		}
		
		//Validate Input
		$surveyForm = $this->survey_maker->getSurvey();
		$validated = array(
			'Normal'=>array(),
			'Custom'=>array()
		);
		
		foreach($surveyForm as $section){
			foreach($section['Questions'] as $question){
				foreach($data as $qID=>$studentAnswer){
					if($qID == $question['Question ID']){
						if($question['Dependent']==null){
							if(!$question['Custom']){
								$isAnswerValid = false;
								foreach($section['Answers'] as $sectionAnswer){
									if($studentAnswer == $sectionAnswer['Answer ID'])
										$isAnswerValid = true;
								}
								if(!$isAnswerValid){
									$this->responseJSON(false,'Invalid answer.');
									return;
								}
							}
							
							$validated[$question['Custom']? 'Custom':'Normal'][$qID.""]=$studentAnswer;
						}else{
							//recursive check for dependencies
							$currQ = $question;
							$nextQ = null;
							$valid=false;
							while(true){
								if($currQ['Dependent']==null){
									$valid = true;
									break;
								}
								foreach($section['Questions'] as $q){
									if($q['Question ID'] == $currQ['Dependent']){
										$nextQ = $q;
									}
								}
								if($nextQ==null){
									break;
								}else{
									if(!isset($data[$nextQ['Question ID']]) || $data[$nextQ['Question ID']]!=$currQ['Dependent AID']){
										break;
									}
									$currQ = $nextQ;
								}
							
							}
							if($valid)
								$validated[$question['Custom']? 'Custom':'Normal'][$qID.""]=$studentAnswer;
						}
					}
				}
			}
		}
		
		$res = $this->survey_maker->submitAnswers($sn,$validated);
		if($res != null){
			$this->responseJSON(false,$res);
			return;
		}
		$this->responseJSON(true,'Answers successfully submitted');
		return;
	}
}