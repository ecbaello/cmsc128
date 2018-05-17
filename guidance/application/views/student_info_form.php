<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<div ng-controller="student_form" layout="row" layout-align="center start" flex ng-init='init(<?=$mode=='manage'?$student_info:""?>)'>
	<md-content layout="column" layout-align="start start" flex='20'>
		<div layout-margin>
		<?php
			if($mode=='add'){
				echo '<h2>Add Students</h2> ';
			}
			if($mode=='manage'){
				echo '<h2>Manage Student:</h2>';
				echo '<h2>'.$student_number.'</h2>';
			}
		?>
		</div>
		<md-button layout-fill style="text-align:left" ng-repeat="(key,value) in tableData"  ng-click="changeCategory(key)" ng-class="{'md-primary md-raised':key == currCategoryKey&&!isSurvey}" title="{{value.Table.Title}}">
			<span layout-padding>{{value.Table.Title}}</span>
		</md-button>
		<div layout="row">
			<a ng-if="'<?=$mode?>'=='manage'" class="md-button md-primary md-raised" href="<?=base_url().'studentinfo/manage/printstudent/'.(isset($student_number)?$student_number:'')?>">Print</a>
			<md-button ng-disabled="busy" class="md-raised md-primary" ng-click="submit('<?=$mode?>','<?=isset($student_id)? $student_id: ""?>')">Submit</md-button>
		</div>
		<div>
		<md-button ng-if="'<?=$mode?>'=='manage'" class=" md-raised md-primary" ng-click="deleteRecord('<?=isset($student_id)? $student_id: ""?>')">
			Delete Record
		</md-button>
		</div>
		<div layout-margin ng-if="'<?=$mode?>'!='add'">
			<h2>Survey Results: </h2>
		</div>
		<md-button layout-fill style="text-align:left" ng-repeat="(key,value) in survey"  ng-click="changeSurvey(key)" ng-class="{'md-primary md-raised':key ==currSurveyKey&&isSurvey}" title="{{value.Category.Title}}">
			<span layout-padding>{{value.Category.Title}}</span>
		</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill  ng-if="!isSurvey">
		<div>
			<h2 class="md-headline">
				<span>Student Information: {{currCategory.Table.Title}}<span>
			</h2>
		</div>
		<div layout-fill class="md-no-padding">
			<form name="student">
				<div ng-repeat="(key,value) in currCategory.Fields | orderBy:'\u0022Input Order\u0022'">
				
					<div ng-if="value['Input Type'] != 'FE'" class="md-no-margin">
						<?=
							$this->load->view('student_info_form_input',array(
								'model'=>'input[currCategory.Table.Name][value.Name]',
								'input'=>'value',
								'name'=>'{{currCategory.Table.Name}}{{value.Name}}',
								'error_name'=>'currCategory.Table.Name+value.Name'
							),true);
						?>
					</div>
					
					<md-content ng-if="value['Input Type'] == 'FE'">
						<span>{{value.Title}}</span>
						<p ng-if="value['Input Tip']!=''" class="md-caption">({{value['Input Tip']}})</p>
						<div layout="column" layout-padding layout-margin>
							<div layout = "column" style="border:1px solid lightgray" flex layout-align="start stretch" ng-repeat="(i,x) in getCardinality(value.ID) track by $index">
								<span layout-padding>#{{$index+1}}</span>
								<div layout="column" ng-repeat="(k,v) in value.FE.Fields | orderBy:'\u0022Input Order\u0022'" class="md-no-padding">
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
		
		<div layout="row">
			<md-button class="md-raised md-fab md-mini md-no-margin md-no-padding" ng-disabled="currCategoryKey==0" ng-click="categoryNav('left')"><i class="fas fa-caret-left"></i></md-button>
			<div layout-padding> </div>
			<md-button class="md-raised md-fab md-mini md-no-margin md-no-padding" ng-disabled="currCategoryKey==getLength(tableData)-1" ng-click="categoryNav('right')"><i class="fas fa-caret-right"></i></md-button>
		</div>
	</div>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill  ng-if="isSurvey">
	
		<div>
			<h2 class="md-headline">
				<span>Survey Results: {{currSurvey.Category.Title}}<span>
			</h2>
		</div>
		<fieldset layout-fill layout="column">
			<span layout-margin>Raw Result: {{currSurvey['Raw Result']}}</span>
			<form layout="column">
				<div layout="row" layout-align="start center">
					<span layout-margin>Interpretation: </span>
					<md-input-container class="md-no-margin md-no-padding" layout="row" layout-fill>
						<input type="text" ng-model="currSurvey['Interpretation']"/>
					</md-input-container>
				</div>
				<div>
				<md-button class="md-primary md-raised" ng-click="editInterpretation('<?=isset($student_id)? $student_id: ""?>',currSurvey.Category.ID)" type="submit">Edit Interpretation</md-button>
				</div>
			</form>
		</fieldset>
		<div ng-if="currSurvey.Category.Tip != null">
			<span>{{currSurvey.Category.Tip}}</span>
		</div>
		<div layout-fill layout="column">
			<div ng-repeat="(qaIndex,qa) in currSurvey.Answers">
				<p style="margin-left:0.25in">{{qa.Question}}: {{qa.Answer}}</p>
			</div>
		</div>
	
	</div>
	
</div>