<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="admin" ng-init="init()">
	
	<div layout="column" layout-padding>
		<h2>Change Admin Account Details</h2>
	</div>
	<div layout="column" layout-padding>
		<div layout="row" layout-padding>
			<span>Change Username</span>
			<md-divider></md-divider>
			<div layout="column" flex>
				<md-input-container class="md-no-margin md-no-padding">
					<label>New Username</label>
					<input ng-model="account.NewUsername" type="text"/>
				</md-input-container>
				<md-input-container class="md-no-margin md-no-padding">
					<label>Password</label>
					<input ng-model="account.password" type="text"/>
				</md-input-container>
				<div>
				<md-button ng-click="changeUsername()" class="md-raised md-no-margin md-no-padding">
					Change
				</md-button>
				</div>
			</div>
		</div>
		<md-divider></md-divider>
		<div layout="row" layout-padding>
			<span>Change Email</span>
			<md-divider></md-divider>
			<div layout="column" flex>
				<md-input-container class="md-no-margin md-no-padding">
					<label>New Email</label>
					<input ng-model="account.NewEmail" type="text"/>
				</md-input-container>
				<md-input-container class="md-no-margin md-no-padding">
					<label>Password</label>
					<input ng-model="account.passowrd" type="text"/>
				</md-input-container>
				<div>
				<md-button ng-click="changeEmail()" class="md-raised md-no-margin md-no-padding">
					Change
				</md-button>
				</div>
			</div>
		</div>
		<md-divider></md-divider>
		<div layout="row" layout-padding>
			<span>Change Password</span>
			<md-divider></md-divider>
			<div layout="column" flex>
				<md-input-container class="md-no-margin md-no-padding">
					<label>New Password</label>
					<input ng-model="account.NewPassword1" type="text"/>
				</md-input-container>
				<md-input-container class="md-no-margin md-no-padding">
					<label>Input New Password Again</label>
					<input ng-model="account.NewPassword2" type="text"/>
				</md-input-container>
				<md-input-container class="md-no-margin md-no-padding">
					<label>Old Password</label>
					<input ng-model="account.password" type="text"/>
				</md-input-container>
				<div>
				<md-button ng-click="changePassword()" class="md-raised md-no-margin md-no-padding">
					Change
				</md-button>
				</div>
			</div>
		</div>
	</div>

</div>
