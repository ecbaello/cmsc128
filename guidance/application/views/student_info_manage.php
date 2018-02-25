<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="student_search" >
	<div layout="row" layout-padding >
		<span>Search Student:</span>
		<input ng-model="searchInput" type="text" placeholder='Input Student Number'/>
		<button ng-click="search()"><i class="fas fa-search" style=""></i></button>
	</div>

</div>