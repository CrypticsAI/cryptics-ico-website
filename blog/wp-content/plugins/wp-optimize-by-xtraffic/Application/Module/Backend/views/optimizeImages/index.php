
<div class="row" style="margin-bottom:2%;">
	<h2 style="margin:0;"><?php echo WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME; ?></h2>
</div>


<div class="row">

	<form class="" id="" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
		
		<input type="hidden" name="_wp_nonce" id="_wp_nonce" value="<?php echo $wp_nonce; ?>" />
		
		<div>

			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
			
				<li role="presentation" class="active">
					<a href="#optimize-alt-title" aria-controls="optimize-alt-title" role="tab" data-toggle="tab">Optimize ALT/TITLE</a>
				</li>
				
				<li role="presentation">
					<a href="#optimize-image-file" aria-controls="optimize-image-file" role="tab" data-toggle="tab">Optimize Image File</a>
				</li>

			</ul>
		</div>
		
		<!-- Tab panes -->
		<div class="tab-content">
			
			<div role="tabpanel" class="tab-pane active" id="optimize-alt-title">
				
				<div style="padding-top: 2%; padding-bottom: 2%;">
					
					<h3 style="margin-bottom: 20px;">Optimize ALT/TITLE</h3>
					
					<div>
						
						<p>You can enter any text in the field including these special tags :</p>
						
						<ul class="" style="list-style-type: disc;margin-left: 5%;">
							<li><b>%title</b> : post title or WooCommerce product title</li>
							<li><b>%category</b> : post category</li>
							<li><b>%tags</b> : post tags</li>
							<li><b>%product_tag</b> : WooCommerce product tags</li>
							<li><b>%product_cat</b> : WooCommerce product categories</li>
							<li><b>%img_name</b> : image file name (without extension)</li>
							<li><b>%img_title</b> : image title attributes</li>
							<li><b>%img_alt</b> : image alt attributes</li>
						</ul>
						
						<div style="margin-left:0; margin-bottom: 20px;margin-top: 20px;">
							<label for="optimize_images_alttext">
								<h4 style="margin: 0;">
									<?php $translate->e('ALT attribute (example: %img_name %title)'); ?> :
								</h4>
							</label>
							<?php echo $form->render('optimize_images_alttext'); ?>
							<p style="margin-left:0px;margin-bottom:0;"><i><?php //$translate->e('Plugin will ignore these request cookie.'); ?></i></p>
						</div>
						
						<div style="margin-left:0; margin-bottom: 20px;">
							<label for="optimize_images_titletext">
								<h4 style="margin: 0;">
									<?php $translate->e('TITLE attribute (example: %img_name photo)'); ?> :
								</h4>
							</label>
							<?php echo $form->render('optimize_images_titletext'); ?>
							<p style="margin-left:0px;margin-bottom:0;"><i><?php //$translate->e('Plugin will ignore these request cookie.'); ?></i></p>
						</div>
						
						<div class="" style="margin-bottom: 20px;">
							<div class="checkbox">
								<label for="optimize_images_override_alt">
									<h4 style="margin: 0;">
										<?php echo $form->render('optimize_images_override_alt'); ?>&nbsp;<?php $translate->e('Override image\'s alt (Recommended)'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div class="" style="margin-bottom: 20px;">
							<div class="checkbox">
								<label for="optimize_images_override_title">
									<h4 style="margin: 0;">
										<?php echo $form->render('optimize_images_override_title'); ?>&nbsp;<?php $translate->e('Override image\'s title'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						
					</div>
					
					<h3 style="margin-bottom: 20px;margin-top: 20px;">Example ALT/TITLE</h3>
					
					<ul class="" style="list-style-type: disc;margin-left: 5%;">
						<li>In a post titled "Landscape Pictures" there is a image named "forest.jpg"</li>
						<li>Setting alt attribute to "%img_name %title" will set alt="forest Landscape Pictures"</li>
						<li>Setting title attribute to "%img_name image" will set title="forest image"</li>
					</ul>
					
				</div>
				
			</div><!-- #optimize-alt-title -->
			
			
			
			<div role="tabpanel" class="tab-pane" id="optimize-image-file">
				
				<div style="padding-top: 2%; padding-bottom: 2%;">
					
					<h3 style="margin-bottom: 20px;">Optimize Image File</h3>
					
					<div>
					
						<div class="" style="margin-bottom: 20px;">
							<div class="checkbox">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('optimize_images_optimize_image_file_enable'); ?>&nbsp;<?php $translate->e('Enable Optimize Image File'); ?>
									</h4>
								</label>
							</div>
							<p style="margin-left:25px; color:red; margin-bottom: 0;"><i><?php //$translate->e('Warning : This option will help your site load faster. However, in some cases, web layout will be error. If an error occurs, you should disable this option.'); ?></i></p>
						</div>
						
						<div id="optimize_images_optimize_image_file_enable_container" class="wppepvn_toggle_show_hide_container">
							
							<hr />
							
							<div>
								<h4 style="margin-bottom: 0px;">Images Limit Size (Width & Height) :</h4>
								
								<div style="margin-left:2%; margin-bottom: 0px;">
									<div class="checkbox" style="margin-bottom: 5px;">
										<label>
											<?php $translate->e('Miminum Images\'s size (width & height) will be processed'); ?> : <?php echo $form->render('optimize_images_file_minimum_width_height'); ?> px (pixel)
										</label>
									</div>
								</div>
								
								<div style="margin-left:2%; margin-bottom: 0px;">
									<div class="checkbox" style="margin-bottom: 5px;">
										<label>
											<?php $translate->e('Maximum Images\'s size (width & height) will be processed'); ?> : <?php echo $form->render('optimize_images_file_maximum_width_height'); ?> px (pixel)
										</label>
									</div>
								</div>
							</div>
							
							<hr />
							
							<div style="<?php echo (WP_PEPVN_DEBUG ? '' : 'display:none;') ?>">
								
								<h4 style="margin-bottom: 20px;">Only handle file when uploading  : </h4>
								
								<div class="" style="margin-left:5%;margin-bottom: 20px;">
									<div class="checkbox">
										<label>
											<h4 style="margin: 0;">
												<?php echo $form->render('optimize_images_only_handle_file_when_uploading_enable'); ?>&nbsp;<?php $translate->e('Enable "Only handle file when uploading"'); ?>
											</h4>
										</label>
									</div>
									<p style="margin-left:25px; color:red; margin-bottom: 0;"><i><?php //$translate->e('Warning : This option will help your site load faster. However, in some cases, web layout will be error. If an error occurs, you should disable this option.'); ?></i></p>
								</div>
							</div>
							
							<div>
								<h4 style="margin-bottom: 20px;">Automatically resize the images :</h4>
								
								<div class="" style="margin-left:5%;margin-bottom: 20px;">
									<div class="checkbox">
										<label>
											<h4 style="margin: 0;">
												<?php echo $form->render('optimize_images_auto_resize_images_enable'); ?>&nbsp;<?php $translate->e('Enable "Auto Resize Images to fit the screen\'s width of the device"'); ?>
											</h4>
										</label>
									</div>
									<p style="margin-left:25px; color:red; margin-bottom: 0;"><i><?php //$translate->e('Warning : This option will help your site load faster. However, in some cases, web layout will be error. If an error occurs, you should disable this option.'); ?></i></p>
								</div>
							</div>
							
							<hr />
							
							<div>
								<h4 style="margin-bottom: 20px;">WATERMARK</h4>
								
								<div class="" style="margin-left:5%;margin-bottom: 20px;">
									<div class="checkbox">
										<label>
											<h4 style="margin: 0;">
												<?php echo $form->render('optimize_images_watermarks_enable'); ?>&nbsp;<?php $translate->e('Enable Watermark Images'); ?>
											</h4>
										</label>
									</div>
									<p style="margin-left:25px; color:red; margin-bottom: 0;"><i><?php //$translate->e('Warning : This option will help your site load faster. However, in some cases, web layout will be error. If an error occurs, you should disable this option.'); ?></i></p>
								</div>
								
								<div style="margin-left:5%;margin-bottom: 20px;" id="optimize_images_watermarks_enable_container" class="wppepvn_toggle_show_hide_container">
									
									<h4>Watermark Position</h4>
									
									<?php echo $watermark_positions_table; ?>
									
									<h4>Watermark Type</h4>
									
									<div class="" style="margin-left:5%;margin-bottom: 20px;">
										<div class="checkbox">
											<label>
												<h4 style="margin: 0;">
													<input type="checkbox" name="optimize_images_watermarks_watermark_type[text]" value="text" <?php echo (isset($bindPostData['optimize_images_watermarks_watermark_type']['text']) ? ' checked ' : ''); ?> class="wppepvn_toggle_show_hide_trigger" data-target="#watermark_type_text_container" />&nbsp;<?php $translate->e('Text'); ?>
												</h4>
											</label>
										</div>
										<p style="margin-left:25px; color:red; margin-bottom: 0;"><i><?php //$translate->e('Warning : This option will help your site load faster. However, in some cases, web layout will be error. If an error occurs, you should disable this option.'); ?></i></p>
										
										<div id="watermark_type_text_container" style="margin-left:5%;margin-bottom: 20px;" class="wppepvn_toggle_show_hide_container">
											
											<h4>Type TEXT Watermark</h4>
											
											<div style="margin-left:5%; margin-bottom: 20px;">
												<label>
													<h4 style="margin: 0;">
														<?php $translate->e('Watermark text value'); ?> :
													</h4>
												</label>
												<?php echo $form->render('optimize_images_watermarks_watermark_text_value'); ?>
												<p style="margin-left:0px;margin-bottom:0;"><i><?php //$translate->e('Plugin will ignore these request cookie.'); ?></i></p>
											</div>
											
											<div style="margin-left:5%; margin-bottom: 20px;">
												<label>
													<h4 style="margin: 0;">
														<?php $translate->e('Fonts'); ?> :
													</h4>
												</label>
												<?php echo $form->render('optimize_images_watermarks_watermark_text_font_name'); ?>
												
											</div>
											
											<div class="" style="margin-left:5%;margin-bottom: 20px;">
												<div class="">
													<label>
														<h4 style="margin: 0;">
															Text size : <?php echo $form->render('optimize_images_watermarks_watermark_text_size'); ?> (pt/%)
														</h4>
													</label>
													<p style="margin-left:0px;margin-bottom:0;"><i><?php $translate->e('In case you set Text size by percent (Ex: 20%), plugin will create watermark having its width is about 20% of Image\'s width'); ?></i></p>
												</div>
											</div>
											
											<div class="" style="margin-left:5%;margin-bottom: 20px;">
												<div class="">
													<label>
														<h4 style="margin: 0;">
															Text color : #<?php echo $form->render('optimize_images_watermarks_watermark_text_color'); ?>
														</h4>
													</label>
												</div>
											</div>
											
											<div style="margin-left:5%; margin-bottom: 20px;">
												<h4>Text Margin :</h4>
												
												<div class="" style="margin-left:5%;margin-bottom: 20px;">
													<div class="">
														<label>
															<h4 style="margin: 0;">
																X : <?php echo $form->render('optimize_images_watermarks_watermark_text_margin_x'); ?> px
															</h4>
														</label>
													</div>
												</div>
												
												<div class="" style="margin-left:5%;margin-bottom: 20px;">
													<div class="">
														<label>
															<h4 style="margin: 0;">
																Y : <?php echo $form->render('optimize_images_watermarks_watermark_text_margin_y'); ?> px
															</h4>
														</label>
													</div>
												</div>
												
											</div>
											
											
											<div style="margin-left:5%; margin-bottom: 20px;">
												<label>
													<h4 style="margin: 0;">
														<?php $translate->e('Text Opacity'); ?> :
													</h4>
												</label>
												<?php echo $form->render('optimize_images_watermarks_watermark_text_opacity_value'); ?>
											</div>
											
											<div style="margin-left:5%; margin-bottom: 20px;">
												<h4 style="margin: 0;">
													<?php $translate->e('Text background'); ?> :
												</h4>
												
												<div class="" style="margin-left:5%;margin-bottom: 20px;">
													<div class="checkbox">
														<label>
															<h4 style="margin: 0;">
																<?php echo $form->render('optimize_images_watermarks_watermark_text_background_enable'); ?>&nbsp;<?php $translate->e('Enable Watermark Text Background'); ?>
															</h4>
														</label>
													</div>
													
												</div>
												
												<div id="optimize_images_watermarks_watermark_text_background_enable_container" class="wppepvn_toggle_show_hide_container" style="margin-left:10%;margin-bottom: 20px;">
													<div class="">
														<label>
															<h4 style="margin: 0;">
																Background color : #<?php echo $form->render('optimize_images_watermarks_watermark_text_background_color'); ?>
															</h4>
														</label>
													</div>
												</div>
												
											</div>
											
										</div>
										
									</div>
									
									<div class="" style="margin-left:5%;margin-bottom: 20px;">
										<div class="checkbox">
											<label>
												<h4 style="margin: 0;">
													<input type="checkbox" name="optimize_images_watermarks_watermark_type[image]" value="image" <?php echo (isset($bindPostData['optimize_images_watermarks_watermark_type']['image']) ? ' checked ' : ''); ?> class="wppepvn_toggle_show_hide_trigger" data-target="#watermark_type_image_container" />&nbsp;<?php $translate->e('Image (Your Logo)'); ?>
												</h4>
											</label>
										</div>
										<p style="margin-left:25px; color:red; margin-bottom: 0;"><i><?php //$translate->e('Warning : This option will help your site load faster. However, in some cases, web layout will be error. If an error occurs, you should disable this option.'); ?></i></p>
									</div>
									
									<div id="watermark_type_image_container" class="wppepvn_toggle_show_hide_container" style="margin-left:5%;margin-bottom: 20px;">
										
										<h4 style="margin-left:5%;margin-bottom: 20px;">Type IMAGE Watermark (Your Logo)</h4>
										
										<div style="margin-left:5%; margin-bottom: 20px;">
											<label>
												<h4 style="margin: 0;">
													<?php $translate->e('Watermark image url (your logo url)'); ?> :
												</h4>
											</label>
											<?php echo $form->render('optimize_images_watermarks_watermark_image_url'); ?>
										</div>
									
										<div class="" style="margin-left:5%;margin-bottom: 20px;">
											<div class="">
												<label>
													<h4 style="margin: 0;">
														Width : <?php echo $form->render('optimize_images_watermarks_watermark_image_width'); ?> (px/%)
													</h4>
												</label>
											</div>
											<p style="margin-left:0px;margin-bottom:0;"><i><?php 
												$translate->e('In the case of you set "Watermark image size" is number percent (Ex: 20%), plugin will resize watermark (your logo) has width is 20% of Image\'s width'); 
											?></i></p>
										</div>
										
										<div style="margin-left:5%; margin-bottom: 20px;">
											<h4>Margin :</h4>
											
											<div class="" style="margin-left:5%;margin-bottom: 20px;">
												<div class="">
													<label>
														<h4 style="margin: 0;">
															X : <?php echo $form->render('optimize_images_watermarks_watermark_image_margin_x'); ?> px
														</h4>
													</label>
												</div>
											</div>
											
											<div class="" style="margin-left:5%;margin-bottom: 20px;">
												<div class="">
													<label>
														<h4 style="margin: 0;">
															Y : <?php echo $form->render('optimize_images_watermarks_watermark_image_margin_y'); ?> px
														</h4>
													</label>
												</div>
											</div>
											
										</div>
										
									</div>
									
								</div>
								
							</div> <!-- WATERMARK -->
							
							<hr />
							
							<div>
								<h4>Optimize Image Quality</h4>
								
								<div style="margin-left:5%; margin-bottom: 20px;">
									
									<?php echo $form->render('optimize_images_image_quality_value'); ?>
									<p style="margin-left:0px;margin-bottom:0;"><i><?php 
										$translate->e('To reduce the size of image file, you can reduce value in image\'s Quality Bar above. If you set a value of 100, your image keeps the original quality and file size. ( Best value recommended is from 80 to 90 )'); 
									?></i></p>
								</div>
								
							</div>
							
							<hr />
							
							<div>
								<h4>Rename Image's Filename</h4>
								
								<div style="margin-left:5%; margin-bottom: 20px;">
									
									<p>You can enter any text in the field including these special tags :</p>
									
									<ul style="list-style-type: disc;margin-left: 5%;margin-bottom: 20px;">
										<li><b>%title</b> : post title or WooCommerce product title</li>
										<li><b>%category</b> : post category</li>
										<li><b>%tags</b> : post tags</li>
										<li><b>%product_tag</b> : WooCommerce product tags</li>
										<li><b>%product_cat</b> : WooCommerce product categories</li>
										<li><b>%img_name</b> : image file name (without extension)</li>
										<li><b>%img_title</b> : image title attributes</li>
										<li><b>%img_alt</b> : image alt attributes</li>
									</ul>
									
									<div style="margin-bottom: 20px;">
										
										<div style="margin-bottom: 20px;">
											<label>
												<h4 style="margin: 0;">
													<?php $translate->e('New Image Filename (example: %img_name %title)'); ?> :
												</h4>
											</label>
											<?php echo $form->render('optimize_images_rename_img_filename_value'); ?>
											
											<p style="margin-left:0px;margin-bottom:0;"><i><?php 
												$translate->e('Leave a blank if you want to keep image\'s original filename'); 
											?></i></p>
										</div>
										
										<h4>Example Rename Image's Filename</h4>
										
										<ul style="list-style-type: disc;margin-left: 5%;">
											<li>In a post titled "Landscape Pictures" there is a image named "forest.jpg"</li>
											<li>Setting New Image Filename to "%title %img_name" will set new image's filename : "Landscape-Pictures-forest.jpg"</li>
											<li>Setting New Image Filename to "xTraffic %img_name %title" will set new image's filename : "xTraffic-forest-Landscape-Pictures.jpg"</li>
										</ul>
									</div>
									
								</div>
								
							</div>
							
							<hr />
							
							<div>
								<h4>Performance</h4>
								
								<div style="margin-left:5%; margin-bottom: 20px;">
									<label>
										<h4 style="margin: 0;">
											<?php $translate->e('The maximum number of files are handled for each request'); ?> :
										</h4>
									</label>
									<?php echo $form->render('optimize_images_maximum_files_handled_each_request'); ?>
									
									<p style="margin-left:0px;margin-bottom:0;"><i><?php 
										$translate->e('In the case of your hosting is in low performance, you should set the maximum number of files handled for each query to avoid overloading your hosting. In case you leave a blank or set a value of 0, plugin will handle all files that are not been handled yet'); 
									?></i></p>
								</div>
								
								<div class="" style="margin-left:5%;margin-bottom: 20px;">
									<div class="checkbox">
										<label>
											<h4 style="margin: 0;">
												<?php echo $form->render('optimize_images_handle_again_files_different_configuration_enable'); ?>&nbsp;<?php $translate->e('Enable to reprocess files which have different configuration with its current configuration'); ?>
											</h4>
										</label>
									</div>
									<p style="margin-left:25px; margin-bottom: 0;"><i><?php $translate->e('In case you change configuration, plugin will check and reprocess all processed files that are different with the current configuration, by overwriting old file if file has the same filename or create a new file if the filename is different (Set at "Rename Image Filename")'); ?></i></p>
								</div>
								
								<div id="optimize_images_handle_again_files_different_configuration_enable_container" class="wppepvn_toggle_show_hide_container" style="margin-left:10%;margin-bottom: 20px;">
									<div class="checkbox">
										<label>
											<h4 style="margin: 0;">
												<input type="checkbox" name="optimize_images_handle_again_files_different_configuration_enable" value="text" class="" data-target="" />&nbsp;<?php $translate->e('Enable to remove old files (if available) that are different with the current configuration'); ?>
											</h4>
										</label>
									</div>
								</div>
							</div>
							
							
							
							<div>
								<h4>Exclude</h4>
								
								<div class="" style="margin-left:5%;margin-bottom: 20px;">
									<div class="checkbox">
										<label>
											<h4 style="margin: 0;">
												<?php echo $form->render('optimize_images_file_exclude_external_url_enable'); ?>&nbsp;<?php $translate->e('Enable exclude external images'); ?>
											</h4>
										</label>
									</div>
									<p style="margin-left:25px; margin-bottom: 0;"><i><?php $translate->e('Plugin will ignore all external images ( Which images not in your self-hosted ).'); ?></i></p>
								</div>
								
								<div id="" class="" style="margin-left:5%;margin-bottom: 20px;">
									
									<label for="optimize_images_alttext">
										<h4 style="margin: 0;">
											<?php $translate->e('Exclude image\'s url (Contained in url, separate them by comma)'); ?> :
										</h4>
									</label>
									<?php echo $form->render('optimize_images_file_exclude_external_url'); ?>
									<p style="margin-left:0px;margin-bottom:0;"><i><?php $translate->e('Plugin will ignore these image\'s url.'); ?></i></p>
								
								</div>
							</div>
							
						</div><!-- #optimize_images_optimize_image_file_enable_container -->
						
					</div>
					
				</div>
				
				<div class="wpoptimizebyxtraffic_fixed wpoptimizebyxtraffic_bottom_right" id="wppepvn_preview_processed_image">
					<div>
						<h6>Preview Processed Image ( <a href="#show_hide">Show/Hide</a> )</h6> 
						<div class="wppepvn_preview_processed_image_content">
							<ul>
								<li>
									<label for="optimize_images_watermarks_preview_processed_image_example_image_url"> Example image url : </label><br />
									<input type="text" 
										name="optimize_images_watermarks_preview_processed_image_example_image_url"  
										value="<?php echo WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_ROOT_URI.'public/images/demo.jpg';?>" 
										title="" style="" 
									/>
								</li>
								
								<li class="wppepvn_preview_process_image_img">
									
								</li>
								
								<li class="wppepvn_preview_process_image_buttons">
									<button type="button" class="button-primary wppepvn_do_preview">Preview</button>
								</li>
							</ul>
						</div>
					</div>
					
				</div>
				
				
			</div><!-- #optimize-image-file -->
			
		</div>
		
		<div class="form-group">
			<div class="col-sm-12">
				<input type="submit" name="submitButton" class="btn btn-primary" value="<?php $translate->e('Update Options'); ?>" />
			</div>
		</div>
		
	</form>

</div>