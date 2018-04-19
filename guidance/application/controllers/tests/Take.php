<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Take extends TestsController {

	public function body()
	{
		show_404();
	}
	
	public function test($testTitle=null){
		if($testTitle==null){
			show_404();
			return;
		}
		$testTitle = urldecode($testTitle);
		if($this->test_maker->getTestID($testTitle)===null)
			show_404();
		$this->showTestForm($testTitle);
	}
	
	public function post(){
		$input = $this->input->post('data');
		
		if($input == null){
			$this->responseJSON(false,'Empty Input');
			return;
		}
		//log_message('debug',$input);die();
		$data= json_decode($input,true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			$this->responseJSON(false,'Invalid JSON');
			return;
		}
		
		if(!isset($data['UTAID'])){
			$this->responseJSON(false,'Please fill up all the required fields.');
			return;
		}
		
		if(!isset($data['Title'])||!isset($data['Questions'])){
			$this->responseJSON(false,'No title/questions found.');
			return;
		}
		
		$testData = $this->getTestData($data['Title']);
		if($testData === null){
			$this->responseJSON(false,'Invalid test.');
			return;
		}
		
		$toInsert = array(
			'Title'=>$data['Title'],
			'Questions'=>array()
		);
		foreach($data['Questions'] as $index=>$question){
			if(!isset($question['Title'])){
				$this->responseJSON(false,'Question must have a title.');
				return;
			}
			if(!isset($question['Choices'])){
				$this->responseJSON(false,'No choices found.');
				return;
			}
			$choices = array();
			foreach($question['Choices'] as $choice){
				if(!isset($choice['Value'])){
					$this->responseJSON(false,'Choices must have a value.');
					return;
				}
				array_push($choices,array(
					'Value'=>$choice['Value']
				));
			}
			
			if(!isset($question['Answer'])){
				$this->responseJSON(false,'You must answer question #'.($index+1));
				return;
			}
			array_push($toInsert['Questions'],array(
				'Title'=>$question['Title'],
				'Choices'=>$choices,
				'Answer'=>$question['Answer']
			));
		}
		
		$this->test_maker->submitAnswers($data['UTAID'],$toInsert);
		$this->responseJSON(true,'Test Successfully Answered');
		return;
		
	}
	
	public function showTestForm($testTitle){
		
		$testData = $this->getTestData($testTitle);
		if($testData === null)
			return;
		
		$this->load->view('header');
		$this->load->view('tests_take',array(
			'test'=>json_encode($testData,JSON_HEX_APOS|JSON_NUMERIC_CHECK)
		));
		$this->load->view('footer');
	}
	
}