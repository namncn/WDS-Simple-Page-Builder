<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WDS_Page_Builder_Layouts' ) ) {

	class WDS_Page_Builder_Layouts {
		/**
		 * Constructor
		 * @since 0.1.0
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
		}

		public function register_layout( $slug, $templates = array(), $args = array() ) {
			// don't register anything if no layout name or templates were passed
			if ( '' == $slug || empty( $templates ) ) {
				return false;
			}

			$defaults = array(
				'name'        => ucwords( str_replace( '-', ' ', $slug ) ),
				'description' => '',
			);
			$args = wp_parse_args( $args, $defaults );
			$this->registered_areas[ $slug ] = array(
				'name'        => esc_attr( $args['name'] ),
				'description' => esc_html( $args['description'] ),
				'templates'   => $templates,
			);
		}

		public function hooks() {
			add_action( 'init', array( $this, 'layouts_cpt') );
			add_action( 'cmb2_init', array( $this, 'register_fields' ) );
		}

		public function layouts_cpt() {
			$labels = array(
				'name'               => _x( 'Saved Layouts', 'post type general name', 'wds-simple-page-builder' ),
				'singular_name'      => _x( 'Saved Layout', 'post type singular name', 'wds-simple-page-builder' ),
				'menu_name'          => _x( 'Saved Layouts', 'admin menu', 'wds-simple-page-builder' ),
				'name_admin_bar'     => _x( 'Saved Layout', 'add new on admin bar', 'wds-simple-page-builder' ),
				'add_new'            => _x( 'Add New Layout', 'page builder layout', 'wds-simple-page-builder' ),
				'add_new_item'       => __( 'Add New Layout', 'wds-simple-page-builder' ),
				'new_item'           => __( 'New Layout', 'wds-simple-page-builder' ),
				'edit_item'          => __( 'Edit Layout', 'wds-simple-page-builder' ),
				'view_item'          => __( 'View Layout', 'wds-simple-page-builder' ),
				'all_items'          => __( 'Saved Layouts', 'wds-simple-page-builder' ),
				'search_items'       => __( 'Search Layouts', 'wds-simple-page-builder' ),
				'not_found'          => __( 'No layouts found.', 'wds-simple-page-builder' ),
				'not_found_in_trash' => __( 'No layouts found in Trash.', 'wds-simple-page-builder' )
			);
			$args = array(
				'labels'        => $labels,
				'public'        => false,
				'show_ui'       => true,
				'has_archive'   => false,
				'hierarchical'  => false,
				'supports'      => array( 'title' ),
				'show_in_menu' => 'edit.php?post_type=wds_pb_layouts',
			);
			register_post_type( 'wds_pb_layouts', $args );
		}

		public function register_fields() {
			$cmb = new_cmb2_box( array(
				'id'           => 'wds_simple_page_builder_layout',
				'title'        => __( 'Page Builder Templates', 'wds-simple-page-builder' ),
				'object_types' => array( 'wds_pb_layouts' ),
				'show_on_cb'   => array( $this->plugin->admin, 'maybe_enqueue_builder_js' ),
			) );

			$group_field = $cmb->add_field( array(
				'id'       => '_wds_builder_layout_template',
				'type'     => 'group',
				'options'  => array(
					'group_title'   => __( 'Template Part {#}', 'wds-simple-page-builder' ),
					'add_button'    => __( 'Add another template part', 'wds-simple-page-builder' ),
					'remove_button' => __( 'Remove template part', 'wds-simple-page-builder' ),
					'sortable'      => true,
				)
			) );

			foreach ( $this->plugin->admin->get_group_fields() as $field ) {
				$cmb->add_group_field( $group_field, $field );
			}
		}
	}

}