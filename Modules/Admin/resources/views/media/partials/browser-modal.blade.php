<div class="modal fade" id="mediaBrowserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">Media Browser</h5>
                    <p class="text-muted mb-0" style="font-size:12px;">Browse uploads, open folders, and select images.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                    <input type="text" class="form-control" id="media-search-input" placeholder="Search file name..." style="max-width:280px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="media-search-btn">Search</button>
                    <button type="button" class="btn btn-primary btn-sm" id="media-add-folder-btn">Add Folder</button>
                    <label class="btn btn-success btn-sm mb-0">
                        Upload
                        <input type="file" id="media-upload-input" multiple accept=".jpg,.jpeg,.png,.webp,.gif,.svg" hidden>
                    </label>
                    <span class="ms-auto text-muted" id="media-current-path" style="font-size:12px;">Home</span>
                </div>

                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb mb-0" id="media-breadcrumbs">
                        <li class="breadcrumb-item"><a href="#" data-media-path="">Home</a></li>
                    </ol>
                </nav>

                <div id="media-browser-feedback" class="alert alert-danger d-none py-2" style="font-size:12px;"></div>

                <div class="border rounded-3 p-3" style="min-height:420px;background:#f8fafc;">
                    <div class="row g-3" id="media-browser-grid"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="text-muted" id="media-selected-count" style="font-size:12px;">0 files selected</div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" id="media-use-selected-btn" disabled>Use Selected</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .media-browser-item {
        cursor: pointer;
    }
    .media-browser-card {
        border: 2px solid transparent;
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        transition: .2s ease;
        min-height: 170px;
    }
    .media-browser-card.active {
        border-color: var(--clr-primary);
        box-shadow: 0 0 0 3px rgba(58, 181, 212, 0.12);
    }
    .media-browser-thumb {
        height: 128px;
        background: #eef2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .media-browser-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .media-browser-thumb.folder {
        background: #7b7d7e;
        color: #fff;
        font-size: 56px;
    }
    .media-browser-name {
        padding: 10px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        if (window.mediaBrowserBooted) return;
        window.mediaBrowserBooted = true;

        const modalElement = document.getElementById('mediaBrowserModal');
        if (!modalElement) return;

        const modal = new bootstrap.Modal(modalElement);
        const grid = document.getElementById('media-browser-grid');
        const feedback = document.getElementById('media-browser-feedback');
        const currentPathLabel = document.getElementById('media-current-path');
        const breadcrumbs = document.getElementById('media-breadcrumbs');
        const selectedCount = document.getElementById('media-selected-count');
        const useSelectedButton = document.getElementById('media-use-selected-btn');
        const searchInput = document.getElementById('media-search-input');
        const uploadInput = document.getElementById('media-upload-input');

        const state = {
            currentPath: '',
            search: '',
            selectedTarget: null,
            multiple: false,
            selectedFiles: [],
        };

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        function showError(message) {
            feedback.textContent = message;
            feedback.classList.remove('d-none');
        }

        function clearError() {
            feedback.textContent = '';
            feedback.classList.add('d-none');
        }

        function updateSelectedState() {
            selectedCount.textContent = `${state.selectedFiles.length} file${state.selectedFiles.length === 1 ? '' : 's'} selected`;
            useSelectedButton.disabled = state.selectedFiles.length === 0;

            grid.querySelectorAll('[data-media-file]').forEach((card) => {
                const id = Number(card.dataset.mediaId || 0);
                card.querySelector('.media-browser-card')?.classList.toggle(
                    'active',
                    state.selectedFiles.some((file) => Number(file.id) === id)
                );
            });
        }

        function fileCard(file) {
            const active = state.selectedFiles.some((selected) => Number(selected.id) === Number(file.id)) ? 'active' : '';
            return `
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 media-browser-item" data-media-file="${file.path}" data-media-id="${file.id || ''}">
                    <div class="media-browser-card ${active}">
                        <div class="media-browser-thumb">
                            <img src="${file.url}" alt="${file.file_name}">
                        </div>
                        <div class="media-browser-name">${file.name}</div>
                    </div>
                </div>
            `;
        }

        function folderCard(folder) {
            return `
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 media-browser-item" data-media-folder="${folder.path}">
                    <div class="media-browser-card">
                        <div class="media-browser-thumb folder">
                            <span>&#128193;</span>
                        </div>
                        <div class="media-browser-name">${folder.name}</div>
                    </div>
                </div>
            `;
        }

        function render(data) {
            currentPathLabel.textContent = data.current_path ? `/${data.current_path}` : 'Home';

            const crumbItems = ['<li class="breadcrumb-item"><a href="#" data-media-path="">Home</a></li>'];
            (data.breadcrumbs || []).forEach((crumb) => {
                crumbItems.push(`<li class="breadcrumb-item"><a href="#" data-media-path="${crumb.path}">${crumb.name}</a></li>`);
            });
            breadcrumbs.innerHTML = crumbItems.join('');

            const items = [];
            (data.folders || []).forEach((folder) => items.push(folderCard(folder)));
            (data.files || []).forEach((file) => items.push(fileCard(file)));
            grid.innerHTML = items.length
                ? items.join('')
                : '<div class="col-12 text-center text-muted py-5">No files found.</div>';

            updateSelectedState();
        }

        async function loadBrowser(path = state.currentPath, search = state.search) {
            clearError();
            state.currentPath = path || '';
            state.search = search || '';

            const query = new URLSearchParams({
                path: state.currentPath,
                search: state.search,
            });

            const response = await fetch(`{{ route('admin.media.browser') }}?${query.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                showError('Unable to load media files.');
                return;
            }

            const data = await response.json();
            render(data);
        }

        function setSelectedFiles(files) {
            state.selectedFiles = files;
            updateSelectedState();
        }

        function parseIds(value) {
            return String(value || '')
                .split(',')
                .map((id) => Number(String(id).trim()))
                .filter((id) => id > 0);
        }

        function seedSelectedFiles(target) {
            if (target === 'featured') {
                return parseIds(document.getElementById('featured-image-id')?.value).map((id) => ({ id }));
            }

            if (target === 'room-featured') {
                return parseIds(document.getElementById('room-image-id')?.value).map((id) => ({ id }));
            }

            if (target === 'banner') {
                return parseIds(document.getElementById('banner-image-id')?.value).map((id) => ({ id }));
            }

            if (target === 'gallery') {
                return parseIds(document.getElementById('gallery-image-ids')?.value).map((id) => ({ id }));
            }

            if (target === 'room-gallery') {
                return parseIds(document.getElementById('room-gallery-image-ids')?.value).map((id) => ({ id }));
            }

            return [];
        }

        function emptyMediaCard(label) {
            return `
                <div class="media-picker-empty">
                    <div class="media-picker-icon">&#128247;</div>
                    <span class="btn btn-primary btn-sm">${label}</span>
                </div>
            `;
        }

        function setHotelFieldSelection(target, files) {
            if (!target) return;

            if (target === 'featured') {
                const hidden = document.getElementById('featured-image-id');
                const preview = document.getElementById('featured-image-preview');
                if (hidden && preview) {
                    hidden.value = files[0]?.id || '';
                    preview.innerHTML = files[0]
                        ? `<img src="${files[0].url}" alt="Featured Image" class="img-fluid rounded border" style="max-height: 180px;">`
                        : emptyMediaCard('Upload image');
                }
            }

            if (target === 'banner') {
                const hidden = document.getElementById('banner-image-id');
                const preview = document.getElementById('banner-image-preview');
                if (hidden && preview) {
                    hidden.value = files[0]?.id || '';
                    preview.innerHTML = files[0]
                        ? `<img src="${files[0].url}" alt="Banner Image" class="img-fluid rounded border" style="max-height: 180px;">`
                        : emptyMediaCard('Upload image');
                }
            }

            if (target === 'gallery') {
                const hidden = document.getElementById('gallery-image-ids');
                const preview = document.getElementById('gallery-images-preview');
                if (hidden && preview) {
                    hidden.value = files
                        .map((file) => Number(file.id || 0))
                        .filter((id) => id > 0)
                        .join(',');
                    preview.innerHTML = files.length
                        ? files.map((file) => (
                            `<img src="${file.url}" alt="Gallery Image" class="rounded border" style="width:110px;height:90px;object-fit:cover;">`
                        )).join('')
                        : '';
                }
            }
        }

        document.addEventListener('click', async (event) => {
            const openButton = event.target.closest('[data-open-media-browser]');
            if (openButton) {
                event.preventDefault();
                state.selectedTarget = openButton.dataset.mediaTarget || null;
                state.multiple = openButton.dataset.mediaMultiple === 'true';
                state.selectedFiles = seedSelectedFiles(state.selectedTarget);
                searchInput.value = '';
                await loadBrowser('');
                modal.show();
                return;
            }

            const folder = event.target.closest('[data-media-folder]');
            if (folder) {
                event.preventDefault();
                await loadBrowser(folder.dataset.mediaFolder || '', searchInput.value.trim());
                return;
            }

            const breadcrumb = event.target.closest('[data-media-path]');
            if (breadcrumb) {
                event.preventDefault();
                await loadBrowser(breadcrumb.dataset.mediaPath || '', searchInput.value.trim());
                return;
            }

            const file = event.target.closest('[data-media-file]');
            if (file) {
                const id = Number(file.dataset.mediaId || 0);
                if (!id) return;

                const path = file.dataset.mediaFile;
                const image = file.querySelector('img');
                const name = file.querySelector('.media-browser-name')?.textContent || '';
                const fileData = { id, path, url: image?.src || '', name };

                if (state.multiple) {
                    const exists = state.selectedFiles.some((selected) => Number(selected.id) === id);
                    setSelectedFiles(
                        exists
                            ? state.selectedFiles.filter((selected) => Number(selected.id) !== id)
                            : [...state.selectedFiles, fileData]
                    );
                } else {
                    setSelectedFiles([fileData]);
                }
            }
        });

        document.getElementById('media-search-btn')?.addEventListener('click', () => loadBrowser(state.currentPath, searchInput.value.trim()));
        searchInput?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                loadBrowser(state.currentPath, searchInput.value.trim());
            }
        });

        document.getElementById('media-add-folder-btn')?.addEventListener('click', async () => {
            const name = window.prompt('Folder name');
            if (!name) return;

            const response = await fetch(`{{ route('admin.media.folder') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ path: state.currentPath, name }),
            });

            if (!response.ok) {
                showError('Unable to create folder.');
                return;
            }

            const payload = await response.json();
            render(payload.data);
        });

        uploadInput?.addEventListener('change', async function () {
            if (!this.files?.length) return;

            const formData = new FormData();
            formData.append('path', state.currentPath);
            Array.from(this.files).forEach((file) => formData.append('files[]', file));

            const response = await fetch(`{{ route('admin.media.upload') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            this.value = '';

            if (!response.ok) {
                showError('Unable to upload files.');
                return;
            }

            const payload = await response.json();
            render(payload.data);
        });

        useSelectedButton?.addEventListener('click', () => {
            setHotelFieldSelection(state.selectedTarget, state.selectedFiles);
            modal.hide();
        });
    })();
</script>
@endpush

