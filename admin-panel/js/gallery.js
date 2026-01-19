
    // Sample media data
    let mediaFiles = [
        {
            id: 1,
            name: "Community Event 2024",
            type: "photo",
            category: "events",
            size: "2.5 MB",
            date: "Jan 28, 2025",
            thumbnail: "https://via.placeholder.com/300x200"
        },
        {
            id: 2,
            name: "Annual Meeting Video",
            type: "video",
            category: "activities",
            size: "15.8 MB",
            date: "Jan 25, 2025",
            thumbnail: "https://via.placeholder.com/300x200"
        },
        {
            id: 3,
            name: "Team Photo",
            type: "photo",
            category: "team",
            size: "3.2 MB",
            date: "Jan 20, 2025",
            thumbnail: "https://via.placeholder.com/300x200"
        },
        {
            id: 4,
            name: "Volunteer Activity",
            type: "photo",
            category: "activities",
            size: "2.8 MB",
            date: "Jan 15, 2025",
            thumbnail: "https://via.placeholder.com/300x200"
        },
        {
            id: 5,
            name: "Workshop Highlights",
            type: "video",
            category: "events",
            size: "22.4 MB",
            date: "Jan 10, 2025",
            thumbnail: "https://via.placeholder.com/300x200"
        },
        {
            id: 6,
            name: "Fundraising Event",
            type: "photo",
            category: "events",
            size: "1.9 MB",
            date: "Jan 5, 2025",
            thumbnail: "https://via.placeholder.com/300x200"
        }
    ];

    let selectedFiles = [];

    // Update statistics
    function updateStats() {
        const total = mediaFiles.length;
        const photos = mediaFiles.filter(m => m.type === 'photo').length;
        const videos = mediaFiles.filter(m => m.type === 'video').length;
        const totalSize = mediaFiles.reduce((sum, m) => sum + parseFloat(m.size), 0);

        document.getElementById('totalMedia').textContent = total;
        document.getElementById('photoCount').textContent = photos;
        document.getElementById('videoCount').textContent = videos;
        document.getElementById('storageUsed').textContent = totalSize.toFixed(1) + ' MB';
    }

    // Render media card
    function renderMediaCard(media) {
        const badgeClass = media.type === 'photo' ? 'badge-photo' : 'badge-video';
        const typeIcon = media.type === 'photo' ? 'fa-image' : 'fa-video';

        return `
            <div class="media-card">
                <img src="${media.thumbnail}" alt="${media.name}" class="media-thumbnail">
                <span class="media-type-badge ${badgeClass}">
                    <i class="fa-solid ${typeIcon}"></i> ${media.type.toUpperCase()}
                </span>
                <div class="media-info">
                    <div class="media-title">${media.name}</div>
                    <div class="media-meta">
                        <span><i class="fa-solid fa-calendar"></i> ${media.date}</span>
                        <span><i class="fa-solid fa-file"></i> ${media.size}</span>
                    </div>
                    <div class="media-actions">
                        <button class="btn-action btn-view" onclick="viewMedia(${media.id})">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button class="btn-action btn-edit" onclick="editMedia(${media.id})">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="deleteMedia(${media.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Render media grid
  
    function renderMedia(files = mediaFiles) {
        const grid = document.getElementById('mediaGrid');
        if (files.length === 0) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column: 1/-1;">
                    <i class="fa-solid fa-images"></i>
                    <h3>No media files found</h3>
                    <p class="text-muted">Upload your first photo or video to get started.</p>
                </div>
            `;
        } else {
            grid.innerHTML = files.map(media => renderMediaCard(media)).join('');
        }
    }

    // File upload handling
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const previewSection = document.getElementById('previewSection');
    const previewGrid = document.getElementById('previewGrid');

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        selectedFiles = Array.from(files);
        if (selectedFiles.length > 0) {
            previewSection.style.display = 'block';
            displayPreviews();
        }
    }

    function displayPreviews() {
        previewGrid.innerHTML = '';
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.createElement('div');
                preview.className = 'preview-item';
                preview.innerHTML = `
                    <img src="${e.target.result}" class="preview-img" alt="${file.name}">
                    <button class="preview-remove" onclick="removeFile(${index})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                previewGrid.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        if (selectedFiles.length === 0) {
            previewSection.style.display = 'none';
        } else {
            displayPreviews();
        }
    }

    function uploadFiles() {
        const progressSection = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const progressPercent = document.getElementById('progressPercent');

        progressSection.style.display = 'block';

        // Simulate upload progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            progressFill.style.width = progress + '%';
            progressPercent.textContent = progress + '%';

            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    alert('Files uploaded successfully!');
                    progressSection.style.display = 'none';
                    previewSection.style.display = 'none';
                    selectedFiles = [];
                    fileInput.value = '';
                    progressFill.style.width = '0%';
                    progressPercent.textContent = '0%';
                    // Here you would add the new files to mediaFiles array
                    updateStats();
                    renderMedia();
                }, 500);
            }
        }, 200);
    }

    // Filter functions
    document.getElementById('typeFilter').addEventListener('change', filterMedia);
    document.getElementById('categoryFilter').addEventListener('change', filterMedia);
    document.getElementById('searchInput').addEventListener('input', filterMedia);

    function filterMedia() {
        const typeFilter = document.getElementById('typeFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();

        const filtered = mediaFiles.filter(media => {
            const matchesType = typeFilter === 'all' || media.type === typeFilter;
            const matchesCategory = categoryFilter === 'all' || media.category === categoryFilter;
            const matchesSearch = media.name.toLowerCase().includes(searchTerm);

            return matchesType && matchesCategory && matchesSearch;
        });

        renderMedia(filtered);
    }

    // Action functions
    function viewMedia(id) {
        console.log('View media:', id);
        alert(`Viewing media #${id}`);
    }

    function editMedia(id) {
        console.log('Edit media:', id);
        alert(`Editing media #${id}`);
    }

    function deleteMedia(id) {
        if (confirm('Are you sure you want to delete this media file?')) {
            const index = mediaFiles.findIndex(m => m.id === id);
            if (index > -1) {
                mediaFiles.splice(index, 1);
                updateStats();
                renderMedia();
                alert('Media deleted successfully!');
            }
        }
    }

    // Initial render
    updateStats();
    renderMedia();

