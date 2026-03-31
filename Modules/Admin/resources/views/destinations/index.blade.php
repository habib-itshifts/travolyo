<x-admin::layouts.master title="Destinations">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Destinations</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Top Destinations</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage the landing page destination cards from admin.</p>
        </div>
        <button type="button" class="btn btn-sm text-white" id="add-destination-btn" style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            Add Destination
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Total</p><h4 class="fw-bold mb-0" data-stat="total">{{ $stats['total'] }}</h4></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Active</p><h4 class="fw-bold mb-0 text-success" data-stat="active">{{ $stats['active'] }}</h4></div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Inactive</p><h4 class="fw-bold mb-0 text-secondary" data-stat="inactive">{{ $stats['inactive'] }}</h4></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-semibold mb-1">Destination List</h6>
                    <p class="text-muted mb-0" style="font-size:12px;">These items render in the website Top Destinations slider.</p>
                </div>
                <div style="max-width:320px;width:100%;">
                    <input type="text" class="form-control form-control-sm" id="destination-search" placeholder="Search destinations...">
                </div>
            </div>

            <div id="destination-feedback" class="alert d-none py-2" style="font-size:12px;"></div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>Country Code</th>
                            <th>Location</th>
                            <th>Accommodations</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="destinations-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="destinationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="destination-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="destination-modal-title">Add Destination</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="destination-form-errors" class="alert alert-danger d-none py-2" style="font-size:12px;"></div>
                        <input type="hidden" id="destination-id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="destination-city" maxlength="120" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" class="form-control" id="destination-country" maxlength="120" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Country Code</label>
                                <input type="text" class="form-control" id="destination-country-code" maxlength="10" placeholder="AE">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" id="destination-location" maxlength="20" placeholder="DXB">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sort Order</label>
                                <input type="number" min="0" class="form-control" id="destination-sort-order" value="0">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold" style="font-size:13px;">Image</label>
                                <p class="text-muted mb-2" style="font-size:12px;">Pick a destination cover image from media library.</p>
                                <input type="hidden" id="destination-image-path" required>
                                <div class="media-picker-card destination-media-picker" data-open-media-browser data-media-target="destination-image" data-media-multiple="false">
                                    <div class="media-picker-preview" id="destination-media-picker-surface">
                                        <img id="destination-image-preview" src="" alt="Preview" style="width:100%;max-height:180px;object-fit:cover;border-radius:14px;display:none;">
                                        <div id="destination-image-preview-empty" class="media-picker-empty">
                                            <div class="media-picker-icon">&#128247;</div>
                                            <span class="btn btn-primary btn-sm">Choose Image</span>
                                            <small class="text-muted d-block mt-2">Select from media browser</small>
                                        </div>
                                    </div>
                                    <div class="destination-media-meta">
                                        <span class="destination-media-label">Selected file</span>
                                        <input type="text" class="form-control destination-media-path" id="destination-image-path-display" placeholder="No image selected yet" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Image Alt</label>
                                <input type="text" class="form-control" id="destination-image-alt" maxlength="255" placeholder="Dubai skyline">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Accommodations Label</label>
                                <input type="text" class="form-control" id="destination-accommodations-label" maxlength="120" placeholder="19,464 accommodations" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="destination-is-active" checked>
                                    <label class="form-check-label" for="destination-is-active">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="destination-submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (() => {
            const items = @json($destinations->values());
            const stats = @json($stats);
            const storeUrl = @json(route('admin.destinations.store'));
            const updateUrlTemplate = @json(route('admin.destinations.update', ['destination' => '__ID__']));
            const deleteUrlTemplate = @json(route('admin.destinations.destroy', ['destination' => '__ID__']));

            const tableBody = document.getElementById('destinations-table-body');
            const feedback = document.getElementById('destination-feedback');
            const searchInput = document.getElementById('destination-search');
            const modalElement = document.getElementById('destinationModal');
            const modal = new bootstrap.Modal(modalElement);
            const form = document.getElementById('destination-form');
            const formErrors = document.getElementById('destination-form-errors');
            const modalTitle = document.getElementById('destination-modal-title');
            const submitButton = document.getElementById('destination-submit-btn');
            const preview = document.getElementById('destination-image-preview');
            const previewEmpty = document.getElementById('destination-image-preview-empty');
            const imagePathInput = document.getElementById('destination-image-path');
            const imagePathDisplay = document.getElementById('destination-image-path-display');
            let destinationMediaPickerOpen = false;

            const state = {
                items: items.slice(),
                stats: { ...stats },
                editingId: null,
                search: '',
            };

            function csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            }

            function normalizeImage(path) {
                const value = String(path || '').trim();
                if (!value) return '';
                if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:')) return value;
                const cleaned = value.replace(/^\/+/, '');

                if (cleaned.startsWith('assets/') || cleaned.startsWith('uploads/')) {
                    return `${window.location.origin}/${cleaned}`;
                }

                return `${window.location.origin}/uploads/${cleaned}`;
            }

            function showFeedback(message, type = 'success') {
                feedback.className = `alert alert-${type} py-2`;
                feedback.textContent = message;
                feedback.classList.remove('d-none');
            }

            function renderStats() {
                Object.entries(state.stats).forEach(([key, value]) => {
                    const node = document.querySelector(`[data-stat="${key}"]`);
                    if (node) node.textContent = value;
                });
            }

            function filteredItems() {
                const q = state.search.trim().toLowerCase();
                if (!q) return state.items;

                return state.items.filter((item) => [item.city, item.country, item.country_code, item.location, item.accommodations_label]
                    .some((value) => String(value || '').toLowerCase().includes(q)));
            }

            function rowHtml(item) {
                const status = item.is_active
                    ? '<span class="badge bg-success-subtle text-success">Active</span>'
                    : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>';

                return `
                    <tr data-id="${item.id}">
                        <td>${item.id}</td>
                        <td><img src="${item.image_url}" alt="${item.image_alt || item.city || ''}" style="width:68px;height:48px;object-fit:cover;border-radius:10px;"></td>
                        <td class="fw-semibold text-dark">${item.city || ''}</td>
                        <td>${item.country || ''}</td>
                        <td>${item.country_code || '-'}</td>
                        <td>${item.location || '-'}</td>
                        <td>${item.accommodations_label || ''}</td>
                        <td>${item.sort_order ?? 0}</td>
                        <td>${status}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-action="edit" data-id="${item.id}">Edit</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${item.id}">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
            }

            function renderTable() {
                const rows = filteredItems();
                tableBody.innerHTML = rows.length
                    ? rows.map(rowHtml).join('')
                    : '<tr><td colspan="10" class="text-center text-muted py-4">No destinations found.</td></tr>';
            }

            function updatePreview(path, alt = '') {
                const rawPath = String(path || '').trim();
                const src = normalizeImage(rawPath);

                if (imagePathDisplay) {
                    imagePathDisplay.value = rawPath;
                }

                if (!src) {
                    preview.style.display = 'none';
                    preview.removeAttribute('src');
                    previewEmpty.classList.remove('d-none');
                    return;
                }

                preview.src = src;
                preview.alt = alt || 'Preview';
                preview.style.display = 'block';
                previewEmpty.classList.add('d-none');
            }

            function resetForm() {
                state.editingId = null;
                form.reset();
                document.getElementById('destination-id').value = '';
                document.getElementById('destination-sort-order').value = '0';
                document.getElementById('destination-is-active').checked = true;
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                modalTitle.textContent = 'Add Destination';
                submitButton.textContent = 'Create';
                updatePreview('', '');
            }

            function fillForm(item) {
                state.editingId = item.id;
                document.getElementById('destination-id').value = item.id;
                document.getElementById('destination-city').value = item.city || '';
                document.getElementById('destination-country').value = item.country || '';
                document.getElementById('destination-country-code').value = item.country_code || '';
                document.getElementById('destination-location').value = item.location || '';
                document.getElementById('destination-image-path').value = item.image_path || '';
                document.getElementById('destination-image-alt').value = item.image_alt || '';
                document.getElementById('destination-accommodations-label').value = item.accommodations_label || '';
                document.getElementById('destination-sort-order').value = item.sort_order ?? 0;
                document.getElementById('destination-is-active').checked = Boolean(item.is_active);
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                modalTitle.textContent = 'Edit Destination';
                submitButton.textContent = 'Update';
                updatePreview(item.image_path || item.image_url || '', item.image_alt || item.city || '');
            }

            function formPayload() {
                return {
                    city: document.getElementById('destination-city').value.trim(),
                    country: document.getElementById('destination-country').value.trim(),
                    country_code: document.getElementById('destination-country-code').value.trim(),
                    location: document.getElementById('destination-location').value.trim(),
                    image_path: document.getElementById('destination-image-path').value.trim(),
                    image_alt: document.getElementById('destination-image-alt').value.trim(),
                    accommodations_label: document.getElementById('destination-accommodations-label').value.trim(),
                    sort_order: document.getElementById('destination-sort-order').value || '0',
                    is_active: document.getElementById('destination-is-active').checked ? '1' : '0',
                };
            }

            function selectedModalFiles() {
                return Array.from(document.querySelectorAll('#media-browser-grid [data-media-file]'))
                    .filter((item) => item.querySelector('.media-browser-card')?.classList.contains('active'))
                    .map((item) => ({
                        path: item.dataset.mediaFile || '',
                        url: item.querySelector('img')?.src || '',
                    }))
                    .filter((file) => file.path && file.url);
            }

            async function sendRequest(url, method, payload) {
                const body = new URLSearchParams(payload);

                if (method !== 'POST') {
                    body.append('_method', method);
                }

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    },
                    body: body.toString(),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw data;
                }

                return data;
            }

            function setFormErrors(error) {
                const messages = Object.values(error.errors || {}).flat();
                formErrors.innerHTML = messages.length
                    ? `<ul class="mb-0 ps-3">${messages.map((message) => `<li>${message}</li>`).join('')}</ul>`
                    : '<div>Something went wrong. Please try again.</div>';
                formErrors.classList.remove('d-none');
            }

            searchInput.addEventListener('input', (event) => {
                state.search = event.target.value || '';
                renderTable();
            });

            document.getElementById('add-destination-btn').addEventListener('click', () => {
                resetForm();
                modal.show();
            });

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-open-media-browser][data-media-target="destination-image"]');
                if (trigger) {
                    destinationMediaPickerOpen = true;
                    return;
                }

                if (event.target.closest('#media-use-selected-btn') && destinationMediaPickerOpen) {
                    const file = selectedModalFiles()[0];
                    imagePathInput.value = file?.path || '';
                    updatePreview(file?.path || '', document.getElementById('destination-image-alt').value);
                }
            });

            imagePathInput.addEventListener('input', () => {
                updatePreview(imagePathInput.value, document.getElementById('destination-image-alt').value);
            });

            document.getElementById('destination-image-alt').addEventListener('input', (event) => {
                if (preview.style.display !== 'none') {
                    preview.alt = event.target.value || 'Preview';
                }
            });

            tableBody.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-action]');
                if (!button) return;

                const item = state.items.find((row) => row.id === Number(button.dataset.id));
                if (!item) return;

                if (button.dataset.action === 'edit') {
                    fillForm(item);
                    modal.show();
                    return;
                }

                if (!confirm(`Delete destination "${item.city}"?`)) return;

                try {
                    const result = await sendRequest(deleteUrlTemplate.replace('__ID__', item.id), 'DELETE', {});
                    state.items = state.items.filter((row) => row.id !== item.id);
                    state.stats = result.stats || state.stats;
                    renderStats();
                    renderTable();
                    showFeedback(result.message || 'Destination deleted successfully.');
                } catch (error) {
                    showFeedback(error.message || 'Unable to delete destination.', 'danger');
                }
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';

                const payload = formPayload();
                const isEditing = Boolean(state.editingId);
                const url = isEditing
                    ? updateUrlTemplate.replace('__ID__', state.editingId)
                    : storeUrl;

                submitButton.disabled = true;
                submitButton.textContent = isEditing ? 'Updating...' : 'Creating...';

                try {
                    const result = await sendRequest(url, isEditing ? 'PUT' : 'POST', payload);
                    const item = result.item;

                    if (isEditing) {
                        state.items = state.items.map((row) => row.id === item.id ? item : row);
                    } else {
                        state.items.unshift(item);
                    }

                    state.items.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0) || String(a.city || '').localeCompare(String(b.city || '')));
                    state.stats = result.stats || state.stats;
                    renderStats();
                    renderTable();
                    showFeedback(result.message || 'Destination saved successfully.');
                    modal.hide();
                    resetForm();
                } catch (error) {
                    setFormErrors(error);
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = state.editingId ? 'Update' : 'Create';
                }
            });

            modalElement.addEventListener('hidden.bs.modal', resetForm);
            document.getElementById('mediaBrowserModal')?.addEventListener('hidden.bs.modal', () => {
                destinationMediaPickerOpen = false;
            });

            renderStats();
            renderTable();
            resetForm();
        })();
    </script>
    @endpush

    @push('styles')
    <style>
        .media-picker-card {
            border: 1px solid #dbe3ef;
            border-radius: 12px;
            background: #f8fafc;
            min-height: 210px;
            padding: 18px;
            cursor: pointer;
            transition: .2s ease;
        }
        .media-picker-card:hover {
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(58, 181, 212, 0.12);
        }
        .media-picker-preview {
            min-height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .media-picker-preview img {
            max-width: 100%;
            max-height: 170px;
            object-fit: cover;
        }
        .media-picker-empty {
            text-align: center;
        }
        .media-picker-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(58, 181, 212, 0.12);
            color: var(--clr-primary);
            font-size: 24px;
            margin-bottom: 12px;
        }
        .destination-media-picker {
            min-height: 0;
        }
        .destination-media-meta {
            border-top: 1px solid #e5edf5;
            margin-top: 14px;
            padding-top: 14px;
        }
        .destination-media-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #7b8794;
            margin-bottom: 8px;
        }
        .destination-media-path {
            background: #fff;
            border-radius: 10px;
            font-size: 13px;
        }
    </style>
    @endpush

    @include('admin::media.partials.browser-modal')
</x-admin::layouts.master>
