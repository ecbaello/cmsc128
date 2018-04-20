<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en" ng-app="app">

<head ng-controller="initializer" ng-init="init('<?=base_url()?>','<?= $this->security->get_csrf_token_name()?>','<?= $this->security->get_csrf_hash()?>')">
	<title>University of the Philippines Baguio Student Registry</title>
	
	<link rel="shortcut icon" href="<?= base_url().'logos/up.png' ?>" type="image/x-icon">
	<link rel="icon" href="<?= base_url().'logos/up.png' ?>" type="image/x-icon">
	
	<link rel="stylesheet" href="<?= base_url().'css/fontawesome-all.min.css' ?>"/>
	<link rel="stylesheet" href="<?= base_url().'css/roboto.css' ?>"/>
	<link rel="stylesheet" href="<?= base_url().'css/angular-material.min.css' ?>"/>

	<script src="<?= base_url().'js/angular.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-animate.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-aria.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-messages.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-material.min.js' ?>"></script>
	<script src="<?= base_url().'js/ng-table-to-csv.min.js' ?>"></script>
	<script src="<?= base_url().'js/app.js' ?>"></script>
	
	<style>
		.md-no-margin{
			margin:0;
		}
		.md-no-padding{
			padding:0;
		}
		
	</style>
	
</head>

<body layout="column" layout-fill>

	<header>
		<md-toolbar layout="row" layout-align="start center" style="background-color:maroon" >
			<img layout-padding style="width:10%;min-width:75px" src="<?=base_url().'logos/up.png'?>"/>
			<div layout="column" layout-align="start stretch" flex>
				<div layout="row">
					<div flex>
						<div layout="row">
							<span flex class="md-display-1" style="font-size:2em;font-family: 'Ostrich Sans'; color:white">University of the Philippines Baguio</span>
						</div>
						<span style="padding:5px; font-family: 'Century Gothic'; color:white;">Guidance Homepage</span>
					</div>
					
				</div>
				<md-content layout="row" layout-align="start center" style="background-color:inherit;font-size:0.8em;color:white">
					<a class="md-button" style="width:30%;font-size:0.7em;background-color:#014421" href="<?=base_url().'studentinfo'?>">Student Info</a>
					<a class="md-button" style="width:30%;font-size:0.7em;background-color:#014421" href="<?=base_url()?>">Home</a>
					<a class="md-button" style="width:30%;font-size:0.7em;background-color:#014421" href="<?=base_url().'tests'?>">Tests</a>
				</md-content>
			</div>
			
		</md-toolbar>
	</header>

	<main layout="column" flex="noshrink">