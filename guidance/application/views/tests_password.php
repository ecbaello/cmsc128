<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<style>
#tables {
    border-collapse: collapse;
    width: 100%;
}

#tables td, #tables th {
    border: 1px solid #ddd;
    padding: 8px;
}

#tables tr:nth-child(even){background-color: #f2f2f2;}

#tables tr:hover {background-color: #ddd;}

#tables th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #800000;
    color: white;
	font-weight:normal;
}
</style>
<div ng-controller="tests_passwords">
	<div layout="column" layout-padding>
		<h2>Student Passwords</h2>
		<div layout="row">
			<form layout="column" layout-align="center start" layout-fill>
				<div layout="row" layout-align="center center">
					<span layout-padding>Display password(s) of: </span>
					<md-select ng-model="disp.option" class="md-no-margin md-no-padding">
						<md-option value=0 ng-selected="true">Batch</md-option>
						<md-option value=1>Student</md-option>
					</md-select>
					<div layout-margin>
						<md-input-container ng-if="disp.option==0" layout="row" layout-align="center">
							<label>Year</label>
							<input required ng-model="disp.value" type="number"/>
						</md-input-container>
						<md-input-container ng-if="disp.option==1" layout="row" layout-align="center">
							<label>Student Number</label>
							<input required ng-model="disp.value" type="text"/>
						</md-input-container>
					</div>
				</div>
				<div layout="row" layout-align="center" layout-fill>
					<md-button type="submit" ng-click="submit(0)" class="md-primary md-raised md-no-margin" ng-disabled="busy">Search</md-button>
				</div>
			</form>
			<md-divider layout-margin></md-divider>
			<form layout="column" layout-align="center start" layout-fill>
				<div layout="row" layout-align="center center">
					<span layout-padding>Generate password(s) for: </span>
					<md-select ng-model="gen.option" class="md-no-margin md-no-padding">
						<md-option value=0 ng-selected="true">Batch</md-option>
						<md-option value=1>Student</md-option>
					</md-select>
					<div layout-margin>
						<md-input-container ng-if="gen.option==0" layout="row" layout-align="center">
							<label>Year</label>
							<input required ng-model="gen.value" type="number"/>
						</md-input-container>
						<md-input-container ng-if="gen.option==1" layout="row" layout-align="center">
							<label>Student Number</label>
							<input required ng-model="gen.value" type="text"/>
						</md-input-container>
					</div>
				</div>
				
				<div layout="row" layout-align="center" layout-fill>
					<md-button type="submit" ng-click="submit(1)" class="md-primary md-raised md-no-margin" ng-disabled="busy">Submit</md-button>
				</div>
			</form>
		</div>
	</div>
	<div layout="column" layout-padding>
		<div>
			<a class="md-button md-fab md-raised md-mini" title="Download Student Passwords" ng-click="csv.generate()" ng-href="{{csv.link()}}" download="<?=date('Y-M-d-')?>passwords.csv">
				<i class="fas fa-download"></i>
			</a>
		</div>
		<div >
			<table id="tables" export-csv="csv">
				<tr>
					<th>
						Student Number
					</th>
					<th>
						Last Name
					</th>
					<th>
						First Name
					</th>
					<th>
						Middle Name
					</th>
					<th>
						Password
					</th>
				</tr>
				<tr ng-repeat="(k,v) in passwords">
					<td>
						{{v.username}}
					</td>
					<td>
						{{v.lastname}}
					</td>
					<td>
						{{v.firstname}}
					</td>
					<td>
						{{v.middlename}}
					</td>
					<td>
						{{v.pword}}
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>