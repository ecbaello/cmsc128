<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-switch="<?=$input?>['Input Type']">

	<div ng-switch-when="text|number" ng-switch-when-separator="|" layout="column" >
		<md-input-container class="md-no-margin md-block">
			<label>{{<?=$input?>.Title}} <span ng-if="<?=$input?>['Input Tip']!=''" class="md-caption">({{<?=$input?>['Input Tip']}})</span></label>
			<input ng-model="<?=$model?>" name="<?=$name?>" type="{{<?=$input?>['Input Type']}}" ng-required="{{<?=$input?>['Input Required']}}" ng-pattern="<?=$input?>['Input Regex']"/>
			
			<div ng-messages="student[<?=$error_name?>].$error">
				<div ng-message="pattern">{{<?=$input?>['Input Regex Error Message']}}</div>
			</div>
		</md-input-container>
	</div>

	<div ng-switch-when="date" layout="column">
		<md-input-container class="md-no-margin">
			<label>{{<?=$input?>.Title}} <span ng-if="<?=$input?>['Input Tip']!=''" class="md-caption">({{<?=$input?>['Input Tip']}})</span></label>
			<md-datepicker ng-model="<?=$model?>"></md-datepicker>
		</md-input-container>
	</div>
	
	<div ng-switch-when="MC" layout="column" layout-padding layout-margin>
		<fieldset >
			<legend>{{<?=$input?>.Title}} </legend>
			<p ng-if="<?=$input?>['Input Tip']!=''" class="md-caption">({{<?=$input?>['Input Tip']}})</p>
			
			<md-radio-group ng-if="<?=$input?>.MC.Type == <?= MCTypes::SINGLE ?>" ng-model="<?=$model?>">
				<md-radio-button ng-repeat="(cindex,cvalue) in <?=$input?>['MC']['Choices']" value="{{cvalue}}">
					{{cvalue}}
				</md-radio-button>
			</md-radio-group>
			
			<div ng-if="<?=$input?>.MC.Type == <?= MCTypes::MULTIPLE ?>" layout="column">
				<md-checkbox ng-repeat="(cindex,cvalue) in <?=$input?>['MC']['Choices']" ng-model="<?=$model?>[cindex]" ng-true-value=" '{{cvalue}}' ">
				{{cvalue}}
				</md-checkbox>
			</div>
			
			<div ng-if="getLength( <?=$input?>['MC']['Custom'])>0 && <?=$input?>.MC.Type == <?= MCTypes::MULTIPLE ?>" layout="column">
				<div layout-padding>Others</div>
				<md-input-container ng-repeat="(cindex,cvalue) in <?=$input?>['MC']['Custom']" class="md-no-margin">
					<label>{{cvalue}}</label>
					<input ng-model="<?=$model?>['Custom'][cvalue]" type="text"></input>
				</md-input-container>
			</div>
			
		</fieldset>
	</div>
	
</div>