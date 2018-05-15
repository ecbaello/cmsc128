<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div layout="row" layout-align="center center" layout-padding layout-margin>
	<span class="md-headline">Welcome to the Guidance Office Homepage.</span>
</div>
<div layout="row" layout-align="center start">
	<md-content>
		<md-button href="<?= base_url().'studentinfo'?>">
			<md-card>
				<md-card-title layout-padding layout="row" layout-align="center center" style="background-color:maroon" class="md-no-margin md-no-padding">
					<md-content style="min-width:200px;background-color:inherit;color:white">Student Information</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-user fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>

		<md-button href="<?= base_url().'survey'?>">
			<md-card>
				<md-card-title  class="md-no-margin md-no-padding" layout-padding layout="row" layout-align="center center" style="background-color:maroon">
					<md-content style="min-width:200px;background-color:inherit;color:white">Survey</md-content>
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
