//  Activities JavaScript

    
document.addEventListener('DOMContentLoaded', function() {
    // Sample activities data
    const activities = [
        {
            id: 1,
            title: "Community Clean-up Drive",
            status: "upcoming",
            type: "Community Service",
            category: "Environmental",
            date: "Feb 10, 2025",
            location: "Central Park",
            participants: 45,
            maxParticipants: 50,
            description: "Join us for a community clean-up initiative at Central Park. We'll be cleaning the park grounds, planting trees, and promoting environmental awareness.",
            duration: "3 hours"
        },
        {
            id: 2,
            title: "Health & Wellness Workshop",
            status: "ongoing",
            type: "Workshop",
            category: "Health",
            date: "Feb 5, 2025",
            location: "Community Center",
            participants: 28,
            maxParticipants: 30,
            description: "8-week wellness series covering nutrition, mental health, exercise, and stress management. Free for all members.",
            duration: "8 weeks"
        },
        {
            id: 3,
            title: "Youth Leadership Training",
            status: "upcoming",
            type: "Training",
            category: "Education",
            date: "Mar 1, 2025",
            location: "Training Hall",
            participants: 15,
            maxParticipants: 25,
            description: "Comprehensive leadership development program for young individuals aged 16-25. Learn essential skills in communication and team management.",
            duration: "6 weeks"
        },
        {
            id: 4,
            title: "Annual Fundraising Gala",
            status: "completed",
            type: "Event",
            category: "Fundraising",
            date: "Dec 15, 2024",
            location: "Grand Hotel",
            participants: 120,
            maxParticipants: 120,
            description: "Successfully raised $50,000 for community programs. Thank you to all attendees and sponsors!",
            duration: "4 hours"
        },
        {
            id: 5,
            title: "Food Distribution Program",
            status: "ongoing",
            type: "Outreach",
            category: "Social Welfare",
            date: "Jan 15, 2025",
            location: "Multiple Locations",
            participants: 35,
            maxParticipants: 40,
            description: "Monthly food distribution program serving underprivileged families. Volunteers needed for food packing and distribution.",
            duration: "Ongoing"
        },
        {
            id: 6,
            title: "Children's Art Workshop",
            status: "upcoming",
            type: "Workshop",
            category: "Education",
            date: "Feb 20, 2025",
            location: "Art Studio",
            participants: 18,
            maxParticipants: 20,
            description: "Creative art workshop for children aged 8-14. Activities include painting, drawing, and crafts. All materials provided.",
            duration: "2 hours"
        }
    ];

    function updateStats() {
        const total = activities.length;
        const upcoming = activities.filter(a => a.status === 'upcoming').length;
        const ongoing = activities.filter(a => a.status === 'ongoing').length;
        const completed = activities.filter(a => a.status === 'completed').length;

        document.getElementById('totalCount').textContent = total;
        document.getElementById('upcomingCount').textContent = upcoming;
        document.getElementById('ongoingCount').textContent = ongoing;
        document.getElementById('completedCount').textContent = completed;
    }

    function renderActivityCard(activity) {
        const statusClass = activity.status;
        const badgeClass = `badge-${activity.status}`;
        
        let statusIcon = '';
        let statusText = '';
        if (activity.status === 'upcoming') {
            statusIcon = 'fa-calendar-days';
            statusText = 'Upcoming';
        } else if (activity.status === 'ongoing') {
            statusIcon = 'fa-spinner';
            statusText = 'Ongoing';
        } else if (activity.status === 'completed') {
            statusIcon = 'fa-circle-check';
            statusText = 'Completed';
        }

        const participationPercentage = Math.round((activity.participants / activity.maxParticipants) * 100);

        return `
            <div class="notice-card ${statusClass}" data-status="${activity.status}" data-type="${activity.type.toLowerCase()}" data-category="${activity.category.toLowerCase()}">
                <div class="notice-header">
                    <h3 class="notice-title">${activity.title}</h3>
                    <div class="notice-badges">
                        <span class="badge-custom ${badgeClass}">
                            <i class="fa-solid ${statusIcon}"></i> ${statusText}
                        </span>
                        <span class="badge-custom badge-type">${activity.type}</span>
                    </div>
                </div>
                <div class="notice-meta">
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-calendar"></i>
                        <span>${activity.date}</span>
                    </div>
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>${activity.location}</span>
                    </div>
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>${activity.duration}</span>
                    </div>
                    <div class="notice-meta-item">
                        <i class="fa-solid fa-folder"></i>
                        <span>${activity.category}</span>
                    </div>
                </div>
                <div class="notice-description">
                    ${activity.description}
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">
                            <i class="fa-solid fa-users me-1"></i>
                            ${activity.participants} / ${activity.maxParticipants} Participants
                        </span>
                        <span class="text-muted small">${participationPercentage}%</span>
                    </div>
                    <div style="height: 8px; background: #e0e0e0; border-radius: 10px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(135deg, #667eea, #764ba2); width: ${participationPercentage}%; transition: width 0.3s;"></div>
                    </div>
                </div>
                <div class="notice-footer">
                    <div class="text-muted small">
                        <i class="fa-solid fa-users me-1"></i>
                        ${activity.participants} registered
                    </div>
                    <div class="action-buttons">
                        <button class="btn-action btn-view" title="View Details" onclick="viewActivity(${activity.id})">
                          <a href="view-activity.php">  <i class="fa-solid fa-eye"></i></a>
                        </button>
                        ${activity.status !== 'completed' ? `
                        <button class="btn-action btn-edit" title="Edit" onclick="editActivity(${activity.id})">
                            <i class="fa-solid fa-pen"></i>
                        </button>` : ''}
                        <button class="btn-action btn-duplicate" title="Duplicate" onclick="duplicateActivity(${activity.id})">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                        <button class="btn-action btn-delete" title="Delete" onclick="deleteActivity(${activity.id})">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function renderActivities(activitiesToRender = activities) {
        const activitiesList = document.getElementById('activitiesList');
        if (activitiesToRender.length === 0) {
            activitiesList.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <h3>No activities found</h3>
                    <p class="text-muted">Try adjusting your filters or create a new activity.</p>
                </div>
            `;
        } else {
            activitiesList.innerHTML = activitiesToRender.map(activity => renderActivityCard(activity)).join('');
        }
    }

    function filterActivities() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const categoryFilter = document.getElementById('categoryFilter').value;

        const filtered = activities.filter(activity => {
            const matchesSearch = activity.title.toLowerCase().includes(searchTerm) || 
                                activity.description.toLowerCase().includes(searchTerm);
            const matchesStatus = statusFilter === 'all' || activity.status === statusFilter;
            const matchesType = typeFilter === 'all' || activity.type.toLowerCase() === typeFilter;
            const matchesCategory = categoryFilter === 'all' || activity.category.toLowerCase() === categoryFilter;

            return matchesSearch && matchesStatus && matchesType && matchesCategory;
        });

        renderActivities(filtered);
    }

    document.getElementById('searchInput').addEventListener('input', filterActivities);
    document.getElementById('statusFilter').addEventListener('change', filterActivities);
    document.getElementById('typeFilter').addEventListener('change', filterActivities);
    document.getElementById('categoryFilter').addEventListener('change', filterActivities);

    window.viewActivity = function(id) {
        window.location.href = `view-activity.php?id=${id}`;
    };

    window.editActivity = function(id) {
        window.location.href = `edit-activity.php?id=${id}`;
    };

    window.duplicateActivity = function(id) {
        if (confirm('Create a copy of this activity?')) {
            alert(`Activity #${id} duplicated successfully!`);
        }
    };

    window.deleteActivity = function(id) {
        if (confirm('Are you sure you want to delete this activity? This action cannot be undone.')) {
            alert(`Activity #${id} deleted successfully!`);
            const index = activities.findIndex(a => a.id === id);
            if (index > -1) {
                activities.splice(index, 1);
                renderActivities();
                updateStats();
            }
        }
    };

    updateStats();
    renderActivities();
});
