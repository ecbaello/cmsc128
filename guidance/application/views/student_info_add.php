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
	</md-content>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill>
		<div>
			<h2 class="md-headline">
				<span>Student Information: {{currCategory.Table.Title}}<span>
			</h2>
		</div>
		<div layout-fill class="md-no-padding">
			<form>
				<div>
					<md-input-container ng-repeat="(key,value) in currCategory.Fields" layout="row" ng-if="value['Input Type'] != 'hidden'">
						<label>{{value.Title}}</label>
						<input type="{{value['Input Type']}}" required="{{value['Input Required']}}"/>
					</md-input-container>
				</div>
			</form>
		</div>
	</div>
	
</div>