<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="border-bottom border-1 mb-4 pb-4 d-flex align-items-center justify-content-between gap-3">
                <h2 class="mb-0">Create Notification</h2>
                <a href="/admin" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- Form column -->
        <div class="col-12 col-lg-6">

            <div id="create-alert" class="alert d-none" role="alert"></div>

            <form id="create-form" novalidate>
                <div class="mb-3">
                    <label for="field-title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="field-title" name="title" maxlength="255" placeholder="Notification title" required>
                    <div class="invalid-feedback" id="field-title-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="field-body" class="form-label">Body <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="field-body" name="body" rows="4" placeholder="Notification body text" required></textarea>
                    <div class="invalid-feedback" id="field-body-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="field-url" class="form-label">URL <span class="text-danger">*</span></label>
                    <input type="url" class="form-control" id="field-url" name="url" maxlength="255" placeholder="https://example.com" required>
                    <div class="invalid-feedback" id="field-url-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="field-icon" class="form-label">Icon URL <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input type="url" class="form-control" id="field-icon" name="icon" maxlength="255" placeholder="https://example.com/icon.png" required>
                        <button class="btn btn-outline-primary" type="button" id="btn-select-icon">Select</button>
                        <div class="invalid-feedback" id="field-icon-feedback"></div>
                    </div>
                    <div class="form-text">Must be a valid URL pointing to an image.</div>
                </div>

                <div class="mb-3">
                    <label for="field-calltoaction" class="form-label">Call to Action <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="field-calltoaction" name="calltoaction" maxlength="255" placeholder="More info" required>
                    <div class="form-text">Label for the action button. Defaults to "More info".</div>
                    <div class="invalid-feedback" id="field-calltoaction-feedback"></div>
                </div>

                <div class="mb-4">
                    <label for="field-user-uuid" class="form-label">Recipient <span class="text-danger">*</span></label>
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" id="field-user-uuid" name="user_uuid" maxlength="255" placeholder="everyone">
                        <button class="btn btn-outline-primary" type="button" id="btn-select-recipient">Select</button>
                        <div class="invalid-feedback" id="field-user-uuid-feedback"></div>
                    </div>
                    <div class="form-text">Enter a user UUID or <code>everyone</code> to broadcast to all users.</div>
                </div>

                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="bi bi-plus-circle-fill me-1"></i> Create Notification
                </button>
            </form>

        </div>

        <!-- Preview column -->
        <div class="col-12 col-lg-6">
            <p class="text-secondary mb-2 small text-uppercase fw-semibold">
                <i class="bi bi-eye-fill me-1"></i> Live Preview
            </p>
            <div class="card border" id="notification-preview" style="max-width: 498px;" aria-live="polite" aria-atomic="true">
                <div class="card-header d-flex align-items-center gap-2 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-body-secondary" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.37 1.566-.659 2.258h10.236c-.29-.692-.5-1.49-.66-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                    </svg>
                    <span class="mb-0 small fw-semibold">Notifications</span>
                    <span class="badge text-bg-primary ms-1">1</span>
                </div>
                <div class="card-body p-0">
                    <ul id="notification-preview-list" class="list-group list-group-flush" role="list" aria-label="Notification preview"></ul>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Recipient select modal -->
<div class="modal fade" id="recipient-select-modal" tabindex="-1" aria-hidden="true" aria-labelledby="recipientModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recipientModalLabel">Select Recipient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="recipient-search-input" class="form-label">Search by username or email</label>
                    <input type="search" class="form-control" id="recipient-search-input" placeholder="Type to search…" autocomplete="off">
                </div>
                <div id="recipient-search-results"></div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">Or type <code>everyone</code> directly in the Recipient field to broadcast to all users.</small>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Icon select modal -->
<div class="modal fade" id="icon-select-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select or upload an icon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Upload icon (PNG, square, min 256x256)</label>
                    <div id="icon-drop-area" class="border rounded p-4 text-center" style="min-height:150px; cursor: pointer;">
                        <input type="file" id="icon-upload-input" accept="image/png" class="d-none">
                        <div id="icon-drop-instructions">
                            <i class="bi bi-upload" style="font-size:24px"></i>
                            <p class="mb-0 mt-2">Drag & drop a PNG here, or <button type="button" class="btn btn-link p-0" id="icon-upload-browse">browse</button></p>
                        </div>
                        <div class="progress mt-3 d-none" id="icon-upload-progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                        <div id="icon-upload-error" class="text-danger small mt-2 d-none" role="alert"></div>
                    </div>
                </div>

                <hr>

                <div>
                    <p class="small text-muted mb-2">Previously uploaded icons</p>
                    <div id="icon-grid" class="row g-2" aria-live="polite"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<!-- Global toast container -->
<!-- removed global toast: using inline upload errors instead -->
