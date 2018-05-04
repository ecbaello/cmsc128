<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>


<style>
.tables {
    border-collapse: collapse;
    width: 100%;
}

.tables td, .tables th {
    border: 1px solid #ddd;
    padding: 10px;
}

.tables tr:nth-child(even){background-color: #f2f2f2;}

.tables tr:hover {background-color: #ddd;}

.tables th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: center;
    background-color: #800000;
    color: white;
}

.tables th:hover{
	cursor:pointer;
}
</style>

<div ng-controller="survey_form" layout="column" layout-align="center start" layout-padding layout-margin>
	
	<div>
		LOGGED-IN AS: <?=$this->ion_auth->user()->row()->username?>
	</div>
	
	<form method="post">
		<table class="tables">
			<tr>
				<td colspan="2"><h3>Demographic Factor: Risk Factors</h3></td>
			</tr>
			<tr>
				<td>In the past six months, I have:</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">felt so hopeless that there are no solution to my problems</span></td>
				<td>
					<md-radio-group ng-model="DFRF[0]" layout="row" ng-required>
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">felt so alone that there is no one to help me</span></td>
				<td>
					<md-radio-group ng-model="DFRF[1]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">experienced financial difficulties</span></td>
				<td>
					<md-radio-group ng-model="DFRF[2]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">experienced personal and/or family health challenges</span></td>
				<td>
					<md-radio-group ng-model="DFRF[3]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">experienced death in the family</span></td>
				<td>
					<md-radio-group ng-model="DFRF[4]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">thought of suicide</span></td>
				<td>
					<md-radio-group ng-model="DFRF[5]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">experienced parental disengagement</span></td>
				<td>
					<md-radio-group ng-model="DFRF[6]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">unresolved family issues</span></td>
				<td>
					<md-radio-group ng-model="DFRF[7]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">experienced poor school/academic </span></td>
				<td>
					<md-radio-group ng-model="DFRF[8]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">poor peer/social relationship</span></td>
				<td>
					<md-radio-group ng-model="DFRF[9]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">romantic relationship problems</span></td>
				<td>
					<md-radio-group ng-model="DFRF[10]" layout="row">
						<md-radio-button value="1">Yes</md-radio-button>
						<md-radio-button value="0">No</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td colspan="2"><h3>Demographic Factor: Protective Factors</h3></td>
			</tr>
			<tr>
				<td>I believe that I have:</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">Strong family connectedness and support</span></td>
				<td>
					<md-radio-group ng-model="DFPF[0]" layout="row">
						<md-radio-button value="0">Not true of me</md-radio-button>
						<md-radio-button value="1">Sometimes true of me</md-radio-button>
						<md-radio-button value="2">Always true of me</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">enhanced social support</span></td>
				<td>
					<md-radio-group ng-model="DFPF[1]" layout="row">
						<md-radio-button value="0">Not true of me</md-radio-button>
						<md-radio-button value="1">Sometimes true of me</md-radio-button>
						<md-radio-button value="2">Always true of me</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">positive coping skills</span></td>
				<td>
					<md-radio-group ng-model="DFPF[2]" layout="row">
						<md-radio-button value="0">Not true of me</md-radio-button>
						<md-radio-button value="1">Sometimes true of me</md-radio-button>
						<md-radio-button value="2">Always true of me</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">positive problem-solving skills</span></td>
				<td>
					<md-radio-group ng-model="DFPF[3]" layout="row">
						<md-radio-button value="0">Not true of me</md-radio-button>
						<md-radio-button value="1">Sometimes true of me</md-radio-button>
						<md-radio-button value="2">Always true of me</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">excellent conflict resolution and non-violent handling of disputes</span></td>
				<td>
					<md-radio-group ng-model="DFPF[4]" layout="row">
						<md-radio-button value="0">Not true of me</md-radio-button>
						<md-radio-button value="1">Sometimes true of me</md-radio-button>
						<md-radio-button value="2">Always true of me</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">personal, social, cultural and religious beliefs that support life preservation</span></td>
				<td>
					<md-radio-group ng-model="DFPF[5]" layout="row">
						<md-radio-button value="0">Not true of me</md-radio-button>
						<md-radio-button value="1">Sometimes true of me</md-radio-button>
						<md-radio-button value="2">Always true of me</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in;">confidence in the importance of help-seeking behavior</span></td>
				<td>
					<md-radio-group ng-model="DFPF[6]" layout="row">
						<md-radio-button value="0">Not true of me</md-radio-button>
						<md-radio-button value="1">Sometimes true of me</md-radio-button>
						<md-radio-button value="2">Always true of me</md-radio-button>
					<md-radio-group>
				</td>
			</tr>
			<tr>
				<td colspan="2"><h3>Ideation</h3></td>
				<td></td>
			</tr>
			<tr>
				<td>Thoughts of dying</td>
				<td>
					<md-radio-group ng-model="IDTN[0]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Wishing I am dead</td>
				<td>
					<md-radio-group ng-model="IDTN[1]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Thinking about the chances of committing suicide</td>
				<td>
					<md-radio-group ng-model="IDTN[2]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Thinking about how I would be gone</td>
				<td>
					<md-radio-group ng-model="IDTN[3]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Thinking about writing down my last wishes before I die</td>
				<td>
					<md-radio-group ng-model="IDTN[4]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Thinking of giving away my possessions</td>
				<td>
					<md-radio-group ng-model="IDTN[5]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Thinking about how to prepare the things I need to carry out plans of dying</td>
				<td>
					<md-radio-group ng-model="IDTN[6]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Thinking about the best time and day to die</td>
				<td>
					<md-radio-group ng-model="IDTN[7]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Wishing that I have the courage to be gone</td>
				<td>
					<md-radio-group ng-model="IDTN[8]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Thinking of how I can be successful in carrying-out my plans of dying</td>
				<td>
					<md-radio-group ng-model="IDTN[9]" layout="row">
						<md-radio-button value="0">Not true</md-radio-button>
						<md-radio-button value="1">Sometimes true</md-radio-button>
						<md-radio-button value="2">Often true</md-radio-button>
						<md-radio-button value="3">Always true</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td colspan="2"><h3>Attempt</h3></td>
			</tr>
			<tr>
				<td>Have you ever tried inflicting injury upon yourself?</td>
				<td>
					<md-radio-group ng-model="ATMP[0]" layout="row">
						<md-radio-button value="0">Yes</md-radio-button>
						<md-radio-button value="1">No</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>If yes, what was the method/s used?</td>
				<td>
					<div layout="column" layout-align="center">
						<md-input-container class="md-no-margin" layout="row" layout-align="start center">
							<input type="text" ng-model="ATMP[1]">
						</md-input-container>
					</div>
				</td>
			</tr>
			<tr>
				<td>How many times have you attempted suicide?</td>
				<td>
					<div layout="column" layout-align="center">
						<md-input-container class="md-no-margin" layout="row" layout-align="start center">
							<input type="text" ng-model="ATMP[2]">
						</md-input-container>
					</div>
				</td>
			</tr>
			<tr>
				<td>When was the most recent attempt</td>
				<td>
					<div layout="column" layout-align="center">
						<md-input-container class="md-no-margin" layout="row" layout-align="start center">
							<input type="text" ng-model="ATMP[3]">
						</md-input-container>
					</div>
				</td>
			</tr>
			<tr>
				<td>Did you require medical attention after the attempt?</td>
				<td>
					<md-radio-group ng-model="ATMP[4]" layout="row">
						<md-radio-button value="0">Yes</md-radio-button>
						<md-radio-button value="1">No</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Did you tell anyone about the attempt?</td>
				<td>
					<md-radio-group ng-model="ATMP[5]" layout="row">
						<md-radio-button value="0">Yes</md-radio-button>
						<md-radio-button value="1">No</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in">If yes, who?</td>
				<td>
					<div layout="column" layout-align="center">
						<md-input-container class="md-no-margin" layout="row" layout-align="start center">
							<input type="text" ng-model="ATMP[6]">
						</md-input-container>
					</div>
				</td>
			</tr>
			<tr>
				<td>Did you talk to a councelor or some other person after your attempt?</td>
				<td>
					<md-radio-group ng-model="ATMP[7]" layout="row">
						<md-radio-button value="0">Yes</md-radio-button>
						<md-radio-button value="1">No</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td><span style="padding-left:0.25in">If yes, who?</td>
				<td>
					<div layout="column" layout-align="center">
						<md-input-container class="md-no-margin" layout="row" layout-align="start center">
							<input type="text" ng-model="ATMP[8]">
						</md-input-container>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2"><h3>Validation: Reasons for living</h3></td>
			</tr>
			<tr>
				<td>Please choose an answer that corresponds to indicate the importance of each statement for NOT killing yourself</td>
			</tr>
			<tr>
				<td>I am afraid of the actual act of killing myself (the pain, the blood, the violence)</td>
				<td>
					<md-radio-group ng-model="VRFL[0]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>I believe I can cope with my problems</td>
				<td>
					<md-radio-group ng-model="VRFL[1]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>I believe I am completely worthy of love</td>
				<td>
					<md-radio-group ng-model="VRFL[2]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>I believe suicide is not the only way to solve my problems</td>
				<td>
					<md-radio-group ng-model="VRFL[3]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>I believe only God has the right to end a life</td>
				<td>
					<md-radio-group ng-model="VRFL[4]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>I believe I can endure the pain and life changes</td>
				<td>
					<md-radio-group ng-model="VRFL[5]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>I value my family too much and could not bear to leave them</td>
				<td>
					<md-radio-group ng-model="VRFL[6]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>I believe I am not a burden to my family</td>
				<td>
					<md-radio-group ng-model="VRFL[7]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
			<tr>
				<td>Other people would think I am weak and selfish</td>
				<td>
					<md-radio-group ng-model="VRFL[8]" layout="row">
						<md-radio-button value="0">Not at all important</md-radio-button>
						<md-radio-button value="1">Somewhat unimportant</md-radio-button>
						<md-radio-button value="2">Somewhat important</md-radio-button>
						<md-radio-button value="3">Extremely important</md-radio-button>
					</md-radio-group>
				</td>
			</tr>
		</table>
		<div layout="row" layout-align="center center" layout-margin>
			<md-button class="md-raised md-primary" type="submit" ng-click="submit('<?=$this->ion_auth->user()->row()->username?>')">
				Submit
			</md-button>
		</div>
	</form>
	
</div>