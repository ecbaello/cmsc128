<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<style>
#tables {
    border-collapse: collapse;
    width: 100%;
}

#tables td, #tables th {
    border: 1px solid #ddd;
    padding: 8px;
}

#tables tr:nth-child(even){background-color: #f2f2f2;}

#tables tr:hover {background-color: #ddd;}

#tables th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #800000;
    color: white;
}
</style>

<div ng-controller="student_search" ng-init="init()" >
	<div layout="column" layout-align="start">
		<h2 layout-padding layout-margin>Search Student</h2>
		<div ng-repeat="(key,value) in filters" layout="row" layout-align="start center" class="md-no-padding">
			<span layout-padding>
				<md-button ng-click="removeFilter(key)" class="md-raised md-primary md-fab md-mini md-no-margin md-no-padding"><i class="fas fa-times"></i></md-button>
			</span>
			<span ng-if="!$first">{{value.type}}</span>
			<div layout="column" layout-padding flex>
				<md-input-container class="md-no-margin md-no-padding">
					<label>{{value.title}}</label>
					<input ng-model="value.value" type="text"/>
				</md-input-container>
			</div>
		</div>
		<div layout-margin>
			<span>Add Filter: </span>
			<md-input-container class="md-no-margin">
				<md-select ng-model="toAddFilter">
					<md-option ng-repeat="(key,value) in params" value='{{value}}' ng-selected="$first">{{value.title}}</md-option>
				</md-select>
			</md-input-container>
			<span ng-if="getLength(filters)==0">
				<md-button class="md-raised" md-no-margin ng-click="addFilter('and')">ADD</md-button>
			</span>
			<span ng-if="getLength(filters)>0">
				<md-button class="md-raised md-no-margin" ng-click="addFilter('and')">AND</md-button>
				<md-button class="md-raised md-no-margin" ng-click="addFilter('or')">OR</md-button>
			</span>
			<md-button class="md-primary md-raised" ng-click="search()"><i class="fas fa-search" style=""></i></md-button>
		</div>
	</div>
	
	<div ng-if="getLength(results)>0" layout-padding flex>
		<md-content>
			<table id="tables">
				<tr>
					<th ng-repeat="(key,value) in params">
						{{value.title}}
					</th>
				</tr>
				<tr ng-repeat="(k,v) in results">
					<td ng-repeat="v2 in v">
						<a ng-if="$first" class=" md-button md-no-margin" href="<?=base_url().'studentinfo/manage/student/'?>{{v2}}">{{v2}}</a>
						<span ng-if="!$first">{{v2}}</span>
					</td>
				</tr>
			</table>
		</md-content>
	</div>

</div>