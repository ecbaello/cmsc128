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
    padding: 4px;
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

<div ng-controller="survey_form" layout="row" layout-align="center start" layout-padding layout-margin>
	
	<form method="post">
		<table class="tables">
			<tr>
				<td colspan="2"><h3>Demographic Factor: Risk Factors</h3></td>
			</tr>
			<tr>
				<td>In the past six months, I have:</td>
			</tr>
			<tr>
				<td>felt so hopeless that there are no solution to my problems</td>
				<td>
					<input type="radio" value="1" name="DFRF1">Yes
					<input type="radio" value="0" name="DFRF1">No
				</td>
			</tr>
			<tr>
				<td>felt so alone that there is no one to help me</td>
				<td>
					<input type="radio" value="1" name="DFRF2">Yes
					<input type="radio" value="0" name="DFRF2">No
				</td>
			</tr>
			<tr>
				<td>experienced financial difficulties</td>
				<td>
					<input type="radio" value="1" name="DFRF3">Yes
					<input type="radio" value="0" name="DFRF3">No
				</td>
			</tr>
			<tr>
				<td>experienced personal and/or family health challenges</td>
				<td>
					<input type="radio" value="1" name="DFRF4">Yes
					<input type="radio" value="0" name="DFRF4">No
				</td>
			</tr>
			<tr>
				<td>experienced death in the family</td>
				<td>
					<input type="radio" value="1" name="DFRF5">Yes
					<input type="radio" value="0" name="DFRF5">No
				</td>
			</tr>
			<tr>
				<td>thought of suicide</td>
				<td>
					<input type="radio" value="1" name="DFRF6">Yes
					<input type="radio" value="0" name="DFRF6">No
				</td>
			</tr>
			<tr>
				<td>experienced parental disengagement</td>
				<td>
					<input type="radio" value="1" name="DFRF7">Yes
					<input type="radio" value="0" name="DFRF7">No
				</td>
			</tr>
			<tr>
				<td>unresolved family issues</td>
				<td>
					<input type="radio" value="1" name="DFRF8">Yes
					<input type="radio" value="0" name="DFRF8">No
				</td>
			</tr>
			<tr>
				<td>experienced poor school/academic performance</td>
				<td>
					<input type="radio" value="1" name="DFRF9">Yes
					<input type="radio" value="0" name="DFRF9">No
				</td>
			</tr>
			<tr>
				<td>poor peer/social relationship</td>
				<td>
					<input type="radio" value="1" name="DFRF10">Yes
					<input type="radio" value="0" name="DFRF10">No
				</td>
			</tr>
			<tr>
				<td>romantic relationship problems</td>
				<td>
					<input type="radio" value="1" name="DFRF11">Yes
					<input type="radio" value="0" name="DFRF11">No
				</td>
			</tr>
			<tr>
				<td colspan="2"><h3>Demographic Factor: Protective Factors</h3></td>
			</tr>
			<tr>
				<td>I believe that I have:</td>
			</tr>
			<tr>
				<td>Strong family connectedness and support</td>
				<td>
					<input type="radio" value="0" name="DFPF1">Not true of me
					<input type="radio" value="1" name="DFPF1">Sometimes true of me
					<input type="radio" value="2" name="DFPF1">Always true of me
				</td>
			</tr>
			<tr>
				<td>enhanced social support</td>
				<td>
					<input type="radio" value="0" name="DFPF2">Not true of me
					<input type="radio" value="1" name="DFPF2">Sometimes true of me
					<input type="radio" value="2" name="DFPF2">Always true of me
				</td>
			</tr>
			<tr>
				<td>positive coping skills</td>
				<<td>
					<input type="radio" value="0" name="DFPF3">Not true of me
					<input type="radio" value="1" name="DFPF3">Sometimes true of me
					<input type="radio" value="2" name="DFPF3">Always true of me
				</td>
			</tr>
			<tr>
				<td>positive problem-solving skills</td>
				<td>
					<input type="radio" value="0" name="DFPF4">Not true of me
					<input type="radio" value="1" name="DFPF4">Sometimes true of me
					<input type="radio" value="2" name="DFPF4">Always true of me
				</td>
			</tr>
			<tr>
				<td>excellent conflict resolution and non-violent handling of disputes</td>
				<td>
					<input type="radio" value="0" name="DFPF5">Not true of me
					<input type="radio" value="1" name="DFPF5">Sometimes true of me
					<input type="radio" value="2" name="DFPF5">Always true of me
				</td>
			</tr>
			<tr>
				<td>personal, social, cultural and religious beliefs that support life preservation</td>
				<td>
					<input type="radio" value="0" name="DFPF6">Not true of me
					<input type="radio" value="1" name="DFPF6">Sometimes true of me
					<input type="radio" value="2" name="DFPF6">Always true of me
				</td>
			</tr>
			<tr>
				<td>confidence in the importance of help-seeking behavior</td>
				<td>
					<input type="radio" value="0" name="DFPF7">Not true of me
					<input type="radio" value="1" name="DFPF7">Sometimes true of me
					<input type="radio" value="2" name="DFPF7">Always true of me
				</td>
			</tr>
			<tr>
				<td colspan="2"><h3>Ideation</h3></td>
				<td></td>
			</tr>
			<tr>
				<td>Thoughts of dying</td>
				<td>
					<input type="radio" value="0" name="IDTN1">Not true
					<input type="radio" value="1" name="IDTN1">Sometimes true
					<input type="radio" value="2" name="IDTN1">Often true
					<input type="radio" value="3" name="IDTN1">Always true
				</td>
			</tr>
			<tr>
				<td>Wishing I am dead</td>
				<td>
					<input type="radio" value="0" name="IDTN2">Not true
					<input type="radio" value="1" name="IDTN2">Sometimes true
					<input type="radio" value="2" name="IDTN2">Often true
					<input type="radio" value="3" name="IDTN2">Always true
				</td>
			</tr>
			<tr>
				<td>Thinking about the chances of committing suicide</td>
				<td>
					<input type="radio" value="0" name="IDTN3">Not true
					<input type="radio" value="1" name="IDTN3">Sometimes true
					<input type="radio" value="2" name="IDTN3">Often true
					<input type="radio" value="3" name="IDTN3">Always true
				</td>
			</tr>
			<tr>
				<td>Thinking about how I would be gone</td>
				<td>
					<input type="radio" value="0" name="IDTN4">Not true
					<input type="radio" value="1" name="IDTN4">Sometimes true
					<input type="radio" value="2" name="IDTN4">Often true
					<input type="radio" value="3" name="IDTN4">Always true
				</td>
			</tr>
			<tr>
				<td>Thinking about writing down my last wishes before I die</td>
				<td>
					<input type="radio" value="0" name="IDTN5">Not true
					<input type="radio" value="1" name="IDTN5">Sometimes true
					<input type="radio" value="2" name="IDTN5">Often true
					<input type="radio" value="3" name="IDTN5">Always true
				</td>
			</tr>
			<tr>
				<td>Thinking of giving away my possessions</td>
				<td>
					<input type="radio" value="0" name="IDTN6">Not true
					<input type="radio" value="1" name="IDTN6">Sometimes true
					<input type="radio" value="2" name="IDTN6">Often true
					<input type="radio" value="3" name="IDTN6">Always true
				</td>
			</tr>
			<tr>
				<td>Thinking about how to prepare the things I need to carry out plans of dying</td>
				<td>
					<input type="radio" value="0" name="IDTN7">Not true
					<input type="radio" value="1" name="IDTN7">Sometimes true
					<input type="radio" value="2" name="IDTN7">Often true
					<input type="radio" value="3" name="IDTN7">Always true
				</td>
			</tr>
			<tr>
				<td>Thinking about the best time and day to die</td>
				<td>
					<input type="radio" value="0" name="IDTN8">Not true
					<input type="radio" value="1" name="IDTN8">Sometimes true
					<input type="radio" value="2" name="IDTN8">Often true
					<input type="radio" value="3" name="IDTN8">Always true
				</td>
			</tr>
			<tr>
				<td>Wishing that I have the courage to be gone</td>
				<td>
					<input type="radio" value="0" name="IDTN9">Not true
					<input type="radio" value="1" name="IDTN9">Sometimes true
					<input type="radio" value="2" name="IDTN9">Often true
					<input type="radio" value="3" name="IDTN9">Always true
				</td>
			</tr>
			<tr>
				<td>Thinking of how I can be successful in carrying-out my plans of dying</td>
				<td>
					<input type="radio" value="0" name="IDTN10">Not true
					<input type="radio" value="1" name="IDTN10">Sometimes true
					<input type="radio" value="2" name="IDTN10">Often true
					<input type="radio" value="3" name="IDTN10">Always true
				</td>
			</tr>
			<tr>
				<td colspan="2"><h3>Attempt</h3></td>
			</tr>
			<tr>
				<td>Have you ever tried inflicting injury upon yourself?</td>
				<td>
					<input type="radio" value="Yes" name="ATMP1">Yes
					<input type="radio" value="No" name="ATMP1">No
				</td>
			</tr>
			<tr>
				<td>If yes, what was the method/s used?</td>
				<td><input type="text" name="suimeth"></td>
			</tr>
			<tr>
				<td>How many times have you attempted suicide?</td>
				<td><input type="number" name="suicount"></td>
			</tr>
			<tr>
				<td>When was the most recent attempt</td>
				<td><input type="text" name="suiat"></td>
			</tr>
			<tr>
				<td>Did you require medical attention after the attempt?</td>
				<td>
					<input type="radio" value="Yes" name="ATMP2">Yes
					<input type="radio" value="No" name="ATMP2">No
				</td>
			</tr>
			<tr>
				<td>Did you tell anyone about the attempt?</td>
				<td>
					<input type="radio" value="Yes" name="ATMP3">Yes
					<input type="text" name="suitell">
					<input type="radio" value="No" name="ATMP3">No
				</td>
			</tr>
			<tr>
				<td>Did you talk to a councelor or some other person after your attempt?</td>
				<td>
					<input type="radio" value="Yes" name="ATMP4">Yes
					<input type="text" name="suitalk">
					<input type="radio" value="No" name="ATMP4">No
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
					<input type="radio" value="0" name="VRFL1">Not at all important
					<input type="radio" value="1" name="VRFL1">Somewhat unimportant
					<input type="radio" value="2" name="VRFL1">Somewhat important
					<input type="radio" value="3" name="VRFL1">Extremely important
				</td>
			</tr>
			<tr>
				<td>I believe I can cope with my problems</td>
				<td>
					<input type="radio" value="0" name="VRFL2">Not at all important
					<input type="radio" value="1" name="VRFL2">Somewhat unimportant
					<input type="radio" value="2" name="VRFL2">Somewhat important
					<input type="radio" value="3" name="VRFL2">Extremely important
				</td>
			</tr>
			<tr>
				<td>I believe I am completely worthy of love</td>
				<td>
					<input type="radio" value="0" name="VRFL3">Not at all important
					<input type="radio" value="1" name="VRFL3">Somewhat unimportant
					<input type="radio" value="2" name="VRFL3">Somewhat important
					<input type="radio" value="3" name="VRFL3">Extremely important
				</td>
			</tr>
			<tr>
				<td>I believe suicide is not the only way to solve my problems</td>
				<td>
					<input type="radio" value="0" name="VRFL4">Not at all important
					<input type="radio" value="1" name="VRFL4">Somewhat unimportant
					<input type="radio" value="2" name="VRFL4">Somewhat important
					<input type="radio" value="3" name="VRFL4">Extremely important
				</td>
			</tr>
			<tr>
				<td>I believe only God has the right to end a life</td>
				<td>
					<input type="radio" value="0" name="VRFL5">Not at all important
					<input type="radio" value="1" name="VRFL5">Somewhat unimportant
					<input type="radio" value="2" name="VRFL5">Somewhat important
					<input type="radio" value="3" name="VRFL5">Extremely important
				</td>
			</tr>
			<tr>
				<td>I believe I can endure the pain and life changes</td>
				<td>
					<input type="radio" value="0" name="VRFL6">Not at all important
					<input type="radio" value="1" name="VRFL6">Somewhat unimportant
					<input type="radio" value="2" name="VRFL6">Somewhat important
					<input type="radio" value="3" name="VRFL6">Extremely important
				</td>
			</tr>
			<tr>
				<td>I value my family too much and could not bear to leave them</td>
				<td>
					<input type="radio" value="0" name="VRFL7">Not at all important
					<input type="radio" value="1" name="VRFL7">Somewhat unimportant
					<input type="radio" value="2" name="VRFL7">Somewhat important
					<input type="radio" value="3" name="VRFL7">Extremely important
				</td>
			</tr>
			<tr>
				<td>I believe I am not a burden to my family</td>
				<td>
					<input type="radio" value="0" name="VRFL8">Not at all important
					<input type="radio" value="1" name="VRFL8">Somewhat unimportant
					<input type="radio" value="2" name="VRFL8">Somewhat important
					<input type="radio" value="3" name="VRFL8">Extremely important
				</td>
			</tr>
			<tr>
				<td>Other people would think I am weak and selfish</td>
				<td>
					<input type="radio" value="0" name="VRFL9">Not at all important
					<input type="radio" value="1" name="VRFL9">Somewhat unimportant
					<input type="radio" value="2" name="VRFL9">Somewhat important
					<input type="radio" value="3" name="VRFL9">Extremely important
				</td>
			</tr>
		</table>
	</form>
	
</div>