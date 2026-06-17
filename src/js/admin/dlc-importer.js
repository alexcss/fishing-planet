document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('fp-dlc-sync-form');
	const button = document.getElementById('fp-sync-button');

	if (!form || !button) {
		return;
	}

	form.addEventListener('submit', (e) => {
		const sheetUrl = document.getElementById('sheet_url').value.trim();

		if (!sheetUrl) {
			e.preventDefault();
			alert('Please enter a Google Sheets URL.');
			return;
		}

		if (!sheetUrl.includes('docs.google.com/spreadsheets')) {
			e.preventDefault();
			alert('Please enter a valid Google Sheets URL.');
			return;
		}

		button.classList.add('is-loading');
		button.disabled = true;

		const originalText = button.innerHTML;
		button.innerHTML = '<span class="dashicons dashicons-update"></span> Syncing...';
	});

	const notices = document.querySelectorAll('.notice.is-dismissible');
	notices.forEach((notice) => {
		const dismissButton = notice.querySelector('.notice-dismiss');
		if (dismissButton) {
			dismissButton.addEventListener('click', () => {
				notice.style.display = 'none';
			});
		}
	});
});
