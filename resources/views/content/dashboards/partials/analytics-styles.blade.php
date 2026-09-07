<style>
    /* ========================================
       Modern Analytics Dashboard Styles
       With Full Dark Mode Support
       ======================================== */

    :root {
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.04);
        --text-primary: #2c3e50;
        --text-secondary: #6c757d;
        --text-muted: #a0aec0;
        --bg-light: #f8fafc;
        --bg-hover: #f1f5f9;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
        --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.12);
        --table-stripe: #f8fafc;
        --border-color: #e9ecef;
        --gradient-purple: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-green: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --gradient-orange: linear-gradient(135deg, #ff6b6b 0%, #feca57 100%);
        --gradient-blue: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-pink: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .dark-layout {
        --card-bg: #283046;
        --card-border: rgba(255, 255, 255, 0.06);
        --text-primary: #d0d2d6;
        --text-secondary: #b4b7bd;
        --text-muted: #676d7d;
        --bg-light: #161d31;
        --bg-hover: #343d55;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
        --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.3);
        --shadow-lg: 0 10px 40px rgba(0, 0, 0, 0.4);
        --table-stripe: #242b3d;
        --border-color: #3b4253;
    }

    /* Dashboard Hero Header */
    .dashboard-header {
        background: var(--gradient-purple);
        border-radius: 20px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header.purple {
        background: var(--gradient-purple);
    }

    .dashboard-header.green {
        background: var(--gradient-green);
    }

    .dashboard-header.blue {
        background: var(--gradient-blue);
    }

    .dashboard-header.orange {
        background: var(--gradient-orange);
    }

    .dashboard-header.pink {
        background: var(--gradient-pink);
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .dashboard-header::after {
        content: '';
        position: absolute;
        bottom: -60%;
        left: 10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .dashboard-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .dashboard-header p {
        margin: 8px 0 0;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .dashboard-header .btn-light {
        position: relative;
        z-index: 1;
    }

    /* Modern Card Styles */
    .analytics-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--shadow-md);
        padding: 24px;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--card-border);
    }

    .analytics-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }

    /* Gradient Stat Cards */
    .stat-card {
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 120px;
        height: 120px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
    }

    .stat-card.primary {
        background: var(--gradient-purple);
        color: white;
    }

    .stat-card.success {
        background: var(--gradient-green);
        color: white;
    }

    .stat-card.warning {
        background: var(--gradient-pink);
        color: white;
    }

    .stat-card.info {
        background: var(--gradient-blue);
        color: white;
    }

    .stat-card.orange {
        background: var(--gradient-orange);
        color: white;
    }

    .stat-card:hover {
        transform: translateY(-6px) scale(1.02);
    }

    .stat-card .stat-icon {
        font-size: 1.5rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }

    .stat-card .stat-icon .material-symbols-outlined {
        font-size: 28px;
    }

    .stat-card .stat-value {
        font-size: 2.25rem;
        font-weight: 700;
        margin: 12px 0 8px;
        position: relative;
        z-index: 1;
    }

    .stat-card .stat-label {
        font-size: 0.875rem;
        opacity: 0.95;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }

    /* Revenue Cards */
    .revenue-card {
        border-radius: 16px;
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .revenue-card.purple {
        background: var(--gradient-purple);
    }

    .revenue-card.green {
        background: var(--gradient-green);
    }

    .revenue-card.orange {
        background: var(--gradient-orange);
    }

    .revenue-card.blue {
        background: var(--gradient-blue);
    }

    .revenue-card.pink {
        background: var(--gradient-pink);
    }

    .revenue-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    }

    .revenue-card:hover {
        transform: translateY(-6px);
    }

    .revenue-card .revenue-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 8px 0;
        position: relative;
        z-index: 1;
    }

    .revenue-card .revenue-label {
        font-size: 0.875rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }

    .revenue-card .revenue-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 4rem;
        opacity: 0.2;
    }

    /* Card Titles */
    .card-title {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 2px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title .material-symbols-outlined {
        font-size: 20px;
        color: #667eea;
    }

    /* Filter Section */
    .filter-section {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px 24px;
        margin-bottom: 28px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--card-border);
    }

    .filter-section .form-label {
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 8px;
    }

    .filter-section .form-control,
    .filter-section .form-select {
        background: var(--bg-light);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        padding: 10px 16px;
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    .filter-section .form-control:focus,
    .filter-section .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        background: var(--card-bg);
    }

    /* Quick Stats */
    .quick-stat {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        background: var(--bg-light);
        border-radius: 12px;
        margin-bottom: 12px;
        transition: all 0.3s ease;
        border: 1px solid var(--card-border);
    }

    .quick-stat:hover {
        background: var(--bg-hover);
        transform: translateX(8px);
    }

    .quick-stat .icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quick-stat .icon .material-symbols-outlined {
        font-size: 24px;
    }

    .quick-stat .h4,
    .quick-stat h4 {
        color: var(--text-primary);
        margin: 0;
    }

    .quick-stat small {
        color: var(--text-secondary);
    }

    /* Stat Box */
    .stat-box {
        background: var(--bg-light);
        border-radius: 12px;
        padding: 18px;
        text-align: center;
        margin-bottom: 12px;
        border: 1px solid var(--card-border);
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .stat-box .value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .stat-box .label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .stat-box.highlight {
        background: var(--bg-light);
        border-left: 4px solid #11998e;
    }

    .stat-box.highlight .value {
        color: #11998e;
    }

    /* Modern Tables */
    .summary-table,
    .user-table,
    .analytics-table {
        width: 100%;
    }

    .summary-table th,
    .user-table th,
    .analytics-table th {
        background: var(--bg-light);
        font-weight: 600;
        padding: 14px 18px;
        text-align: left;
        border-bottom: 2px solid var(--border-color);
        color: var(--text-primary);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-table td,
    .user-table td,
    .analytics-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .summary-table tbody tr,
    .user-table tbody tr,
    .analytics-table tbody tr {
        transition: all 0.2s ease;
    }

    .summary-table tbody tr:hover,
    .user-table tbody tr:hover,
    .analytics-table tbody tr:hover {
        background: var(--bg-hover);
    }

    .summary-table tbody tr:nth-child(even),
    .user-table tbody tr:nth-child(even),
    .analytics-table tbody tr:nth-child(even) {
        background: var(--table-stripe);
    }

    .summary-table tbody tr:nth-child(even):hover,
    .user-table tbody tr:nth-child(even):hover,
    .analytics-table tbody tr:nth-child(even):hover {
        background: var(--bg-hover);
    }

    /* Chart Container */
    .chart-container {
        position: relative;
        height: 300px;
    }

    /* Avatar */
    .avatar-circle {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Badge Styles */
    .badge-modern {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-modern.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .badge-modern.bg-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
    }

    /* Trend Indicators */
    .trend-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.875rem;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
    }

    .trend-indicator.up {
        color: #11998e;
        background: rgba(17, 153, 142, 0.15);
    }

    .trend-indicator.down {
        color: #f5576c;
        background: rgba(245, 87, 108, 0.15);
    }

    /* Metric Rows */
    .metric-row {
        display: flex;
        justify-content: space-between;
        padding: 14px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .metric-row:last-child {
        border-bottom: none;
    }

    .metric-label {
        color: var(--text-secondary);
    }

    .metric-value {
        font-weight: 600;
        color: var(--text-primary);
    }

    /* Progress Bars */
    .progress-modern {
        height: 8px;
        border-radius: 4px;
        background: var(--bg-light);
        overflow: hidden;
    }

    .progress-modern .progress-bar {
        border-radius: 4px;
    }

    .progress-modern .progress-bar.purple {
        background: var(--gradient-purple);
    }

    .progress-modern .progress-bar.green {
        background: var(--gradient-green);
    }

    .progress-modern .progress-bar.blue {
        background: var(--gradient-blue);
    }

    .progress-modern .progress-bar.orange {
        background: var(--gradient-orange);
    }

    /* Rating Stars */
    .rating-stars {
        color: #ffc107;
        font-size: 1rem;
    }

    /* Section Titles */
    .section-title {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 1.1rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title .title-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .section-title .title-icon .material-symbols-outlined {
        font-size: 18px;
    }

    /* Global Material Icons */
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }

    .dashboard-header .material-symbols-outlined {
        font-size: 28px;
    }

    .revenue-card .material-symbols-outlined {
        font-size: 28px;
    }

    .filter-section .material-symbols-outlined {
        font-size: 16px;
        vertical-align: text-bottom;
    }

    .btn .material-symbols-outlined {
        font-size: 18px;
        vertical-align: middle;
    }

    /* Flatpickr Calendar Styles */
    .flatpickr-calendar {
        z-index: 9999 !important;
        background: var(--card-bg) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 12px !important;
        box-shadow: var(--shadow-lg) !important;
        font-family: inherit !important;
    }

    .flatpickr-calendar.open {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .flatpickr-months {
        background: var(--bg-light) !important;
        border-radius: 12px 12px 0 0 !important;
        padding: 8px 0 !important;
    }

    .flatpickr-months .flatpickr-month {
        background: transparent !important;
        color: var(--text-primary) !important;
        fill: var(--text-primary) !important;
    }

    .flatpickr-current-month {
        color: var(--text-primary) !important;
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        background: var(--bg-light) !important;
        color: var(--text-primary) !important;
    }

    .flatpickr-current-month input.cur-year {
        color: var(--text-primary) !important;
    }

    .flatpickr-months .flatpickr-prev-month,
    .flatpickr-months .flatpickr-next-month {
        color: var(--text-primary) !important;
        fill: var(--text-primary) !important;
    }

    .flatpickr-months .flatpickr-prev-month:hover,
    .flatpickr-months .flatpickr-next-month:hover {
        color: #667eea !important;
        fill: #667eea !important;
    }

    .flatpickr-months .flatpickr-prev-month svg,
    .flatpickr-months .flatpickr-next-month svg {
        fill: currentColor !important;
    }

    .flatpickr-weekdays {
        background: var(--bg-light) !important;
    }

    .flatpickr-weekday {
        color: var(--text-secondary) !important;
        font-weight: 600 !important;
    }

    .flatpickr-days {
        background: var(--card-bg) !important;
        border: none !important;
    }

    .flatpickr-day {
        color: var(--text-primary) !important;
        border-radius: 8px !important;
    }

    .flatpickr-day:hover {
        background: var(--bg-hover) !important;
        border-color: transparent !important;
    }

    .flatpickr-day.today {
        border-color: #667eea !important;
    }

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background: #667eea !important;
        border-color: #667eea !important;
        color: #fff !important;
    }

    .flatpickr-day.inRange {
        background: rgba(102, 126, 234, 0.2) !important;
        border-color: transparent !important;
        box-shadow: none !important;
    }

    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover,
    .flatpickr-day.prevMonthDay,
    .flatpickr-day.nextMonthDay {
        color: var(--text-muted) !important;
    }

    .dark-layout .flatpickr-calendar {
        background: #283046 !important;
        border-color: #3b4253 !important;
    }

    .dark-layout .flatpickr-months {
        background: #161d31 !important;
    }

    .dark-layout .flatpickr-days {
        background: #283046 !important;
    }

    .dark-layout .flatpickr-weekdays {
        background: #161d31 !important;
    }

    .dark-layout .flatpickr-day.inRange {
        background: rgba(102, 126, 234, 0.3) !important;
    }

    /* Input with Calendar Icon */
    .filter-section input[readonly] {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 40px !important;
    }
</style>
