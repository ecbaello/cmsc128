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
		<md-button class="md-raised md-primary" ng-disabled="busy" ng-click="showAddTable()">Add Table</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill>
		<h2 class="md-no-margin">
			{{currCategory.Table.Title}} <span ng-if="currCategory.Table.Flag=='<?=Flags::FLOATING?>'">(Floating)</span>
			<md-button class="md-fab md-mini md-raised" ng-click="showEditTableTitle()">
				<i class="fas fa-edit"></i>
			</md-button>
		</h2>
		<div layout="column">
			<span>Table Name: {{currCategory.Table.Name}}</span>
			<div>
				<md-button class="md-primary md-raised" ng-disabled="busy" ng-click="deleteTable(key)">
					<span>Delete Table</span>
				</md-button>
				<md-button class="md-primary md-raised" ng-disabled="busy" ng-click="showAddField()">
					<span>Add Field</span>
				</md-button>
			</div>
		</div>
		<form id="nfnewChoice"></form> <!--THESE FORMS ARE IMPORTANT-->
		<form id="nfnewCustom"></form> <!--THESE FORMS ARE IMPORTANT-->
		<form id="newChoice"></form> <!--THESE FORMS ARE IMPORTANT-->
		<form id="newCustom"></form> <!--THESE FORMS ARE IMPORTANT-->
		<div layout-fill class="md-no-padding">
			<md-card layout="column" ng-repeat="(key,value) in currCategory.Fields | orderBy:'\u0022Input Order\u0022'" ng-if="value['Input Type']!='hidden'">
				<md-toolbar layout="row" layout-align="space-between center" style="background-color:#014421;color:white">
					<div layout-padding>
						<md-button ng-if="!value.Essential" ng-click="changeField('delete',key)" class="md-no-margin md-no-padding md-fab md-mini md-raised"><i class="fas fa-times"></i></md-button>
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
				<md-card-content ng-if="fields[currCategoryKey][key].expanded">
					<form layout="column" layout-padding>
						<md-switch ng-if="!value.Essential" ng-model="value['Input Required']" ng-true-value="1" ng-false-value="0">
							Required?
						</md-switch>
						<md-input-container class="md-no-margin">
							<label>Name</label>
							<input type="text" ng-model="value.Name" disabled></input>
						</md-input-container>
						<md-input-container class="md-no-margin">
							<label>Title</label>
							<input type="text" ng-model="value.Title"></input>
						</md-input-container>
						<md-input-container class="md-no-margin">
							<label>Input Tip</label>
							<input type="text" ng-model="value['Input Tip']"></input>
						</md-input-container>
						<md-input-container class="md-no-margin" ng-if="value['Input Type']!='FE'&&value['Input Type']!='MC'&&value['Input Type']!='date'">
							<label>Input Regex <span class="md-subhead">(Leave this blank if you don't know what it is.)</span></label>
							<input type="text" ng-model="value['Input Regex']"></input>
						</md-input-container>
						<md-input-container class="md-no-margin" ng-if="value['Input Type']!='FE'&&value['Input Type']!='MC'&&value['Input Type']!='date'">
							<label>Input Regex Error Message</label>
							<input type="text" ng-model="value['Input Regex Error Message']"></input>
						</md-input-container>
						<div layout="row" class="md-no-padding">
							<span layout-margin>Input Type: 
								<span ng-if="value['Input Type']=='FE'">
									Floating Entity
								</span>
								<span ng-if="value['Input Type']=='MC'">
									Multiple Choice
								</span>
								<span ng-if="value['Input Type']=='text'">
									Text
								</span>
								<span ng-if="value['Input Type']=='date'">
									Date
								</span>
								<span ng-if="value['Input Type']=='number'">
									Number
								</span>
							</span>
						</div>
						<fieldset layout="column" ng-if="value['Input Type']=='FE'" layout-padding>
							<legend>Floating Entity Settings</legend>
							<div layout="row" layout-align="center center" layout-padding>
								<span>Referenced Table: </span>
								<md-select ng-model="value.FE.Table.Name" class="md-no-margin" ng-required="true" flex>
									<md-option ng-repeat="table in tables" ng-if="table.Table.Flag=='<?=Flags::FLOATING?>'" ng-value="table.Table.Name">
										{{table.Table.Title}}
									</md-option>
								</md-select>
							</div>
							<div layout="row" layout-align="center center" layout-padding>
								<span>Cardinality Field:</span>
								<md-select ng-model="value.FE['Cardinality Field Name']" class="md-no-margin" flex>
									<md-option value=''>None</md-option>
									<md-option ng-repeat="field in getCardinalityCandidates()" ng-value="field.Name">
										{{field.Title}}
									</md-option>
								</md-select>
							</div>
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
								<div layout="row" ng-repeat="(index,choice) in value.MC.Choices track by $index" layout="row" layout-align="center">
									<div layout-padding>
										{{$index+1}}
									</div>
									<md-input-container class="md-no-margin" flex>
										<label>Value</label>
										<input type="text" ng-model="value.MC.Choices[index]"/>
									</md-input-container>
									<div>
										<md-button class="md-fab md-mini md-raised md-primary" layout-align="center center" ng-click="deleteChoice(key,false,index)"><i class="fas fa-times"></i></md-button>
									</div>
								</div>
								<div layout="row" layout-align="center">
									<div layout-padding>
										New Choice: 
									</div>
									<md-input-container flex class="md-no-margin md-no-padding">
										<label>Value </label>
										<input type="text" form="newChoice" ng-model="newChoice[key]"/>
									</md-input-container>
									<div>
										<md-button class="md-primary md-raised" type="submit" form="newChoice" ng-click="addChoice(key,false)">
											Add Choice
										</md-button>
									</div>
								</div>
							</fieldset>
							<fieldset layout-padding>
								<legend>Custom Choices:</legend>
								<div layout="row" ng-repeat="(index,choice) in value.MC.Custom track by $index" layout="row" layout-align="center">
									<div layout-padding>
										{{$index+1}}
									</div>
									<md-input-container class="md-no-margin" flex>
										<label>Value</label>
										<input type="text" ng-model="value.MC.Custom[index]"/>
									</md-input-container>
									<div>
										<md-button class="md-fab md-mini md-raised md-primary" layout-align="center center" ng-click="deleteChoice(key,true,index)"><i class="fas fa-times"></i></md-button>
									</div>
								</div>
								<div layout="row" layout-align="center">
									<div layout-padding>
										New Choice: 
									</div>
									<md-input-container flex class="md-no-margin md-no-padding">
										<label>Value</label>
										<input type="text" form="newCustom" ng-model="newCustom[key]"/>
									</md-input-container>
									<div>
										<md-button class="md-primary md-raised" type="submit" form="newCustom" ng-click="addChoice(key,true)">
											Add Choice
										</md-button>
									</div>
								</div>
							</fieldset>
						</fieldset>
						<div layout="row" layout-align="center center">
							<md-button type="submit" class="md-raised md-primary md-no-margin" ng-disabled="busy" ng-click="changeField('edit',key)">
									Save Changes
							</md-button>
						</div>
					</form>
				</md-card-content>
			</md-card>
		</div>

	</div>
	
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="addField">
			<md-dialog flex="75">
				<md-toolbar style="background-color:#014421;color:white" layout-padding>
					<h4 class="md-no-margin">Add Field</h4>
				</md-toolbar>
				<md-dialog-content>
					<form layout="column" layout-padding>
						<md-switch ng-model="newField['Input Required']" ng-true-value="1" ng-false-value="0">
							Required?
						</md-switch>
						<md-input-container class="md-no-margin">
							<label>Name</label>
							<input type="text" ng-model="newField.Name" disabled></input>
						</md-input-container>
						<md-input-container class="md-no-margin">
							<label>Title</label>
							<input type="text" ng-change="updateFieldName()" ng-model="newField.Title"></input>
						</md-input-container>
						<md-input-container class="md-no-margin">
							<label>Input Tip</label>
							<input type="text" ng-model="newField['Input Tip']"></input>
						</md-input-container>
						<md-input-container class="md-no-margin" ng-if="newField['Input Type']!='FE'&&newField['Input Type']!='MC'&&newField['Input Type']!='date'">
							<label>Input Regex <span class="md-subhead">(Leave this blank if you don't know what it is.)</span></label>
							<input type="text" ng-model="newField['Input Regex']"></input>
						</md-input-container>
						<md-input-container class="md-no-margin" ng-if="newField['Input Type']!='FE'&&newField['Input Type']!='MC'&&newField['Input Type']!='date'">
							<label>Input Regex Error Message</label>
							<input type="text" ng-model="newField['Input Regex Error Message']"></input>
						</md-input-container>
						<div layout="row" layout-align="center">
							<p>WARNING. Input type CANNOT be changed upon addition of field.</p>
						</div>
						<div layout="row" class="md-no-padding">
							<span layout-margin>Input Type: </span>
							<md-select ng-model="newField['Input Type']" class="md-no-padding md-no-margin" flex>
								<md-option value='text'>Text</md-option>
								<md-option value='number'>Number</md-option>
								<md-option value='date'>Date</md-option>
								<md-option value='MC'>Multiple Choice</md-option>
								<md-option ng-if="currCategory.Table.Flag!=<?=Flags::FLOATING?>" value='FE'>Floating Entity</md-option>
							</md-select>
						</div>
						<fieldset layout="column" ng-if="newField['Input Type']=='FE'" layout-padding>
							<legend>Floating Entity Settings</legend>
							<div layout="row" layout-align="center center" layout-padding>
								<span>Referenced Table: </span>
								<md-select ng-model="newField.FE.Table.Name" class="md-no-margin" ng-required="true" flex>
									<md-option ng-repeat="(tabIndex,table) in tables" ng-if="table.Table.Flag=='<?=Flags::FLOATING?>'" ng-value="table.Table.Name">
										{{table.Table.Title}}
									</md-option>
								</md-select>
							</div>
							<div layout="row" layout-align="center center" layout-padding>
								<span>Cardinality Field:</span>
								<md-select ng-model="newField.FE['Cardinality Field Name']" class="md-no-margin" flex>
									<md-option value=''>None</md-option>
									<md-option ng-repeat="field in getCardinalityCandidates()" ng-value="field.Name">
										{{field.Title}}
									</md-option>
								</md-select>
							</div>
							<md-input-container>
								<label>Default Cardinality</label>
								<input type="number" ng-model="newField.FE['Default Cardinality']" ng-pattern="/^[0-9]+$/"></input>
							</md-input-container>
						</fieldset>
						<fieldset layout="column" ng-if="newField['Input Type']=='MC'" layout-padding>
							<legend>Multiple Choice Settings</legend>
							<span>Type: </span>
							<md-radio-group ng-model="newField.MC.Type">
								<md-radio-button value="<?=MCTypes::SINGLE?>">Single Answer</md-radio-button>
								<md-radio-button value="<?=MCTypes::MULTIPLE?>">Multiple Answers</md-radio-button>
							</md-radio-group>
							<fieldset layout-padding>
								<legend>Choices:</legend>
								<div layout="row" ng-repeat="(index,choice) in newField.MC.Choices track by $index" layout="row" layout-align="center">
									<div layout-padding>
										{{$index+1}}
									</div>
									<md-input-container class="md-no-margin" flex>
										<label>Value</label>
										<input type="text" ng-model="newField.MC.Choices[index]"/>
									</md-input-container>
									<div>
										<md-button class="md-fab md-mini md-raised md-primary" layout-align="center center" ng-click="deleteChoice(-1,false,index)"><i class="fas fa-times"></i></md-button>
									</div>
								</div>
								<div layout="row" layout-align="center">
									<div layout-padding>
										New Choice: 
									</div>
									<md-input-container flex class="md-no-margin md-no-padding">
										<label>Value</label>
										<input type="text" form="nfnewChoice" ng-model="newField.newChoice"/>
									</md-input-container>
									<div>
										<md-button class="md-primary md-raised" type="submit" form="nfnewChoice" ng-click="addChoice(-1,false)">
											Add Choice
										</md-button>
									</div>
								</div>
							</fieldset>
							<fieldset layout-padding>
								<legend>Custom Choices:</legend>
								<div layout="row" ng-repeat="(index,choice) in newField.MC.Custom track by $index" layout="row" layout-align="center">
									<div layout-padding>
										{{$index+1}}
									</div>
									<md-input-container class="md-no-margin" flex>
										<label>Value</label>
										<input type="text" ng-model="newField.MC.Custom[index]"/>
									</md-input-container>
									<div>
										<md-button class="md-fab md-mini md-raised md-primary" layout-align="center center" ng-click="deleteChoice(-1,true,index)"><i class="fas fa-times"></i></md-button>
									</div>
								</div>
								<div layout="row" layout-align="center">
									<div layout-padding>
										New Choice: 
									</div>
									<md-input-container flex class="md-no-margin md-no-padding">
										<label>Value</label>
										<input type="text" form="nfnewCustom" ng-model="newField.newCustom"/>
									</md-input-container>
									<div>
										<md-button class="md-primary md-raised" type="submit" form="nfnewCustom" ng-click="addChoice(-1,true)">
											Add Choice
										</md-button>
									</div>
								</div>
							</fieldset>
						</fieldset>
						<div layout="row" layout-align="end center" layout-padding>
							<div layout="row" layout-align="end center">
							<md-button class="md-no-margin" ng-click="closeDialog()" >Cancel</md-button>
							<md-button class="md-no-margin" ng-click="addField()" type="submit" ng-disabled="!newField.Title">Submit</md-button>
						</div>
						</div>
					</form>
				</md-dialog-content>
			</md-dialog>
		</div>
	</div>
	
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="addTable">
			<md-dialog flex="30">
				<md-toolbar style="background-color:#014421;color:white" layout-padding>
					<h4 class="md-no-margin">Add Table</h4>
				</md-toolbar>
				<md-dialog-content>
					<form layout="column" layout-padding>
						<div layout="column">
							<md-input-container class="md-no-margin">
								<label>Title</label>
								<input ng-change="updateTableName()" ng-model="newTable.Title" type="text" required></input>
							</md-input-container>
							<div>
								<span>Name: </span>
								<span>{{newTable.Name}}</span>
							</div>
							<md-switch ng-model="newTable['Floating']" ng-true-value="1" ng-false-value="0">
								Floating? 
							</md-switch>
						</div>
						<div>
							<p>Warning. You can't change the 'floating' attribute once the table is created.</p>
						</div>
						<div layout="row" layout-align="end center">
							<md-button class="md-no-margin" ng-click="closeDialog()" >Cancel</md-button>
							<md-button class="md-no-margin" ng-click="addTable()" type="submit" ng-disabled="!newTable.Title">Submit</md-button>
						</div>
					</form>
				</md-dialog-content>
			</md-dialog>
		</div>
	</div>
	
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="editTableTitle">
			<md-dialog flex="30">
				<md-toolbar style="background-color:#014421;color:white" layout-padding>
					<h4 class="md-no-margin">Edit Table Title</h4>
				</md-toolbar>
				<md-dialog-content>
					<form layout="column" layout-padding>
						<div layout="column" layout-margin>
							<md-input-container class="md-no-margin">
								<label>Title</label>
								<input ng-model="currCategory.Table.Title" type="text" required></input>
							</md-input-container>
						</div>
						<div layout="row" layout-align="end center" class="md-no-padding">
							<md-button class="md-no-margin" ng-click="closeDialog()" >Cancel</md-button>
							<md-button class="md-no-margin" ng-click="editTableTitle()" type="submit">Submit</md-button>
						</div>
					</form>
				</md-dialog-content>
			</md-dialog>
		</div>
	</div>

</div>