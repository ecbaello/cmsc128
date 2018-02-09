<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
	
	<link rel="stylesheet" href="<?= base_url().'css/fontawesome-all.min.css' ?>"/>
	<link rel="stylesheet" href="<?= base_url().'css/roboto.css' ?>"/>
	<link rel="stylesheet" href="<?= base_url().'css/angular-material.min.css' ?>"/>

	<script src="<?= base_url().'js/angular.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-animate.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-aria.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-messages.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-material.min.js' ?>"></script>
	<script src="<?= base_url().'js/app.js' ?>"></script>
	
</head>

<header>
	<md-toolbar>
		<div layout="column" layout-align="start start" layout-padding style="background-color:maroon" >
			<span class="md-display-1">University of the Philippines Baguio</span>
			<span>Student Information</span>
		</div>
    </md-toolbar>
</header>

<body layout="column">

