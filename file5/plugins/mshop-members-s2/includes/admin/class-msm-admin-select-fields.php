<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class MSM_Admin_Select_Fields {
	public static function output() {
		$result = '';
		$action = '';

		// Action to perform: add, edit, delete or none
		if ( ! empty( $_POST['add_new_select_field'] ) ) {
			$action = 'add';
		} elseif ( ! empty( $_POST['save_select_field'] ) && ! empty( $_GET['edit'] ) ) {
			$action = 'edit';
		} elseif ( ! empty( $_GET['delete'] ) ) {
			$action = 'delete';
		}

		switch ( $action ) {
			case 'add' :
				$result = self::process_add_select_field();
				break;
			case 'edit' :
				$result = self::process_edit_select_field();
				break;
			case 'delete' :
				$result = self::process_delete_select_field();
				break;
		}

		if ( is_wp_error( $result ) ) {
			echo '<div id="woocommerce_errors" class="error"><p>' . $result->get_error_message() . '</p></div>';
		}

		// Show admin interface
		if ( ! empty( $_GET['edit'] ) ) {
			self::edit_select_field();
		} else {
			self::add_select_field();
		}
	}
	private static function get_posted_select_field() {
		$attribute = array(
			'select_field_label'   => isset( $_POST['select_field_label'] )   ? stripslashes( $_POST['select_field_label'] ) : '',
			'select_field_name'    => isset( $_POST['select_field_name'] )    ? wc_sanitize_taxonomy_name( stripslashes( $_POST['select_field_name'] ) ) : '',
			'select_field_type'    => isset( $_POST['select_field_type'] )    ? $_POST['select_field_type'] : 'select',
			'select_field_orderby' => isset( $_POST['select_field_orderby'] ) ? $_POST['select_field_orderby'] : '',
			'select_field_public'  => isset( $_POST['select_field_public'] )  ? 1 : 0
		);

		if ( empty( $attribute['select_field_type'] ) ) {
			$attribute['select_field_type'] = 'select';
		}
		if ( empty( $attribute['select_field_label'] ) ) {
			$attribute['select_field_label'] = ucfirst( $attribute['select_field_name'] );
		}
		if ( empty( $attribute['select_field_name'] ) ) {
			$attribute['select_field_name'] = wc_sanitize_taxonomy_name( $attribute['select_field_label'] );
		}

		return $attribute;
	}
	private static function valid_select_field_name( $select_field_name ) {
		if ( strlen( $select_field_name ) >= 28 ) {
			return new WP_Error( 'error', sprintf( __( 'Slug "%s" is too long (28 characters max). Shorten it, please.', 'mshop-members-s2' ), sanitize_title( $select_field_name ) ) );
		}

		return true;
	}
	private static function process_add_select_field() {
		global $wpdb;
		check_admin_referer( 'msm-add-new_attribute' );

		$attribute = self::get_posted_select_field();

		if ( empty( $attribute['select_field_name'] ) || empty( $attribute['select_field_label'] ) ) {
			return new WP_Error( 'error', __( 'Please, provide an attribute name and slug.', 'mshop-members-s2' ) );
		} elseif ( ( $valid_select_field_name = self::valid_select_field_name( $attribute['select_field_name'] ) ) && is_wp_error( $valid_select_field_name ) ) {
			return $valid_select_field_name;
		} elseif ( taxonomy_exists( msm_select_field_taxonomy_name( $attribute['select_field_name'] ) ) ) {
			return new WP_Error( 'error', sprintf( __( 'Slug "%s" is already in use. Change it, please.', 'mshop-members-s2' ), sanitize_title( $attribute['select_field_name'] ) ) );
		}

		$wpdb->insert( $wpdb->prefix . 'msm_select_field_taxonomies', $attribute );

		do_action( 'msm_select_field_added', $wpdb->insert_id, $attribute );

		flush_rewrite_rules();
		delete_transient( 'msm_select_field_taxonomies' );

		return true;
	}
	private static function process_edit_select_field() {
		global $wpdb;
		$attribute_id = absint( $_GET['edit'] );
		check_admin_referer( 'msm-save-attribute_' . $attribute_id );

		$attribute = self::get_posted_select_field();

		if ( empty( $attribute['select_field_name'] ) || empty( $attribute['select_field_label'] ) ) {
			return new WP_Error( 'error', __( 'Please, provide an attribute name and slug.', 'mshop-members-s2' ) );
		} elseif ( ( $valid_select_field_name = self::valid_select_field_name( $attribute['select_field_name'] ) ) && is_wp_error( $valid_select_field_name ) ) {
			return $valid_select_field_name;
		}

		$taxonomy_exists    = taxonomy_exists( msm_select_field_taxonomy_name( $attribute['select_field_name'] ) );
		$old_select_field_name = $wpdb->get_var( "SELECT select_field_name FROM {$wpdb->prefix}msm_select_field_taxonomies WHERE attribute_id = $attribute_id" );
		if ( $old_select_field_name != $attribute['select_field_name'] && wc_sanitize_taxonomy_name( $old_select_field_name ) != $attribute['select_field_name'] && $taxonomy_exists ) {
			return new WP_Error( 'error', sprintf( __( 'Slug "%s" is already in use. Change it, please.', 'mshop-members-s2' ), sanitize_title( $attribute['select_field_name'] ) ) );
		}

		$wpdb->update( $wpdb->prefix . 'msm_select_field_taxonomies', $attribute, array( 'attribute_id' => $attribute_id ) );

		do_action( 'msm_select_field_updated', $attribute_id, $attribute, $old_select_field_name );

		if ( $old_select_field_name != $attribute['select_field_name'] && ! empty( $old_select_field_name ) ) {
			// Update taxonomies in the wp term taxonomy table
			$wpdb->update(
				$wpdb->term_taxonomy,
				array( 'taxonomy' => msm_select_field_taxonomy_name( $attribute['select_field_name'] ) ),
				array( 'taxonomy' => 'pa_' . $old_select_field_name )
			);

			// Update taxonomy ordering term meta
			if ( get_option( 'db_version' ) < 34370 ) {
				$wpdb->update(
					$wpdb->prefix . 'woocommerce_termmeta',
					array( 'meta_key' => 'order_pa_' . sanitize_title( $attribute['select_field_name'] ) ),
					array( 'meta_key' => 'order_pa_' . sanitize_title( $old_select_field_name ) )
				);
			} else {
				$wpdb->update(
					$wpdb->termmeta,
					array( 'meta_key' => 'order_pa_' . sanitize_title( $attribute['select_field_name'] ) ),
					array( 'meta_key' => 'order_pa_' . sanitize_title( $old_select_field_name ) )
				);
			}

			// Update product attributes which use this taxonomy
			$old_select_field_name_length = strlen( $old_select_field_name ) + 3;
			$select_field_name_length     = strlen( $attribute['select_field_name'] ) + 3;

			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE( meta_value, %s, %s ) WHERE meta_key = '_msm_select_fields'",
				's:' . $old_select_field_name_length . ':"pa_' . $old_select_field_name . '"',
				's:' . $select_field_name_length . ':"pa_' . $attribute['select_field_name'] . '"'
			) );

			// Update variations which use this taxonomy
			$wpdb->update(
				$wpdb->postmeta,
				array( 'meta_key' => 'attribute_pa_' . sanitize_title( $attribute['select_field_name'] ) ),
				array( 'meta_key' => 'attribute_pa_' . sanitize_title( $old_select_field_name ) )
			);
		}

		echo '<div class="updated"><p>' . __( 'Attribute updated successfully', 'mshop-members-s2' ) . '</p></div>';

		flush_rewrite_rules();
		delete_transient( 'msm_select_field_taxonomies' );

		return true;
	}
	private static function process_delete_select_field() {
		global $wpdb;

		$attribute_id = absint( $_GET['delete'] );

		check_admin_referer( 'msm-delete-attribute_' . $attribute_id );

		$select_field_name = $wpdb->get_var( "SELECT select_field_name FROM {$wpdb->prefix}msm_select_field_taxonomies WHERE attribute_id = $attribute_id" );
		$taxonomy       = msm_select_field_taxonomy_name( $select_field_name );

		do_action( 'woocommerce_before_attribute_delete', $attribute_id, $select_field_name, $taxonomy );

		if ( $select_field_name && $wpdb->query( "DELETE FROM {$wpdb->prefix}msm_select_field_taxonomies WHERE attribute_id = $attribute_id" ) ) {
			if ( taxonomy_exists( $taxonomy ) ) {
				$terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
				foreach ( $terms as $term ) {
					wp_delete_term( $term->term_id, $taxonomy );
				}
			}

			do_action( 'msm_select_field_deleted', $attribute_id, $select_field_name, $taxonomy );
			delete_transient( 'msm_select_field_taxonomies' );
			return true;
		}

		return false;
	}
	public static function edit_select_field() {
		global $wpdb;

		$edit = absint( $_GET['edit'] );

		$attribute_to_edit = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "msm_select_field_taxonomies WHERE attribute_id = '$edit'" );

		?>
		<div class="wrap woocommerce">
			<div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
			<h1><?php _e( 'Edit Attribute', 'mshop-members-s2' ) ?></h1>

			<?php

			if ( ! $attribute_to_edit ) {
				echo '<div id="woocommerce_errors" class="error"><p>' . __( 'Error: non-existing attribute ID.', 'mshop-members-s2' ) . '</p></div>';
			} else {
				$att_type    = $attribute_to_edit->select_field_type;
				$att_label   = $attribute_to_edit->select_field_label;
				$att_name    = $attribute_to_edit->select_field_name;
				$att_orderby = $attribute_to_edit->select_field_orderby;
				$att_public  = $attribute_to_edit->select_field_public;

				?>

				<form action="edit.php?post_type=mshop_members_form&amp;page=msm_select_fields&amp;edit=<?php echo absint( $edit ); ?>" method="post">
					<table class="form-table">
						<tbody>
						<tr class="form-field form-required">
							<th scope="row" valign="top">
								<label for="select_field_label"><?php _e( 'Name', 'mshop-members-s2' ); ?></label>
							</th>
							<td>
								<input name="select_field_label" id="select_field_label" type="text" value="<?php echo esc_attr( $att_label ); ?>" />
								<p class="description"><?php _e( 'Name for the attribute (shown on the front-end).', 'mshop-members-s2' ); ?></p>
							</td>
						</tr>
						<tr class="form-field form-required">
							<th scope="row" valign="top">
								<label for="select_field_name"><?php _e( 'Slug', 'mshop-members-s2' ); ?></label>
							</th>
							<td>
								<input name="select_field_name" id="select_field_name" type="text" value="<?php echo esc_attr( $att_name ); ?>" maxlength="28" />
								<p class="description"><?php _e( 'Unique slug/reference for the attribute; must be shorter than 28 characters.', 'mshop-members-s2' ); ?></p>
							</td>
						</tr>
						<tr class="form-field form-required">
							<th scope="row" valign="top">
								<label for="select_field_public"><?php _e( 'Enable Archives?', 'mshop-members-s2' ); ?></label>
							</th>
							<td>
								<input name="select_field_public" id="select_field_public" type="checkbox" value="1" <?php checked( $att_public, 1 ); ?> />
								<p class="description"><?php _e( 'Enable this if you want this attribute to have product archives in your store.', 'mshop-members-s2' ); ?></p>
							</td>
						</tr>
						<tr class="form-field form-required">
							<th scope="row" valign="top">
								<label for="select_field_type"><?php _e( 'Type', 'mshop-members-s2' ); ?></label>
							</th>
							<td>
								<select name="select_field_type" id="select_field_type">
									<?php foreach ( msm_get_select_field_types() as $key => $value ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $att_type, $key ); ?>><?php echo esc_attr( $value ); ?></option>
									<?php endforeach; ?>

									<?php
									do_action( 'woocommerce_admin_select_field_types' );
									?>
								</select>
								<p class="description"><?php _e( 'Determines how you select attributes for products. Under admin panel -> products -> product data -> attributes -> values, <strong>Text</strong> allows manual entry whereas <strong>select</strong> allows pre-configured terms in a drop-down list.', 'mshop-members-s2' ); ?></p>
							</td>
						</tr>
						<tr class="form-field form-required">
							<th scope="row" valign="top">
								<label for="select_field_orderby"><?php _e( 'Default sort order', 'mshop-members-s2' ); ?></label>
							</th>
							<td>
								<select name="select_field_orderby" id="select_field_orderby">
									<option value="menu_order" <?php selected( $att_orderby, 'menu_order' ); ?>><?php _e( 'Custom ordering', 'mshop-members-s2' ); ?></option>
									<option value="name" <?php selected( $att_orderby, 'name' ); ?>><?php _e( 'Name', 'mshop-members-s2' ); ?></option>
									<option value="name_num" <?php selected( $att_orderby, 'name_num' ); ?>><?php _e( 'Name (numeric)', 'mshop-members-s2' ); ?></option>
									<option value="id" <?php selected( $att_orderby, 'id' ); ?>><?php _e( 'Term ID', 'mshop-members-s2' ); ?></option>
								</select>
								<p class="description"><?php _e( 'Determines the sort order of the terms on the frontend shop product pages. If using custom ordering, you can drag and drop the terms in this attribute.', 'mshop-members-s2' ); ?></p>
							</td>
						</tr>
						</tbody>
					</table>
					<p class="submit"><input type="submit" name="save_select_field" id="submit" class="button-primary" value="<?php esc_attr_e( 'Update', 'mshop-members-s2' ); ?>"></p>
					<?php wp_nonce_field( 'msm-save-attribute_' . $edit ); ?>
				</form>
			<?php } ?>
		</div>
		<?php
	}
	public static function add_select_field() {
		?>
		<div class="wrap woocommerce">
			<div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
			<h1><?php _e( 'Select Fields', 'mshop-members-s2' ); ?></h1>
			<br class="clear" />
			<div id="col-container">
				<div id="col-right">
					<div class="col-wrap">
						<table class="widefat attributes-table wp-list-table ui-sortable" style="width:100%">
							<thead>
							<tr>
								<th scope="col"><?php _e( 'Name', 'mshop-members-s2' ); ?></th>
								<th scope="col"><?php _e( 'Slug', 'mshop-members-s2' ); ?></th>
								<th scope="col"><?php _e( 'Type', 'mshop-members-s2' ); ?></th>
								<th scope="col"><?php _e( 'Order by', 'mshop-members-s2' ); ?></th>
								<th scope="col" colspan="2"><?php _e( 'Terms', 'mshop-members-s2' ); ?></th>
							</tr>
							</thead>
							<tbody>
							<?php
							if ( $attribute_taxonomies = msm_get_select_fields() ) :
								foreach ( $attribute_taxonomies as $tax ) :
									?><tr>

									<td>
										<strong><a href="edit-tags.php?taxonomy=<?php echo esc_html( msm_select_field_taxonomy_name( $tax->select_field_name ) ); ?>&amp;post_type=mshop_members_form"><?php echo esc_html( $tax->select_field_label ); ?></a></strong>

										<div class="row-actions"><span class="edit"><a href="<?php echo esc_url( add_query_arg( 'edit', $tax->attribute_id, 'edit.php?post_type=mshop_members_form&amp;page=msm_select_fields' ) ); ?>"><?php _e( 'Edit', 'mshop-members-s2' ); ?></a> | </span><span class="delete"><a class="delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'delete', $tax->attribute_id, 'edit.php?post_type=mshop_members_form&amp;page=msm_select_fields' ), 'msm-delete-attribute_' . $tax->attribute_id ) ); ?>"><?php _e( 'Delete', 'mshop-members-s2' ); ?></a></span></div>
									</td>
									<td><?php echo esc_html( $tax->select_field_name ); ?></td>
									<td><?php echo esc_html( ucfirst( $tax->select_field_type ) ); ?> <?php echo $tax->select_field_public ? '(' . __( 'Public', 'mshop-members-s2' ) . ')' : ''; ?></td>
									<td><?php
										switch ( $tax->select_field_orderby ) {
											case 'name' :
												_e( 'Name', 'mshop-members-s2' );
												break;
											case 'name_num' :
												_e( 'Name (numeric)', 'mshop-members-s2' );
												break;
											case 'id' :
												_e( 'Term ID', 'mshop-members-s2' );
												break;
											default:
												_e( 'Custom ordering', 'mshop-members-s2' );
												break;
										}
										?></td>
									<td class="attribute-terms"><?php
										$taxonomy = msm_select_field_taxonomy_name( $tax->select_field_name );

										if ( taxonomy_exists( $taxonomy ) ) {
											$terms = get_terms( $taxonomy, 'hide_empty=0' );

											switch ( $tax->select_field_orderby ) {
												case 'name_num' :
													usort( $terms, '_wc_get_product_terms_name_num_usort_callback' );
													break;
												case 'parent' :
													usort( $terms, '_wc_get_product_terms_parent_usort_callback' );
													break;
											}

											$terms_string = implode( ', ', wp_list_pluck( $terms, 'name' ) );
											if ( $terms_string ) {
												echo $terms_string;
											} else {
												echo '<span class="na">&ndash;</span>';
											}
										} else {
											echo '<span class="na">&ndash;</span>';
										}
										?></td>
									<td class="attribute-actions"><a href="edit-tags.php?taxonomy=<?php echo esc_html( msm_select_field_taxonomy_name( $tax->select_field_name ) ); ?>&amp;post_type=mshop_members_form" class="button alignright tips configure-terms" data-tip="<?php esc_attr_e( 'Configure terms', 'mshop-members-s2' ); ?>"><?php _e( 'Configure terms', 'mshop-members-s2' ); ?></a></td>
									</tr><?php
								endforeach;
							else :
								?><tr><td colspan="6"><?php _e( 'No attributes currently exist.', 'mshop-members-s2' ) ?></td></tr><?php
							endif;
							?>
							</tbody>
						</table>
					</div>
				</div>
				<div id="col-left">
					<div class="col-wrap">
						<div class="form-wrap">
							<h2><?php _e( 'Add New Select Field', 'mshop-members-s2' ); ?></h2>
							<form action="edit.php?post_type=mshop_members_form&amp;page=msm_select_fields" method="post">
								<div class="form-field">
									<label for="select_field_label"><?php _e( 'Name', 'mshop-members-s2' ); ?></label>
									<input name="select_field_label" id="select_field_label" type="text" value="" />
									<p class="description"><?php _e( 'Name for the select field.', 'mshop-members-s2' ); ?></p>
								</div>

								<div class="form-field">
									<label for="select_field_name"><?php _e( 'Slug', 'mshop-members-s2' ); ?></label>
									<input name="select_field_name" id="select_field_name" type="text" value="" maxlength="28" />
									<p class="description"><?php _e( 'Unique slug/reference for the select field; must be shorter than 28 characters.', 'mshop-members-s2' ); ?></p>
								</div>

								<div class="form-field">
									<label for="select_field_public"><input name="select_field_public" id="select_field_public" type="checkbox" value="1" /> <?php _e( 'Enable Archives?', 'mshop-members-s2' ); ?></label>

									<p class="description"><?php _e( 'Enable this if you want this attribute to have product archives in your store.', 'mshop-members-s2' ); ?></p>
								</div>

								<div class="form-field">
									<label for="select_field_type"><?php _e( 'Type', 'mshop-members-s2' ); ?></label>
									<select name="select_field_type" id="select_field_type">
										<?php foreach ( msm_get_select_field_types() as $key => $value ) : ?>
											<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $value ); ?></option>
										<?php endforeach; ?>

										<?php
										do_action( 'woocommerce_admin_select_field_types' );
										?>
									</select>
									<p class="description"><?php _e( 'Determines how you select attributes for products. Under admin panel -> products -> product data -> attributes -> values, <strong>Text</strong> allows manual entry whereas <strong>select</strong> allows pre-configured terms in a drop-down list.', 'mshop-members-s2' ); ?></p>
								</div>

								<div class="form-field">
									<label for="select_field_orderby"><?php _e( 'Default sort order', 'mshop-members-s2' ); ?></label>
									<select name="select_field_orderby" id="select_field_orderby">
										<option value="menu_order"><?php _e( 'Custom ordering', 'mshop-members-s2' ); ?></option>
										<option value="name"><?php _e( 'Name', 'mshop-members-s2' ); ?></option>
										<option value="name_num"><?php _e( 'Name (numeric)', 'mshop-members-s2' ); ?></option>
										<option value="id"><?php _e( 'Term ID', 'mshop-members-s2' ); ?></option>
									</select>
									<p class="description"><?php _e( 'Determines the sort order of the terms on the frontend shop product pages. If using custom ordering, you can drag and drop the terms in this attribute.', 'mshop-members-s2' ); ?></p>
								</div>

								<p class="submit"><input type="submit" name="add_new_select_field" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Attribute', 'mshop-members-s2' ); ?>"></p>
								<?php wp_nonce_field( 'msm-add-new_attribute' ); ?>
							</form>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript">

				jQuery( 'a.delete' ).click( function() {
					if ( window.confirm( '<?php _e( "Are you sure you want to delete this attribute?", "woocommerce" ); ?>' ) ) {
						return true;
					}
					return false;
				});
			</script>
		</div>
		<?php
	}
}
