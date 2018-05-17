<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en" ng-app="app">

<head ng-controller="initializer" ng-init="init('<?=base_url()?>','<?= $this->security->get_csrf_token_name()?>','<?= $this->security->get_csrf_hash()?>')">
	<title>University of the Philippines Baguio Student Registry</title>
	
	<link rel="shortcut icon" href="<?= base_url().'logos/up.png' ?>" type="image/x-icon">
	<link rel="icon" href="<?= base_url().'logos/up.png' ?>" type="image/x-icon">
	
	<link rel="stylesheet" href="<?= base_url().'css/fontawesome-all.min.css' ?>"/>
	<link rel="stylesheet" href="<?= base_url().'css/roboto.css' ?>"/>
	<link rel="stylesheet" href="<?= base_url().'css/angular-material.min.css' ?>"/>

	<script src="<?= base_url().'js/angular.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-animate.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-aria.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-messages.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-material.min.js' ?>"></script>
	<script src="<?= base_url().'js/ng-table-to-csv.min.js' ?>"></script>
	<script src="<?= base_url().'js/app.js' ?>"></script>
	<script src="<?= base_url().'js/app_studentinfo.js' ?>"></script>
	<script src="<?= base_url().'js/app_survey.js' ?>"></script>

	<link rel="stylesheet" href="<?= base_url().'css/paper.css' ?>"/>
	<style>@page { size: legal }</style>
	<style>
		.md-no-margin{
			margin:0;
		}
		.md-no-padding{
			padding:0;
		}
		
	</style>
	
</head>


<style>
.fetables {
    border-collapse: collapse;
	width:98%;
	table-layout:fixed;
}

.fetables td {
    border: 1px solid black;
    padding: 4px;
	word-wrap: break-word;
}
</style>


<body class="legal">

	<section class="sheet padding-10mm" ng-controller="student_print" ng-init='init(<?=$student_info?>)'>
		<div layout="column" layout-align="start center">
			<h3 class="md-title md-no-margin"> UNIVERSITY of the PHILIPPINES BAGUIO</h3>
			<span>Office of Counseling & Guidance</span>
			<span class="md-body-2">Baguio City</span>
		</div>
		
		<div style="margin:0.25in 0in 0.25in 0in;">
			
			<fieldset layout="column" style="font-size:0.9em">
				<p ng-repeat="(qaIndex,qa) in data['Survey Answers']">
					<span style="font-weight:bold">{{qa.Category.Title}}</span>
					<span>{{qa.Interpretation}}</span>
				</p>
			</fieldset>
			
			<div ng-repeat="(fIndex,f) in form" style="margin:0.5">
				<div layout="row" layout-align="center center" layout-margin style="border-style:dashed;border-width:1px 0 1px 0;">
					<span style="font-weight:bold">{{f.Table.Title}}</span>
				</div>
				<div style="margin-left:0.05in;font-size:0.9em;" layout="row" layout-wrap>
					<div style="margin:10px 0 10px 5px" layout="row" ng-repeat="field in f.Fields | orderBy:'\u0022Input Order\u0022'" ng-class="{'flex-100':field['Input Type']=='FE'}">
						<span style="margin-right:5px;font-weight:bold;" ng-if="field['Input Type']!='FE'">
							{{field.Title}}
						</span>
						<div style="border-style:solid;border-width:0 0 1px 0;min-width:50px;margin-right:5px;" ng-if="field['Input Type']!='FE'" class="separable">
							<span style="padding-right:10px" ng-if="field['Input Type']!='MC'">{{data[f.Table.Name][field.Name]}}</span>
							<span style="padding-right:10px" ng-if="field['Input Type']=='MC'">
								<span ng-if="field.MC.Type==<?=MCTypes::SINGLE?>">
									{{data[f.Table.Name][field.Name]}}
								</span>
								<span ng-if="field.MC.Type==<?=MCTypes::MULTIPLE?>">
									<span ng-repeat="(cIndex,choice) in data[f.Table.Name][field.Name]" ng-if="cIndex!='Custom'">
										<span ng-if="!$first"> , </span>
										<span>{{choice}}</span>
									</span>
									<span> . </span>
									<span ng-repeat="choice in data[f.Table.Name][field.Name].Custom">
										<span ng-if="!$first"> , </span>
										<span>{{choice}}</span>
									</span>
								</span>
							</span>
						</div>
						<div layout="column" ng-if="field['Input Type']=='FE'" flex class="separable">
							<div style="font-weight:bold;"> {{field.Title}} </div>
							<div layout="row" layout-align="center center" ng-repeat="(entityKey,entity) in data[f.Table.Name][field.Name]">
							<table class="fetables">
								<caption style="text-align:left">{{field.Title}} #{{entityKey+1}}</caption>
								<tr ng-repeat="(FEFieldKey, FEField) in field.FE.Fields">
									<td style="width:25%;max-width:25%;font-weight:bold;">{{FEField.Title}}</td>
									<td>
										<span ng-if="FEField['Input Type']!='MC'">
											{{entity[FEField['Name']]}}
										</span>
										<span style="padding-right:10px" ng-if="FEField['Input Type']=='MC'">
											<span ng-if="FEField.MC.Type==<?=MCTypes::SINGLE?>">
												{{entity[FEField['Name']]}}
											</span>
											<span ng-if="FEField.MC.Type==<?=MCTypes::MULTIPLE?>">
												<span ng-repeat="(cIndex,choice) in entity[FEField['Name']]" ng-if="cIndex!='Custom'">
													<span ng-if="!$first"> , </span>
													<span>{{choice}}</span>
												</span>
												<span> . </span>
												<span ng-repeat="choice in entity[FEField['Name']].Custom">
													<span ng-if="!$first"> , </span>
													<span>{{choice}}</span>
												</span>
											</span>
										</span>
									</td>		
								</tr>
							</table>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		
</section>
</body>

</html>