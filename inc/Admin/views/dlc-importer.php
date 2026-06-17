<?php
defined( 'ABSPATH' ) || exit;

$success = isset( $_GET['success'] ) ? sanitize_text_field( wp_unslash( $_GET['success'] ) ) : '';
$error   = isset( $_GET['error'] ) ? sanitize_text_field( wp_unslash( $_GET['error'] ) ) : '';
$added   = isset( $_GET['added'] ) ? intval( $_GET['added'] ) : 0;
$updated = isset( $_GET['updated'] ) ? intval( $_GET['updated'] ) : 0;
$errors  = isset( $_GET['errors'] ) ? intval( $_GET['errors'] ) : 0;

$error_details = get_transient( 'fp_dlc_import_errors' );
if ( $error_details ) {
	delete_transient( 'fp_dlc_import_errors' );
}
?>

<div class="wrap fp-dlc-importer">
	<h1><?php esc_html_e( 'DLC Importer from Google Sheets', 'fp' ); ?></h1>

	<?php if ( $success === 'sync_complete' ) : ?>
		<div class="notice notice-success is-dismissible">
			<p>
				<strong><?php esc_html_e( 'Sync completed successfully!', 'fp' ); ?></strong>
			</p>
			<ul>
				<li><?php printf( esc_html__( 'Added: %d DLC', 'fp' ), $added ); ?></li>
				<li><?php printf( esc_html__( 'Updated: %d DLC', 'fp' ), $updated ); ?></li>
				<?php if ( $errors > 0 ) : ?>
					<li class="error-count"><?php printf( esc_html__( 'Errors: %d', 'fp' ), $errors ); ?></li>
				<?php endif; ?>
			</ul>
		</div>

		<?php if ( ! empty( $error_details ) ) : ?>
			<div class="notice notice-error">
				<p><strong><?php esc_html_e( 'Import Errors:', 'fp' ); ?></strong></p>
				<ul>
					<?php foreach ( $error_details as $error_msg ) : ?>
						<li><?php echo esc_html( $error_msg ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $error === 'no_url' ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Please enter a Google Sheets URL.', 'fp' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $error === 'invalid_url' ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Invalid Google Sheets URL. Please check the URL and try again.', 'fp' ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $error === 'fetch_failed' ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Failed to fetch data from Google Sheets. Make sure the sheet is publicly accessible.', 'fp' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="fp-importer-card">
		<h2><?php esc_html_e( 'Import Settings', 'fp' ); ?></h2>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="fp-dlc-sync-form">
			<?php wp_nonce_field( 'fp_dlc_sync' ); ?>
			<input type="hidden" name="action" value="fp_dlc_sync">

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="sheet_url"><?php esc_html_e( 'Google Sheets URL', 'fp' ); ?></label>
					</th>
					<td>
						<input
							type="url"
							name="sheet_url"
							id="sheet_url"
							class="regular-text"
							value="<?php echo esc_attr( $sheet_url ); ?>"
							placeholder="https://docs.google.com/spreadsheets/d/..."
							required
						>
						<p class="description">
							<?php esc_html_e( 'Enter the full URL of your Google Sheets document. Make sure the sheet is publicly accessible.', 'fp' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<button type="submit" class="button button-primary button-hero" id="fp-sync-button">
					<?php esc_html_e( 'Sync DLC from Google Sheets', 'fp' ); ?>
				</button>
			</p>
		</form>
	</div>

	<div class="fp-importer-card">
		<h2><?php esc_html_e( 'Column Mapping Reference', 'fp' ); ?></h2>
		<p><?php esc_html_e( 'Your Google Sheets should have the following columns (exact names):', 'fp' ); ?></p>

		<div class="fp-columns-grid">
			<div class="fp-column-group">
				<h3><?php esc_html_e( 'Basic Information', 'fp' ); ?></h3>
				<ul>
					<li><code>title</code> - <?php esc_html_e( 'DLC title (required)', 'fp' ); ?></li>
					<li><code>short_description</code> - <?php esc_html_e( 'Short description', 'fp' ); ?></li>
					<li><code>content</code> - <?php esc_html_e( 'Full content/description', 'fp' ); ?></li>
				</ul>
			</div>

			<div class="fp-column-group">
				<h3><?php esc_html_e( 'Taxonomies (one term per line)', 'fp' ); ?></h3>
				<ul>
					<li><code>dlc_category</code> - <?php esc_html_e( 'DLC categories', 'fp' ); ?></li>
					<li><code>dlc_includes</code> - <?php esc_html_e( 'What DLC includes', 'fp' ); ?></li>
					<li><code>dlc_waterways</code> - <?php esc_html_e( 'Waterways', 'fp' ); ?></li>
				</ul>
			</div>

			<div class="fp-column-group">
				<h3><?php esc_html_e( 'Store Links', 'fp' ); ?></h3>
				<ul>
					<li><code>store_steam</code> - <?php esc_html_e( 'Steam store URL', 'fp' ); ?></li>
					<li><code>store_epic_games</code> - <?php esc_html_e( 'Epic Games store URL', 'fp' ); ?></li>
					<li><code>store_ps</code> - <?php esc_html_e( 'PlayStation store URL', 'fp' ); ?></li>
					<li><code>store_xbox</code> - <?php esc_html_e( 'Xbox store URL', 'fp' ); ?></li>
					<li><code>store_windows</code> - <?php esc_html_e( 'Windows store URL', 'fp' ); ?></li>
					<li><code>store_mac</code> - <?php esc_html_e( 'Mac store URL', 'fp' ); ?></li>
					<li><code>store_android</code> - <?php esc_html_e( 'Android store URL', 'fp' ); ?></li>
					<li><code>store_ios</code> - <?php esc_html_e( 'iOS store URL', 'fp' ); ?></li>
					<li><code>store_switch</code> - <?php esc_html_e( 'Nintendo Switch store URL', 'fp' ); ?></li>
				</ul>
			</div>

			<div class="fp-column-group">
				<h3><?php esc_html_e( 'Media', 'fp' ); ?></h3>
				<ul>
					<li><code>thumbnail</code> - <?php esc_html_e( 'Featured image URL', 'fp' ); ?></li>
					<li><code>gallery</code> - <?php esc_html_e( 'Gallery image URLs (one per line)', 'fp' ); ?></li>
				</ul>
			</div>
		</div>

		<div class="fp-info-box">
			<h4><?php esc_html_e( 'Important Notes:', 'fp' ); ?></h4>
			<ul>
				<li><?php esc_html_e( 'For taxonomy columns (dlc_category, dlc_includes, dlc_waterways), enter one term per line within the cell.', 'fp' ); ?></li>
				<li><?php esc_html_e( 'For gallery column, enter one image URL per line within the cell.', 'fp' ); ?></li>
				<li><?php esc_html_e( 'Terms will be created automatically if they don\'t exist.', 'fp' ); ?></li>
				<li><?php esc_html_e( 'Existing DLC (matched by title) will be updated with new data from the sheet.', 'fp' ); ?></li>
				<li><?php esc_html_e( 'Images will be downloaded and attached to the DLC post.', 'fp' ); ?></li>
			</ul>
		</div>
	</div>
</div>
