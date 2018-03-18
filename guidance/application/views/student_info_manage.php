<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="student_search" ng-init="init()" >
	<div layout="column" layout-align="start">
		<h2 layout-padding>Search Student</h2>
		<div ng-repeat="(key,value) in filters" layout="row" layout-padding>
			<div>
				<md-button ng-click="removeFilter(key)" class="md-raised md-fab md-mini md-no-margin md-no-padding"><i class="fas fa-times"></i></md-button>
			</div>
			<span ng-if="!$first">{{value.type}}</span>
			<md-input-container class="md-no-margin md-no-padding" flex>
				<label>{{value.title}}</label>
				<input ng-model="value.value" type="text"/>
			</md-input-container>
		</div>
		<div layout-margin>
			<span>Add Filter: </span>
			<md-input-container class="md-no-margin">
				<md-select ng-model="toAddFilter">
					<md-option ng-repeat="(key,value) in params" value='{{value}}' ng-selected="$first">{{value.title}}</md-option>
				</md-select>
			</md-input-container>
			<span ng-if="getFiltersLength()==0">
				<md-button class="md-raised" md-no-margin ng-click="addFilter('and')">ADD</md-button>
			</span>
			<span ng-if="getFiltersLength()>0">
				<md-button class="md-raised md-no-margin" ng-click="addFilter('and')">AND</md-button>
				<md-button class="md-raised md-no-margin" ng-click="addFilter('or')">OR</md-button>
			</span>
			<md-button class="md-primary md-raised" ng-click="search()"><i class="fas fa-search" style=""></i></md-button>
		</div>
	</div>

</div>