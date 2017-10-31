<div class="row" style="margin-bottom:2%;">
	<h2 style="margin:0;"><?php echo WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME,' - (<small>Header & Footer</small>)'; ?></h2>
</div>

<div class="row">

	<form class="" id="header_footer_settings_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
		<div class="form-group">
			<h3 style="margin:0;">HEAD</h3>
		</div>
		
		<div class="form-group">
			<label for="code_add_head_all" class="col-sm-12 control-label">Code to be added on "<b><u>HEAD tag (&#x3C;head&#x3E;...&#x3C;/head&#x3E;)</u></b>" of "<b><u>EVERY PAGES</u></b>"</label>
			<div class="col-sm-12">
				<?php echo $form->render('code_add_head_all'); ?>
			</div>
		</div>
		
		<div class="form-group">
			<label for="code_add_head_home" class="col-sm-12 control-label">Code to be added on "<b><u>HEAD tag (&#x3C;head&#x3E;...&#x3C;/head&#x3E;)</u></b>" of the "<b><u>HOME PAGE ONLY</u></b>"</label>
			<div class="col-sm-12">
				<?php echo $form->render('code_add_head_home'); ?>
			</div>
		</div>
		
		
		<div class="form-group">
			<h3 style="margin:0;">FOOTER</h3>
		</div>
		
		<div class="form-group">
			<label for="code_add_footer_all" class="col-sm-12 control-label">Code to be added "<b><u>BEFORE THE END (&#x3C;/body&#x3E;)</u></b>" of the "<b><u>EVERY PAGES</u></b>"</label>
			<div class="col-sm-12">
				<?php echo $form->render('code_add_footer_all'); ?>
			</div>
		</div>
		
		<div class="form-group">
			<label for="code_add_footer_home" class="col-sm-12 control-label">Code to be added "<b><u>BEFORE THE END (&#x3C;/body&#x3E;)</u></b>" of the "<b><u>HOME PAGE ONLY</u></b>"</label>
			<div class="col-sm-12">
				<?php echo $form->render('code_add_footer_home'); ?>
			</div>
		</div>
		
		
		<div class="form-group">
			<h3 style="margin:0;">ARTICLE (POSTS/PAGES)</h3>
		</div>
		
		<div class="form-group">
			<label for="code_add_before_articles_all" class="col-sm-12 control-label">Code to be inserted "<b><u>BEFORE</u></b>" each "<b><u>ARTICLE</u></b>"</label>
			<div class="col-sm-12">
				<?php echo $form->render('code_add_before_articles_all'); ?>
			</div>
		</div>
		
		<div class="form-group">
			<label for="code_add_after_articles_all" class="col-sm-12 control-label">Code to be inserted "<b><u>AFTER</u></b>" each "<b><u>ARTICLE</u></b>"</label>
			<div class="col-sm-12">
				<?php echo $form->render('code_add_after_articles_all'); ?>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-12">
				<input type="submit" name="submitButton" class="btn btn-primary" value="<?php $translate->e('Update Options'); ?>" />
			</div>
		</div>

	</form>

</div>