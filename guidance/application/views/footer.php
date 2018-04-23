<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
			
	</main>
	<footer style="background-color:lightgray">
		<div layout="row" layout-align="space-between center" style="font-family:'Century Gothic'">
			<div flex>
				<?php if ($this->ion_auth->is_Admin()):?>
					<a class="md-button md-no-margin" style="font-size:0.7em;" href="<?=base_url().'admin'?>">Admin</a>
				<?php endif;?>
			</div>
			<div layout="row" flex layout-align="center">
				<?php if ($this->ion_auth->logged_in()):?>
					<div layout-padding layout="row" style="padding:0 2vw 0 0" layout-align="center end">
						<span class="md-subhead" style="font-size:0.7em;">
							Logged in as: <?=$this->ion_auth->user()->row()->username?>
						</span>
						<span class="md-subhead"><a class="md-primary" style="font-size:0.7em;" href="<?=base_url().'main/logout'?>">Logout</a></span>						
					</div>
				<?php endif;?>
			</div>
			<div flex layout="column" layout-align="center end">
				<span layout-padding style="font-size:0.7em;">Copyright © 2018 BaelloDogupRamosSarmiento | All Rights Reserved</span>
			</div>
		</div>
	</footer>
</body>

</html>