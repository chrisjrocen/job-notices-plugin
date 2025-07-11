document.addEventListener('DOMContentLoaded', function () {
	const copyButtons = document.querySelectorAll('.copy-link-button');

	copyButtons.forEach(button => {
		button.addEventListener('click', function () {
			const url = this.getAttribute('data-url');
			navigator.clipboard.writeText(url).then(() => {
				alert('Link copied to clipboard!');
			});
		});
	});
});