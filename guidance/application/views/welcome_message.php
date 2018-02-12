<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div flex layout="row" layout-align="start center" layout-padding>
	<span>Welcome to the University of the Philippines Baguio Student Registry.</span>
</div>
<div layout="row" layout-align="center center" flex>
	<md-content>
		<md-button href="<?= base_url().'/studentinfo'?>">
			<md-card>
				<md-card-title layout="row" layout-align="center center" style="background-color:maroon">
					<md-content style="min-width:200px;background-color:inherit">Student Information</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-user fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>

		<md-button href="<?= base_url().'/studentinfo'?>">
			<md-card>
				<md-card-title layout="row" layout-align="center center" style="background-color:maroon">
					<md-content style="min-width:200px;background-color:inherit">Tests</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-file-alt fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>
	</md-content>

</div>
