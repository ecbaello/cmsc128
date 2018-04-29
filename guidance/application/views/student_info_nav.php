<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div flex layout="row" layout-align="space-around center">
	<div flex="10"></div>
	<span flex="80" class="md-headline" layout="row" layout-align="center center">Student Information</span>
	<div flex="10">
		<a href="<?= base_url().'studentinfo/bin'?>" class="md-button md-fab md-raised md-primary" layout="column" layout-align="center" title="Recycle Bin"><i class="fa fa-trash fa-2x" title="Recycle Bin"></i></a>
	</div>
</div>
<div layout="row" layout-align="center start" flex>
	<md-content>
		<md-button href="<?= base_url().'studentinfo/add'?>">
			<md-card>
				<md-card-title layout-padding layout="row" layout-align="center center" style="background-color:maroon" class="md-no-margin md-no-padding">
					<md-content style="min-width:200px;background-color:inherit;color:white">Add Students</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-user fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>
		
		<md-button href="<?= base_url().'studentinfo/manage'?>">
			<md-card>
				<md-card-title  class="md-no-margin md-no-padding" layout-padding layout="row" layout-align="center center" style="background-color:maroon">
					<md-content style="min-width:200px;background-color:inherit;color:white">Manage Students</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-user-md fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>
		
		<md-button href="<?= base_url().'studentinfo/formedit'?>">
			<md-card>
				<md-card-title  class="md-no-margin md-no-padding" layout-padding layout="row" layout-align="center center" style="background-color:maroon">
					<md-content style="min-width:200px;background-color:inherit;color:white">Edit Student Form</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-clipboard fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>
	</md-content>

</div>
