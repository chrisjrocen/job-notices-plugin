// JavaScript for handling job archive filter interactions
//TODO Implement Ajax so that the page does not reload. 
(function () {
  document.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector('.job-filter-form');
    const inputs = form ? form.querySelectorAll('input, select') : [];
    const submitButton = form ? form.querySelector('button[type="submit"]') : null;

    submitButton?.addEventListener('click', function (event) {
      event.preventDefault(); // Prevent default form submission
      form.submit(); // Submit the form programmatically
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

/**
 * Filter function for job results based on keywords
 */
function filterJobsByKeywords() {
  var input, filter, ul, li, a, i, txtValue;
  input = document.getElementById("keywords");
  filter = input.value.toUpperCase();
  ul = document.getElementById("job-results");
  li = ul.querySelectorAll(".job-card");
  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("a")[0];
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}
