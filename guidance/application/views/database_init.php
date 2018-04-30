<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div layout-margin layout-padding>
	<h2 class="md-headline">
	Initialize Database
	</h2>
	<div>
		Enter Password:
		<form ng-controller="admin">
			<input type="password" ng-model="initdbpassword"/>
			<md-button class="md-raised" type="submit" ng-click="initDB()">Submit</md-button>
		</form>
	</div>
</div>