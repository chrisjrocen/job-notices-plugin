/**
 * Job Archive Ajax Filtering System
 * Handles live filtering of jobs via Ajax requests
 */
(function() {
	'use strict';

	// Global variables
	let filterTimeout;
	let currentFilters = {};
	let isLoading = false;

	// Initialize when DOM is ready
	document.addEventListener('DOMContentLoaded', function() {
		initializeFilters();
		setupEventListeners();
	});

	/**
	 * Initialize filter system
	 */
	function initializeFilters() {
		// Get current filter values
		currentFilters = {
			post_type: document.getElementById('post_type')?.value || '',
			keywords: document.getElementById('keywords')?.value || '',
			location: document.getElementById('location')?.value || '',
			job_category: document.getElementById('job_category')?.value || '',
			job_type: document.getElementById('job_type')?.value || '',
			bid_location: document.getElementById('bid_location')?.value || '',
			bid_type: document.getElementById('bid_type')?.value || '',
			study_field: document.getElementById('study_field')?.value || '',
			study_level: document.getElementById('study_level')?.value || '',
			study_location: document.getElementById('study_location')?.value || '',
			sort: document.querySelector('.sort-select')?.value || 'date',
			paged: 1,
			posts_per_page: document.querySelector('.per-page-select')?.value || 12
		};
	}

	/**
	 * Setup event listeners for all filter inputs
	 */
	function setupEventListeners() {
		// Text inputs with debouncing
		const textInputs = ['keywords'];
		textInputs.forEach(function(inputId) {
			const input = document.getElementById(inputId);
			if (input) {
				input.addEventListener('input', debounce(function() {
					currentFilters[inputId] = this.value;
					triggerFilter();
				}, 500));
			}
		});

		// Select dropdowns
		const selectInputs = [
			'job_category', 
			'job_type', 
			'location', 
			'study_location', 
			'post_type', 
			'bid_location', 
			'bid_type', 
			'study_field', 
			'study_level'
		];
		selectInputs.forEach(function(inputId) {
			const select = document.getElementById(inputId);
			if (select) {
				select.addEventListener('change', function() {
					currentFilters[inputId] = this.value;
					triggerFilter();
				});
			}
		});

		// Sort select
		const sortSelect = document.querySelector('.sort-select');
		if (sortSelect) {
			sortSelect.addEventListener('change', function() {
				currentFilters.sort = this.value;
				triggerFilter();
			});
		}

		// Per page select
		const perPageSelect = document.querySelector('.per-page-select');
		if (perPageSelect) {
			perPageSelect.addEventListener('change', function() {
				currentFilters.posts_per_page = parseInt(this.value);
				currentFilters.paged = 1; // Reset to first page
				triggerFilter();
			});
		}

		// Clear filters button
		const clearButton = document.getElementById('clear-filters');
		if (clearButton) {
			clearButton.addEventListener('click', clearAllFilters);
		}

		// Prevent form submission (we handle it via Ajax)
		const form = document.querySelector('.job-notices__filter-form');
		if (form) {
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				triggerFilter();
			});
		}
	}

	/**
	 * Debounce function to limit API calls
	 */
	function debounce(func, wait) {
		return function executedFunction(...args) {
			const later = () => {
				clearTimeout(filterTimeout);
				func.apply(this, args);
			};
			clearTimeout(filterTimeout);
			filterTimeout = setTimeout(later, wait);
		};
	}

	/**
	 * Trigger the filter request
	 */
	function triggerFilter() {
		if (isLoading) return;

		showLoadingState();

		// Prepare data for Ajax request
		const formData = new FormData();
		formData.append('action', 'filter_jobs');
		formData.append('nonce', jobNoticesAjax.nonce);

		// Add all filter values
		Object.keys(currentFilters).forEach(function(key) {
			formData.append(key, currentFilters[key]);
		});

		// Make Ajax request
		fetch(jobNoticesAjax.ajaxurl, {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				updateJobResults(data);
			} else {
				showError(jobNoticesAjax.strings.error);
			}
		})
		.catch(error => {
			console.error('Filter error:', error);
			showError(jobNoticesAjax.strings.error);
		})
		.finally(() => {
			hideLoadingState();
		});
	}

	/**
	 * Update the job results section with new HTML
	 */
	function updateJobResults(data) {
		const resultsContainer = document.querySelector('.job-notices__results');
		if (!resultsContainer) return;

		// Update results count
		const resultsCount = resultsContainer.querySelector('.job-notices__results-count');
		if (resultsCount) {
			resultsCount.textContent = `Showing ${data.count} results`;
		}

		// Replace the job results section
		const jobResults = resultsContainer.querySelector('#job-results');
		if (jobResults) {
			jobResults.outerHTML = data.html;
		} else {
			// If no existing results, insert new ones
			const resultsHeader = resultsContainer.querySelector('.job-notices__results-header');
			if (resultsHeader) {
				resultsHeader.insertAdjacentHTML('afterend', data.html);
			}
		}

	}

	/**
	 * Show loading state
	 */
	function showLoadingState() {
		isLoading = true;
		const resultsContainer = document.querySelector('.job-notices__results');
		if (resultsContainer) {
			resultsContainer.classList.add('job-notices__results--loading');

			// Add loading spinner
			const loadingSpinner = document.createElement('div');
			loadingSpinner.className = 'job-notices__loading-spinner';
			loadingSpinner.innerHTML = `<div class="job-notices__spinner"></div><p>${jobNoticesAjax.strings.loading}</p>`;
			resultsContainer.appendChild(loadingSpinner);
		}
	}

	/**
	 * Hide loading state
	 */
	function hideLoadingState() {
		isLoading = false;
		const resultsContainer = document.querySelector('.job-notices__results');
		if (resultsContainer) {
			resultsContainer.classList.remove('job-notices__results--loading');

			// Remove loading spinner
			const loadingSpinner = resultsContainer.querySelector('.job-notices__loading-spinner');
			if (loadingSpinner) {
				loadingSpinner.remove();
			}
		}
	}

	/**
	 * Show error message
	 */
	function showError(message) {
		const resultsContainer = document.querySelector('.job-notices__results');
		if (resultsContainer) {
			const errorDiv = document.createElement('div');
			errorDiv.className = 'job-notices__filter-error';
			errorDiv.innerHTML = `<p>${message}</p>`;
			resultsContainer.appendChild(errorDiv);

			// Remove error after 5 seconds
			setTimeout(() => {
				errorDiv.remove();
			}, 5000);
		}
	}

	/**
	 * Clear all filters
	 */
	function clearAllFilters() {
		// Reset form inputs
		document.getElementById('keywords').value = '';
		document.getElementById('location').value = 0;
		document.getElementById('job_category').value = 0;
		document.getElementById('job_type').value = 0;
		document.getElementById('bid_location').value = 0;
		document.getElementById('bid_type').value = 0;
		document.getElementById('study_field').value = 0;
		document.getElementById('study_level').value = 0;
		document.getElementById('study_location').value = 0;

		if (document.querySelector('.sort-select')) {
			document.querySelector('.sort-select').value = 'date';
		}
		if (document.querySelector('.per-page-select')) {
			document.querySelector('.per-page-select').value = '12';
		}

		// Reset current filters
		currentFilters = {
			post_type: '',
			keywords: '',
			location: '',
			job_category: '',
			job_type: '',
			bid_location: '',
			bid_type: '',
			study_field: '',
			study_level: '',
			study_location: '',
			sort: 'date',
			paged: 1,
			posts_per_page: 12
		};

		// Trigger filter to show all jobs
		triggerFilter();
	}

	// Handle browser back/forward buttons
	window.addEventListener('popstate', function() {
		// Reload page to show correct state
		window.location.reload();
	});

	/**
	 * Handle load more taxonomies functionality
	 */
	function setupTaxonomyLoadMore() {
		const loadMoreButtons = document.querySelectorAll('.job-notices__load-more-taxonomies');

		loadMoreButtons.forEach(function(button) {
			button.addEventListener('click', function() {
				const taxonomyList = this.previousElementSibling;
				if (!taxonomyList) return;

				// Find all hidden taxonomy items
				const hiddenItems = taxonomyList.querySelectorAll('.job-notices__taxonomy-item--hidden');

				// Show all hidden items
				hiddenItems.forEach(function(item) {
					item.style.display = 'list-item';
					item.style.listStyle = 'none';
				});

				// Hide the load more button
				this.style.display = 'none';
			});
		});
	}

	// Initialize taxonomy load more when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', setupTaxonomyLoadMore);
	} else {
		setupTaxonomyLoadMore();
	}

})();
