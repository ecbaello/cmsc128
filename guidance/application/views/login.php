<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php if (!$this->ion_auth->logged_in()):?>
<div ng-controller="login" layout="row" layout-align="center center" flex>
	<md-content flex="25">
		<md-card>
			<md-card-title style="background-color:maroon;" class="md-no-padding md-no-margin">
				<md-content layout-padding layout="row" layout-align="center center" flex style="background-color:inherit">
					<span class="md-button md-no-margin md-no-padding">login</span>
				</md-content>
			</md-card-title>
			<md-card-content class="md-primary">
				<div layout-padding layout="row" layout-align="center">
					<i class="fas fa-user fa-10x fa-fw" style="font-size:12vw"></i>
				</div>
				<form layout="column" layout-margin>
					<md-input-container class="md-no-margin">
						<label>Username/Student Number</label>
						<input ng-model="account.username" required type='text'/>
					</md-input-container>
					<md-input-container class="md-no-margin">
						<label>Password</label>
						<input ng-model="account.password" required type='password'/>
					</md-input-container>
					<md-button type="submit" ng-click="login()">Submit
					</md-button>
				</form>
			</md-card-content>
		</md-card>
	</md-content>

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

<?php endif;?>
