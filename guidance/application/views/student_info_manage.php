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
    padding: 4px;
}

#tables tr:nth-child(even){background-color: #f2f2f2;}

#tables tr:hover {background-color: #ddd;}

#tables th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: center;
    background-color: #800000;
    color: white;
}

#tables th:hover{
	cursor:pointer;
}
</style>

<div ng-controller="student_search" ng-init="init()" >
	<form layout="column" layout-align="start">
		<h2 layout-padding layout-margin>Search Student</h2>
		<div ng-repeat="(key,value) in filters" layout="row" layout-align="center" class="md-no-padding">
			<span layout-padding>
				<md-button ng-click="removeFilter(key)" class="md-raised md-primary md-fab md-mini md-no-margin md-no-padding"><i class="fas fa-times"></i></md-button>
			</span>
			<div ng-if="!$first" layout="column" layout-align="center">
				<span layout-padding>{{value.type}}</span>
			</div>
			<div layout="column" flex layout-padding>
				<md-input-container class="md-no-margin md-no-padding" layout="row" layout-align="center">
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
			<md-button class="md-primary md-raised" ng-click="search()" ng-disabled="busy" type="submit"><i class="fas fa-search" style=""></i></md-button>
		</div>
	</form>
	
	<div ng-if="getLength(results)>0" layout-padding flex>
		<md-content>
			<table id="tables">
				<tr>
					<th ng-repeat="(key,value) in params" ng-click="sort(key)">
						{{value.title}}
					</th>
				</tr>
				<tr ng-repeat="(k,v) in results | limitTo:division:((currIndex-1)*division)">
					<td ng-repeat="v2 in v">
						<a ng-if="$first" class=" md-button md-no-margin" href="<?=base_url().'studentinfo/manage/student/'?>{{v2}}">{{v2}}</a>
						<span ng-if="!$first">{{v2}}</span>
					</td>
				</tr>
			</table>
			<div layout="row" layout-align="space-between center" layout-margin>
				<div layout="row" layout-padding>
					<span>Jump To:</span>
					<md-select ng-model="currIndex" class="md-no-margin md-no-padding">
						<md-option ng-repeat="v in getNumber(results.length/division) track by $index" ng-value="$index+1">
							{{$index+1}}
						</md-option>
					</md-select>
				</div>
				<div layout="row" layout-align="center" layout-padding>
					<md-button class="md-no-margin md-no-padding md-fab md-mini md-raised" ng-disabled="currIndex<=1" ng-click="nav(-1)">
						<i class="fas fa-angle-left"></i>
					</md-button>
					<span>{{currIndex}}</span>
					<md-button class="md-no-margin md-no-padding md-fab md-mini md-raised" ng-disabled="currIndex>=parseInt(results.length/division)" ng-click="nav(1)">
						<i class="fas fa-angle-right"></i>
					</md-button>
				</div>
				<div layout="row" layout-padding>
					<span>
						No. of Results Per Page:
					</span>
					<md-select ng-model="division" class="md-no-margin md-no-padding">
						<md-option value=5>
							5
						</md-option>
						<md-option value=10>
							10
						</md-option>
						<md-option value=25>
							25
						</md-option>
						<md-option value=50>
							50
						</md-option>
						<md-option value=100>
							100
						</md-option>
					</md-select>
				</div>
			</div>
		</md-content>
	</div>

</div>