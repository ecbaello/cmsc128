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

#tables tr:hover {background-color: #ddd;cursor:pointer;}

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

<div layout="row" ng-controller="student_recbin" flex ng-init="init()">
	<md-content layout="column" layout-align="start stretch" flex="20">
		<div layout="column" layout-margin >
			<h2>Recycle Bin</h2>
		</div>
		<md-button layout="row" layout-align="start start" class="md-no-margin" ng-class="{'md-primary md-raised':currType=='Tables'}" ng-click="currType='Tables'">
			<span style="padding-left:8px">Tables</span>
		</md-button>
		<md-button layout="row" layout-align="start start" class="md-no-margin" ng-class="{'md-primary md-raised':currType=='Fields'}" ng-click="currType='Fields'">
			<span style="padding-left:8px">Fields</span>
		</md-button>
		<md-button layout="row" layout-align="start start" class="md-no-margin" ng-class="{'md-primary md-raised':currType=='Records'}" ng-click="currType='Records'">
			<span style="padding-left:8px">Records</span>
		</md-button>
	</md-content>
	<md-content flex layout-margin>
		<div layout-margin>
			<h2 class="md-headline">{{currType}}</h2>
		</div>
		<div>
			<table id='tables'>
				<tr>
					<th ng-repeat="h in filters[currType].Headers" ng-click="sort(currType,h)">
						{{h}}
					</th>
				</tr>
				<tr ng-repeat="d in filters[currType].Data">
					<td ng-repeat="(k,v) in d" ng-click="showDialog(currType,d.ID)">
						<span ng-if="k!='Student Number'">{{v}}</span>
						<span ng-if="k=='Student Number'">
							<a class="md-button md-no-margin" href="<?=base_url().'studentinfo/manage/student/'?>{{v}}">
								{{v}}
							</a>
						</span>
					</td>
				</tr>
			</table>
			<div layout="row" layout-align="space-between center" layout-margin>
				<div layout="row" layout-padding>
					<span>Jump To:</span>
					<md-select ng-model="filters[currType].Index" class="md-no-margin md-no-padding">
						<md-option ng-repeat="v in getNumber(filters[currType].Data.length/filters[currType].Division) track by $index" ng-value="$index+1">
							{{$index+1}}
						</md-option>
					</md-select>
				</div>
				<div layout="row" layout-align="center" layout-padding>
					<md-button class="md-no-margin md-no-padding md-fab md-mini md-raised" ng-disabled="filters[currType].Index<=1" ng-click="nav(-1)">
						<i class="fas fa-angle-left"></i>
					</md-button>
					<span>{{filters[currType].Index}}</span>
					<md-button class="md-no-margin md-no-padding md-fab md-mini md-raised" ng-disabled="filters[currType].Index>=parseInt(filters[currType].Data.length/filters[currType].Division)" ng-click="nav(1)">
						<i class="fas fa-angle-right"></i>
					</md-button>
				</div>
				<div layout="row" layout-padding>
					<span>
						No. of Results Per Page:
					</span>
					<md-select ng-model="filters[currType].Division" class="md-no-margin md-no-padding">
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
		</div>
	</md-content>
	<div style="visibility: hidden">
		<div class="md-dialog-container" id="action">
			<md-dialog>
				<md-toolbar style="background-color:maroon;color:white" layout-padding>
					<h4 class="md-no-margin">{{currType}}</h4>
				</md-toolbar>
				<md-dialog-content layout-padding>
					<div layout-margin layout-padding>
						What do you want to do with 
						<span ng-switch="currType" class="md-no-margin md-no-padding">
							<span ng-switch-when="Tables">table</span>
							<span ng-switch-when="Fields">field</span>
							<span ng-switch-when="Records">record</span>
						</span>
						{{dialogID}}?
					</div>
					<div layout="row" layout-align="center center">
						<md-button ng-click="closeDialog()">
							Cancel
						</md-button>
						<md-button style="color:maroon" ng-click="action('delete',currType,dialogID)">
							Delete
						</md-button>
						<md-button style="color:#014421" ng-click="action('restore',currType,dialogID)">
							Restore
						</md-button>
					</div>
				</md-dialog-content>
			</md-dialog>
		</div>
	</div>
</div>