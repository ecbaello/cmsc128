<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="student_add" >
<div layout="row" layout-padding >
	<span>Search Student:</span>
	<input ng-model="searchInput" type="text" placeholder='Input Student Number'/>
	<button ng-click="search()"><i class="fas fa-search" style=""></i></button>
</div>

<div layout="row" layout-align="center start" flex ng-init="init()">
	<md-content layout="column" layout-align="start start" flex='20'>
		<md-button layout-fill style="text-align:left" ng-repeat="(key,value) in tableData"  ng-click="changeCategory(key)" ng-class="{'md-primary md-raised':key == currCategoryKey}">
			<span layout-padding>{{value.Table.Title}}</span>
		</md-button>
		<md-button class="md-raised md-primary" ng-disabled="true" ng-click="submit()">Submit</md-button>
	</md-content>
	
	<div layout="column" layout-align="start start" flex layout-padding layout-fill>
		<div>
			<h2 class="md-headline">
				<span>Student Information: {{currCategory.Table.Title}}<span>
			</h2>
		</div>
		<div layout-fill class="md-no-padding">
			<form name="{{currCategory.Table.Name}}" ng-init='setCSRF(<?= '"'.$this->security->get_csrf_token_name().'","'.$this->security->get_csrf_hash().'"'?>)'>
				<div ng-repeat="(key,value) in currCategory.Fields" layout="column" >
					<md-input-container ng-if="value['Input Type'] != 'AET'" class="md-no-margin">
						<label>{{value.Title}}</label>
						<input ng-model="input[currCategory.Table.Name][value.Name]" type="{{value['Input Type']}}" ng-required="{{value['Input Required']}}" ng-pattern="value['Input Regex']"/>
					</md-input-container>
					<md-content ng-if="value['Input Type'] == 'AET'">
						<span>{{value.AET.Table.Title}}</span>
						<div layout="column" layout-padding layout-margin>
							<div style="border:1px solid lightgray" flex layout-align="start center" ng-repeat="(i,x) in getCardinality(currCategory.Table.Name,value.AET.Table.Name) track by $index">
								<span>{{$index+1}}</span>
								<div layout="column" ng-repeat="(k,v) in value.AET.Fields" class="md-no-padding">
									<md-input-container class="md-no-margin">
										<label>{{v.Title}}</label>
										<input ng-model="input[currCategory.Table.Name][value.Name][i][v.Name]" type="{{v['Input Type']}}"  ng-required="{{v['Input Required']}}" ng-pattern="v['Input Regex']"/>
									</md-input-container>
								</div>
							</div>
						</div>
						
					</md-content>
				</div>
			</form>
		</div>
	</div>
	
</div>
</div>