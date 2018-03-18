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
			<label>{{<?=$input?>.Title}} <span ng-if="<?=$input?>['Input Tip']!=''">({{<?=$input?>['Input Tip']}})</span></label>
			<md-datepicker ng-model="<?=$model?>"></md-datepicker>
		</md-input-container>
	</div>
	
</div>