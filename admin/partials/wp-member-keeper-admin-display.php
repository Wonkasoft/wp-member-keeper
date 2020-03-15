<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wonkasoft.com/
 * @since      1.0.0
 *
 * @package    Wp_Member_Keeper
 * @subpackage Wp_Member_Keeper/admin/partials
 */

defined( 'WPINC' ) || exit;

global $wpdb;
$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );
$results = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $table_name ) );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wp-member-keeper-settings-page-wrap">
	<section class="wpmk-header-section">
		<div class="logo-container">
			<?php
				
				$current_logo_id = get_option( 'wp_member_keeper_logo' );
					
				if ( !empty( $_FILES ) ) :

					$wordpress_upload_dir = wp_upload_dir();
					$logo_img = $_FILES['logo-img'];
					$new_file_path = $wordpress_upload_dir['path'] . '/' . $logo_img['name'];
					$new_file_mime = mime_content_type( $logo_img['tmp_name'] );
					$i = 1;

					if( empty( $logo_img ) )
						die( 'File is not selected.' );
					 
					if( $logo_img['error'] )
						die( $logo_img['error'] );
					 
					if( $logo_img['size'] > wp_max_upload_size() )
						die( 'It is too large than expected.' );
					 
					if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
						die( 'WordPress doesn\'t allow this type of uploads.' );

					while( file_exists( $new_file_path ) ) {
						$i++;
						$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $logo_img['name'];
					}

					// looks like everything is OK
					if( move_uploaded_file( $logo_img['tmp_name'], $new_file_path ) ) {
					 
					 
						$upload_id = wp_insert_attachment( array(
							'guid'           => $new_file_path, 
							'post_mime_type' => $new_file_mime,
							'post_title'     => preg_replace( '/\.[^.]+$/', '', $logo_img['name'] ),
							'post_content'   => '',
							'post_status'    => 'inherit'
						), $new_file_path );
					 
						// wp_generate_attachment_metadata() won't work if you do not include this file
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
					 
						// Generate and save the attachment metas into the database
						wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

						if ( $current_logo_id !== $upload_id ) :
							wp_delete_attachment( $current_logo_id, true );
							$current_logo_id = $upload_id;
						endif;

						update_option( 'wp_member_keeper_logo', $upload_id );

					}

				endif;
			?>
			<img src="<?php echo esc_url( wp_get_attachment_image_src( $current_logo_id, 'thumbnail', false )[0] ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $current_logo_id, 'medium', null ); ?>">
			<?php
				if ( empty( $current_logo_id ) ) : ?>
					
					<form method="post" enctype="multipart/form-data">
						<input type="file" id="logo-img" name="logo-img" accept="image/*.[jpeg|jpg|png|gif]">
						<input type="submit" id="logo-submit" name="logo-submit">
					</form>

			<?php endif; 
			?>
		</div>
		<div class="header-title-container">
			<h2 class="title-text"><?php echo esc_html( WP_MEMBER_KEEPER_NAME ); ?></h2>
		</div>
	</section>
	<section class="wpmk-fields-container">
		<div class="tab-row">
			<ul class="tab-list">
				<li class="tab-item member-list active" data-target="member-list">
					<h5>Member List</h5>
				</li>
				<li class="tab-item add-member" data-target="add-member">
					<h5>Add Member</h5>
				</li>
				<li class="tab-item edit-member disabled" disabled data-target="edit-member">
					<h5>Edit Member</h5>
				</li>
			</ul>
		</div>
		<div class="tab-content-wrap">
			<div class="tab-content active" data-content="member-list">
				<?php 
					if ( !empty( $results ) ) : ?>
						<table>
                        	<thead>
                        		<tr>
	                        		<th>
	                        			<input type="checkbox" name="check-all">
	                        		</th>
	                        		<th>
	                        			<h6>First Name</h6>
	                        		</th>
	                        		<th>
	                        			<h6>Last Name</h6>
	                        		</th>
	                        		<th>
	                        			<h6>Phone #</h6>
	                        		</th>
	                        		<th>
	                        			<h6>Email</h6>
	                        		</th>
                        		</tr>
                        	</thead>         
                        	<tbody>
                        		
				<?php foreach( $results as $member ) : ?>
								<tr>
									<td><input type="checkbox" name="member_<?php echo esc_html( $member['id'] ); ?>"></td>
									<td><?php echo esc_html( $member['first_name'] ); ?></td>
									<td><?php echo esc_html( $member['last_name'] ); ?></td>
									<td><?php echo esc_html( $member['phone'] ); ?></td>
									<td><?php echo esc_html( $member['email'] ); ?></td>
								</tr>
				<?php endforeach; ?>
						
                        	</tbody>      
						</table>

				<?php else : ?>
						<span>Currently there are no members in the keeper!</span>

				<?php endif; ?>
			</div>
			<div class="tab-content" data-content="add-member">
				<form method="post" enctype="multipart/form-data">
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">First Name</span>
							<input type="text" class="member-field-inputs" id="first_name" name="first_name">
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Last Name</span>
							<input type="text" class="member-field-inputs" id="last_name" name="last_name">
						</div>
					</div>
					<div class="input-group">
					</div>
					<input type="submit" name="add_member_submit">
				</form>
			</div>
			<div class="tab-content" data-content="edit-member"></div>
		</div>
	</section>
</div>