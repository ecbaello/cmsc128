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
						<md-option value="batch" ng-selected="true">Batch</md-option>
						<md-option value="student">Student</md-option>
					</md-select>
				</div>
				<div>
					<md-input-container ng-if="disp.option=='batch'" class="md-no-padding md-no-margin">
						<label>Year</label>
						<input ng-model="disp.value" type="number"/>
					</md-input-container>
					<md-input-container ng-if="disp.option=='student'" class="md-no-padding md-no-margin">
						<label>Student Number</label>
						<input ng-model="disp.value" type="text"/>
					</md-input-container>
				</div>
				<div layout="row" layout-align="center">
					<md-button type="submit" ng-click="search()" class="md-primary md-raised md-no-margin">Search</md-button>
				</div>
			</form>
			<md-divider layout-margin></md-divider>
			<form layout="column" layout-align="center start" layout-fill>
				<div layout="row" layout-align="center center">
					<span layout-padding>Generate password(s) for: </span>
					<md-select ng-model="gen.option" class="md-no-margin md-no-padding">
						<md-option value="batch" ng-selected="true">Batch</md-option>
						<md-option value="student">Student</md-option>
					</md-select>
				</div>
				<div>
					<md-input-container ng-if="gen.option=='batch'" class="md-no-padding md-no-margin">
						<label>Year</label>
						<input ng-model="gen.value" type="number"/>
					</md-input-container>
					<md-input-container ng-if="gen.option=='student'" class="md-no-padding md-no-margin">
						<label>Student Number</label>
						<input ng-model="gen.value" type="text"/>
					</md-input-container>
				</div>
				<div layout="row" layout-align="center">
					<md-button type="submit" ng-click="submit()" class="md-primary md-raised md-no-margin">Submit</md-button>
				</div>
			</form>
		</div>
	</div>
	<div>
		<table id="tables">
			<tr>
				<th>
					Student Number
				</th>
				<th>
					Password
				</th>
			</tr>
			<tr ng-repeat="(k,v) in results">
				<td ng-repeat="v2 in v">
				</td>
			</tr>
		</table>
	</div>
</div>