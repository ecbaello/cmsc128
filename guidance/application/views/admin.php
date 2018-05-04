<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div ng-controller="admin" ng-init="init()">
	
	<div layout="column" layout-padding>
		<h2>Change Admin Account Details</h2>
		<div>
			<md-button class="md-primary md-raised" ng-click="showForgotPass()">Forgot Password</md-button>
		</div>
	</div>
	<div layout="column" layout-padding>
		<div layout="row" layout-padding>
			<span>Change Username</span>
			<md-divider></md-divider>
			<form layout="column" flex>
				<span>Current Username: {{account.username}}</span>
				<div layout="column" layout-margin layout-padding>
					<md-input-container class="md-no-margin md-no-padding">
						<label>New Username</label>
						<input ng-model="account.newUsername" type="text"/>
					</md-input-container>
					<md-input-container class="md-no-margin md-no-padding">
						<label>Password</label>
						<input type="password" ng-model="account.userPassword" type="text"/>
					</md-input-container>
				</div>
				<div>
				<md-button type="submit" ng-click="change(0)" class="md-raised md-no-margin md-no-padding">
					Change
				</md-button>
				</div>
			</form>
		</div>
		<md-divider></md-divider>
		<div layout="row" layout-padding>
			<span>Change Email</span>
			<md-divider></md-divider>
			<form layout="column" flex>
				<span>Current Email: {{account.email}}</span>
				<div layout="column" layout-margin layout-padding>
					<md-input-container class="md-no-margin md-no-padding">
						<label>New Email</label>
						<input ng-model="account.newEmail" type="text"/>
					</md-input-container>
					<md-input-container class="md-no-margin md-no-padding">
						<label>Password</label>
						<input type="password" ng-model="account.emailPassword" type="text"/>
					</md-input-container>
				</div>
				<div>
				<md-button type="submit" ng-click="change(1)" class="md-raised md-no-margin md-no-padding">
					Change
				</md-button>
				</div>
			</form>
		</div>
		<md-divider></md-divider>
		<div layout="row" layout-padding>
			<span>Change Password</span>
			<md-divider></md-divider>
			<form layout="column" flex>
				<md-input-container class="md-no-margin md-no-padding">
					<label>New Password</label>
					<input ng-model="account.newPassword1" type="password"/>
				</md-input-container>
				<md-input-container class="md-no-margin md-no-padding">
					<label>Input New Password Again</label>
					<input ng-model="account.newPassword2" type="password"/>
				</md-input-container>
				<md-input-container class="md-no-margin md-no-padding">
					<label>Old Password</label>
					<input  type="password" ng-model="account.passPassword" type="text"/>
				</md-input-container>
				<div>
				<md-button type="submit" ng-click="change(2)" class="md-raised md-no-margin md-no-padding">
					Change
				</md-button>
				</div>
			</form>
		</div>
	</div>

	<div style="visibility: hidden">
		<div class="md-dialog-container" id="forgotPass">
			<md-dialog flex="30">
				<md-toolbar style="background-color:#014421;color:white" layout-padding>
					<h4 class="md-no-margin">Reset Password</h4>
				</md-toolbar>
				<md-dialog-content>
					<div layout-padding layout-margin><p>
						A code was sent to your e-mail account. Use it to fill-up the form below.
					</p></div>
					<form layout="column" layout-padding>
						<div layout="column" layout-margin>
							<md-input-container class="md-no-margin">
								<label>Code</label>
								<input ng-model="resPas.Code" type="text" required></input>
							</md-input-container>
							<md-input-container class="md-no-margin">
								<label>New Password</label>
								<input ng-model="resPas.Password1" type="password" required></input>
							</md-input-container>
							<md-input-container class="md-no-margin">
								<label>Confirm New Password</label>
								<input ng-model="resPas.Password2" type="password" required></input>
							</md-input-container>
						</div>
						<div layout="row" layout-align="end center" class="md-no-padding">
							<md-button class="md-no-margin" ng-click="closeDialog()" >Cancel</md-button>
							<md-button class="md-no-margin" ng-click="forgotPass()" type="submit">Submit</md-button>
						</div>
					</form>
				</md-dialog-content>
			</md-dialog>
		</div>
	</div>
	
</div>

