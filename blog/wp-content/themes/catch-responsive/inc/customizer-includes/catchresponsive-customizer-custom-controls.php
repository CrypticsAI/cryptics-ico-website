<?php
/**
* The template for adding Customizer Custom Controls
*
* @package Catch Themes
* @subpackage Catch Responsive
* @since Catch Responsive 1.0
*/


//Custom control for dropdown category multiple select
class Catchresponsive_Customize_Dropdown_Categories_Control extends WP_Customize_Control {
	public $type = 'dropdown-categories';

	public $name;

	public function render_content() {
		$dropdown = wp_dropdown_categories(
			array(
				'name'             => $this->name,
				'echo'             => 0,
				'hide_empty'       => false,
				'show_option_none' => false,
				'hide_if_empty'    => false,
				'show_option_all'  => __( 'All Categories', 'catch-responsive' )
			)
		);

		$dropdown = str_replace('<select', '<select multiple = "multiple" style = "height:95px;" ' . $this->get_link(), $dropdown );

		printf(
			'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
			$this->label,
			$dropdown
		);

		echo '<p class="description">'. __( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'catch-responsive' ) . '</p>';
	}
}

//Custom control for dropdown category multiple select
class Catchresponsive_Important_Links extends WP_Customize_Control {
    public $type = 'important-links';

    public function render_content() {
    	//Add Theme instruction, Support Forum, Changelog, Donate link, Review, Facebook, Twitter, Google+, Pinterest links
        $important_links = array(
						'theme_instructions' => array(
							'link'	=> esc_url( 'https://catchthemes.com/theme-instructions/catch-responsive/' ),
							'text' 	=> __( 'Theme Instructions', 'catch-responsive' ),
							),
						'support' => array(
							'link'	=> esc_url( 'https://catchthemes.com/support/' ),
							'text' 	=> __( 'Support', 'catch-responsive' ),
							),
						'changelog' => array(
							'link'	=> esc_url( 'https://catchthemes.com/changelogs/catch-responsive-theme/' ),
							'text' 	=> __( 'Changelog', 'catch-responsive' ),
							),
						'donate' => array(
							'link'	=> esc_url( 'https://catchthemes.com/donate/' ),
							'text' 	=> __( 'Donate Now', 'catch-responsive' ),
							),
						'review' => array(
							'link'	=> esc_url( 'https://wordpress.org/support/view/theme-reviews/catch-responsive' ),
							'text' 	=> __( 'Review', 'catch-responsive' ),
							),
						'facebook' => array(
							'link'	=> esc_url( 'https://www.facebook.com/catchthemes/' ),
							'text' 	=> __( 'Facebook', 'catch-responsive' ),
							),
						'twitter' => array(
							'link'	=> esc_url( 'https://twitter.com/catchthemes/' ),
							'text' 	=> __( 'Twitter', 'catch-responsive' ),
							),
						'gplus' => array(
							'link'	=> esc_url( 'https://plus.google.com/+Catchthemes/' ),
							'text' 	=> __( 'Google+', 'catch-responsive' ),
							),
						'pinterest' => array(
							'link'	=> esc_url( 'http://www.pinterest.com/catchthemes/' ),
							'text' 	=> __( 'Pinterest', 'catch-responsive' ),
							),
						);
		foreach ( $important_links as $important_link) {
			echo '<p><a target="_blank" href="' . $important_link['link'] .'" >' . esc_attr( $important_link['text'] ) .' </a></p>';
		}
    }
}