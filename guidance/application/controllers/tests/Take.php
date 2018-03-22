<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Take extends TestsController {

	public function body()
	{
		//$this->load->view('tests_take');
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
	
	private function getTestData($testTitle){
		$testID = $this->test_maker->getTestID($testTitle);
		if($testID == null){
			return null;
		}
		$output = array(
			'Title' => $testTitle,
			'Desc' => $this->test_maker->getTestDescription($testTitle)
		);
		
		$output['Questions'] = $this->test_maker->getQuestions($testTitle);
		
		//print('<pre>');print_r($output);print('</pre>');
		return $output;
	}
	
}