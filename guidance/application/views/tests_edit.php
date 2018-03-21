<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="tests_edit" ng-init='init(<?=$test?>)'>
	<div layout-padding>
		<h2>Editing Test: {{test.Title}}</h2>
	</div>
	<div layout="column" layout-padding>
		<md-input-container class="md-no-margin">
			<label>Test Title</label>
			<input type="text" ng-model="test.Title" required />
		</md-input-container>
		<md-input-container class="md-no-margin">
			<label>Test Description</label>
			<textarea ng-model="test.Desc" rows="2" md-maxlength="400"></textarea>
		</md-input-container>
		<div>
			<md-button class="md-raised md-primary md-no-margin" ng-click="submit()">
				Submit
			</md-button>
		</div>
	</div>
	<div>
		<md-card>
			<md-toolbar layout-padding style="background-color:maroon">
				<h3 class="md-no-margin">Questions</h3>
			</md-toolbar>
			<md-card-content>
				<div layout="column" layout-align="start-center" layout-margin>
					<fieldset layout-fill ng-repeat="(index,value) in test.Questions | orderBy:'Order'" layout="column" layout-padding>
						<legend>
							<md-button ng-click="deleteQuestion(index)" class="md-no-margin md-no-padding md-fab md-mini md-primary"><i class="fas fa-times"></i></md-button>
							#{{$index+1}}
						</legend>
						<div>
							<md-input-container class="md-no-margin" layout-fill>
								<label>Title</label>
								<input type="text" ng-model="test.Questions[index].Title"/>
							</md-input-container>
							<div>
								<span layout-padding>Order:</span> 
								<md-input-container class="md-no-margin">
									<md-select ng-model="test.Questions[index].Order" ng-change="changeOrder(index,test.Questions[index].Order-1)">
										<md-option ng-repeat="i in getNumber(test.Questions.length) track by $index" value={{$index+1}}>{{$index+1}}</md-option>
									</md-select>
								</md-input-container>
							</div>
						</div>
						<span>Choices:</span>
						
						<div layout-fill  class="md-no-padding" ng-repeat="(i,v) in test.Questions[index].Choices" layout="row" layout-margin >
							<span>{{$index+1}}.</span>
							<md-input-container class="md-no-margin" flex>
								<label>Value</label>
								<input type="text" ng-model="test.Questions[index].Choices[i].Value" required/>
							</md-input-container>
							<md-button class="md-fab md-mini md-raised md-no-margin md-primary" ng-click="deleteChoice(index,i)" layout-align="center center"><i class="fas fa-times"></i></md-button>
						</div>
						
						<div layout-margin>
							<md-button class="md-primary md-raised md-no-margin " ng-click="addChoice(index)">Add Choice</md-button>
						</div>
					</fieldset>
				</div>
				<div layout-margin>
					<md-button class="md-primary md-raised md-no-margin" ng-click="addQuestion()">Add Question</md-button>
				</div>
			</md-card-content>
		</md-card>
		<div layout-margin layout="row" layout-align="center">
			<md-button class="md-raised md-primary md-no-margin" ng-click="submit()">
				Submit
			</md-button>
		</div>
	</div>
</div>