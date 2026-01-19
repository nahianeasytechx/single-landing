// ============================================
// Shared Donations Data - Single Source of Truth
// This file can be used across multiple pages
// ============================================

const donationsData = [
    {
        id: 1,
        category: "education",
        name: "Md. Rahim Uddin",
        phone: "+880 1712-345678",
        address: "Mirpur, Dhaka",
        amount: 5000,
        trx: "TRX123456789",
        month: "10",
        date: "2024-10-15"
    },
    {
        id: 2,
        category: "health",
        name: "Fatima Khatun",
        phone: "+880 1812-987654",
        address: "Banani, Dhaka",
        amount: 10000,
        trx: "TRX987654321",
        month: "10",
        date: "2024-10-20"
    },
    {
        id: 3,
        category: "emergency",
        name: "Kamal Hossain",
        phone: "+880 1912-456789",
        address: "Chittagong",
        amount: 15000,
        trx: "TRX456789123",
        month: "09",
        date: "2024-09-28"
    },
    {
        id: 4,
        category: "food",
        name: "Ayesha Siddika",
        phone: "+880 1612-789456",
        address: "Uttara, Dhaka",
        amount: 3000,
        trx: "TRX789456123",
        month: "08",
        date: "2024-08-12"
    },
    {
        id: 5,
        category: "general",
        name: "Ibrahim Khan",
        phone: "+880 1512-321654",
        address: "Rajshahi",
        amount: 7500,
        trx: "TRX321654987",
        month: "09",
        date: "2024-09-05"
    },
    {
        id: 6,
        category: "education",
        name: "Nasrin Akter",
        phone: "+880 1712-654321",
        address: "Barisal",
        amount: 2000,
        trx: "TRX654321789",
        month: "08",
        date: "2024-08-22"
    },
    {
        id: 7,
        category: "health",
        name: "Mizanur Rahman",
        phone: "+880 1812-147258",
        address: "Sylhet",
        amount: 12000,
        trx: "TRX147258369",
        month: "07",
        date: "2024-07-18"
    },
    {
        id: 8,
        category: "food",
        name: "Sultana Begum",
        phone: "+880 1912-963852",
        address: "Khulna",
        amount: 4500,
        trx: "TRX963852741",
        month: "10",
        date: "2024-10-08"
    },
    {
        id: 9,
        category: "emergency",
        name: "Abdul Jabbar",
        phone: "+880 1612-852963",
        address: "Gazipur",
        amount: 20000,
        trx: "TRX852963147",
        month: "09",
        date: "2024-09-14"
    }
];

// ============================================
// Helper Functions
// ============================================

/**
 * Get Bootstrap badge class for category
 * @param {string} category - Donation category
 * @returns {string} Bootstrap badge class
 */
function getCategoryBadgeClass(category) {
    const badges = {
        'education': 'primary',
        'health': 'success',
        'emergency': 'danger',
        'food': 'warning text-dark',
        'general': 'secondary'
    };
    return badges[category] || 'secondary';
}

/**
 * Format date to readable format
 * @param {string} dateString - Date in YYYY-MM-DD format
 * @returns {string} Formatted date (e.g., "15 Oct, 2024")
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric' 
    });
}

/**
 * Get total donations count
 * @returns {number} Total number of donations
 */
function getTotalDonations() {
    return donationsData.length;
}

/**
 * Get total donation amount
 * @returns {number} Sum of all donations
 */
function getTotalAmount() {
    return donationsData.reduce((sum, d) => sum + d.amount, 0);
}

/**
 * Get current month donations amount
 * @returns {number} Sum of current month donations
 */
function getCurrentMonthAmount() {
    const currentMonth = String(new Date().getMonth() + 1).padStart(2, '0');
    return donationsData
        .filter(d => d.month === currentMonth)
        .reduce((sum, d) => sum + d.amount, 0);
}

/**
 * Get total unique donors count
 * @returns {number} Number of unique donors
 */
function getTotalDonors() {
    return new Set(donationsData.map(d => d.name)).size;
}