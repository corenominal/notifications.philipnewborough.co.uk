document.addEventListener("DOMContentLoaded", function() {
    const sidebarLinks = document.querySelectorAll("#sidebar .nav-link");
    sidebarLinks.forEach(link => {
        if (link.getAttribute("href") === "/admin") {
            link.classList.remove("text-white-50");
            link.classList.add("active");
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {

    const exampleTable = new DataTable('#example-table', {

        // ── Layout & UI ────────────────────────────────────────────────────────
        autoWidth:      true,           // Auto-calculate column widths
        info:           true,           // Show "Showing X to Y of Z entries"
        lengthChange:   true,           // Allow user to change page length
        ordering:       true,           // Enable column sorting
        paging:         true,           // Enable pagination
        searching:      true,           // Enable global search box
        orderMulti:     true,           // Allow multi-column sort (shift+click)
        orderClasses:   true,           // Add sorting CSS classes to columns
        pagingType:     'simple_numbers', // 'simple' | 'simple_numbers' | 'full' | 'full_numbers' | 'first_last_numbers'
        pageLength:     10,             // Rows per page
        lengthMenu:     [10, 25, 50, 100], // Page length options

        // ── Default sort ───────────────────────────────────────────────────────
        order: [[0, 'asc']],            // [[columnIndex, 'asc'|'desc'], ...]

        // ── Performance ────────────────────────────────────────────────────────
        deferRender:    false,          // Defer rendering off-screen rows (useful for large datasets)
        processing:     false,          // Show a processing indicator (useful with serverSide)
        serverSide:     false,          // Enable server-side processing (requires ajax option)
        stateSave:      false,          // Persist state (paging, sorting, search) in sessionStorage

        // ── Data source ────────────────────────────────────────────────────────
        // ajax: '/api/example-table',  // URL or config object for server-side / ajax data loading
        // data: [],                    // Inline JS data array (alternative to HTML or ajax)

        // ── Scroll ─────────────────────────────────────────────────────────────
        scrollX:        false,          // Horizontal scrolling
        scrollY:        '',             // Vertical scroll height, e.g. '400px'
        scrollCollapse: false,          // Shrink table when fewer rows than scrollY height

        // ── Column definitions ─────────────────────────────────────────────────
        columns: [
            {
                // Column 0 — #
                name:        'id',
                title:       '#',
                type:        'num',         // 'string' | 'num' | 'num-fmt' | 'html' | 'html-num' | 'date'
                orderable:   true,
                searchable:  false,         // No value in searching the row number
                visible:     true,
                width:       '3rem',
                className:   'text-end',
            },
            {
                // Column 1 — First Name
                name:        'first_name',
                title:       'First Name',
                type:        'string',
                orderable:   true,
                searchable:  true,
                visible:     true,
                width:       '',            // Leave empty to let autoWidth decide
                className:   '',
            },
            {
                // Column 2 — Last Name
                name:        'last_name',
                title:       'Last Name',
                type:        'string',
                orderable:   true,
                searchable:  true,
                visible:     true,
                width:       '',
                className:   '',
            },
            {
                // Column 3 — Email
                name:        'email',
                title:       'Email',
                type:        'string',
                orderable:   true,
                searchable:  true,
                visible:     true,
                width:       '',
                className:   '',
            },
            {
                // Column 4 — Role
                name:        'role',
                title:       'Role',
                type:        'string',
                orderable:   true,
                searchable:  true,
                visible:     true,
                width:       '',
                className:   '',
            },
            {
                // Column 5 — Status (contains HTML, so we use a custom render function to ensure sorting and searching work on the text content, not the HTML)
                name:        'status',
                title:       'Status',
                type:        'string',
                orderable:   true,
                searchable:  true,
                visible:     true,
                width:       '6rem',
                className:   'text-center',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'filter') {
                        const tmp = document.createElement('div');
                        tmp.innerHTML = data;
                        return tmp.textContent || tmp.innerText || '';
                    }
                    return data;
                },
            },
            {
                // Column 6 — Joined (ISO date string sorts correctly as a string)
                name:        'joined',
                title:       'Joined',
                type:        'date',
                orderable:   true,
                searchable:  false,
                visible:     true,
                width:       '7rem',
                className:   '',
            },
        ],

        // ── Language / localisation ────────────────────────────────────────────
        language: {
            emptyTable:     'No data available in table',
            info:           'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty:      'Showing 0 to 0 of 0 entries',
            infoFiltered:   '(filtered from _MAX_ total entries)',
            lengthMenu:     'Show _MENU_ entries',
            loadingRecords: 'Loading...',
            processing:     'Processing...',
            search:         'Search:',
            zeroRecords:    'No matching records found',
            paginate: {
                first:    'First',
                last:     'Last',
                next:     'Next',
                previous: 'Previous',
            },
        },

        // ── Callbacks ──────────────────────────────────────────────────────────
        // initComplete: function(settings, json) {},   // Fires once table is fully initialised
        // drawCallback: function(settings) {},         // Fires on every draw (page change, sort, search)
        // rowCallback:  function(row, data, index) {}, // Fires for each row on every draw
        // createdRow:   function(row, data, index) {}, // Fires once per row when the TR element is created
        // headerCallback: function(thead, data, start, end, display) {},

    });

    // ── Refresh button ─────────────────────────────────────────────────────────
    document.getElementById('btn-datatable-refresh').addEventListener('click', function() {
        // exampleTable.ajax.reload(null, false); // null keeps current page; false = don't reset paging
        // For non-ajax tables use exampleTable.draw() to simply redraw:
        exampleTable.draw();
        console.log('Table refreshed');
    });

});

