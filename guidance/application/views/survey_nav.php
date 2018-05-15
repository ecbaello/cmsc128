<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!$this->ion_auth->is_admin()){
	header("Location: ".base_url()."survey");
}
?><!DOCTYPE html>
<div flex>
	<div flex layout="row" layout-align="center center" layout-padding>
		<span class="md-headline">UPB Guidance Survey</span> 
	</div>
	<div layout="row" layout-align="center center" layout-padding>
	<?php if($this->ion_auth->is_admin()): ?>
	<div layout="row" layout-align="center center">
		<a class="md-primary md-button md-no-margin md-raised" href="<?=base_url().'survey/passwords'?>">Student Passwords</a>
	</div>
	<?php endif; ?>
	<div layout="row" layout-align="center center">
		<a class="md-primary md-button md-no-margin md-raised" href="<?=base_url().'survey/take'?>">Go To Survey</a>
	</div>
	</div>
</div>