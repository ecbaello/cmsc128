<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div layout="row" layout-align="center center" flex>
	<form ng-controller="tests" ng-init='init(<?= '"'.$this->security->get_csrf_token_name().'","'.$this->security->get_csrf_hash().'"'?>);'>
		<input type="submit" value="Boom" ng-click="test()"></input>
	</form>

</div>
