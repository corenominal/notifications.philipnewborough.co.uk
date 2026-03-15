document.addEventListener("DOMContentLoaded", function () {

    // Mark sidebar link as active
    document.querySelectorAll("#sidebar .nav-link").forEach(function (link) {
        if (link.getAttribute("href") === "/admin") {
            link.classList.remove("text-white-50");
            link.classList.add("active");
        }
    });

    // ── Helpers (mirrored from shared/notifications.js) ────────────────────────

    function escapeHtml(str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function sanitizeUrl(url) {
        if (!url) return '';
        try {
            const parsed = new URL(url, window.location.origin);
            return ['http:', 'https:'].includes(parsed.protocol) ? parsed.href : '';
        } catch {
            return '';
        }
    }

    // ── Elements ───────────────────────────────────────────────────────────────

    const titleEl       = document.getElementById('field-title');
    const bodyEl        = document.getElementById('field-body');
    const urlEl         = document.getElementById('field-url');
    const iconEl        = document.getElementById('field-icon');
    const ctaEl         = document.getElementById('field-calltoaction');
    const userUuidEl    = document.getElementById('field-user-uuid');
    const previewList   = document.getElementById('notification-preview-list');
    const form          = document.getElementById('create-form');
    const submitBtn     = document.getElementById('btn-submit');
    const alertEl       = document.getElementById('create-alert');

    // ── LocalStorage persistence ───────────────────────────────────────────────

    const LS_KEY = 'admin_create_notification';

    const storageFields = [
        { el: titleEl,    key: 'title' },
        { el: bodyEl,     key: 'body' },
        { el: urlEl,      key: 'url' },
        { el: iconEl,     key: 'icon' },
        { el: ctaEl,      key: 'calltoaction' },
        { el: userUuidEl, key: 'user_uuid' },
    ];

    function saveToStorage() {
        const values = {};
        storageFields.forEach(function (item) {
            values[item.key] = item.el.value;
        });
        try {
            localStorage.setItem(LS_KEY, JSON.stringify(values));
        } catch (_) {}
    }

    function loadFromStorage() {
        try {
            const raw = localStorage.getItem(LS_KEY);
            if (!raw) return;
            const values = JSON.parse(raw);
            storageFields.forEach(function (item) {
                if (values[item.key] !== undefined) {
                    item.el.value = values[item.key];
                }
            });
        } catch (_) {}
    }

    // Populate from storage on page load, then build initial preview
    loadFromStorage();

    // Persist on every change
    storageFields.forEach(function (item) {
        item.el.addEventListener('input', saveToStorage);
    });

    // ── Live preview ───────────────────────────────────────────────────────────

    function buildPreview() {
        const title        = titleEl.value.trim();
        const body         = bodyEl.value.trim();
        const url          = urlEl.value.trim();
        const icon         = iconEl.value.trim();
        const calltoaction = ctaEl.value.trim() || 'More info';

        const safeIcon = sanitizeUrl(icon);
        const safeUrl  = sanitizeUrl(url);

        const iconHtml = safeIcon
            ? `<img src="${escapeHtml(safeIcon)}" alt="" class="notification-icon rounded" width="40" height="40" loading="lazy">`
            : `<div class="notification-icon notification-icon-placeholder rounded d-flex align-items-center justify-content-center bg-secondary-subtle text-secondary" aria-hidden="true">
                 <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                   <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.37 1.566-.659 2.258h10.236c-.29-.692-.5-1.49-.66-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                 </svg>
               </div>`;

        const ctaHtml = safeUrl
            ? `<a href="${escapeHtml(safeUrl)}"
                  class="btn btn-sm btn-outline-primary mt-2 position-relative"
                  target="_blank" rel="noopener noreferrer">
                 ${escapeHtml(calltoaction)}
               </a>`
            : '';

        previewList.innerHTML = `
            <li class="list-group-item list-group-item-action px-3 py-3 position-relative notification-item-unread">
                <button type="button" class="btn-close notification-clear-btn" aria-label="Dismiss" tabindex="-1"></button>
                <div class="d-flex gap-3 align-items-start">
                    ${iconHtml}
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="mb-1">
                            <span class="fw-semibold text-white">
                                ${safeUrl
                                    ? `<a href="${escapeHtml(safeUrl)}" class="stretched-link text-reset me-2 text-decoration-none" target="_blank" rel="noopener noreferrer">${escapeHtml(title || '(No title)')}</a>`
                                    : escapeHtml(title || '(No title)')}
                            </span>
                            <br><small class="text-muted text-nowrap">just now</small>
                        </div>
                        ${body ? `<p class="mb-0 small text-white">${escapeHtml(body)}</p>` : ''}
                        ${ctaHtml}
                    </div>
                </div>
            </li>`;
    }

    // Build preview on load (after storage populate) and on every input change
    buildPreview();
    [titleEl, bodyEl, urlEl, iconEl, ctaEl].forEach(function (el) {
        el.addEventListener('input', buildPreview);
    });

    // ── Form submission ────────────────────────────────────────────────────────

    function showAlert(html, type) {
        alertEl.className = 'alert alert-' + type;
        alertEl.innerHTML = html;
        alertEl.classList.remove('d-none');
        alertEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // (toasts removed) fall back to inline alert for global messages

    // Upload-specific error (shows below the upload area)
    function showUploadError(message) {
        const el = document.getElementById('icon-upload-error');
        if (el) {
            el.innerHTML = message;
            el.classList.remove('d-none');
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            showAlert(message, 'danger');
        }
    }

    function clearUploadError() {
        const el = document.getElementById('icon-upload-error');
        if (el) {
            el.innerHTML = '';
            el.classList.add('d-none');
        }
    }

    function clearFieldErrors() {
        form.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(function (el) {
            el.textContent = '';
        });
    }

    const fieldIdMap = {
        title:        'field-title',
        body:         'field-body',
        url:          'field-url',
        icon:         'field-icon',
        calltoaction: 'field-calltoaction',
        user_uuid:    'field-user-uuid',
    };

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearFieldErrors();
        alertEl.classList.add('d-none');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving\u2026';

        fetch('/admin/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                title:        titleEl.value.trim(),
                body:         bodyEl.value.trim(),
                url:          urlEl.value.trim(),
                icon:         iconEl.value.trim(),
                calltoaction: ctaEl.value.trim(),
                user_uuid:    userUuidEl.value.trim(),
            }),
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, data: data };
            });
        })
        .then(function (result) {
            if (result.ok && result.data.success) {
                showAlert(
                    '<i class="bi bi-check-circle-fill me-2"></i>Notification created successfully. <a href="/admin" class="alert-link">View all notifications</a>',
                    'success'
                );
            } else if (result.data.errors) {
                Object.entries(result.data.errors).forEach(function (entry) {
                    const field    = entry[0];
                    const message  = entry[1];
                    const inputEl    = document.getElementById(fieldIdMap[field]);
                    const feedbackEl = document.getElementById(fieldIdMap[field] + '-feedback');
                    if (inputEl)    inputEl.classList.add('is-invalid');
                    if (feedbackEl) feedbackEl.textContent = message;
                });
                showAlert(
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the errors highlighted below.',
                    'danger'
                );
            } else {
                showAlert(
                    '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (result.data.message || 'An unexpected error occurred.'),
                    'danger'
                );
            }
        })
        .catch(function () {
            showAlert(
                '<i class="bi bi-exclamation-triangle-fill me-2"></i>A network error occurred. Please try again.',
                'danger'
            );
        })
        .finally(function () {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-plus-circle-fill me-1"></i> Create Notification';
        });
    });

    // ── Recipient select modal ────────────────────────────────────────────────
    const selectRecipientBtn = document.getElementById('btn-select-recipient');
    const recipientModalEl   = document.getElementById('recipient-select-modal');
    if (selectRecipientBtn && recipientModalEl) {
        const recipientModal = new bootstrap.Modal(recipientModalEl);
        const searchInput    = recipientModalEl.querySelector('#recipient-search-input');
        const resultsEl      = recipientModalEl.querySelector('#recipient-search-results');
        let   searchTimer    = null;

        function renderRecipientResults(users) {
            if (!Array.isArray(users) || users.length === 0) {
                resultsEl.innerHTML = '<p class="text-muted mb-0">No users found.</p>';
                return;
            }
            const list = document.createElement('ul');
            list.className = 'list-group';
            users.forEach(function (user) {
                const item = document.createElement('li');
                item.className = 'list-group-item list-group-item-action';
                item.style.cursor = 'pointer';
                item.innerHTML =
                    '<div class="fw-semibold">' + escapeHtml(user.username) + '</div>' +
                    '<div class="small text-muted">' + escapeHtml(user.email) + '</div>';
                item.addEventListener('click', function () {
                    userUuidEl.value = user.uuid;
                    userUuidEl.dispatchEvent(new Event('input'));
                    saveToStorage();
                    recipientModal.hide();
                });
                list.appendChild(item);
            });
            resultsEl.innerHTML = '';
            resultsEl.appendChild(list);
        }

        function doRecipientSearch(q) {
            resultsEl.innerHTML =
                '<div class="d-flex align-items-center gap-2 text-muted">' +
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Searching…</div>';
            fetch('/admin/create/users-search?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(function (r) {
                if (!r.ok) throw new Error('server error');
                return r.json();
            })
            .then(function (data) {
                renderRecipientResults(Array.isArray(data) ? data : []);
            })
            .catch(function () {
                resultsEl.innerHTML =
                    '<p class="text-danger mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Failed to load users. Please try again.</p>';
            });
        }

        recipientModalEl.addEventListener('shown.bs.modal', function () {
            searchInput.focus();
            if (searchInput.value.trim()) {
                doRecipientSearch(searchInput.value.trim());
            }
        });

        recipientModalEl.addEventListener('hidden.bs.modal', function () {
            clearTimeout(searchTimer);
        });

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            const q = searchInput.value.trim();
            if (q.length === 0) {
                resultsEl.innerHTML = '';
                return;
            }
            searchTimer = setTimeout(function () {
                doRecipientSearch(q);
            }, 400);
        });

        selectRecipientBtn.addEventListener('click', function () {
            recipientModal.show();
        });
    }

    // ── Icon select modal / upload ─────────────────────────────────────────
    const selectIconBtn = document.getElementById('btn-select-icon');
    const iconModalEl = document.getElementById('icon-select-modal');
    if (selectIconBtn && iconModalEl) {
        const iconModal = new bootstrap.Modal(iconModalEl);
        const dropArea = iconModalEl.querySelector('#icon-drop-area');
        const fileInput = iconModalEl.querySelector('#icon-upload-input');
        const browseBtn = iconModalEl.querySelector('#icon-upload-browse');
        const progressWrap = iconModalEl.querySelector('#icon-upload-progress');
        const progressBar = progressWrap ? progressWrap.querySelector('.progress-bar') : null;
        const iconGrid = iconModalEl.querySelector('#icon-grid');

        function setProgress(pct) {
            if (!progressBar) return;
            progressBar.style.width = pct + '%';
            progressBar.textContent = pct + '%';
            if (pct === 0) {
                progressWrap.classList.add('d-none');
            } else {
                progressWrap.classList.remove('d-none');
            }
        }

        function loadIcons() {
            fetch('/admin/create/icons', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(function (data) {
                    if (!data.success) return;
                    iconGrid.innerHTML = '';
                    data.icons.forEach(function (url) {
                        const col = document.createElement('div');
                        col.className = 'col-3 col-sm-2';
                        col.innerHTML = `
                            <div class="card p-1 h-100" style="cursor:pointer;">
                                <img src="${escapeHtml(url)}" alt="icon" class="img-fluid selectable-icon" data-url="${escapeHtml(url)}">
                            </div>`;
                        iconGrid.appendChild(col);
                    });
                    // attach click handlers
                    iconGrid.querySelectorAll('.selectable-icon').forEach(function (img) {
                        img.addEventListener('click', function () {
                            const url = this.dataset.url;
                            iconEl.value = url;
                            iconEl.dispatchEvent(new Event('input'));
                            saveToStorage();
                            iconModal.hide();
                        });
                    });
                })
                .catch(function () { /* ignore */ });
        }

        selectIconBtn.addEventListener('click', function () {
            loadIcons();
            iconModal.show();
        });

        // drag & drop and browse
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (evt) {
            dropArea.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        dropArea.addEventListener('dragover', function () {
            dropArea.classList.add('border-primary');
        });
        dropArea.addEventListener('dragleave', function () {
            dropArea.classList.remove('border-primary');
        });

        dropArea.addEventListener('drop', function (e) {
            dropArea.classList.remove('border-primary');
            const files = e.dataTransfer.files;
            if (files && files.length) handleFile(files[0]);
        });

        browseBtn.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            if (fileInput.files && fileInput.files[0]) handleFile(fileInput.files[0]);
        });

        function handleFile(file) {
            // basic client-side validation
            clearUploadError();
            if (file.type !== 'image/png') {
                showUploadError('<i class="bi bi-exclamation-triangle-fill me-2"></i>Only PNG images are allowed.');
                return;
            }
            // upload with progress
            const form = new FormData();
            form.append('icon', file, file.name);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/admin/create/icon-upload');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    setProgress(pct);
                }
            };

            xhr.onloadstart = function () { setProgress(0); };
            xhr.onload = function () {
                setProgress(100);
                // If non-2xx, try to show server message
                if (xhr.status < 200 || xhr.status >= 300) {
                    let msg = 'Upload failed (server error).';
                    try {
                        const parsed = JSON.parse(xhr.responseText || '{}');
                        if (parsed && parsed.message) msg = parsed.message;
                    } catch (e) {
                        // ignore parse errors
                    }
                    showUploadError('<i class="bi bi-exclamation-triangle-fill me-2"></i>' + msg);
                    setTimeout(function () { setProgress(0); }, 800);
                    return;
                }

                try {
                    const res = JSON.parse(xhr.responseText || '{}');
                    if (res.success && res.url) {
                        // add to grid and select
                        const url = res.url;
                        const col = document.createElement('div');
                        col.className = 'col-3 col-sm-2';
                        col.innerHTML = `
                            <div class="card p-1 h-100" style="cursor:pointer;">
                                <img src="${escapeHtml(url)}" alt="icon" class="img-fluid selectable-icon" data-url="${escapeHtml(url)}">
                            </div>`;
                        // insert at start
                        iconGrid.insertBefore(col, iconGrid.firstChild);
                        col.querySelector('.selectable-icon').addEventListener('click', function () {
                            iconEl.value = this.dataset.url;
                            iconEl.dispatchEvent(new Event('input'));
                            saveToStorage();
                            iconModal.hide();
                        });
                        // auto-select
                        iconEl.value = url;
                        iconEl.dispatchEvent(new Event('input'));
                        saveToStorage();
                        iconModal.hide();
                    } else {
                        showUploadError('<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (res.message || 'Upload failed.'));
                    }
                } catch (err) {
                    showUploadError('<i class="bi bi-exclamation-triangle-fill me-2"></i>Unexpected server response.');
                }
                setTimeout(function () { setProgress(0); }, 800);
            };

            xhr.onerror = function () {
                showUploadError('<i class="bi bi-exclamation-triangle-fill me-2"></i>Upload failed.');
                setProgress(0);
            };

            xhr.send(form);
        }
    }

});
