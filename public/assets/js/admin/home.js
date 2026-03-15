document.addEventListener("DOMContentLoaded", function () {

    // Mark sidebar link as active
    document.querySelectorAll("#sidebar .nav-link").forEach(function (link) {
        if (link.getAttribute("href") === "/admin") {
            link.classList.remove("text-white-50");
            link.classList.add("active");
        }
    });

    // Initialise DataTable
    const table = $("#example-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: "/admin/datatable",
        order: [[5, "desc"]],
        columns: [
            { data: null,           orderable: false, searchable: false, className: "text-center" },
            { data: "icon",  orderable: false, searchable: false, className: "text-center" },
            { data: "title" },
            { data: "body",         orderable: false },
            { data: "calltoaction", orderable: false, searchable: false },
            { data: "created_at" },
            { data: "is_read",      orderable: false, searchable: false, className: "text-center" },
            { data: "is_cleared",   orderable: false, searchable: false, className: "text-center" },
            { data: "actions",      orderable: false, searchable: false, className: "text-center" },
        ],
        columnDefs: [
            {
                targets: 0,
                render: function () {
                    return '<input type="checkbox" class="form-check-input row-checkbox">';
                },
            },
        ],
    });

    // --- Row selection ---

    function getSelectedUuids() {
        const uuids = [];
        table.rows().nodes().to$().find(".row-checkbox:checked").each(function () {
            uuids.push($(this).closest("tr").attr("id"));
        });
        return uuids;
    }

    function updateDeleteButton() {
        const count = table.rows().nodes().to$().find(".row-checkbox:checked").length;
        $("#btn-delete-selected").prop("disabled", count === 0);
    }

    function syncSelectAll() {
        const all   = table.rows().nodes().to$().find(".row-checkbox").length;
        const checked = table.rows().nodes().to$().find(".row-checkbox:checked").length;
        const selectAll = document.getElementById("select-all");
        selectAll.checked       = all > 0 && checked === all;
        selectAll.indeterminate = checked > 0 && checked < all;
    }

    // Select-all header checkbox
    $("#select-all").on("change", function () {
        const checked = this.checked;
        table.rows().nodes().to$().find(".row-checkbox").prop("checked", checked);
        updateDeleteButton();
    });

    // Individual row checkbox
    $("#example-table").on("change", ".row-checkbox", function () {
        syncSelectAll();
        updateDeleteButton();
    });

    // Reset selection state after each DataTables draw
    table.on("draw", function () {
        document.getElementById("select-all").checked       = false;
        document.getElementById("select-all").indeterminate = false;
        updateDeleteButton();
    });

    // --- Refresh ---

    $("#btn-datatable-refresh").on("click", function () {
        table.ajax.reload(null, false);
    });

    // --- Edit modal ---

    const editModal        = new bootstrap.Modal(document.getElementById("editModal"));
    const $editUuid        = $("#edit-uuid");
    const $editTitle       = $("#edit-title");
    const $editBody        = $("#edit-body");
    const $editUrl         = $("#edit-url");
    const $editCta         = $("#edit-calltoaction");
    const $editIsRead      = $("#edit-is-read");
    const $editIsCleared   = $("#edit-is-cleared");
    const $editStatusFields = $("#edit-status-fields");
    const $editError       = $("#edit-modal-error");
    const $confirmEdit     = $("#btn-confirm-edit");

    $("#example-table").on("click", ".btn-edit-row", function () {
        const uuid = $(this).data("uuid");
        $editError.addClass("d-none").text("");
        $editUuid.val("");
        $editTitle.val("");
        $editBody.val("");
        $editUrl.val("");
        $editCta.val("");
        $editIsRead.prop("checked", false);
        $editIsCleared.prop("checked", false);
        $editStatusFields.addClass("d-none");
        $confirmEdit.prop("disabled", true).text("Save changes");

        fetch("/admin/notification/" + encodeURIComponent(uuid))
            .then(function (response) {
                if (! response.ok) throw new Error("Server returned " + response.status);
                return response.json();
            })
            .then(function (json) {
                if (! json.success) throw new Error(json.message || "Failed to load notification.");
                $editUuid.val(json.data.uuid);
                $editTitle.val(json.data.title);
                $editBody.val(json.data.body);
                $editUrl.val(json.data.url);
                $editCta.val(json.data.calltoaction);
                if (json.data.user_uuid !== "everyone") {
                    $editIsRead.prop("checked", json.data.is_read == 1);
                    $editIsCleared.prop("checked", json.data.is_cleared == 1);
                    $editStatusFields.removeClass("d-none");
                } else {
                    $editStatusFields.addClass("d-none");
                }
                $confirmEdit.prop("disabled", false);
                editModal.show();
            })
            .catch(function (err) {
                console.error("Edit load failed:", err);
            });
    });

    $confirmEdit.on("click", function () {
        const uuid  = $editUuid.val().trim();
        const title = $editTitle.val().trim();
        const body  = $editBody.val().trim();
        const url          = $editUrl.val().trim();
        const calltoaction = $editCta.val().trim();

        $editError.addClass("d-none").text("");

        if (title === "" || body === "" || url === "") {
            $editError.text("Title, body, and URL are required.").removeClass("d-none");
            return;
        }

        $confirmEdit.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving…');

        const payload = { uuid, title, body, url, calltoaction };
        if (! $editStatusFields.hasClass("d-none")) {
            payload.is_read    = $editIsRead.prop("checked");
            payload.is_cleared = $editIsCleared.prop("checked");
        }

        fetch("/admin/notification/update", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
        })
            .then(function (response) {
                if (! response.ok) throw new Error("Server returned " + response.status);
                return response.json();
            })
            .then(function (json) {
                if (! json.success) throw new Error(json.message || "Failed to save.");
                editModal.hide();
                table.ajax.reload(null, false);
            })
            .catch(function (err) {
                $editError.text(err.message || "An error occurred. Please try again.").removeClass("d-none");
            })
            .finally(function () {
                $confirmEdit.prop("disabled", false).text("Save changes");
            });
    });

    document.getElementById("editModal").addEventListener("hidden.bs.modal", function () {
        $editError.addClass("d-none").text("");
    });

    // --- Delete modal ---

    const deleteModal     = new bootstrap.Modal(document.getElementById("deleteModal"));
    const $modalMessage   = $("#delete-modal-message");
    const $confirmBtn     = $("#btn-confirm-delete");
    let pendingUuids      = [];

    function openDeleteModal(uuids) {
        pendingUuids = uuids;
        const n = uuids.length;
        $modalMessage.text(
            n === 1
                ? "Are you sure you want to delete this notification? This action cannot be undone."
                : "Are you sure you want to delete " + n + " notifications? This action cannot be undone."
        );
        deleteModal.show();
    }

    // Single-row delete button
    $("#example-table").on("click", ".btn-delete-row", function () {
        const uuid = $(this).data("uuid");
        openDeleteModal([uuid]);
    });

    // Bulk delete button
    $("#btn-delete-selected").on("click", function () {
        const uuids = getSelectedUuids();
        if (uuids.length > 0) {
            openDeleteModal(uuids);
        }
    });

    // Confirm deletion
    $confirmBtn.on("click", function () {
        if (pendingUuids.length === 0) return;

        $confirmBtn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Deleting…');

        fetch("/admin/notifications/delete", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ uuids: pendingUuids }),
        })
            .then(function (response) {
                if (! response.ok) {
                    throw new Error("Server returned " + response.status);
                }
                return response.json();
            })
            .then(function () {
                deleteModal.hide();
                table.ajax.reload(null, false);
            })
            .catch(function (err) {
                console.error("Delete failed:", err);
            })
            .finally(function () {
                pendingUuids = [];
                $confirmBtn.prop("disabled", false).text("Delete");
            });
    });

    // Clear pending state when modal is closed without confirming
    document.getElementById("deleteModal").addEventListener("hidden.bs.modal", function () {
        pendingUuids = [];
    });

});

