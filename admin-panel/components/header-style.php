<style>
/* ============================================
   MODERN DASHBOARD CSS - CARD STYLE
   ============================================ */

/* === ROOT VARIABLES === */
:root {
    --primary-color: #059669;
    --primary-dark: #047857;
    --primary-light: #10b981;
    --secondary-color: #0d9488;
    
    --text-dark: #1f2937;
    --text-medium: #4b5563;
    --text-light: #6b7280;
    --text-lighter: #9ca3af;
    
    --bg-white: #ffffff;
    --bg-gray: #f9fafb;
    --bg-light: #f3f4f6;
    --border-color: #e5e7eb;
    
    --success: #10b981;
    --danger: #ef4444;
    --warning: #f59e0b;
    --info: #3b82f6;
    
    --shadow-sm:0 20px 40px rgba(0, 0, 0, 0.15);;
    --shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
    
    --radius: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    
    --transition: all 0.3s ease;
}

/* === GLOBAL RESETS === */
* {
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: var(--bg-gray);
    color: var(--text-dark);
}

/* === CONTENT WRAPPER === */
.content-wrapper {
    width: 100%;
    max-width: 100%;
    padding: 24px;
    background-color: var(--bg-gray);
}

.dashboard {
    max-width: 1400px;
    margin: 0 auto;
}

/* === PAGE TITLE === */
.page-title-section {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
}

.page-title-section .icon-box {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.page-title-section h1 {
    font-size: 24px;
    font-weight: 600;
    margin: 0;
    color: var(--text-dark);
}

/* === STAT CARDS CONTAINER === */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

/* === INDIVIDUAL STAT CARD === */
.stat-card {
    background: var(--bg-white);
    border-radius: var(--radius-xl);
    padding: 20px;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: transparent;
}

/* === CARD HEADER === */
.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.stat-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-lighter);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1.4;
    max-width: 60%;
}

/* === TREND ICON === */
.stat-trend {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    font-size: 14px;
}

/* === BADGES === */
.stat-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.stat-badge.warning {
    background-color: #fef3c7;
    color: #92400e;
}

.stat-badge.success {
    background-color: #d1fae5;
    color: #065f46;
}

.stat-badge.info {
    background-color: #e0e7ff;
    color: #3730a3;
}

.stat-badge.danger {
    background-color: #fee2e2;
    color: #991b1b;
}

/* === CARD BODY === */
.stat-card-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-value {
    font-size: 36px;
    font-weight: 700;
    color: var(--text-dark);
    margin: 0;
    line-height: 1;
}

/* === ICON WRAPPER === */
.stat-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}

/* Icon Color Variants */
.stat-icon-wrapper.red {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.stat-icon-wrapper.blue {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.stat-icon-wrapper.green {
    background: linear-gradient(135deg, #10b981, #059669);
}

.stat-icon-wrapper.green-dark {
    background: linear-gradient(135deg, #059669, #047857);
}

.stat-icon-wrapper.purple {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.stat-icon-wrapper.purple-light {
    background: linear-gradient(135deg, #a78bfa, #8b5cf6);
}

.stat-icon-wrapper.orange {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.stat-icon-wrapper.blue-light {
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
}

.stat-icon-wrapper.gray {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

/* === QUICK ACTIONS === */
.quick-actions-title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: var(--text-dark);
}

.quick-actions-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.quick-action-card {
    background: var(--bg-white);
    border-radius: var(--radius-xl);
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}

.quick-action-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-light);
}

.quick-action-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-size: 28px;
    color: white;
}

.quick-action-icon.new-notice {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.quick-action-icon.upload-media {
    background: linear-gradient(135deg, #0d9488, #0f766e);
}

.quick-action-icon.add-activity {
    background: linear-gradient(135deg, #10b981, #059669);
}

.quick-action-icon.edit-about {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

.quick-action-title {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: var(--text-dark);
}

/* === TABLE CARD === */
.table-card {
    background: var(--bg-white);
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
    margin-bottom: 24px;
}

.table-card:hover {
    box-shadow: var(--shadow-md);
}

.table-header {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    padding: 16px 20px;
    border: none;
}

.table-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 16px;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    margin-bottom: 0;
    width: 100%;
}

.table thead th {
    background-color: var(--bg-light);
    font-weight: 600;
    text-transform: uppercase;
    font-size: 11px;
    border-bottom: 2px solid var(--border-color);
    padding: 12px 16px;
    letter-spacing: 0.5px;
    color: var(--text-medium);
}

.table tbody td {
    color: var(--text-dark);
    font-size: 14px;
    vertical-align: middle;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
}

.table tbody tr:hover {
    background-color: var(--bg-gray);
}

.table tbody tr:last-child td {
    border-bottom: none;
}

/* === BADGES === */
.badge {
    font-size: 11px;
    padding: 5px 12px;
    border-radius: 6px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge.bg-primary {
    background-color: #3b82f6 !important;
}

.badge.bg-secondary {
    background-color: #6b7280 !important;
}

.badge.bg-success {
    background-color: #10b981 !important;
}

.badge.bg-warning {
    background-color: #f59e0b !important;
    color: white !important;
}

.badge.bg-danger {
    background-color: #ef4444 !important;
}

.badge.bg-info {
    background-color: #3b82f6 !important;
}

/* === TABLE BUTTONS === */
.table-btn {
    background: var(--primary-color);
    padding: 10px 20px;
    border-radius: var(--radius);
    color: white !important;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    font-size: 14px;
}

.table-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 6px;
    border: none;
    color: white;
    cursor: pointer;
    transition: var(--transition);
}

.btn-sm.btn-info {
    background-color: #3b82f6;
}

.btn-sm.btn-info:hover {
    background-color: #2563eb;
    transform: scale(1.05);
}

/* === UTILITIES === */
.text-end {
    text-align: right;
}

.p-3 {
    padding: 16px;
}

.ms-2 {
    margin-left: 8px;
}

/* === RESPONSIVE === */
@media (max-width: 1400px) {
    .stats-container {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 1200px) {
    .stats-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 16px;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .stat-card {
        padding: 16px;
    }
    
    .stat-value {
        font-size: 30px;
    }
    
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
    
    .quick-actions-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;

    }

    .page-title-section h1 {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .stat-value {
        font-size: 24px;
    }
    
    .stat-icon-wrapper {
        width: 44px;
        height: 44px;
        font-size: 18px;
    }
    
    .quick-actions-container {
        grid-template-columns: 1fr;
    }
}

/* === SCROLLBAR === */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--bg-gray);
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}
</style>