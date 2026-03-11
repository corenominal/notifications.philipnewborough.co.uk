document.addEventListener('DOMContentLoaded', function() {
    const filterInput = document.getElementById('filter');
    const matches = document.getElementById('matches');
    const links = document.querySelectorAll('.list-group a');

    // Focus on filter input
    filterInput.focus();

    // On filter input event
    filterInput.addEventListener('keyup', handleFilter);
    filterInput.addEventListener('paste', handleFilter);

    function handleFilter() {
        const filter = filterInput.value.trim();

        if (filter === '') {
            // If empty, show all
            links.forEach(function(el) { el.style.display = ''; });

            // Clear any notifications
            matches.innerHTML = '';
        } else {
            // Flag for matches
            let matchCount = 0;

            // Hide all items, show matches
            links.forEach(function(el) {
                if (el.textContent.toLowerCase().includes(filter)) {
                    el.style.display = '';
                    matchCount++;
                } else {
                    el.style.display = 'none';
                }
            });

            // Check if there are no matches and show notification
            if (matchCount === 0) {
                matches.innerHTML = '<div class="alert alert-warning" role="alert">Uh-oh, not results </div>';
            } else {
                matches.innerHTML = '';
            }
        }
    }
});
