<x-admin::layouts.master title="Currencies">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb" style="font-size:12px;">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Currencies</li>
                </ol>
            </nav>
            <h5 class="fw-bold mb-0 text-dark">Currencies</h5>
            <p class="text-muted mb-0" style="font-size:13px;">Manage all active currencies with AJAX CRUD.</p>
        </div>
        <button type="button" class="btn btn-sm text-white" id="add-currency-btn" style="background:var(--clr-primary);border-radius:var(--radius-btn);">
            Add Currency
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Total</p><h4 class="fw-bold mb-0" data-stat="total">{{ $stats['total'] }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Active</p><h4 class="fw-bold mb-0 text-success" data-stat="active">{{ $stats['active'] }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Default</p><h4 class="fw-bold mb-0 text-primary" data-stat="default">{{ $stats['default'] }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100"><div class="card-body"><p class="text-muted text-uppercase mb-1" style="font-size:11px;letter-spacing:.08em;">Inactive</p><h4 class="fw-bold mb-0 text-secondary" data-stat="inactive">{{ $stats['inactive'] }}</h4></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-semibold mb-1">Currencies List</h6>
                    <p class="text-muted mb-0" style="font-size:12px;">All currencies appear in one list.</p>
                </div>
                <div style="max-width:320px;width:100%;">
                    <input type="text" class="form-control form-control-sm" id="currency-search" placeholder="Search currencies...">
                </div>
            </div>

            <div id="currency-feedback" class="alert d-none py-2" style="font-size:12px;"></div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Label</th>
                            <th>Symbol</th>
                            <th>Flag</th>
                            <th>Sort</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="currencies-table-body"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="currencyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="currency-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="currency-modal-title">Add Currency</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="currency-form-errors" class="alert alert-danger d-none py-2" style="font-size:12px;"></div>
                        <input type="hidden" id="currency-id">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Code</label>
                                <input type="text" class="form-control" id="currency-code" maxlength="3" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Label</label>
                                <input type="text" class="form-control" id="currency-label" maxlength="10" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Symbol</label>
                                <input type="text" class="form-control" id="currency-symbol" maxlength="20" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="currency-name" maxlength="100" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Flag</label>
                                <input type="text" class="form-control" id="currency-flag" maxlength="5" placeholder="us" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" min="0" class="form-control" id="currency-sort-order" value="0">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch me-4">
                                    <input class="form-check-input" type="checkbox" id="currency-is-active" checked>
                                    <label class="form-check-label" for="currency-is-active">Active</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="currency-is-default">
                                    <label class="form-check-label" for="currency-is-default">Default</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="currency-submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (() => {
            const items = @json($currencies->values());
            const stats = @json($stats);
            const storeUrl = @json(route('admin.currencies.store'));
            const updateUrlTemplate = @json(route('admin.currencies.update', ['currency' => '__ID__']));
            const deleteUrlTemplate = @json(route('admin.currencies.destroy', ['currency' => '__ID__']));

            const tableBody = document.getElementById('currencies-table-body');
            const feedback = document.getElementById('currency-feedback');
            const searchInput = document.getElementById('currency-search');
            const modalElement = document.getElementById('currencyModal');
            const modal = new bootstrap.Modal(modalElement);
            const form = document.getElementById('currency-form');
            const formErrors = document.getElementById('currency-form-errors');
            const modalTitle = document.getElementById('currency-modal-title');
            const submitButton = document.getElementById('currency-submit-btn');

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
                    return [item.code, item.label, item.symbol, item.flag, item.name]
                        .some((value) => String(value || '').toLowerCase().includes(q));
                });
            }

            function badgeStatus(item) {
                return item.is_active
                    ? '<span class="badge bg-success-subtle text-success">Active</span>'
                    : '<span class="badge bg-secondary-subtle text-secondary">Inactive</span>';
            }

            function badgeDefault(item) {
                return item.is_default
                    ? '<span class="badge bg-primary-subtle text-primary">Default</span>'
                    : '<span class="text-muted">-</span>';
            }

            function rowHtml(item) {
                return `
                    <tr data-id="${item.id}">
                        <td>${item.id}</td>
                        <td class="fw-semibold text-dark">${item.code || ''}</td>
                        <td>${item.name || ''}</td>
                        <td>${item.label || ''}</td>
                        <td>${item.symbol || ''}</td>
                        <td><span class="text-uppercase">${item.flag || ''}</span></td>
                        <td>${item.sort_order ?? 0}</td>
                        <td>${badgeStatus(item)}</td>
                        <td>${badgeDefault(item)}</td>
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
                    : '<tr><td colspan="10" class="text-center text-muted py-4">No currencies found.</td></tr>';
            }

            function resetForm() {
                state.editingId = null;
                form.reset();
                document.getElementById('currency-id').value = '';
                document.getElementById('currency-sort-order').value = '0';
                document.getElementById('currency-is-active').checked = true;
                document.getElementById('currency-is-default').checked = false;
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                modalTitle.textContent = 'Add Currency';
                submitButton.textContent = 'Create';
            }

            function fillForm(item) {
                state.editingId = item.id;
                document.getElementById('currency-id').value = item.id;
                document.getElementById('currency-code').value = item.code || '';
                document.getElementById('currency-label').value = item.label || '';
                document.getElementById('currency-symbol').value = item.symbol || '';
                document.getElementById('currency-name').value = item.name || '';
                document.getElementById('currency-flag').value = item.flag || '';
                document.getElementById('currency-sort-order').value = item.sort_order ?? 0;
                document.getElementById('currency-is-active').checked = Boolean(item.is_active);
                document.getElementById('currency-is-default').checked = Boolean(item.is_default);
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                modalTitle.textContent = 'Edit Currency';
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

                if (!response.ok) throw data;
                return data;
            }

            function syncItem(item) {
                const index = state.items.findIndex((row) => row.id === item.id);
                if (index >= 0) {
                    state.items[index] = item;
                } else {
                    state.items.unshift(item);
                }
                state.items.sort((a, b) => Number(b.is_default) - Number(a.is_default) || (a.sort_order ?? 0) - (b.sort_order ?? 0) || String(a.code).localeCompare(String(b.code)));
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

            document.getElementById('add-currency-btn')?.addEventListener('click', () => {
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
                payload.append('code', document.getElementById('currency-code').value);
                payload.append('label', document.getElementById('currency-label').value);
                payload.append('symbol', document.getElementById('currency-symbol').value);
                payload.append('name', document.getElementById('currency-name').value);
                payload.append('flag', document.getElementById('currency-flag').value);
                payload.append('sort_order', document.getElementById('currency-sort-order').value);
                payload.append('is_active', document.getElementById('currency-is-active').checked ? '1' : '0');
                payload.append('is_default', document.getElementById('currency-is-default').checked ? '1' : '0');

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
                    showFeedback(data.message || 'Currency saved successfully.');
                } catch (error) {
                    if (error?.errors) {
                        showValidationErrors(error.errors);
                    } else {
                        showFeedback(error?.message || 'Unable to save currency.', 'danger');
                    }
                }
            });

            tableBody?.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-action]');
                if (!button) return;

                const id = Number(button.dataset.id || 0);
                const item = state.items.find((row) => row.id === id);
                if (!item) return;

                if (button.dataset.action === 'edit') {
                    fillForm(item);
                    modal.show();
                    return;
                }

                if (button.dataset.action === 'delete') {
                    if (!window.confirm(`Delete currency "${item.code}"?`)) return;

                    const payload = new FormData();
                    payload.append('_method', 'DELETE');

                    try {
                        const data = await sendRequest(deleteUrlTemplate.replace('__ID__', id), 'POST', payload);
                        removeItem(id);
                        applyStats(data.stats);
                        renderTable();
                        showFeedback(data.message || 'Currency deleted successfully.');
                    } catch (error) {
                        showFeedback(error?.message || 'Unable to delete currency.', 'danger');
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
