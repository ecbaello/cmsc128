<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="tests_take" ng-init='init(<?=$test?>)'>
	<div layout="column" layout-padding>
		<h2>{{test.Title}}</h2>
		<p ng-if="test.Desc!=''">
			{{test.Desc}}
		</p>
		<md-input-container class="md-no-margin">
			<input type="hidden" ng-model="test.UTAID" value="<?=$this->ion_auth->logged_in()?$this->ion_auth->user()->row()->username:''?>" required />
		</md-input-container>
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
							#{{$index+1}}
						</legend>
						<span>
							{{value.Title}}
						</span>
						
						<div layout-fill  class="md-no-padding" layout="row" layout-margin >
							<md-radio-group ng-model="test.Questions[index].Answer">
								<md-radio-button ng-repeat="(cindex,cvalue) in test.Questions[index].Choices" value="{{cvalue.Value}}">
									{{cvalue.Value}}
								</md-radio-button>
							</md-radio-group>
						</div>
					</fieldset>
				</div>
			</md-card-content>
		</md-card>
		<div layout-margin layout="row" layout-align="center">
			<md-button class="md-raised md-primary md-no-margin" ng-disabled="busy" ng-click="submit()">
				Submit
			</md-button>
		</div>
	</div>
</div>