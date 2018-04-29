<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div layout="row" ng-controller="student_recbin" flex>
	<md-content layout="column" layout-align="start stretch" flex="20">
		<div layout="column" layout-margin >
			<h2>Recycle Bin</h2>
		</div>
		<md-button layout="row" layout-align="start start" class="md-no-margin" ng-class="{'md-primary md-raised':currCategory=='Tables'}" ng-click="currCategory='Tables'">
			<span style="padding-left:8px">Tables</span>
		</md-button>
		<md-button layout="row" layout-align="start start" class="md-no-margin" ng-class="{'md-primary md-raised':currCategory=='Fields'}" ng-click="currCategory='Fields'">
			<span style="padding-left:8px">Fields</span>
		</md-button>
		<md-button layout="row" layout-align="start start" class="md-no-margin" ng-class="{'md-primary md-raised':currCategory=='Records'}" ng-click="currCategory='Records'">
			<span style="padding-left:8px">Records</span>
		</md-button>
	</md-content>
	<md-content flex layout-margin>
		<div layout-margin>
			<h2 class="md-headline">{{currCategory}}</h2>
		</div>
		<div>
		</div>
	</md-content>
</div>