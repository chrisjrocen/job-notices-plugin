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
			keywords: document.getElementById('keywords')?.value || '',
			location: document.getElementById('location')?.value || '',
			category: document.getElementById('category')?.value || '',
			job_type: document.getElementById('job_type')?.value || '',
			salary_min: 0,
			salary_max: parseInt(document.getElementById('salary_range')?.value || 850) * 1000,
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
		const selectInputs = ['category', 'job_type', 'location'];
		selectInputs.forEach(function(inputId) {
			const select = document.getElementById(inputId);
			if (select) {
				select.addEventListener('change', function() {
					currentFilters[inputId] = this.value;
					triggerFilter();
				});
			}
		});

		// Salary range slider
		const salaryRange = document.getElementById('salary_range');
		const salaryOutput = document.getElementById('salary_output');
		if (salaryRange && salaryOutput) {
			salaryRange.addEventListener('input', function() {
				const value = parseInt(this.value);
				currentFilters.salary_max = value * 1000;
				salaryOutput.textContent = `$0 - $${value}k`;
				triggerFilter();
			});
		}

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

		// Update URL without page reload
		updateURL();
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
		document.getElementById('location').value = '';
		document.getElementById('category').value = '';
		document.getElementById('job_type').value = '';
		document.getElementById('salary_range').value = '850';
		document.getElementById('salary_output').textContent = '$0 - $850k';

		if (document.querySelector('.sort-select')) {
			document.querySelector('.sort-select').value = 'date';
		}
		if (document.querySelector('.per-page-select')) {
			document.querySelector('.per-page-select').value = '12';
		}

		// Reset current filters
		currentFilters = {
			keywords: '',
			location: '',
			category: '',
			job_type: '',
			salary_min: 0,
			salary_max: 850000,
			sort: 'date',
			paged: 1,
			posts_per_page: 12
		};

		// Trigger filter to show all jobs
		triggerFilter();
	}

	/**
	 * Update URL with current filters (without page reload)
	 */
	function updateURL() {
		const url = new URL(window.location);

		// Update URL parameters
		if (currentFilters.keywords) {
			url.searchParams.set('s', currentFilters.keywords);
		} else {
			url.searchParams.delete('s');
		}

		if (currentFilters.location) {
			url.searchParams.set('location', currentFilters.location);
		} else {
			url.searchParams.delete('location');
		}

		if (currentFilters.category) {
			url.searchParams.set('job_category', currentFilters.category);
		} else {
			url.searchParams.delete('job_category');
		}

		if (currentFilters.job_type) {
			url.searchParams.set('job_type', currentFilters.job_type);
		} else {
			url.searchParams.delete('job_type');
		}

		if (currentFilters.sort !== 'date') {
			url.searchParams.set('sort', currentFilters.sort);
		} else {
			url.searchParams.delete('sort');
		}

		if (currentFilters.posts_per_page !== 12) {
			url.searchParams.set('posts_per_page', currentFilters.posts_per_page);
		} else {
			url.searchParams.delete('posts_per_page');
		}

		// Update URL without page reload
		window.history.pushState({}, '', url);
	}

	// Handle browser back/forward buttons
	window.addEventListener('popstate', function() {
		// Reload page to show correct state
		window.location.reload();
	});

})();
