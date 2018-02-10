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

<body layout="column" layout-fill>

	<header>
		<md-toolbar layout="row" layout-align="start center" layout-padding style="background-color:maroon" >
			<img style="width:10%;min-width:75px" src="<?=base_url().'logos/up.png'?>"/>
			<div layout="column" layout-align="start stretch" flex>
				<span class="md-display-1" >University of the Philippines Baguio</span>
				<span style="background-color:green">Student Registry</span>
			</div>
		</md-toolbar>
	</header>

	<main layout="column" flex="noshrink">