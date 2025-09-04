document.addEventListener('DOMContentLoaded', function () {
	const copyButtons = document.querySelectorAll('.job-notices__share-button--copy');

	copyButtons.forEach(button => {
		button.addEventListener('click', function () {
			const url = this.getAttribute('data-url');

			// Modern API
			if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
				navigator.clipboard.writeText(url).then(() => {
					alert('Link copied to clipboard!');
				}).catch(() => {
					fallbackCopy(url);
				});
			} else {
				// Fallback
				fallbackCopy(url);
			}
		});
	});

	function fallbackCopy(text) {
		const textarea = document.createElement('textarea');
		textarea.value = text;
		textarea.setAttribute('readonly', '');
		textarea.style.position = 'absolute';
		textarea.style.left = '-9999px';
		document.body.appendChild(textarea);
		textarea.select();
		try {
			document.execCommand('copy');
			alert('Link copied to clipboard!');
		} catch (err) {
			alert('Failed to copy. Please copy manually: ' + text);
		}
		document.body.removeChild(textarea);
	}
});
