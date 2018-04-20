<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
			
	</main>
	<footer style="background-color:lightgray">
		<div layout="row" layout-align="space-between center">
			<?php if ($this->ion_auth->is_Admin()):?>
				<a class="md-button md-no-margin" style="font-size:0.7em;font-family:'Century Gothic'" href="<?=base_url().'admin'?>">Admin</a>
			<?php endif;?>
			<div layout="row" >
						<?php if ($this->ion_auth->logged_in()):?>
							<div layout-padding layout="row" style="padding:0 2vw 0 0" layout-align="start end">
								<span class="md-subhead" style="font-size:0.7em;font-family:'Century Gothic'">
									Logged in as: <?=$this->ion_auth->user()->row()->username?>
								</span>
								<span class="md-subhead"><a class="md-primary" style="font-size:0.7em;font-family:'Century Gothic'" href="<?=base_url().'main/logout'?>">Logout</a></span>						
							</div>
						<?php endif;?>
					</div>
			<span layout-padding style="font-size:0.7em;font-family:'Century Gothic'">Copyright © 2018 BaelloDogupRamosSarmiento | All Rights Reserved</span>
		</div>
	</footer>
</body>

</html>