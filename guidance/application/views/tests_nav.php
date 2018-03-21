<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div ng-controller="tests_nav" ng-init="init()" flex>
	<div flex layout="row" layout-align="center center" layout-padding>
		<span class="md-headline">UPB Guidance Tests</span> <span><md-button title="Add Tests" class="md-primary md-raised md-fab" ng-click="addDialog()"><i class="fas fa-plus"></i></md-button></span>
	</div>
	<div>
		<md-content layout="row" layout-wrap layout-align="center center" layout-margin>
			<md-card ng-repeat="(i,v) in tests" flex="25">
				<md-card-title layout-padding layout="row" layout-align="center center" style="background-color:maroon">
					<md-content style="background-color:inherit">
						<span class="md-headline">{{v.Title}}<span>
					</md-content>
				</md-card-title>
				<md-card-content class="md-primary" layout="column" layout-align"center center">
					<div layout-padding layout-align="center center">
						<p>{{v.Desc}}</p>	
					</div>
					<div layout="row" layout-align="center">
						<md-button class="md-no-margin">Take</md-button>
						<md-button class="md-no-margin" ng-click="edit(v.Title)">Edit</md-button>
					</div>
				</md-card-content>
			</md-card>
		</md-content>

		
	</div>
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="addDialog">
			<md-dialog flex="40">
				<md-toolbar style="background-color:maroon" layout-padding>
					<h4 class="md-no-margin" style="color:white">ADD TEST</h4>
				</md-toolbar>
				<md-dialog-content layout="column" layout-padding>
				<form>
					<div layout="column">
						
						<md-input-container class="md-no-margin">
							<label>Title</label>
							<input ng-model="newTest.Title" type="text" required></input>
						</md-input-container>
						<md-input-container class="md-no-margin">
							<label>Description</label>
							<textarea ng-model="newTest.Description" md-maxlength="300"></textarea>
						</md-input-container>
						
					</div>
					<div layout="row" layout-align="end center">
						<md-button class="md-no-margin" ng-click="closeDialog()" >Cancel</md-button>
						<md-button class="md-no-margin" ng-click="add()" type="submit" ng-disabled="!newTest.Title">Submit</md-button>
					</div>
				</form>
				</md-dialog-content>
			</md-dialog>
		</div>
	</div>
</div>