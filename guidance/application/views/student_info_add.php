<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<div ng-controller="student_add" layout="row" layout-align="center start" flex ng-init="init()">
	<md-content layout="column" layout-align="start start" flex='20'>
		<md-toolbar layout-padding style="background-color:darkgray">
			<span class="md-headline">Categories</span>
		</md-toolbar>
		<md-button layout-fill style="text-align:left" ng-repeat="(key,value) in tableData"  ng-click="changeCategory(value.Table.Title)" ng-class="{'md-primary md-raised':currCategory == value.Table.Title}">
			<span layout-padding>{{value.Table.Title}}</span>
		</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex='80' layout-padding>
		<h2 class="md-headline" layout-padding>
			<span>Student Information: {{currCategory}}<span>
		</h2>
	</div>
	
</div>