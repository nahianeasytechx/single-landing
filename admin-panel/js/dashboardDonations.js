// ============================================
// Dashboard Donations Preview
// Displays only the 3 most recent donations
// Requires: donationData.js to be loaded first
// ============================================

/**
 * Render donation rows for dashboard preview
 * Limited to 3 most recent donations
 */
function renderDashboardDonations() {
    const tableBody = document.getElementById('donationTableBody');
    
    if (!tableBody) {
        console.error('Table body element not found');
        return;
    }
    
    // Limit to 3 rows for dashboard preview
    const previewDonations = donationsData.slice(0, 3);
    
    // Handle empty state
    if (previewDonations.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="fa-solid fa-inbox fa-3x mb-3 d-block"></i>
                    <h5>No donations found</h5>
                    <p class="mb-0">Start by adding your first donation</p>
                </td>
            </tr>
        `;
        return;
    }
    
    // Generate table rows
    tableBody.innerHTML = previewDonations.map((donation, index) => {
        const badgeClass = getCategoryBadgeClass(donation.category);
        const formattedDate = formatDate(donation.date);
        const serialNumber = String(donation.id).padStart(3, '0');
        
        return `
            <tr>
                <td><strong>#${serialNumber}</strong></td>
                <td class="fw-semibold">${donation.name}</td>
                <td><small class="text-muted">${donation.phone}</small></td>
                <td>
                    <span class="badge bg-${badgeClass}">
                        ${donation.category.charAt(0).toUpperCase() + donation.category.slice(1)}
                    </span>
                </td>
                <td class="fw-bold text-success">৳ ${donation.amount.toLocaleString()}</td>
                <td>${formattedDate}</td>
                <td><code class="text-muted small">${donation.trx}</code></td>
                <td>
                    <a class="btn btn-sm  bg-success text-white" 
                                href = 'view-donation-fund.php?id=' + id;" 
                            title="View Details">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Navigate to donation details page
 * @param {number} id - Donation ID
 */
function viewDonation(id) {
    window.location.href = `view-donation-fund.php?id=${id}`;
}

// ============================================
// Initialize on page load
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Check if donationsData is available
    if (typeof donationsData === 'undefined') {
        console.error('donationsData not found. Make sure donationData.js is loaded first.');
        return;
    }
    
    // Render the donations table
    renderDashboardDonations();
    
    console.log(`Dashboard loaded: Showing ${Math.min(3, donationsData.length)} of ${donationsData.length} donations`);
});