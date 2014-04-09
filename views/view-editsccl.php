<?php
/**
 * Edit Table View
 * Sublcassed for SCCL site.
 *
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */

// Prohibit direct script loading
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

include_once('view-edit.php');

/**
 * Edit Table View class
 * @package TablePress
 * @subpackage Views
 * @author Tobias Bäthge
 * @since 1.0.0
 */
class TablePress_Editsccl_View extends TablePress_Edit_View {

	/**
	 * Set up the view with data and do things that are specific for this view
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Action for this view
	 * @param array $data Data for this view
	 */
	public function setup( $action, array $data ) {
	  parent::setup( $action, $data );
	  
	  if (!current_user_can('administrator')) {
	    $this->remove_meta_box('table-information', 'normal');
	  }
	}

	/**
	 * Register a post meta box for the view, that is drag/droppable with WordPress functionality
	 *
	 * @since 1.0.0
	 * @uses add_meta_box()
	 *
	 * @param string $id Unique ID for the meta box
	 * @param string $title Title for the meta box
	 * @param string $context (optional) Context/position of the post meta box (normal, side, additional)
	 */
	protected function remove_meta_box( $id, $context = 'normal') {
	  remove_meta_box( "tablepress_{$this->action}-{$id}", null, $context);
	}

	/**
	 * Print hidden field with a nonce for the screen's action, to be transmitted in HTTP requests
	 *
	 * @since 1.0.0
	 * @uses wp_nonce_field()
	 *
	 * @param array $data Data for this screen
	 * @param array $box Information about the text box
	 */
	protected function action_nonce_field( array $data, array $box ) {
	  // use custom nonce field here, that includes the table ID
	  // instead of using $this->action use 'edit' to fool the controller later
	  wp_nonce_field( TablePress::nonce( 'edit', $data['table']['id'] ), 'nonce-edit-table' ); echo "\n";
	  wp_nonce_field( TablePress::nonce( 'preview_table', $data['table']['id'] ), 'nonce-preview-table', false, true );
	}

        public function postbox_table_data($data, $box) {
	  parent::postbox_table_data($data, $box);

	  if (!current_user_can('administrator')) {
	    $this->postbox_hidden_fields($data);
	  }
	}

	public function postbox_hidden_fields($data) {
?>
<div class="hidden-fields-from-table-information">
  <input type="hidden" name="table[id]" id="table-id" value="<?php echo esc_attr( $data['table']['id'] ); ?>" />
  <input type="hidden" name="table[new_id]" id="table-new-id" value="<?php echo esc_attr( $data['table']['id'] ); ?>"/>
  <input type="hidden" id="table-information-shortcode" value="<?php echo esc_attr( '[' . TablePress::$shortcode . " id={$data['table']['id']} /]" ); ?>" />
  <input type="hidden" name="table[name]" id="table-name" value="<?php echo esc_attr( $data['table']['name'] ); ?>" />
  <input type="hidden" name="table[description]" id="table-description" value="<?php echo esc_textarea( $data['table']['description'] ); ?>" />
</div>
<?php
	}

} // class TablePress_Editsccl_View
