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
		$this->remove_meta_box( 'table-options', 'normal' );
		$this->remove_meta_box( 'datatables-features', 'normal' );
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
	
	/**
	 * Print the content of the "Table Content" post meta box
	 *
	 * @since 1.0.0
	 */
	public function postbox_table_data( $data, $box ) {
		$table = $data['table']['data'];
		$options = $data['table']['options'];
		$visibility = $data['table']['visibility'];
		$rows = count( $table );
		$columns = count( $table[0] );

		$head_row_idx = $foot_row_idx = -1;
		// determine row index of the table head row, by excluding all hidden rows from the beginning
		if ( $options['table_head'] ) {
			for ( $row_idx = 0; $row_idx < $rows; $row_idx++ ) {
				if ( 1 === $visibility['rows'][ $row_idx ] ) {
					$head_row_idx = $row_idx;
					break;
				}
			}
		}
		// determine row index of the table foot row, by excluding all hidden rows from the end
		if ( $options['table_foot'] ) {
			for ( $row_idx = $rows - 1; $row_idx > -1; $row_idx-- ) {
				if ( 1 === $visibility['rows'][ $row_idx ] ) {
					$foot_row_idx = $row_idx;
					break;
				}
			}
		}
?>
<table id="edit-form" class="tablepress-edit-screen-id-<?php echo esc_attr( $data['table']['id'] ); ?>">
	<thead>
		<tr id="edit-form-head">
			<th></th>
			<th></th>
<?php
	for ( $col_idx = 0; $col_idx < $columns; $col_idx++ ) {
		$column_class = '';
		if ( 0 === $visibility['columns'][ $col_idx ] ) {
			$column_class = ' column-hidden';
		}
		$column = TablePress::number_to_letter( $col_idx + 1 );
		echo "\t\t\t<th class=\"head{$column_class}\"><span class=\"sort-control sort-desc hide-if-no-js\" title=\"" . esc_attr__( 'Sort descending', 'tablepress' ) . "\"><span class=\"sorting-indicator\"></span></span><span class=\"sort-control sort-asc hide-if-no-js\" title=\"" . esc_attr__( 'Sort ascending', 'tablepress' ) . "\"><span class=\"sorting-indicator\"></span></span><span class=\"move-handle\">{$column}</span></th>\n";
	}
?>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr id="edit-form-foot">
			<th></th>
			<th></th>
<?php
	for ( $col_idx = 0; $col_idx < $columns; $col_idx++ ) {
		$column_class = '';
		if ( 0 === $visibility['columns'][ $col_idx ] ) {
			$column_class = ' class="column-hidden"';
		}
		echo "\t\t\t<th{$column_class}><input type=\"checkbox\" class=\"hide-if-no-js\" />";
		echo "<input type=\"hidden\" class=\"visibility\" name=\"table[visibility][columns][]\" value=\"{$visibility['columns'][ $col_idx ]}\" /></th>\n";
	}
?>
			<th></th>
		</tr>
	</tfoot>
	<tbody id="edit-form-body">
<?php

	foreach ( $table as $row_idx => $row_data ) {
		$row = $row_idx + 1;
		$classes = array();
		$row_input_attrs = '';
		if ( $row_idx % 2 == 0 ) {
			$classes[] = 'odd';
		}
		if ( $head_row_idx == $row_idx ) {
			$classes[] = 'head-row';
			if (!current_user_can('administrator')) {
				$row_input_attrs = ' readonly="readonly" ';
			}
		} elseif ( $foot_row_idx == $row_idx ) {
			$classes[] = 'foot-row';
		}
		if ( 0 === $visibility['rows'][ $row_idx ] ) {
			$classes[] = 'row-hidden';
		}
		$row_class = ( ! empty( $classes ) ) ? ' class="' . implode( ' ', $classes ) . '"' : '';
		echo "\t\t<tr{$row_class}>\n";
		echo "\t\t\t<td><span class=\"move-handle\">{$row}</span></td>";
		echo "<td><input type=\"checkbox\" class=\"hide-if-no-js\" /><input type=\"hidden\" class=\"visibility\" name=\"table[visibility][rows][]\" value=\"{$visibility['rows'][ $row_idx ]}\" /></td>";
		foreach ( $row_data as $col_idx => $cell ) {
			$column = TablePress::number_to_letter( $col_idx + 1 );
			$column_class = '';
			if ( 0 === $visibility['columns'][ $col_idx ] ) {
				$column_class = ' class="column-hidden"';
			}
			$cell = esc_textarea( $cell ); // sanitize, so that HTML is possible in table cells
			echo "<td{$column_class}><textarea name=\"table[data][{$row_idx}][{$col_idx}]\" id=\"cell-{$column}{$row}\" rows=\"1\" {$row_input_attrs}>{$cell}</textarea></td>";
		}
		echo "<td><span class=\"move-handle\">{$row}</span></td>\n";
		echo "\t\t</tr>\n";
	}
?>
	</tbody>
</table>
<input type="hidden" id="number-rows" name="table[number][rows]" value="<?php echo $rows; ?>" />
<input type="hidden" id="number-columns" name="table[number][columns]" value="<?php echo $columns; ?>" />
<?php
		if (!current_user_can('administrator')) {
			$this->postbox_hidden_fields($data);
		}
	}
	
	/**
	 * Print the content of the "Table Manipulation" post meta box
	 *
	 * @since 1.0.0
	 */
	public function postbox_table_manipulation( $data, $box ) {
		//Only admins get the full manipulation box
		//Everyone else just gets cell content features, no structural changes.
		if (current_user_can('administrator')) {
			parent::postbox_table_manipulation($data, $box);
		} else {
			$media_library_url = esc_url( add_query_arg( array( 'post_id' => '0', 'type' => 'image', 'tab' => 'library' ), admin_url( 'media-upload.php' ) ) );
?>
<table class="tablepress-postbox-table fixed hide-if-no-js">
<tbody>
	<tr class="bottom-border">
		<td class="column-1">
			<?php printf( __( 'Add %s row(s)', 'tablepress' ), '<input type="number" id="rows-append-number" class="small-text numbers-only" title="' . esc_attr__( 'This field must contain a positive number.', 'tablepress' ) . '" value="1" min="1" max="99999" maxlength="5" required />' ); ?>&nbsp;<input type="button" class="button" id="rows-append" value="<?php esc_attr_e( 'Add', 'tablepress' ); ?>" />
		</td>
		<td class="column-2">
			<?php _e( 'Selected rows', 'tablepress' ); ?>:&nbsp;
			<input type="button" class="button" id="rows-duplicate" value="<?php esc_attr_e( 'Duplicate', 'tablepress' ); ?>" />
			<input type="button" class="button" id="rows-insert" value="<?php esc_attr_e( 'Insert', 'tablepress' ); ?>" />
			<input type="button" class="button" id="rows-remove" value="<?php esc_attr_e( 'Delete', 'tablepress' ); ?>" />
		</td>
	</tr>
	<tr>
		<td class="column-2" colspan=2>
			<?php _e( 'Edit cell', 'tablepress' ); ?>:&nbsp;
			<input type="button" class="button" id="link-add" value="<?php esc_attr_e( 'Insert Link', 'tablepress' ); ?>" />
			<a href="<?php echo $media_library_url; ?>" class="button" id="image-add"><?php _e( 'Insert Image', 'tablepress' ); ?></a>
			<input type="button" class="button" id="advanced-editor-open" value="<?php esc_attr_e( 'Advanced Editor', 'tablepress' ); ?>" />
		</td>
	</tr>
</table>
<p class="hide-if-js"><?php _e( 'To use the Table Manipulation features, JavaScript needs to be enabled in your browser.', 'tablepress' ); ?></p>
<?php
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
