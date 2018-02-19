<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<div ng-controller="student_add" layout="row" layout-align="center start" flex ng-init="init()">
	<md-content layout="column" layout-align="start start" flex='20'>
		<md-toolbar layout-padding style="background-color:darkgray">
			<span class="md-headline">Add Student</span>
		</md-toolbar>
		<md-button layout-fill style="text-align:left" ng-repeat="(key,value) in tableData"  ng-click="changeCategory(key)" ng-class="{'md-primary md-raised':key == currCategoryKey}">
			<span layout-padding>{{value.Table.Title}}</span>
		</md-button>
		<md-button layout-fill style="text-align:left" ng-click="alert(' ')" >
			<span layout-padding>Test</span>
		</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill>
		<div>
			<h2 class="md-headline">
				<span>Student Information: {{currCategory.Table.Title}}<span>
			</h2>
		</div>
		<div layout-fill class="md-no-padding">
			<form name="{{currCategory.Table.Name}}">
				<div ng-repeat="(key,value) in currCategory.Fields" layout="row" >
					<md-input-container ng-if="value['Input Type'] != 'AET'" class="md-no-margin" layout-fill>
						<label>{{value.Title}}</label>
						<input ng-model="input[currCategory.Table.Name][value.Name]" type="{{value['Input Type']}}" name="{{value.Name}}" ng-required="{{value['Input Required']}}" ng-pattern="value['Input Regex']"/>
					</md-input-container>
					<div ng-if="value['Input Type'] == 'AET'" class="">
						<span>{{value.AET.Table.Title}}</span>
						<span ng-if></span>
						<div layout="row">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	
</div>