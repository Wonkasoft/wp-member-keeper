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
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wp-member-keeper-settings-page-wrap">
	<section class="wpmk-header-section">
		<div class="logo-container">
			<?php

				global $wpdb;
				$table_name = $wpdb->prefix . str_replace( ' ', '_', str_replace( 'wp ', '', strtolower( WP_MEMBER_KEEPER_NAME ) ) );
				$results    = $wpdb->get_results( "SELECT * FROM $table_name", OBJECT );
					
				$current_logo_id = ( ! empty( get_option( 'wp_member_keeper_logo' ) ) ) ? get_option( 'wp_member_keeper_logo' ) : '' ;

			if ( ! empty( $_FILES ) ) :

				$wordpress_upload_dir = wp_upload_dir();
				$logo_img             = $_FILES['logo-img'];
				$new_file_path        = $wordpress_upload_dir['path'] . '/' . $logo_img['name'];
				$new_file_mime        = mime_content_type( $logo_img['tmp_name'] );
				$i                    = 1;

				if ( empty( $logo_img ) ) {
					die( 'File is not selected.' );
				}

				if ( $logo_img['error'] ) {
					die( $logo_img['error'] );
				}

				if ( $logo_img['size'] > wp_max_upload_size() ) {
					die( 'It is too large than expected.' );
				}

				if ( ! in_array( $new_file_mime, get_allowed_mime_types() ) ) {
					die( 'WordPress doesn\'t allow this type of uploads.' );
				}

				while ( file_exists( $new_file_path ) ) {
					$i++;
					$new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $logo_img['name'];
				}

				// looks like everything is OK
				if ( move_uploaded_file( $logo_img['tmp_name'], $new_file_path ) ) {


					$upload_id = wp_insert_attachment(
						array(
							'guid'           => $new_file_path,
							'post_mime_type' => $new_file_mime,
							'post_title'     => preg_replace( '/\.[^.]+$/', '', $logo_img['name'] ),
							'post_content'   => '',
							'post_status'    => 'inherit',
						),
						$new_file_path
					);

					// wp_generate_attachment_metadata() won't work if you do not include this file
					require_once ABSPATH . 'wp-admin/includes/image.php';

					// Generate and save the attachment metas into the database
					wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

					if ( $current_logo_id !== $upload_id ) :
						wp_delete_attachment( $current_logo_id, true );
						$current_logo_id = $upload_id;
					endif;

					update_option( 'wp_member_keeper_logo', $upload_id );

				}

				endif;
			if ( empty( $current_logo_id ) ) :
				?>
					
					<form method="post" enctype="multipart/form-data">
						<?php wp_nonce_field( 'wpmk_logo_form', '_wpmk_logo_nonce', true, true ); ?>
						<input type="file" id="logo-img" name="logo-img" accept="image/*.[jpeg|jpg|png|gif]">
						<input type="submit" id="logo-submit" name="logo-submit">
					</form>
			<?php else: ?>
				<img src="<?php echo esc_url( wp_get_attachment_image_src( $current_logo_id, 'thumbnail', false )[0] ); ?>" srcset="<?php echo wp_get_attachment_image_srcset( $current_logo_id, 'medium', null ); ?>">
			<?php endif; ?>
		</div>
		<div class="header-title-container">
			<h2 class="title-text"><?php echo esc_html( WP_MEMBER_KEEPER_NAME ); ?></h2>
		</div>
	</section>
	<section class="wpmk-fields-container">
		<div class="tab-row">
			<ul class="tab-list">
				<li class="tab-item member-list active" data-target="member-list">
					<h5>Members List</h5>
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
			<!-- tab-content -->
			<div class="tab-content active" data-content="member-list">
				<?php
				if ( ! empty( $results ) ) :
					?>
						<div class="table-responsive">
						<table class="member-table" data-security="<?php echo wp_create_nonce( '_wpmk_member_table' ); ?>">
							<thead>
								<tr>
									<th>
										<h3>Family ID</h3>
									</th>
									<th>
										<h3>First Name</h3>
									</th>
									<th>
										<h3>Last Name</h3>
									</th>
									<th>
										<h3>Phone #</h3>
									</th>
									<th>
										<h3>Email</h3>
									</th>
									<th>
										<h3>Ministries</h3>
									</th>
									<th>
										<h3>Manage <i class="fas fa-download download" title="Download CSV"></i></h3> 
									</th>
								</tr>
							</thead>         
							<tbody>
								
					<?php
					foreach ( $results as $member ) :
						$family_id = ( 0 == $member->family_id ) ? $member->id : $member->family_id;
						?>
								<tr>
									<td><?php echo esc_html( $family_id ); ?></td>
									<td><?php echo esc_html( $member->first_name ); ?></td>
									<td><?php echo esc_html( $member->last_name ); ?></td>
									<td><?php echo esc_html( $member->phone ); ?></td>
									<td><?php echo esc_html( $member->email ); ?></td>
									<td><?php echo esc_html( $member->ministries ); ?></td>
									<td><i data-member="<?php echo esc_html( $member->id ); ?>" class="fas fa-user-edit edit" title="Edit Member"></i> <i data-member="<?php echo esc_html( $member->id ); ?>" class="fas fa-trash-alt remove" title="Delete Member"></i></td>
								</tr>
					<?php endforeach; ?>
						
							</tbody>      
						</table>

						</div>
				<?php else : ?>
						<span>Currently there are no members in the keeper!</span>

				<?php endif; ?>
			</div>
			<!-- end-tab-content -->

			<!-- tab-content -->
			<div class="tab-content" data-content="add-member">
				<form class="add-member-form" method="post" enctype="multipart/form-data">
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">First Name*</span>
							<input type="text" class="member-field-inputs" id="first_name" name="first_name" required>
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Last Name*</span>
							<input type="text" class="member-field-inputs" id="last_name" name="last_name" required>
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Email*</span>
							<input type="email" class="member-field-inputs" id="email" name="email" required>
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Phone #</span>
							<input type="tel" class="member-field-inputs" id="phone" name="phone">
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Street Address</span>
							<input type="text" class="member-field-inputs" id="street_address" name="street_address">
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">City</span>
							<input type="text" class="member-field-inputs" id="city" name="city">
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">State</span>
							<input type="text" class="member-field-inputs" id="state" name="state" disabled value="CA">
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Zip Code</span>
							<input type="text" class="member-field-inputs" id="zip_code" name="zip_code">
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Birthday</span>
							<input type="date" class="member-field-inputs" id="birth_date" name="birth_date">
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Family ID</span>
							<input type="number" class="member-field-inputs" id="family_id" name="family_id">
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Ministries</span>
							<input type="text" class="member-field-inputs" id="ministries" name="ministries">
						</div>
					</div>
					<input type="hidden" name="member_form" value="add">
					<?php wp_nonce_field( 'wpmk_add_member_form', '_wpmk_nonce', true, true ); ?>
					<div class="input-group">
						<input type="submit" id="add_member_submit" name="add_member_submit" role="add">
					</div>
				</form>
			</div>
			<!-- end-tab-content -->
			
			<!-- tab-content -->
			<div class="tab-content" data-content="edit-member">
				<form class="edit-member-form" method="post" enctype="multipart/form-data">
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">First Name*</span>
							<input type="text" class="member-field-inputs" id="edit_first_name" name="first_name" required>
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Last Name*</span>
							<input type="text" class="member-field-inputs" id="edit_last_name" name="last_name" required>
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Email*</span>
							<input type="email" class="member-field-inputs" id="edit_email" name="email" required>
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Phone #</span>
							<input type="tel" class="member-field-inputs" id="edit_phone" name="phone">
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Street Address</span>
							<input type="text" class="member-field-inputs" id="edit_street_address" name="street_address">
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">City</span>
							<input type="text" class="member-field-inputs" id="edit_city" name="city">
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">State</span>
							<input type="text" class="member-field-inputs" id="edit_state" name="state" value="CA" disabled>
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Zip Code</span>
							<input type="text" class="member-field-inputs" id="edit_zip_code" name="zip_code">
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Birthday</span>
							<input type="date" class="member-field-inputs" id="edit_birth_date" name="birth_date">
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Family ID</span>
							<input type="number" class="member-field-inputs" id="edit_family_id" name="family_id">
						</div>
						<div class="prepend-wrap">
							<span class="prepend-label">Member ID</span>
							<input type="number" class="member-field-inputs" id="edit_id" name="id" disabled>
						</div>
					</div>
					<div class="input-group">
						<div class="prepend-wrap">
							<span class="prepend-label">Ministries</span>
							<input type="text" class="member-field-inputs" id="edit_ministries" name="ministries">
						</div>
					</div>
					<input type="hidden" name="member_form" value="edit">
					<?php wp_nonce_field( 'wpmk_edit_member_form', '_wpmk_edit_nonce', true, true ); ?>
					<div class="input-group">
						<input type="submit" id="edit_member_submit" name="edit_member_submit" role="edit">
					</div>
				</form>
			</div>
		</div>
	</section>
</div>
