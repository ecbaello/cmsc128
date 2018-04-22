<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="student_form_edit" layout="row" layout-align="center start" flex ng-init='init()'>
	<md-content layout="column" layout-align="start start" flex='20'>
		<div layout-margin>
			<h2>Edit Form</h2>
		</div>
		<md-button layout-fill style="text-align:left" ng-repeat="(key,value) in tables"  ng-click="changeCategory(key)" ng-class="{'md-primary md-raised':key == currCategoryKey}">
			<span layout-padding>{{value.Table.Title}}</span>
		</md-button>
		<md-button class="md-raised md-primary" ng-disabled="busy" ng-click="addTable()">Add Table</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill  ng-if="!isTests">
		<div>
			<h2 class="md-headline">
				<span>Table Title: {{currCategory.Table.Title}}<span>
			</h2>
			<span>Table Name: {{currCategory.Table.Name}}</span>
			<br/>
			<md-button class="md-primary md-raised" ng-disabled="busy" ng-click="deleteTable(key)">
				<span>Delete Table</span>
			</md-button>
			<md-button class="md-primary md-raised" ng-disabled="busy" ng-click="addField()">
				<span>Add Field</span>
			</md-button>
		</div>
		<div layout-fill class="md-no-padding">
			<form name="student">
				<md-card layout="column" ng-repeat="(key,value) in currCategory.Fields | orderBy:'\u0022Input Order\u0022'" ng-if="value['Input Type']!='hidden'">
					<md-toolbar layout="row" layout-align="space-between center" style="background-color:maroon">
						<div layout-padding>
							<md-button ng-if="!value.Essential" ng-click="changeField('delete',key)" class="md-no-margin md-no-padding md-fab md-mini md-primary"><i class="fas fa-times"></i></md-button>
							<span class="md-subhead md-no-margin">{{value['Title']}}</span>
						</div>
						<div layout="row" class="md-subhead" layout-align="center center" layout-padding>
							<span class="md-no-padding">Order: </span>
							<md-select ng-disabled="busy" ng-model="fields[currCategoryKey][key].order" ng-change="changeOrder(value['Input Order'],fields[currCategoryKey][key].order)" class="md-no-margin">
								<md-option ng-repeat="i in getNumber(tables[currCategoryKey].Fields.length) track by $index" value={{$index+1}}>{{$index+1}}</md-option>
							</md-select>
							<md-button class="md-no-margin md-no-padding md-primary md-raised" ng-click="toggleSettings(key)">
								Settings
							</md-button>
						</div>
					</md-toolbar>
					<md-card-content layout="column" layout-padding>
						<md-switch ng-model="value['Input Required']" ng-true-value="1" ng-false-value="0">
							Required?
						</md-switch>
						<md-input-container class="md-no-margin" ng-if="value['Input Type']!='FE'">
							<label>Name</label>
							<input type="text" ng-model="value.Name" disabled></input>
						</md-input-container>
						<md-input-container class="md-no-margin">
							<label>Title</label>
							<input type="text" ng-model="value.Title" ng-disabled="value['Input Type']=='FE'"></input>
						</md-input-container>
						<md-input-container class="md-no-margin">
							<label>Input Tip</label>
							<input type="text" ng-model="value['Input Tip']"></input>
						</md-input-container>
						<md-input-container class="md-no-margin" ng-if="value['Input Type']!='FE'&&value['Input Type']!='MC'&&value['Input Type']!='date'">
							<label>Input Regex</label>
							<input type="text" ng-model="value['Input Regex']"></input>
						</md-input-container>
						<md-input-container class="md-no-margin" ng-if="value['Input Type']!='FE'&&value['Input Type']!='MC'&&value['Input Type']!='date'">
							<label>Input Regex Error Message</label>
							<input type="text" ng-model="value['Input Regex Error Message']"></input>
						</md-input-container>
						<div layout="row" class="md-no-padding">
							<span layout-margin>Input Type: </span>
							<md-select ng-model="value['Input Type']" class="md-no-padding md-no-margin" flex>
								<md-option value='text'>Text</md-option>
								<md-option value='number'>Number</md-option>
								<md-option value='date'>Date</md-option>
								<md-option value='MC'>Multiple Choice</md-option>
								<md-option value='FE'>Floating Entity</md-option>
							</md-select>
						</div>
						<fieldset layout="column" ng-if="value['Input Type']=='FE'" layout-padding>
							<legend>Floating Entity Settings</legend>
							<div>Referenced Table Name: {{value.FE.Table.Name}}</div>
							<div>Referenced Table Title: {{value.FE.Table.Title}}</div>
							<div>Cardinality Field: {{value.FE['Cardinality Field Name']}}</div>
							<md-input-container>
								<label>Default Cardinality</label>
								<input type="number" ng-model="value.FE['Default Cardinality']" ng-pattern="/^[0-9]$/"></input>
							</md-input-container>
						</fieldset>
						<fieldset layout="column" ng-if="value['Input Type']=='MC'" layout-padding>
							<legend>Multiple Choice Settings</legend>
							<span>Type: </span>
							<md-radio-group ng-model="value.MC.Type">
								<md-radio-button value="<?=MCTypes::SINGLE?>">Single Answer</md-radio-button>
								<md-radio-button value="<?=MCTypes::MULTIPLE?>">Multiple Answers</md-radio-button>
							</md-radio-group>
							<fieldset layout-padding>
								<legend>Choices:</legend>
								<div layout="row" ng-repeat="(index,choice) in value.MC.Choices track by $index">
									<span>{{$index+1}}</span>
									<md-input-container class="md-no-margin" flex>
										<label>Value</label>
										<input type="text" value="{{choice}}"/>
									</md-input-container>
									<md-button class="md-fab md-mini md-raised md-no-margin md-primary" layout-align="center center"><i class="fas fa-times"></i></md-button>
								</div>
								<md-button class="md-primary md-raised md-no-margin md-no-padding">Add Choice</md-button>
							</fieldset>
							<fieldset layout-padding>
								<legend>Custom Choices:</legend>
								<div layout="row" ng-repeat="(index,choice) in value.MC.Custom track by $index">
									<span>{{$index+1}}</span>
									<md-input-container class="md-no-margin" flex>
										<label>Value</label>
										<input type="text" value="{{choice}}"/>
									</md-input-container>
									<md-button class="md-fab md-mini md-raised md-no-margin md-primary" layout-align="center center"><i class="fas fa-times"></i></md-button>
								</div>
								<md-button class="md-primary md-raised md-no-margin md-no-padding">Add Custom Choice</md-button>
							</fieldset>
						</fieldset>
						<div layout="row" layout-align="center center">
							<md-button class="md-raised md-primary md-no-margin" ng-disabled="busy" ng-click="changeField('edit',key)">
									Save Changes
							</md-button>
						</div>
					</md-card-content>
				</md-card>
			</form>
		</div>

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