// JavaScript for handling job archive filter interactions
//TODO Implement Ajax so that the page does not reload. 
(function () {
  document.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector('.job-filter-form');
    const inputs = form ? form.querySelectorAll('input, select') : [];

    // Auto-submit form on change
    inputs.forEach((input) => {
      input.addEventListener('change', function () {
        form.submit();
      });
    });

    // Salary slider output update
    const salaryRange = document.querySelector('#salary_range');
    const salaryOutput = document.querySelector('#salary_output');
    if (salaryRange && salaryOutput) {
      salaryOutput.textContent = `$0 - $${salaryRange.value}`;
      salaryRange.addEventListener('input', function () {
        salaryOutput.textContent = `$0 - $${this.value}`;
      });
    }

    // Sort select (optional AJAX or redirect)
    const sortSelect = document.querySelector('.sort-select');
    if (sortSelect) {
      sortSelect.addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
      });
    }

    // Per page select
    const perPageSelect = document.querySelector('.per-page-select');
    if (perPageSelect) {
      perPageSelect.addEventListener('change', function () {
        const url = new URL(window.location.href);
        url.searchParams.set('posts_per_page', this.value);
        window.location.href = url.toString();
      });
    }

  });
})();
