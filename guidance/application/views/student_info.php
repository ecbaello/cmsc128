<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<div ng-controller="student" layout="row" layout-align="center start" flex ng-init="init()">
	<md-content layout="column" layout-align="start start" flex='20'>
		<md-toolbar layout-padding style="background-color:darkgray">
			<span class="md-headline">Categories</span>
		</md-toolbar>
		<md-button class="md-raised md-primary" layout-fill style="text-align:left" ng-repeat="(key,value) in tableData">
			<span layout-padding>{{value.Title}}</span>
		</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex='80' layout-padding>
		<h2 class="md-headline" layout-padding>
			<span>Student Information: {{currentCategory}}<span>
		</h2>
	</div>
	
</div>