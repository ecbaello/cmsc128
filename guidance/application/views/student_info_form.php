<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$ci =& get_instance();
?><!DOCTYPE html>


<div ng-controller="student_form" layout="row" layout-align="center start" flex ng-init="init()">
	<md-content layout="column" layout-align="start start" flex='20'>
		<div layout-margin>
		<?php
			if($mode=='add'){
				echo '<h2>Add Students</h2> ';
			}
			if($mode=='manage'){
				echo '<h2>Manage Students</h2>';
			}
		?>
		</div>
		<md-button layout-fill style="text-align:left" ng-repeat="(key,value) in tableData"  ng-click="changeCategory(key)" ng-class="{'md-primary md-raised':key == currCategoryKey}">
			<span layout-padding>{{value.Table.Title}}</span>
		</md-button>
		<md-button class="md-raised md-primary" ng-click="submit()">Submit</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill>
		<div>
			<h2 class="md-headline">
				<span>Student Information: {{currCategory.Table.Title}}<span>
			</h2>
		</div>
		<div layout-fill class="md-no-padding">
			<form name="student">
				<div ng-repeat="(key,value) in currCategory.Fields">
				
					<div ng-if="value['Input Type'] != 'FE'" class="md-no-margin">
						<?=/*$this->load->view('student_info_form_input',array(
							'model'=>'input[currCategory.Table.Name][value.Name]',
							'title'=>'{{value.Title}}',
							'name' => '{{value.Name}}',
							'tip'=>'value["Input Tip"]',
							'regex'=>'value["Input Regex"]',
							'type'=>'{{value["Input Type"]}}',
							'required'=>'value["Input Required"]',
							'regex_error'=>'value["Input Regex Error Message"]',
							'order'=>'value["Input Order"]'
						),true)*/
							$this->load->view('student_info_form_input',array(
								'model'=>'input[currCategory.Table.Name][value.Name]',
								'input'=>'value',
								'name'=>'{{currCategory.Table.Name}}{{value.Name}}',
								'error_name'=>'currCategory.Table.Name+value.Name'
							),true);
						?>
					</div>
					
					<md-content ng-if="value['Input Type'] == 'FE'">
						<span>{{value.FE.Table.Title}}</span>
						<p ng-if="value['Input Tip']!=''" class="md-caption">({{value['Input Tip']}})</p>
						<div layout="column" layout-padding layout-margin>
							<div layout = "column" style="border:1px solid lightgray" flex layout-align="start stretch" ng-repeat="(i,x) in getCardinality(currCategory.Table.Name,value.FE.Table.Name) track by $index">
								<span layout-padding>#{{$index+1}}</span>
								<div layout="column" ng-repeat="(k,v) in value.FE.Fields" class="md-no-padding">
									<?=
										$this->load->view('student_info_form_input',array(
											'model'=>'input[currCategory.Table.Name][value.Name][i][v.Name]',
											'input'=>'v',
											'name'=>'{{currCategory.Table.Name}}{{value.Name}}{{i}}{{v.Name}}',
											'error_name'=>'currCategory.Table.Name+value.Name+i+v.Name'
										),true);
									?>
								</div>
							</div>
						</div>
					</md-content>
					
				</div>
			</form>
		</div>
	</div>
	
</div>