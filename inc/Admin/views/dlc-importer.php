<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="wrap fp-dlc-importer">
	<h1><?php esc_html_e( 'DLC Importer from Google Sheets', 'fp' ); ?></h1>

	<div class="notice notice-error is-dismissible" id="fp-import-error" hidden>
		<p id="fp-import-error-message"></p>
	</div>

	<div class="fp-importer-card">
		<h2><?php esc_html_e( 'Import Settings', 'fp' ); ?></h2>

		<form id="fp-dlc-sync-form">
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

		<div class="fp-import-progress" id="fp-import-progress" hidden>
			<div class="fp-progress-bar">
				<div class="fp-progress-bar-fill" id="fp-progress-fill" style="width: 0%"></div>
			</div>
			<p class="fp-progress-status">
				<span id="fp-progress-text"><?php esc_html_e( 'Preparing import...', 'fp' ); ?></span>
				<span id="fp-progress-count"></span>
			</p>
		</div>

		<div class="fp-import-progress" id="fp-image-progress" hidden>
			<div class="fp-progress-bar">
				<div class="fp-progress-bar-fill fp-progress-bar-fill--images" id="fp-image-progress-fill" style="width: 0%"></div>
			</div>
			<p class="fp-progress-status">
				<span id="fp-image-progress-text"><?php esc_html_e( 'Uploading images...', 'fp' ); ?></span>
				<span id="fp-image-progress-count"></span>
			</p>
		</div>

		<div class="fp-import-report" id="fp-import-report" hidden>
			<h3><?php esc_html_e( 'Sync Report', 'fp' ); ?></h3>
			<ul>
				<li><?php esc_html_e( 'Added:', 'fp' ); ?> <strong id="fp-report-added">0</strong></li>
				<li><?php esc_html_e( 'Updated:', 'fp' ); ?> <strong id="fp-report-updated">0</strong></li>
				<li class="error-count" id="fp-report-errors-row" hidden><?php esc_html_e( 'Errors:', 'fp' ); ?> <strong id="fp-report-errors">0</strong></li>
			</ul>
			<div id="fp-report-error-list" hidden>
				<p><strong><?php esc_html_e( 'Import Errors:', 'fp' ); ?></strong></p>
				<ul id="fp-report-error-items"></ul>
			</div>
		</div>
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
					<li><code>release_date</code> - <?php esc_html_e( 'Post publish date', 'fp' ); ?></li>
				</ul>
			</div>

			<div class="fp-column-group">
				<h3><?php esc_html_e( 'Taxonomies (one term per line)', 'fp' ); ?></h3>
				<ul>
					<li><code>dlc_category</code> - <?php esc_html_e( 'DLC categories', 'fp' ); ?></li>
					<li><code>dlc_includes</code> - <?php esc_html_e( 'What DLC includes', 'fp' ); ?></li>
					<li><code>dlc_waterways</code> - <?php esc_html_e( 'Waterways', 'fp' ); ?></li>
					<li><code>dlc_fishing_style</code> - <?php esc_html_e( 'Fishing styles', 'fp' ); ?></li>
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
