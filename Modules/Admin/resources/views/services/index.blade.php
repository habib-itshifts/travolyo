<x-admin::layouts.master title="Services">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Services</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Services</h5>
            <p class="text-muted mb-0" style="font-size:13px;">All services are listed here with AJAX-based CRUD.</p>
        </div>
        <button type="button" class="btn btn-sm text-white" id="add-service-btn" style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            Add Service
        </button>
    </div>

    <div class="row g-3 mb-4" id="service-stats">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Total</p>
                    <h4 class="fw-bold mb-0" data-stat="total">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Active</p>
                    <h4 class="fw-bold mb-0 text-success" data-stat="active">{{ $stats['active'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Free</p>
                    <h4 class="fw-bold mb-0 text-primary" data-stat="free">{{ $stats['free'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Paid</p>
                    <h4 class="fw-bold mb-0 text-warning" data-stat="paid">{{ $stats['paid'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-semibold mb-1">Services List</h6>
                    <p class="text-muted mb-0" style="font-size:12px;">All items appear in one list.</p>
                </div>
                <div style="max-width:320px;width:100%;">
                    <input type="text" class="form-control form-control-sm" id="service-search" placeholder="Search services...">
                </div>
            </div>

            <div id="service-feedback" class="alert d-none py-2" style="font-size:12px;"></div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Category</th>
                            <th>Charge</th>
                            <th>Price</th>
                            <th>Price Type</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="services-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="service-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="service-modal-title">Add Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="service-form-errors" class="alert alert-danger d-none py-2" style="font-size:12px;"></div>
                        <input type="hidden" id="service-id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="service-name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" id="service-slug" placeholder="auto-from-name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" id="service-category" value="general" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Icon</label>
                                <input type="text" class="form-control" id="service-icon" placeholder="bell-concierge">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sort Order</label>
                                <input type="number" min="0" class="form-control" id="service-sort-order" value="0">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="service-is-chargeable">
                                    <label class="form-check-label" for="service-is-chargeable">Paid Service</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="service-is-active" checked>
                                    <label class="form-check-label" for="service-is-active">Active</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price Type</label>
                                <input type="text" class="form-control" id="service-price-type" placeholder="per_request">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Default Price</label>
                                <input type="number" min="0" step="0.01" class="form-control" id="service-default-price">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="service-description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="service-submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (() => {
            const items = @json($services->values());
            const stats = @json($stats);
            const storeUrl = @json(route('admin.services.store'));
            const updateUrlTemplate = @json(route('admin.services.update', ['service' => '__ID__']));
            const deleteUrlTemplate = @json(route('admin.services.destroy', ['service' => '__ID__']));

            const tableBody = document.getElementById('services-table-body');
            const feedback = document.getElementById('service-feedback');
            const searchInput = document.getElementById('service-search');
            const modalElement = document.getElementById('serviceModal');
            const modal = new bootstrap.Modal(modalElement);
            const form = document.getElementById('service-form');
            const formErrors = document.getElementById('service-form-errors');
            const modalTitle = document.getElementById('service-modal-title');
            const submitButton = document.getElementById('service-submit-btn');

            const state = {
                items: items.slice(),
                stats: { ...stats },
                editingId: null,
                search: '',
            };

            function csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            }

            function showFeedback(message, type = 'success') {
                feedback.className = `alert alert-${type} py-2`;
                feedback.textContent = message;
                feedback.classList.remove('d-none');
            }

            function hideFeedback() {
                feedback.classList.add('d-none');
                feedback.textContent = '';
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

                return state.items.filter((item) => {
                    return [
                        item.name,
                        item.slug,
                        item.category,
                        item.icon,
                        item.description,
                        item.price_type,
                    ].some((value) => String(value || '').toLowerCase().includes(q));
                });
            }

            function priceLabel(item) {
                if (!item.is_chargeable || !item.default_price) {
                    return '<span class="text-muted">-</span>';
                }

                return `$${Number(item.default_price).toFixed(2)}`;
            }

            function rowHtml(item) {
                return `
                    <tr data-id="${item.id}">
                        <td>${item.id}</td>
                        <td class="fw-semibold text-dark">${item.name || ''}</td>
                        <td class="text-muted">${item.slug || ''}</td>
                        <td>${item.category || ''}</td>
                        <td>${item.is_chargeable ? '<span class="badge bg-warning-subtle text-warning">Paid</span>' : '<span class="badge bg-success-subtle text-success">Free</span>'}</td>
                        <td>${priceLabel(item)}</td>
                        <td>${item.price_type || '<span class="text-muted">-</span>'}</td>
                        <td>${item.sort_order ?? 0}</td>
                        <td>${item.is_active ? '<span class="badge bg-success-subtle text-success">Active</span>' : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>'}</td>
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
                    : '<tr><td colspan="10" class="text-center text-muted py-4">No services found.</td></tr>';
            }

            function resetForm() {
                state.editingId = null;
                form.reset();
                document.getElementById('service-id').value = '';
                document.getElementById('service-category').value = 'general';
                document.getElementById('service-sort-order').value = '0';
                document.getElementById('service-is-active').checked = true;
                document.getElementById('service-is-chargeable').checked = false;
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                modalTitle.textContent = 'Add Service';
                submitButton.textContent = 'Create';
            }

            function fillForm(item) {
                state.editingId = item.id;
                document.getElementById('service-id').value = item.id;
                document.getElementById('service-name').value = item.name || '';
                document.getElementById('service-slug').value = item.slug || '';
                document.getElementById('service-category').value = item.category || 'general';
                document.getElementById('service-icon').value = item.icon || '';
                document.getElementById('service-sort-order').value = item.sort_order ?? 0;
                document.getElementById('service-description').value = item.description || '';
                document.getElementById('service-price-type').value = item.price_type || '';
                document.getElementById('service-default-price').value = item.default_price ?? '';
                document.getElementById('service-is-active').checked = Boolean(item.is_active);
                document.getElementById('service-is-chargeable').checked = Boolean(item.is_chargeable);
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                modalTitle.textContent = 'Edit Service';
                submitButton.textContent = 'Update';
            }

            async function sendRequest(url, method, payload) {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken(),
                    },
                    body: payload,
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw data;
                }

                return data;
            }

            function syncItem(item) {
                const index = state.items.findIndex((row) => row.id === item.id);
                if (index >= 0) {
                    state.items[index] = item;
                } else {
                    state.items.unshift(item);
                }
            }

            function removeItem(id) {
                state.items = state.items.filter((item) => item.id !== id);
            }

            function applyStats(newStats) {
                state.stats = { ...newStats };
                renderStats();
            }

            function showValidationErrors(errors) {
                const messages = Object.values(errors || {}).flat();
                formErrors.innerHTML = messages.join('<br>');
                formErrors.classList.remove('d-none');
            }

            document.getElementById('add-service-btn')?.addEventListener('click', () => {
                resetForm();
                modal.show();
            });

            searchInput?.addEventListener('input', function () {
                state.search = this.value;
                renderTable();
            });

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                hideFeedback();
                formErrors.classList.add('d-none');

                const payload = new FormData();
                payload.append('name', document.getElementById('service-name').value);
                payload.append('slug', document.getElementById('service-slug').value);
                payload.append('category', document.getElementById('service-category').value);
                payload.append('icon', document.getElementById('service-icon').value);
                payload.append('sort_order', document.getElementById('service-sort-order').value);
                payload.append('description', document.getElementById('service-description').value);
                payload.append('price_type', document.getElementById('service-price-type').value);
                payload.append('default_price', document.getElementById('service-default-price').value);
                payload.append('is_active', document.getElementById('service-is-active').checked ? '1' : '0');
                payload.append('is_chargeable', document.getElementById('service-is-chargeable').checked ? '1' : '0');

                let url = storeUrl;
                let method = 'POST';

                if (state.editingId) {
                    url = updateUrlTemplate.replace('__ID__', state.editingId);
                    payload.append('_method', 'PUT');
                }

                try {
                    const data = await sendRequest(url, method, payload);
                    syncItem(data.item);
                    applyStats(data.stats);
                    renderTable();
                    modal.hide();
                    showFeedback(data.message);
                } catch (error) {
                    if (error.errors) {
                        showValidationErrors(error.errors);
                    } else {
                        showFeedback(error.message || 'Unable to save service.', 'danger');
                    }
                }
            });

            tableBody?.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-action]');
                if (!button) return;

                const id = Number(button.dataset.id);
                const item = state.items.find((row) => row.id === id);
                if (!item) return;

                if (button.dataset.action === 'edit') {
                    fillForm(item);
                    modal.show();
                    return;
                }

                if (button.dataset.action === 'delete') {
                    if (!window.confirm(`Delete "${item.name}"?`)) return;

                    hideFeedback();

                    const payload = new FormData();
                    payload.append('_method', 'DELETE');

                    try {
                        const data = await sendRequest(deleteUrlTemplate.replace('__ID__', id), 'POST', payload);
                        removeItem(id);
                        applyStats(data.stats);
                        renderTable();
                        showFeedback(data.message);
                    } catch (error) {
                        showFeedback(error.message || 'Unable to delete service.', 'danger');
                    }
                }
            });

            modalElement?.addEventListener('hidden.bs.modal', resetForm);

            renderStats();
            renderTable();
        })();
    </script>
    @endpush
</x-admin::layouts.master>
