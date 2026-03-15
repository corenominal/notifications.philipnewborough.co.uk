<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="border-bottom border-1 mb-4 pb-4 d-flex align-items-center justify-content-between gap-3">
                <h2 class="mb-0">Admin Home</h2>
                <div class="" role="group" aria-label="Page actions">
                    <a href="/admin/create" type="button" class="btn btn-outline-primary"><i class="bi bi-plus-circle-fill"></i><span class="d-none d-lg-inline"> New</span></a>
                    
                    <button type="button" class="btn btn-outline-primary" id="btn-datatable-refresh">
                        <i class="bi bi-arrow-clockwise"></i><span class="d-none d-lg-inline"> Refresh</span>
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="btn-delete-selected" disabled>
                        <i class="bi bi-trash-fill"></i><span class="d-none d-lg-inline"> Delete</span>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="example-table" class="table table-bordered table-striped table-hover align-middle" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:2.5rem;">
                                <input type="checkbox" class="form-check-input" id="select-all" title="Select all">
                            </th>
                            <th style="width:3.5rem;">Icon</th>
                            <th>Title</th>
                            <th>Body</th>
                            <th>Call to Action</th>
                            <th>Date</th>
                            <th class="text-center">Read</th>
                            <th class="text-center">Cleared</th>
                            <th style="width:6rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="delete-modal-message" class="mb-0">Are you sure you want to delete this notification?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel"><i class="bi bi-pencil-fill me-2"></i>Edit Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="edit-modal-error" class="alert alert-danger d-none" role="alert"></div>
                <input type="hidden" id="edit-uuid">
                <div class="mb-3">
                    <label for="edit-title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="edit-title" maxlength="255">
                </div>
                <div class="mb-3">
                    <label for="edit-body" class="form-label">Body</label>
                    <textarea class="form-control" id="edit-body" rows="5"></textarea>
                </div>
                <div class="mb-3">
                    <label for="edit-url" class="form-label">URL</label>
                    <input type="url" class="form-control" id="edit-url" maxlength="255">
                </div>
                <div class="mb-3">
                    <label for="edit-calltoaction" class="form-label">Call to Action</label>
                    <input type="text" class="form-control" id="edit-calltoaction" maxlength="255">
                </div>
                <div class="mb-0" id="edit-status-fields">
                    <label class="form-label">Status</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit-is-read">
                            <label class="form-check-label" for="edit-is-read">Read</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="edit-is-cleared">
                            <label class="form-check-label" for="edit-is-cleared">Cleared</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-edit">Save changes</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>