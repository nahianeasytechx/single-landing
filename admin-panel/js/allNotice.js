// <!-- All Notices JavaScript -->
    // Sample notices data - Replace with PHP data from database
    const notices = [
        {
            id: 1,
            title: "Annual General Meeting 2025",
            status: "active",
            type: "Meeting",
            category: "Administrative",
            publishDate: "Jan 25, 2025",
            expiryDate: "Apr 25, 2025",
            ageLimit: "All Ages",
            description: "All members are requested to attend the Annual General Meeting scheduled for February 15, 2025. The meeting will cover financial reports, new initiatives, and election of board members.",
            daysRemaining: 90
        },
        {
            id: 2,
            title: "Volunteer Registration for Community Clean-up Drive",
            status: "active",
            type: "Recruitment",
            category: "Volunteer",
            publishDate: "Jan 28, 2025",
            expiryDate: "Feb 28, 2025",
            ageLimit: "18+ years",
            description: "Join us for our upcoming community service initiative! We're organizing a clean-up drive in the local park on February 10, 2025. Volunteers needed to help make our community cleaner and greener.",
            daysRemaining: 31
        },
        {
            id: 3,
            title: "Youth Leadership Training Program",
            status: "draft",
            type: "Training",
            category: "Program",
            publishDate: "Jan 30, 2025",
            expiryDate: null,
            ageLimit: "16-25 years",
            description: "Exciting leadership development opportunity for young individuals. Learn essential skills in communication, team management, problem-solving, and community organizing. Program runs for 6 weeks.",
            daysRemaining: null
        },
        {
            id: 4,
            title: "Fundraising Campaign - December 2024",
            status: "expired",
            type: "Event",
            category: "Fundraising",
            publishDate: "Dec 1, 2024",
            expiryDate: "Jan 1, 2025",
            ageLimit: "All Ages",
            description: "Thank you to everyone who participated in our December fundraising campaign. Together we raised over $50,000 to support our community programs. Your generosity makes a real difference!",
            daysRemaining: -27
        },
        {
            id: 5,
            title: "New Office Hours - Effective February 2025",
            status: "active",
            type: "Announcement",
            category: "Administrative",
            publishDate: "Jan 20, 2025",
            expiryDate: "Jul 20, 2025",
            ageLimit: "All Ages",
            description: "Please note our updated office hours starting February 1st: Monday-Friday 9:00 AM - 5:00 PM, Saturday 10:00 AM - 2:00 PM. Closed on Sundays and public holidays.",
            daysRemaining: 167
        },
        {
            id: 6,
            title: "Health & Wellness Workshop Series",
            status: "active",
            type: "Event",
            category: "Program",
            publishDate: "Jan 22, 2025",
            expiryDate: "Mar 22, 2025",
            ageLimit: "All Ages",
            description: "Join our 8-week wellness series covering nutrition, mental health, exercise, and stress management. Free for all members. Register now as spaces are limited!",
            daysRemaining: 77
        }
    ];

    // Update statistics
    function updateStats() {
        const total = notices.length;
        const active = notices.filter(n => n.status === 'active').length;
        const expired = notices.filter(n => n.status === 'expired').length;
        const draft = notices.filter(n => n.status === 'draft').length;

        document.getElementById('totalCount').textContent = total;
        document.getElementById('activeCount').textContent = active;
        document.getElementById('expiredCount').textContent = expired;
        document.getElementById('draftCount').textContent = draft;
    }

    // Function to render notice card
    function renderNoticeCard(notice) {
        const statusClass = notice.status;
        const badgeClass = `badge-${notice.status}`;
        
        let footerInfo = '';
        if (notice.status === 'active') {
            footerInfo = `<i class="fa-solid fa-hourglass-half me-1"></i>${notice.daysRemaining} days remaining`;
        } else if (notice.status === 'expired') {
            footerInfo = `<i class="fa-solid fa-circle-xmark me-1"></i>Expired ${Math.abs(notice.daysRemaining)} days ago`;
        } else if (notice.status === 'draft') {
            footerInfo = `<i class="fa-solid fa-file-pen me-1"></i>Last edited: 2 hours ago`;
        }

        return `
            <div class="notice-card ${statusClass}" data-status="${notice.status}" data-type="${notice.type.toLowerCase()}" data-category="${notice.category.toLowerCase()}">
                <div class="notice-header">
                    <h3 class="notice-title">${notice.title}</h3>
                    <div class="notice-badges">
                        <span class="badge-custom ${badgeClass}">${notice.status.charAt(0).toUpperCase() + notice.status.slice(1)}</span>
                        <span class="badge-custom badge-type">${notice.type}</span>
                    </div>
                </div>
                <div class="notice-meta">
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-calendar"></i>
                        <span>Published: ${notice.publishDate}</span>
                    </div>
                    ${notice.expiryDate ? `
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Expires: ${notice.expiryDate}</span>
                    </div>` : ''}
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-folder"></i>
                        <span>${notice.category}</span>
                    </div>
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-users"></i>
                        <span>${notice.ageLimit}</span>
                    </div>
                </div>
                <div class="notice-description">
                    ${notice.description}
                </div>
                <div class="notice-footer">
                    <div class="text-muted small">
                        ${footerInfo}
                    </div>
                    <div class="action-buttons">
                        <button class="btn-action btn-view" title="View Details" onclick="viewNotice(${notice.id})">
                      <a href="view-notice.php">      <i class="fa-solid fa-eye"></i></a>
                        </button>
                        ${notice.status !== 'expired' ? `
                        <button class="btn-action btn-edit" title="Edit" onclick="editNotice(${notice.id})">
                            <i class="fa-solid fa-pen"></i>
                        </button>` : ''}
                        <button class="btn-action btn-duplicate" title="Duplicate" onclick="duplicateNotice(${notice.id})">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                        <button class="btn-action btn-delete" title="Delete" onclick="deleteNotice(${notice.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Render all notices
    function renderNotices(noticesToRender = notices) {
        const noticesList = document.getElementById('noticesList');
        if (noticesToRender.length === 0) {
            noticesList.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <h3>No notices found</h3>
                    <p class="text-muted">Try adjusting your filters or create a new notice.</p>
                </div>
            `;
        } else {
            noticesList.innerHTML = noticesToRender.map(notice => renderNoticeCard(notice)).join('');
        }
    }

    // Filter notices
    function filterNotices() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;

        const filtered = notices.filter(notice => {
            const matchesSearch = notice.title.toLowerCase().includes(searchTerm) || 
                                notice.description.toLowerCase().includes(searchTerm);
            const matchesStatus = statusFilter === 'all' || notice.status === statusFilter;
            const matchesType = typeFilter === 'all' || notice.type.toLowerCase() === typeFilter;
            const matchesCategory = categoryFilter === 'all' || notice.category.toLowerCase() === categoryFilter;

            return matchesSearch && matchesStatus && matchesType && matchesCategory;
        });

        renderNotices(filtered);
    }

    // Event listeners
    document.getElementById('searchInput').addEventListener('input', filterNotices);
    document.getElementById('statusFilter').addEventListener('change', filterNotices);
    document.getElementById('typeFilter').addEventListener('change', filterNotices);
    document.getElementById('categoryFilter').addEventListener('change', filterNotices);

    // Action functions
    function viewNotice(id) {
        console.log('View notice:', id);
        window.location.href = `view-notice.php?id=${id}`;
    }

    function editNotice(id) {
        console.log('Edit notice:', id);
        window.location.href = `edit-notice.php?id=${id}`;
    }

    function duplicateNotice(id) {
        console.log('Duplicate notice:', id);
        if (confirm('Create a copy of this notice?')) {
            alert(`Notice #${id} duplicated successfully!`);
            // Add duplication logic here
        }
    }

    function deleteNotice(id) {
        console.log('Delete notice:', id);
        if (confirm('Are you sure you want to delete this notice? This action cannot be undone.')) {
            // Add AJAX call to delete from database
            alert(`Notice #${id} deleted successfully!`);
            
            // Remove from array and re-render (for demo)
            const index = notices.findIndex(n => n.id === id);
            if (index > -1) {
                notices.splice(index, 1);
                renderNotices();
                updateStats();
            }
        }
    }

    // Initial render
    updateStats();
    renderNotices();
