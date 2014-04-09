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


	public function postbox_table_information( $data, $box ) {
?>
asdf
<?php
        }

} // class TablePress_Editsccl_View
