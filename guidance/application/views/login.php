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

</div>
<?php endif;?>
