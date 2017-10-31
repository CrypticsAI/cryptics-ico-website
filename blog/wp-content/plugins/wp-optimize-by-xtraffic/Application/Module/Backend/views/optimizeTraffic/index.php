<div class="row" style="margin-bottom:2%;">
	<h2 style="margin:0;"><?php echo WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME,' - (<small>Optimize Traffic</small>)'; ?></h2>
</div>

<div class="row">

	<form class="" id="optimize_traffic_settings_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
		
		<h3 class="optimize_traffic_button_add_traffic_module_container">
			<a href="#" class="button optimize_traffic_button_add_traffic_module"><b>Add Traffic Module</b></a>
		</h3>
		
		<?php echo $modulesOptionsText; ?>
		
		<div style="display:none;" class="wppepvn_hide optimize_traffic_traffic_module_sample" data_module_sample="<?php echo base64_encode($trafficModuleSample['module']); ?>"></div>
		
		<hr />
		
		<div class="form-group">
			<div class="col-sm-12">
				<input type="submit" name="submitButton" class="btn btn-primary" value="<?php $translate->e('Update Options'); ?>" />
			</div>
		</div>

	</form>

</div>