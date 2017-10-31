<?php
namespace WPOptimizeByxTraffic\Application\Service;

use WPOptimizeByxTraffic\Application\Service\PepVN_Data
	, WPOptimizeByxTraffic\Application\Service\PepVN_Cache
	, WPOptimizeByxTraffic\Application\Service\PepVN_CacheSimpleFile
	, WpPepVN\DependencyInjection
	, WpPepVN\System
;

class WpActionManager 
{
	public $di = false;
	
	private $_activated_plugins_data = array();
	private $_deactivated_plugins_data = array();
	
    public function __construct(DependencyInjection $di) 
    {
		$this->di = $di;
		
		add_action('comment_post', array($this, 'comment_post'), WP_PEPVN_PRIORITY_LAST, 2);
		add_action('wp_insert_comment', array($this, 'wp_insert_comment'), WP_PEPVN_PRIORITY_LAST, 2);
		add_action('wp_set_comment_status', array($this, 'wp_set_comment_status'), WP_PEPVN_PRIORITY_LAST, 2);
		add_action('delete_comment', array($this, 'delete_comment'), WP_PEPVN_PRIORITY_FIRST, 1);
		add_action('deleted_comment', array($this, 'deleted_comment'), WP_PEPVN_PRIORITY_FIRST, 1);
		add_action('edit_comment', array($this, 'edit_comment'), WP_PEPVN_PRIORITY_LAST, 1);
		add_action('transition_comment_status', array($this, 'transition_comment_status'), WP_PEPVN_PRIORITY_LAST, 3);
		
		add_action('transition_post_status', array($this, 'transition_post_status'), WP_PEPVN_PRIORITY_LAST, 3);
		add_action('edit_post', array($this, 'edit_post'), WP_PEPVN_PRIORITY_FIRST, 2);
		add_action('save_post', array($this, 'save_post'), WP_PEPVN_PRIORITY_FIRST, 3);
		add_action('wp_insert_post', array($this, 'wp_insert_post'), WP_PEPVN_PRIORITY_FIRST, 3);
		add_action('updated_postmeta', array($this, 'updated_postmeta'), WP_PEPVN_PRIORITY_FIRST, 4);
		add_action('publish_future_post', array($this, 'publish_future_post'), WP_PEPVN_PRIORITY_FIRST, 1);
		add_action('delete_post', array($this, 'delete_post'), WP_PEPVN_PRIORITY_FIRST, 1);
		add_action('after_delete_post', array($this, 'after_delete_post'), WP_PEPVN_PRIORITY_FIRST, 1);
		add_action('deleted_post', array($this, 'deleted_post'), WP_PEPVN_PRIORITY_FIRST, 1);
		
		add_action('edit_attachment', array($this, 'edit_attachment'), WP_PEPVN_PRIORITY_FIRST, 1);
		add_action('add_attachment', array($this, 'add_attachment'), WP_PEPVN_PRIORITY_FIRST, 1);
		add_action('delete_attachment', array($this, 'delete_attachment'), WP_PEPVN_PRIORITY_FIRST, 1);
		
		//add_action('clean_post_cache', array($this, 'clean_post_cache'), WP_PEPVN_PRIORITY_FIRST, 2);	//should not use. It will clean cache when user add new post
		
		add_action('activated_plugin', array($this, 'activated_plugin'), WP_PEPVN_PRIORITY_LAST, 2);
		add_action('deactivated_plugin', array($this, 'deactivated_plugin'), WP_PEPVN_PRIORITY_LAST, 2);
		
		add_action('switch_theme', array($this, 'switch_theme'), WP_PEPVN_PRIORITY_LAST, 2);
		
		add_action('wp_login', array($this, 'wp_login'), WP_PEPVN_PRIORITY_FIRST, 2);
		
	}
	
	private function _registerCleanCache($data_type = ',common,')
	{
		
		$cacheManager = $this->di->getShared('cacheManager');
		
		$cacheManager->registerCleanCache($data_type);
	}
	
	/*
		Runs just after a comment is saved in the database. 
		Action function arguments: comment ID, approval status ("spam", or 0/1 for disapproved/approved).
		http://codex.wordpress.org/Plugin_API/Action_Reference/comment_post
	*/
	public function comment_post(
		$comment_ID	//The comment that is created.
		, $comment_approved //$comment_approved
	) {
		$this->_registerCleanCache();
	}
	
	/*
		Runs whenever a comment is created.
		http://codex.wordpress.org/Plugin_API/Action_Reference/wp_insert_comment
		@param int $id      The comment ID.
		@param obj $comment Comment object.
	*/
	public function wp_insert_comment(
		$id
		, $comment
	) {
		$this->_registerCleanCache();
	}
	
	/*
		Runs when the status of a comment changes. 
		Action function arguments: comment ID, status string indicating the new status ("delete", "approve", "spam", "hold").
		@param int         $comment_id     Comment ID.
		@param string|bool $comment_status Current comment status. Possible values include
			'hold', 'approve', 'spam', 'trash', or false.
	*/
	public function wp_set_comment_status(
		$comment_id
		, $comment_status
	) {
		$this->_registerCleanCache();
	}
	
	/*
		Runs just before a comment is deleted. Action function arguments: comment ID.
		Fires immediately before a comment is deleted from the database.
		@param int $comment_id The comment ID.
	*/
	public function delete_comment(
		$comment_id
	) {
		$this->_registerCleanCache();
	}
	
	/*
		Runs just after a comment is deleted. Action function arguments: comment ID.
		Fires immediately after a comment is deleted from the database.
		@param int $comment_id The comment ID.
	*/
	public function deleted_comment(
		$comment_id
	) {
		$this->_registerCleanCache();
	}
	
	/*
		Fires immediately after a comment is updated in the database.
		The hook also fires immediately before comment status transition hooks are fired.
		@param int $comment_ID The comment ID.
	*/
	public function edit_comment(
		$comment_ID
	) {
		$this->_registerCleanCache();
	}
	
	/*
		Fires when the comment status is in transition.
		@param int|string $new_status The new comment status.
		@param int|string $old_status The old comment status.
		@param object     $comment    The comment data.
	*/
	public function transition_comment_status(
		$new_status
		, $old_status
		, $comment
	) {
		$this->_registerCleanCache();
	}
	
	/*
		Fires when a post is transitioned from one status to another.
		@param string  $new_status New post status.
		@param string  $old_status Old post status.
		@param WP_Post $post       Post object.
	*/
	public function transition_post_status(
		$new_status
		, $old_status
		, $post
	) {
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('transition_post_status')) {
			$hook->do_action('transition_post_status', array(
				'new_status' => $new_status
				, 'old_status' => $old_status
				, 'post' => $post
			));
		}
		
		if('new' !== $old_status) {
			if($new_status !== $old_status) {
				
				if($hook->has_action('change_post_status')) {
					$hook->do_action('change_post_status', array(
						'new_status' => $new_status
						, 'old_status' => $old_status
						, 'post' => $post
					));
				}
				
				$this->_registerCleanCache();
			}
		}
	}
	
	/*
		Fires once an existing post has been updated.
		@param int     $post_ID Post ID.
		@param WP_Post $post    Post object.
	*/
	public function edit_post(
		$post_ID
		, $post
	) {
		
	}
	
	/*
		save_post is an action triggered whenever a post or page is created or updated, which could be from an import
			, post/page edit form, xmlrpc, or post by email.
		The data for the post is stored in $_POST, $_GET or the global $post_data, depending on how the post was edited. 
		For example, quick edits use $_POST.
		Since this action is triggered right after the post has been saved
			, you can easily access this post object by using get_post($post_id)
		Fires once a post has been saved.
		@param int     $post_ID Post ID.
		@param WP_Post $post    Post object.
		@param bool    $update  Whether this is an existing post being updated or not.
	*/
	public function save_post(
		$post_ID
		, $post
		, $update
	) {
		$wpExtend = $this->di->getShared('wpExtend');
		$hook = $this->di->getShared('hook');
		
		// If this is just a revision/autosave, don't clean cache
		if(false === $wpExtend->isRequestIsAutoSavePosts()) {
			if ( false === wp_is_post_revision( $post_ID ) ) {
				if ( false === wp_is_post_autosave( $post_ID ) ) {
					if ( 'publish' === get_post_status($post_ID) ) {
						
						if($hook->has_action('save_post_primary')) {
							$hook->do_action('save_post_primary', array(
								'post_ID' => $post_ID
								, 'post' => $post
								, 'update' => $update
							));
						}
						
						if(
							('publish' === get_post_status($post_ID))
						) {
							if($hook->has_action('save_post_publish')) {
								$hook->do_action('save_post_publish', array(
									'post_ID' => $post_ID
									, 'post' => $post
									, 'update' => $update
								));
							}
							
							$this->_registerCleanCache();
							
						}
						
					}
				}
			}
		}
		
	}
	
	/**
	 * Fires once a post has been saved.
	 *
	 * @since 2.0.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function wp_insert_post(
		$post_ID
		, $post
		, $update
	) {
		$wpExtend = $this->di->getShared('wpExtend');
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('wp_insert_post')) {
			
			$hook->do_action('wp_insert_post', array(
				'post_ID' => $post_ID
				, 'post' => $post
				, 'update' => $update
			));
		}
		
	}
	
	/**
	 * Fires immediately after updating a post's metadata.
	 *
	 * @since 2.9.0
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Object ID.
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 */
	public function updated_postmeta(
		$meta_id
		, $object_id
		, $meta_key
		, $meta_value
	) {
		$wpExtend = $this->di->getShared('wpExtend');
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('updated_postmeta')) {
			$hook->do_action('updated_postmeta', array(
				'meta_id' => $meta_id
				, 'object_id' => $object_id
				, 'meta_key' => $meta_key
				, 'meta_value' => $meta_value
			));
		}
	}
	
	/*
		Fires once an existing post has been updated.
		@param int     $post_ID Post ID.
		@param WP_Post $post    Post object.
	*/
	public function publish_future_post(
		$post_id
	) {
		$this->_registerCleanCache();
	}
	
	/**
	 * Fires immediately before a post is deleted from the database.
	 *
	 * @since 1.2.0
	 *
	 * @param int $postid Post ID.
	 */
	public function delete_post(
		$postid
	) {
		$postid = (int)$postid;
		$hook = $this->di->getShared('hook');
		if($hook->has_action('delete_post')) {
			$hook->do_action('delete_post', $postid);
		}
		
		$this->_registerCleanCache();
	}
	
	/**
	* Fires after a post is deleted, at the conclusion of wp_delete_post().
	*
	* @since 3.2.0
	*
	* @see wp_delete_post()
	*
	* @param int $postid Post ID.
	*/
	public function after_delete_post(
		$postid
	) {
		$postid = (int)$postid;
		$hook = $this->di->getShared('hook');
		if($hook->has_action('after_delete_post')) {
			$hook->do_action('after_delete_post', $postid);
		}
		
		$this->_registerCleanCache();
	}
	
	/**
	 * Fires immediately after a post is deleted from the database.
	 *
	 * @since 2.2.0
	 *
	 * @param int $postid Post ID.
	 */
	public function deleted_post(
		$postid
	) {
		$postid = (int)$postid;
		$hook = $this->di->getShared('hook');
		if($hook->has_action('deleted_post')) {
			$hook->do_action('deleted_post', $postid);
		}
		
		$this->_registerCleanCache();
	}

	/**
	 * Fires once an existing attachment has been updated.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_ID Attachment ID.
	 */
	public function edit_attachment(
		$post_id
	) {
		$post_id = (int)$post_id;
		
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('edit_attachment')) {
			$hook->do_action('edit_attachment', $post_id);
		}
		
		if($hook->has_action('update_attachment')) {
			$hook->do_action('update_attachment', $post_id);
		}
		
		$this->_registerCleanCache();
	}
	
	/**
	* Fires once an attachment has been added.
	*
	* @since 2.0.0
	*
	* @param int $post_ID Attachment ID.
	*/
	public function add_attachment(
		$post_id
	) {
		$post_id = (int)$post_id;
		
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('add_attachment')) {
			$hook->do_action('add_attachment', $post_id);
		}
		
		if($hook->has_action('update_attachment')) {
			$hook->do_action('update_attachment', $post_id);
		}
		
		$this->_registerCleanCache();
	}
	
	/**
	 * Fires before an attachment is deleted, at the start of wp_delete_attachment().
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Attachment ID.
	 */
	public function delete_attachment(
		$post_id
	) {
		$post_id = (int)$post_id;
		$hook = $this->di->getShared('hook');
		if($hook->has_action('delete_attachment')) {
			$hook->do_action('delete_attachment', $post_id);
		}
		
		$this->_registerCleanCache();
	}
	
	
	public function clean_post_cache(
		$post_id
		, $post
	) {
		$post_id = (int)$post_id;
		
		$wpExtend = $this->di->getShared('wpExtend');
		
		$post = $wpExtend->getAndParsePostByPostId($post_id);
		
		if(isset($post['cacheTags']) && !empty($post['cacheTags'])) {
			wppepvn_clean_cache(',common,', array(
				'cache_tags' => $post['cacheTags']
			));
		}
		
		unset($post);
		
		$hook = $this->di->getShared('hook');
		if($hook->has_action('clean_post_cache')) {
			$hook->do_action('clean_post_cache', $post_id);
		}
		
		$this->_registerCleanCache();
	}
	
	/**
	* Fires after a plugin has been activated.
	*
	* If a plugin is silently activated (such as during an update),
	* this hook does not fire.
	*
	* @since 2.9.0
	*
	* @param string $plugin       Plugin path to main plugin file with plugin data.
	* @param bool   $network_wide Whether to enable the plugin for all sites in the network
	* 		or just the current site. Multisite only. Default is false.
	*/
	public function activated_plugin(
		$plugin
		, $network_wide
	) {
		$this->_registerCleanCache();
		wppepvn_register_clean_cache(',all,');
		
		$session = $this->di->getShared('session');
		
		$sessionKey = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-activated-any-plugins-status';
		$session->set($sessionKey, 'y');
		
		$this->_activated_plugins_data[] = array(
			'plugin' => $plugin
			,'network_wide' => $network_wide
		);
		
	}
	
	/**
	* Fires after a plugin has been deactivated.
	*
	* If a plugin is silently deactivated (such as during an update),
	* this hook does not fire.
	*
	* @since 2.9.0
	*
	* @param string $plugin               Plugin basename.
	* @param bool   $network_deactivating Whether the plugin is deactivated for all sites in the network
	* 		or just the current site. Multisite only. Default false.
	*/
	public function deactivated_plugin(
		$plugin
		, $network_deactivating
	) {
		$this->_registerCleanCache();
		wppepvn_register_clean_cache(',all,');
		
		$session = $this->di->getShared('session');
		
		$sessionKey = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-deactivated-any-plugins-status';
		$session->set($sessionKey, 'y');
		
		$this->_deactivated_plugins_data[] = array(
			'plugin' => $plugin
			,'network_deactivating' => $network_deactivating
		);
		
	}
	
	/**
	* Fires after the theme is switched.
	*
	* @since 1.5.0
	*
	* @param string   $new_name  Name of the new theme.
	* @param WP_Theme $new_theme WP_Theme instance of the new theme.
	*/
	public function switch_theme(
		$new_name
		, $new_theme 
	) {
		$this->_registerCleanCache();
		wppepvn_register_clean_cache(',all,');
	}
	
	/**
	 * Fires after the user has successfully logged in.
	 *
	 * @since 1.5.0
	 *
	 * @param string  $user_login Username.
	 * @param WP_User $user       WP_User object of the logged-in user.
	 */
	public function wp_login(
		$user_login
		, $user 
	) {
		
		$hook = $this->di->getShared('hook');
		
		if($hook->has_action('wp_login')) {
			$hook->do_action('wp_login', array(
				'user_login' => $user_login
				, 'user' => $user 
			));
		}
		
	}
	
	public function wp_action_shutdown()
	{
		$hook = $this->di->getShared('hook');
		
		$session = $this->di->getShared('session');
		
		$sessionKey = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-activated-any-plugins-status';
		
		if(
			($session->has($sessionKey) && ('y' === $session->get($sessionKey)))
		) {
			$session->set($sessionKey, 'n');
			$session->remove($sessionKey);
			
			if($hook->has_action('activated_plugin')) {
				$hook->do_action('activated_plugin', $this->_activated_plugins_data);
			}
			
		}
		
		$sessionKey = WP_OPTIMIZE_BY_XTRAFFIC_PLUGIN_SLUG.'-deactivated-any-plugins-status';
		
		if(
			($session->has($sessionKey) && ('y' === $session->get($sessionKey)))
		) {
			$session->set($sessionKey, 'n');
			$session->remove($sessionKey);
			
			if($hook->has_action('deactivated_plugin')) {
				$hook->do_action('deactivated_plugin', $this->_deactivated_plugins_data);
			}
			
		}
		
	}
	
}