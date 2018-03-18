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
	<link rel="stylesheet" href="<?= base_url().'css/font-awesome.css' ?>"/>

	<script src="<?= base_url().'js/angular.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-animate.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-aria.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-messages.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-material.min.js' ?>"></script>
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
				<span class="md-display-1" >University of the Philippines Baguio</span>
				<span style="background-color:#014421;padding:5px">Guidance Homepage</span>
				<md-content layout="row" layout-align="start center" style="background-color:inherit;font-size:0.8em;color:white">
					<a class="md-button" style="width:30%;font-size:0.7em;background-color:#5D0F0D" href="<?=base_url().'studentinfo'?>">Student Information</a>
					<a class="md-button" style="width:30%;font-size:0.7em;background-color:#5D0F0D" href="<?=base_url()?>">Home</a>
					<a class="md-button" style="width:30%;font-size:0.7em;background-color:#5D0F0D" href="<?=base_url().'tests'?>">Tests</a>
				</md-content>
			</div>
		</md-toolbar>
	</header>

	<main layout="column" flex="noshrink">