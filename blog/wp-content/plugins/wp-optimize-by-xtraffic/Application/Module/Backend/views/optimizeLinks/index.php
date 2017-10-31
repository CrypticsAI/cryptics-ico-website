<div class="row" style="margin-bottom:0%;">
	<h2 style="margin:0;margin-bottom:2%;"><?php echo WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_NAME,' - (<small>Optimize Links</small>)'; ?></h2>
</div>

<div class="row">

	<form class="" id="" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
		
		<div style="padding-top: 0%; padding-bottom: 2%;">
			
			<h3 style="margin-bottom: 20px;">Overview "Optimize Links"</h3>
			
			<p>
				"Optimize Links" can automatically link keywords in your posts and comments with your focused links or best related posts.
				<br />This plugin allows you to set nofollow attribute and open links in a new window.
			</p>
			<br />
			<div>
			
				<div class="" style="margin-bottom: 20px;">
					<div class="checkbox">
						<label>
							<h4 style="margin: 0;">
								<?php echo $form->render('optimize_links_enable'); ?>&nbsp;<?php $translate->e('Enable Optimize Links'); ?>
							</h4>
						</label>
					</div>
				</div>
				
				<div id="optimize_links_enable_container" class="wppepvn_toggle_show_hide_container">
					
					<div style="margin-left:5%; margin-bottom: 20px;">
						
						<h4 style="margin: 0;">
							Internal Links (Process Internal Links In Posts/Pages)
						</h4>
						
						<p>"Optimize Links" can automatically process your posts, pages, comments and feed's content with keywords and links.</p>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('process_in_post'); ?>&nbsp;<?php $translate->e('Process Posts\'s Content ( Recommended )'); ?>
									</h4>
								</label>
							</div>
							<p style="margin-left:25px;"><i><?php //$translate->e('Front Page include : Home page, category page, tag page, author page, date page, archives page,...'); ?></i></p>
							
							<div id="process_in_post_container" class="wppepvn_toggle_show_hide_container">
								<div style="margin-left:5%; margin-bottom: 20px;">
									<div class="checkbox" style="margin-bottom: 5px;">
										<label>
											<h4 style="margin: 0;">
												<?php echo $form->render('link_to_postself'); ?>&nbsp;<?php $translate->e('Allow links to itself'); ?>
											</h4>
										</label>
									</div>
									<p style="margin-left:25px;"><i><?php //$translate->e('Front Page include : Home page, category page, tag page, author page, date page, archives page,...'); ?></i></p>
								</div>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('process_in_page'); ?>&nbsp;<?php $translate->e('Process Pages\'s Content'); ?>
									</h4>
								</label>
							</div>
							
							
							<div id="process_in_page_container" class="wppepvn_toggle_show_hide_container">
								<div style="margin-left:5%; margin-bottom: 20px;">
									<div class="checkbox" style="margin-bottom: 5px;">
										<label>
											<h4 style="margin: 0;">
												<?php echo $form->render('link_to_pageself'); ?>&nbsp;<?php $translate->e('Allow links to itself'); ?>
											</h4>
										</label>
									</div>
									<p style="margin-left:25px;"><i><?php //$translate->e('Front Page include : Home page, category page, tag page, author page, date page, archives page,...'); ?></i></p>
								</div>
							</div>
							
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<input type="checkbox" name="link_to[cats]" value="cats" <?php 
											echo (isset($bindPostData['link_to']['cats']) ? ' checked ' : '');
										?> />&nbsp;<?php $translate->e('Link to Categories'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<input type="checkbox" name="link_to[tags]" value="tags" <?php 
											echo (isset($bindPostData['link_to']['tags']) ? ' checked ' : '');
										?> />&nbsp;<?php $translate->e('Link to Tags'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('process_in_comment'); ?>&nbsp;<?php $translate->e('Process Comments\'s Content'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('process_in_feed'); ?>&nbsp;<?php $translate->e('Process RSS feeds Content'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('use_cats_as_keywords'); ?>&nbsp;<?php $translate->e('Use categories\'s name as keywords'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('use_tags_as_keywords'); ?>&nbsp;<?php $translate->e('Use tags\'s name as keywords'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('autolinks_case_sensitive'); ?>&nbsp;<?php $translate->e('Case sensitive matching'); ?>
									</h4>
								</label>
							</div>
							<p style="margin-left:25px;"><i><?php $translate->e('Set whether matching should be case sensitive.'); ?></i></p>
							
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('autolinks_new_window'); ?>&nbsp;<?php $translate->e('Open autolinks in new window'); ?>
									</h4>
								</label>
							</div>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('process_only_in_single'); ?>&nbsp;<?php $translate->e('Process only single page'); ?>
									</h4>
								</label>
							</div>
							<p style="margin-left:25px;"><i><?php $translate->e('To reduce database load you can choose "Optimize Links" process only on single page (for example not on home page or archives).'); ?></i></p>
						</div>
						
						
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label style="padding: 0;">
									<h4 style="margin: 0;">
										Max Links : <?php echo $form->render('maxlinks'); ?> (Recomend from 2 to 5 links) 
									</h4>
								</label>
							</div>
							<p style="margin-left:0;"><i><?php $translate->e('You can limit the maximum number of different links which will generate per post/page/comments. Set to 0 for no limit.'); ?></i></p>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<h4 style="">
								Exclude :
							</h4>
							
							<div style="margin-left:5%; margin-bottom: 20px;">
								<label>
									<h4 style="margin: 0;">
										<?php $translate->e('Exclude url (Contained in url, separate them by comma)'); ?> :
									</h4>
								</label>
								<?php echo $form->render('autolinks_exclude_url'); ?>
								<p><i>You may wish to forbid automatically linking on certain posts or pages. Separate them by comma.</i></p>
							</div>
							
							
							<div style="margin-left:5%; margin-bottom: 20px;">
								<div class="checkbox" style="margin-bottom: 5px;">
									<label>
										<h4 style="margin: 0;">
											<?php echo $form->render('exclude_heading'); ?>&nbsp;<?php $translate->e('Prevent linking in heading tags (h1,h2,h3,h4,h5,h6).'); ?>
										</h4>
									</label>
								</div>
							</div>
							
						</div>
						
						
						
					</div>
					
					<hr />
					
					<div style="margin-left:5%; margin-bottom: 20px;">
						
						<h4 style="">
							Custom Keywords/Targets Links
						</h4>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<p><i>Here you can enter manually the extra keywords you want to automatically link. Use comma (,) to separate keywords and target url. Use a new line for new set of urls and keywords. You can have these keywords link to any urls, not only your site.</i></p>
							<p><i>If you don't set any url with keywords, this plugin will automatically find posts/pages having the best related content and link to these keywords</i></p>
							<p><i>You must use full link with <u>http://</u> or <u>https://</u> (example : <u>http://wordpress.org/plugins/</u> or <u>https://wordpress.org/plugins/</u>)</i></p>
							
							<div style="margin:0; margin-bottom: 1%;">
								<?php echo $form->render('data_custom'); ?>
							</div>
							
							<p style="margin:0;">
								<b>Example :</b>
							</p>
							
							<p style="margin-left:2%; margin-bottom: 20px;">
								seo, wordpress, plugin, http://wordpress.org/<br />
								ads, marketing<br />
								seo plugin, wordpress plugin,http://wordpress.org/,http://wordpress.org/plugins/
							</p>
							
							<h4 style="">
								Load custom keywords & links from a URL. (Note: this appends to the list above.)
							</h4>
							<div style="margin:0; margin-bottom: 1%;">
								<?php echo $form->render('data_custom_url'); ?>
							</div>
						</div>
					</div>
					
					<hr />
					
					<div style="margin-left:5%; margin-bottom: 20px;">
						
						<h4 style="margin: 0;">
							External Links
						</h4>
						
						<p><i>"Optimize Links" can open external links in new window and add nofollow attribute.</i></p>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('external_nofollow'); ?>&nbsp;<?php $translate->e('Add nofollow attribute to external links'); ?>
									</h4>
								</label>
							</div>
							<p style="margin-left:25px;"><i><?php //$translate->e('Front Page include : Home page, category page, tag page, author page, date page, archives page,...'); ?></i></p>
						</div>
						
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<div class="checkbox" style="margin-bottom: 5px;">
								<label>
									<h4 style="margin: 0;">
										<?php echo $form->render('external_new_window'); ?>&nbsp;<?php $translate->e('Open external links in new window'); ?>
									</h4>
								</label>
							</div>
							<p style="margin-left:25px;"><i><?php //$translate->e('Front Page include : Home page, category page, tag page, author page, date page, archives page,...'); ?></i></p>
						</div>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<label>
								<h4 style="margin: 0;">
									<?php $translate->e('Exclude url (Contained in url, separate them by comma)'); ?> :
								</h4>
							</label>
							<?php echo $form->render('external_exclude_url'); ?>
							<p><i></i></p>
						</div>
						
					</div>
					
					
					<div style="margin-left:5%; margin-bottom: 20px;">
						
						<h4 style="margin: 0;">
							Nofollow Links
						</h4>
						
						<p><i>You may wish to add nofollow links (include internal links & external links). Separate them by comma. (contained in url)</i></p>
						
						<div style="margin-left:5%; margin-bottom: 20px;">
							<label>
								<h4 style="margin: 0;">
									<?php $translate->e('Add nofollow attribute to urls (Contained in url, separate them by comma)'); ?> :
								</h4>
							</label>
							<?php echo $form->render('nofollow_url'); ?>
							<p><i></i></p>
						</div>
						
					</div>
					
				</div><!-- #optimize_links_enable_container -->
				
			</div>
			
		</div>
		
		
		
		<div class="form-group">
			<div class="col-sm-12">
				<input type="submit" name="submitButton" class="btn btn-primary" value="<?php $translate->e('Update Options'); ?>" />
			</div>
		</div>

		
		
	</form>

</div>