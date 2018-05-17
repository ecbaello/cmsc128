<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>

<div layout-margin layout-padding>
	LOGGED-IN AS: <?=$this->ion_auth->user()->row()->username?><span class="md-subhead"> ( <a style="font-color:black" href="<?=base_url().'main/logout'?>">Logout</a> )</span>	
</div>

<?php if ($answered!=null): ?>
<div layout-padding layout-margin>
	Survey already answered.
</div>

<?php else: ?>
<div ng-controller="survey_form" layout="column" layout-align="center start" layout-padding layout-margin ng-init="init()">

	<div layout="column" layout-align="center center">
		<h2 class="md-no-margin" style="margin:0 0 0.25in 0">
		UPB Risk_Protective Assessment Survey
		</h2>
		
		The following is a list of statements about life in general and its challenges that college students like you may have thought about. Kindly read the statements carefully and decide how it is applicable to you. There are no correct or incorrect answers.
	</div>
	
	<form layout="column" layout-fill>
		<md-card ng-repeat="(index,section) in survey">
			<md-toolbar layout="row" layout-align="center center" style="background-color:lightgray">
				<span layout-margin>{{section.Category.Title}}</span>
			</md-toolbar>
			<md-card-content layout="column">
				<span layout-margin>{{section.Category.Tip}}</span>
				<div ng-repeat="(qIndex,question) in section.Questions track by $index" layout-padding ng-if=" checkDependency(index,qIndex)">
					<span>
						{{$index+1}}.) {{question.Question}}
					</span>
					<div layout-margin flex>
						<md-radio-group required ng-model="answers[question['Question ID']]" ng-if="question.Custom==0" layout="row">
							<md-radio-button ng-repeat="answer in section.Answers" value="{{answer['Answer ID']}}">
								{{answer['Value']}}
							</md-radio-button>
						</md-radio-group>
						<md-input-container ng-if="question.Custom==1" class="md-no-margin md-no-padding" layout-fill>
							<input type="text" required ng-model="answers[question['Question ID']]"></input>
						</md-input-container>
					</div>
				</div>
			</md-card-content>
		</md-card>
		<div layout="row" layout-align="center center" layout-margin>
			<md-button class="md-raised md-primary" type="submit" ng-click="submit('<?=$this->ion_auth->user()->row()->username?>')">
				Submit
			</md-button>
		</div>
	</form>
	
</div>
<?php endif;?>