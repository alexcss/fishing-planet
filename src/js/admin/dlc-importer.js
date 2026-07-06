document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('fp-dlc-sync-form');
	const button = document.getElementById('fp-sync-button');
	const progressWrap = document.getElementById('fp-import-progress');
	const progressFill = document.getElementById('fp-progress-fill');
	const progressText = document.getElementById('fp-progress-text');
	const progressCount = document.getElementById('fp-progress-count');
	const reportWrap = document.getElementById('fp-import-report');
	const reportAdded = document.getElementById('fp-report-added');
	const reportUpdated = document.getElementById('fp-report-updated');
	const reportErrorsRow = document.getElementById('fp-report-errors-row');
	const reportErrors = document.getElementById('fp-report-errors');
	const reportErrorList = document.getElementById('fp-report-error-list');
	const reportErrorItems = document.getElementById('fp-report-error-items');
	const errorNotice = document.getElementById('fp-import-error');
	const errorMessage = document.getElementById('fp-import-error-message');

	if (!form || !button || typeof fpDlcImporter === 'undefined') {
		return;
	}

	const totals = { added: 0, updated: 0, errors: [] };

	const ajax = async (action, data = {}) => {
		const body = new FormData();
		body.append('action', action);
		body.append('_ajax_nonce', fpDlcImporter.nonce);
		Object.entries(data).forEach(([key, value]) => body.append(key, value));

		const response = await fetch(fpDlcImporter.ajaxUrl, { method: 'POST', body });

		if (!response.ok) {
			throw new Error(`Server error (${response.status})`);
		}

		const json = await response.json();

		if (!json.success) {
			throw new Error(json.data?.message || 'Unknown error');
		}

		return json.data;
	};

	const showError = (message) => {
		errorMessage.textContent = message;
		errorNotice.hidden = false;
	};

	const hideError = () => {
		errorNotice.hidden = true;
	};

	const updateProgress = (processed, total) => {
		const percent = total > 0 ? Math.round((processed / total) * 100) : 0;
		progressFill.style.width = `${percent}%`;
		progressText.textContent = processed >= total ? 'Finishing...' : 'Importing...';
		progressCount.textContent = `${processed} / ${total} (${percent}%)`;
	};

	const showReport = () => {
		reportAdded.textContent = totals.added;
		reportUpdated.textContent = totals.updated;

		if (totals.errors.length > 0) {
			reportErrors.textContent = totals.errors.length;
			reportErrorsRow.hidden = false;
			reportErrorItems.innerHTML = '';
			totals.errors.forEach((error) => {
				const li = document.createElement('li');
				li.textContent = error;
				reportErrorItems.appendChild(li);
			});
			reportErrorList.hidden = false;
		} else {
			reportErrorsRow.hidden = true;
			reportErrorList.hidden = true;
		}

		reportWrap.hidden = false;
	};

	const runImport = async (sheetUrl) => {
		totals.added = 0;
		totals.updated = 0;
		totals.errors = [];

		progressText.textContent = 'Fetching sheet data...';
		progressCount.textContent = '';
		progressFill.style.width = '0%';
		progressWrap.hidden = false;
		reportWrap.hidden = true;

		const prepared = await ajax('fp_dlc_prepare', { sheet_url: sheetUrl });
		const total = prepared.total;

		if (total === 0) {
			throw new Error('No rows found in the sheet.');
		}

		updateProgress(0, total);

		let offset = 0;
		let done = false;

		while (!done) {
			const batch = await ajax('fp_dlc_import_batch', { offset });

			totals.added += batch.added;
			totals.updated += batch.updated;
			totals.errors.push(...batch.errors);

			offset = batch.processed;
			done = batch.done;

			updateProgress(batch.processed, batch.total);
		}

		progressText.textContent = 'Sync completed!';
		showReport();
	};

	form.addEventListener('submit', async (e) => {
		e.preventDefault();

		const sheetUrl = document.getElementById('sheet_url').value.trim();

		if (!sheetUrl) {
			showError('Please enter a Google Sheets URL.');
			return;
		}

		if (!sheetUrl.includes('docs.google.com/spreadsheets')) {
			showError('Please enter a valid Google Sheets URL.');
			return;
		}

		hideError();
		button.classList.add('is-loading');
		button.disabled = true;

		try {
			await runImport(sheetUrl);
		} catch (error) {
			showError(error.message);
			progressWrap.hidden = true;
		} finally {
			button.classList.remove('is-loading');
			button.disabled = false;
		}
	});
});
