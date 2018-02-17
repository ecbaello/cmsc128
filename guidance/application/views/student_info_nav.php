<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div flex layout="row" layout-align="start center" layout-padding>
	<span class="md-headline">Student Information</span>
</div>
<div layout="row" layout-align="center start" flex>
	<md-content>
		<md-button href="<?= base_url().'studentinfo/manage'?>">
			<md-card>
				<md-card-title layout-padding layout="row" layout-align="center center" style="background-color:maroon" class="md-no-margin md-no-padding">
					<md-content style="min-width:200px;background-color:inherit">Manage Students</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-user fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>

		<md-button href="<?= base_url().'studentinfo/search'?>">
			<md-card>
				<md-card-title  class="md-no-margin md-no-padding" layout-padding layout="row" layout-align="center center" style="background-color:maroon">
					<md-content style="min-width:200px;background-color:inherit">Search Students</md-content>
				</md-card-title>
				<md-card-content class="md-primary">
					<div layout-padding>
						<i class="fas fa-user fa-10x fa-fw" style="font-size:12vw"></i>
					</div>
				</md-card-content>
			</md-card>
		</md-button>
	</md-content>

</div>
