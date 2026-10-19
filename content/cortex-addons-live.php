<?php
/**
 * Process flows and live control rooms for HR & Payroll, POS, Property Management and Assets Management
 * (used by cortex_addon_page() in content/cortex-addons.php). Control rooms use sections/ops_control.php; the room
 * itself is the same for both languages (English application UI, sample data), headings and process are translated.
 * Card actions and approvals: fx changes the top figures, rows changes the card's own figures, status/label changes
 * the card state, log writes to the event log.
 */
$R = fn(string $t) => '[AR-REVIEW]' . $t . '[/AR-REVIEW]';
$step = fn($icon, $title, $text, array $f) => ['icon' => $icon, 'title' => $title, 'text' => $text, 'functions' => $f];

$rooms = [
'hr-payroll' => [
    'title' => 'HR Control', 'sub' => 'OEC Computers, 412 employees', 'warn' => ['docs'],
    'kpis' => [['present', 'Present today', 318, 'int', 'Of 412 employees'], ['leave', 'On leave', 9, 'int', '3 back tomorrow'], ['requests', 'Open requests', 5, 'int', 'Leave, loans, overtime'], ['payroll', 'October payroll', 3104500, 'sarm', 'Ready for approval'], ['docs', 'Documents expiring', 6, 'int', 'In the next 30 days']],
    'cards_title' => 'Departments',
    'cards' => [
        ['id' => 'ops', 'title' => 'Operations', 'status' => 'run', 'label' => 'On site', 'bar' => 0, 'max' => 210, 'rows' => [['Present', 160], ['Headcount', 210], ['Late today', 7], ['Overtime this month', 642, 'int']],
            'actions' => [['label' => 'Send reminder to late employees', 'log' => 'Reminder sent to 7 late employees in Operations', 'once' => true], ['label' => 'Approve overtime, 42 hours', 'kind' => 'ok', 'log' => 'Overtime approved: 42 hours for Operations, added to October payroll', 'fx' => ['payroll' => 5250], 'rows' => [3 => 42], 'once' => true]]],
        ['id' => 'sales', 'title' => 'Sales', 'status' => 'run', 'label' => 'On site', 'bar' => 0, 'max' => 68, 'rows' => [['Present', 61], ['Headcount', 68], ['Late today', 2], ['In the field', 14]],
            'actions' => [['label' => 'Add new joiner', 'kind' => 'ok', 'log' => 'New joiner added to Sales: contract, GOSI registration and onboarding tasks created', 'rows' => [1 => 1], 'once' => true]]],
        ['id' => 'fin', 'title' => 'Finance', 'status' => 'run', 'label' => 'On site', 'bar' => 0, 'max' => 24, 'rows' => [['Present', 21], ['Headcount', 24], ['Late today', 0], ['Payroll inputs', 'Open', 'text']],
            'actions' => [['label' => 'Lock payroll inputs', 'log' => 'Payroll inputs locked for October', 'status' => 'chg', 'state' => 'Payroll locked', 'rows' => [3 => 'Locked'], 'once' => true]]],
        ['id' => 'wh', 'title' => 'Warehouse', 'status' => 'idle', 'label' => 'Short staffed', 'bar' => 0, 'max' => 52, 'rows' => [['Present', 38], ['Headcount', 52], ['Late today', 9], ['Shift', 'Morning', 'text']],
            'actions' => [['label' => 'Request 4 temporary workers', 'log' => 'Request sent for 4 temporary workers at the Warehouse', 'status' => 'run', 'state' => 'Covered', 'once' => true], ['label' => 'Approve shift swap', 'kind' => 'ok', 'log' => 'Shift swap approved for 2 warehouse employees', 'once' => true]]],
        ['id' => 'it', 'title' => 'IT', 'status' => 'run', 'label' => 'On site', 'bar' => 0, 'max' => 16, 'rows' => [['Present', 15], ['Headcount', 16], ['Late today', 0], ['Remote today', 1]],
            'actions' => [['label' => 'Approve remote work', 'kind' => 'ok', 'log' => 'Remote work approved for 1 IT employee', 'once' => true]]],
        ['id' => 'adm', 'title' => 'Admin and HR', 'status' => 'run', 'label' => 'On site', 'bar' => 0, 'max' => 42, 'rows' => [['Present', 23], ['Headcount', 42], ['Late today', 1], ['Open vacancies', 3]],
            'actions' => [['label' => 'Publish vacancy', 'log' => 'Vacancy for HR officer published', 'rows' => [3 => 1], 'once' => true]]],
    ],
    'inbox_title' => 'Requests',
    'inbox' => [
        ['title' => 'Leave request, 5 days', 'sub' => 'Sara Al Qahtani, Finance, 20 to 24 Oct', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Approved', 'log' => 'Leave approved for Sara Al Qahtani, 5 days, balance updated', 'fx' => ['requests' => -1]], ['label' => 'Reject', 'kind' => 'bad', 'done' => 'Rejected', 'log' => 'Leave request returned to Sara Al Qahtani', 'fx' => ['requests' => -1]]]],
        ['title' => 'Loan request, SAR 15,000', 'sub' => 'Omar Fahad, 6 monthly deductions', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Approved', 'log' => 'Loan approved: SAR 15,000 deducted over 6 months from November payroll', 'fx' => ['requests' => -1]]]],
        ['title' => 'Iqama renewal', 'sub' => 'Ahmed Saleh, expires 28 Oct 2026', 'actions' => [['label' => 'Start renewal', 'kind' => 'ok', 'done' => 'Renewal started', 'log' => 'Iqama renewal started for Ahmed Saleh', 'fx' => ['docs' => -1]]]],
        ['title' => 'October payroll', 'sub' => '412 employees, GOSI included', 'actions' => [['label' => 'Approve and post', 'kind' => 'ok', 'done' => 'Posted', 'log' => 'October payroll approved and posted to SAP Business One by cost centre, bank file ready']]],
    ],
    'chart_title' => 'Check ins per minute', 'chart_kpi' => 'present', 'chart' => [30, 42, 55, 61, 48, 40, 36, 30, 28, 22, 20, 18, 16, 14, 18, 12, 10, 14, 12, 9, 8, 12, 10, 8, 6, 9, 7, 6, 8, 5],
    'log' => [['10:40', 'Check in: 12 employees at the Jeddah branch', ''], ['10:35', 'Medical insurance renewed for 18 employees', 'ok'], ['10:31', 'Contract of Lina Hassan ends in 45 days', 'warn'], ['10:24', 'Overtime request: 6 hours, Warehouse', '']],
    'tick' => ['ms' => 1500, 'feedEvery' => 4, 'chartMax' => 4, 'cards' => [['ops', 0, 0, 2, 'present', 1], ['sales', 0, 0, 1, 'present', 1], ['wh', 0, 0, 1, 'present', 1], ['adm', 0, 0, 1, 'present', 1], ['fin', 0, 0, 1, 'present', 1]]],
    'feed' => [['log' => 'Check in: 9 employees at the Riyadh office'], ['log' => 'Passport of Rajesh Kumar expires in 33 days', 'kind' => 'warn', 'fx' => ['docs' => 1]], ['log' => 'Lina Hassan completed onboarding', 'kind' => 'ok'], ['log' => 'GOSI contributions for October calculated', 'kind' => 'ok'], ['log' => 'New leave request from the Warehouse', 'fx' => ['requests' => 1]]],
],
'pos' => [
    'title' => 'Retail Control', 'sub' => '7 branches, 21 registers', 'warn' => ['alerts'],
    'kpis' => [['sales', 'Sales today', 84300, 'sar', 'All branches'], ['trx', 'Transactions', 612, 'int', 'E-invoices cleared'], ['basket', 'Average basket', 'SAR 138', 'text', 'Up SAR 6'], ['online', 'Branches online', '7 / 7', 'text', 'All registers synced'], ['alerts', 'Open alerts', 2, 'int', 'Stock and cash']],
    'cards_title' => 'Branches',
    'cards' => [
        ['id' => 'olaya', 'title' => 'Riyadh, Olaya', 'status' => 'run', 'label' => 'Selling', 'rows' => [['Sales today', 21400, 'sar'], ['Transactions', 148], ['Registers open', 4], ['Shift', 'A, 09:00 to 17:00', 'text']],
            'actions' => [['label' => 'Push new price list', 'log' => 'Price list Oct 2 pushed to Riyadh, Olaya: 38 items updated', 'once' => true], ['label' => 'Close shift A', 'kind' => 'ok', 'log' => 'Shift A closed at Olaya: cash counted, no difference, posted to SAP Business One', 'rows' => [3 => 'B, 17:00 to 01:00'], 'once' => true]]],
        ['id' => 'malqa', 'title' => 'Riyadh, Malqa', 'status' => 'run', 'label' => 'Selling', 'rows' => [['Sales today', 16900, 'sar'], ['Transactions', 121], ['Registers open', 3], ['Promotion', 'Buy 2 get 1', 'text']],
            'actions' => [['label' => 'End promotion', 'log' => 'Promotion Buy 2 get 1 ended at Malqa', 'rows' => [3 => 'None'], 'once' => true]]],
        ['id' => 'tahlia', 'title' => 'Jeddah, Tahlia', 'status' => 'run', 'label' => 'Selling', 'rows' => [['Sales today', 18200, 'sar'], ['Transactions', 133], ['Registers open', 3], ['Cash in drawers', 9400, 'sar']],
            'actions' => [['label' => 'Cash pick up', 'kind' => 'ok', 'log' => 'Cash pick up at Tahlia: SAR 8,000 moved to the safe', 'rows' => [3 => -8000], 'once' => true]]],
        ['id' => 'dammam', 'title' => 'Dammam', 'status' => 'run', 'label' => 'Selling', 'rows' => [['Sales today', 12700, 'sar'], ['Transactions', 96], ['Registers open', 2], ['Returns today', 3]],
            'actions' => [['label' => 'Open extra register', 'log' => 'Register 3 opened at Dammam', 'rows' => [2 => 1], 'once' => true]]],
        ['id' => 'khobar', 'title' => 'Khobar', 'status' => 'idle', 'label' => 'Low stock', 'rows' => [['Sales today', 15100, 'sar'], ['Transactions', 114], ['Registers open', 2], ['Items low on stock', 6]],
            'actions' => [['label' => 'Transfer stock from warehouse', 'kind' => 'ok', 'log' => 'Stock transfer to Khobar created: 6 items, 240 units', 'status' => 'run', 'state' => 'Selling', 'rows' => [3 => -6], 'fx' => ['alerts' => -1], 'once' => true]]],
        ['id' => 'madinah', 'title' => 'Madinah', 'status' => 'chg', 'label' => 'Opening', 'rows' => [['Sales today', 0, 'sar'], ['Transactions', 0], ['Registers open', 0], ['Opens at', '11:00', 'text']],
            'actions' => [['label' => 'Open branch', 'kind' => 'ok', 'log' => 'Madinah branch opened, 2 registers online', 'status' => 'run', 'state' => 'Selling', 'rows' => [2 => 2], 'once' => true]]],
    ],
    'inbox_title' => 'Approvals',
    'inbox' => [
        ['title' => 'Price override, 20%', 'sub' => 'Olaya register 2, order 8812', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Approved', 'log' => 'Price override approved at Olaya by the area manager'], ['label' => 'Reject', 'kind' => 'bad', 'done' => 'Rejected', 'log' => 'Price override rejected at Olaya']]],
        ['title' => 'Return over limit, SAR 2,350', 'sub' => 'Malqa, receipt R 55120', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Approved', 'log' => 'Return approved at Malqa: credit note issued and e-invoice reported', 'fx' => ['sales' => -2350]]]],
        ['title' => 'Cash difference, SAR 120 short', 'sub' => 'Tahlia, shift A', 'actions' => [['label' => 'Accept and post', 'kind' => 'ok', 'done' => 'Posted', 'log' => 'Cash difference of SAR 120 posted for Tahlia shift A', 'fx' => ['alerts' => -1]]]],
        ['title' => 'Stock transfer request', 'sub' => 'Khobar needs 60 units of SKU 4412', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Approved', 'log' => 'Transfer of 60 units to Khobar approved', 'card' => 'khobar', 'status' => 'run', 'state' => 'Selling']]],
    ],
    'chart_title' => 'Sales per minute', 'chart_kpi' => 'sales', 'chart' => [40, 52, 48, 61, 55, 44, 58, 62, 70, 66, 59, 63, 72, 68, 75, 71, 64, 69, 77, 80, 74, 70, 78, 82, 79, 73, 81, 86, 84, 80],
    'log' => [['10:41', 'Receipt R 55229 at Olaya, SAR 640, e-invoice cleared', 'ok'], ['10:38', 'Khobar: 6 items below minimum stock', 'warn'], ['10:33', 'Price list Oct 1 synced to all branches', ''], ['10:30', 'Malqa shift A opened, float SAR 1,500', '']],
    'tick' => ['ms' => 1500, 'feedEvery' => 4, 'chartMax' => 2400, 'cards' => [['olaya', 0, 0, 900, 'sales'], ['olaya', 1, 0, 3, 'trx'], ['malqa', 0, 0, 700, 'sales'], ['malqa', 1, 0, 2, 'trx'], ['tahlia', 0, 0, 800, 'sales'], ['tahlia', 1, 0, 2, 'trx'], ['dammam', 0, 0, 500, 'sales'], ['dammam', 1, 0, 2, 'trx'], ['khobar', 0, 0, 400, 'sales'], ['madinah', 0, 0, 450, 'sales']]],
    'feed' => [['log' => 'Receipt R 55240 at Tahlia, SAR 1,240, e-invoice cleared', 'kind' => 'ok'], ['log' => 'Loyalty points redeemed at Malqa, SAR 85'], ['log' => 'Dammam: item 7781 below minimum stock', 'kind' => 'warn', 'fx' => ['alerts' => 1]], ['log' => 'Sales of the last hour posted to SAP Business One', 'kind' => 'ok'], ['log' => 'Return at Olaya: SAR 210, credit note issued']],
],
'property-management' => [
    'title' => 'Property Control', 'sub' => '14 buildings, 1,240 units', 'warn' => ['overdue'],
    'kpis' => [['occ', 'Occupancy', 92.0, 'pct', 'Up 1.5% this quarter'], ['collected', 'Rent collected', 1846000, 'sarm', 'October, 71% of due'], ['overdue', 'Overdue rent', 310000, 'sark', '23 tenants'], ['maint', 'Open maintenance', 17, 'int', '4 urgent'], ['expiring', 'Leases ending', 11, 'int', 'In the next 60 days']],
    'cards_title' => 'Buildings',
    'cards' => [
        ['id' => 'olaya', 'title' => 'Olaya Tower', 'status' => 'run', 'label' => 'Collecting', 'bar' => 0, 'max' => 220, 'rows' => [['Leased units', 214], ['Units', 220], ['Collected this month', 512000, 'sar'], ['Overdue', 42000, 'sar']],
            'actions' => [['label' => 'Send overdue reminders', 'log' => 'Overdue reminders sent to 4 tenants in Olaya Tower by SMS and email', 'once' => true], ['label' => 'Issue November rent invoices', 'kind' => 'ok', 'log' => 'November rent invoices issued for Olaya Tower: 214 invoices with VAT', 'once' => true]]],
        ['id' => 'malqa', 'title' => 'Malqa Residences', 'status' => 'run', 'label' => 'Collecting', 'bar' => 0, 'max' => 180, 'rows' => [['Leased units', 164], ['Units', 180], ['Collected this month', 286000, 'sar'], ['Overdue', 58000, 'sar']],
            'actions' => [['label' => 'Lease a vacant unit', 'kind' => 'ok', 'log' => 'Unit B 305 leased in Malqa Residences, contract created', 'rows' => [0 => 1], 'fx' => ['occ' => 0.1], 'once' => true]]],
        ['id' => 'yasmin', 'title' => 'Yasmin Villas', 'status' => 'idle', 'label' => 'Vacancies', 'bar' => 0, 'max' => 96, 'rows' => [['Leased units', 84], ['Units', 96], ['Collected this month', 198000, 'sar'], ['Overdue', 71000, 'sar']],
            'actions' => [['label' => 'Publish vacant villas', 'log' => '12 vacant villas published to the leasing team and partners', 'once' => true], ['label' => 'Lease villa C 12', 'kind' => 'ok', 'log' => 'Villa C 12 leased, deposit received', 'rows' => [0 => 1], 'fx' => ['occ' => 0.1], 'once' => true]]],
        ['id' => 'khobar', 'title' => 'Khobar Plaza', 'status' => 'run', 'label' => 'Collecting', 'bar' => 0, 'max' => 140, 'rows' => [['Leased units', 132], ['Units', 140], ['Collected this month', 344000, 'sar'], ['Overdue', 39000, 'sar']],
            'actions' => [['label' => 'Schedule fire system check', 'log' => 'Fire system inspection scheduled for Khobar Plaza on 20 Oct', 'fx' => ['maint' => 1], 'once' => true]]],
        ['id' => 'jeddah', 'title' => 'Jeddah Corniche', 'status' => 'stop', 'label' => 'Lift out of service', 'bar' => 0, 'max' => 160, 'rows' => [['Leased units', 138], ['Units', 160], ['Collected this month', 301000, 'sar'], ['Overdue', 64000, 'sar']],
            'actions' => [['label' => 'Assign lift contractor', 'kind' => 'ok', 'log' => 'Lift repair assigned to Al Waha Lifts, cost charged to Jeddah Corniche', 'status' => 'run', 'state' => 'Collecting', 'once' => true]]],
        ['id' => 'retail', 'title' => 'Retail Strip, Malqa', 'status' => 'run', 'label' => 'Collecting', 'bar' => 0, 'max' => 44, 'rows' => [['Leased units', 40], ['Units', 44], ['Collected this month', 205000, 'sar'], ['Overdue', 36000, 'sar']],
            'actions' => [['label' => 'Renew anchor tenant', 'kind' => 'ok', 'log' => 'Anchor tenant renewed for 5 years in Retail Strip, Malqa', 'fx' => ['expiring' => -1], 'once' => true]]],
    ],
    'inbox_title' => 'Approvals',
    'inbox' => [
        ['title' => 'Lease renewal, 2 years', 'sub' => 'Najd Trading, Olaya A 1204', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Renewed', 'log' => 'Lease of Najd Trading renewed for 2 years, new payment schedule created', 'fx' => ['expiring' => -1]]]],
        ['title' => 'Maintenance quote, SAR 18,500', 'sub' => 'Chiller repair, Malqa Residences B', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Approved', 'log' => 'Chiller repair approved, work order sent to the contractor', 'fx' => ['maint' => -1]], ['label' => 'Reject', 'kind' => 'bad', 'done' => 'Rejected', 'log' => 'Chiller quote rejected, second quote requested']]],
        ['title' => 'Reservation', 'sub' => 'Yasmin villa C 14, deposit received', 'actions' => [['label' => 'Confirm', 'kind' => 'ok', 'done' => 'Confirmed', 'log' => 'Reservation confirmed for villa C 14, lease contract drafted', 'card' => 'yasmin', 'rows' => [0 => 1], 'fx' => ['occ' => 0.1]]]],
        ['title' => 'Move out inspection', 'sub' => 'Khobar Plaza G04, deposit SAR 8,000', 'actions' => [['label' => 'Release deposit', 'kind' => 'ok', 'done' => 'Released', 'log' => 'Deposit of SAR 8,000 released after move out inspection, unit G04 vacant']]],
    ],
    'chart_title' => 'Rent received per minute', 'chart_kpi' => 'collected', 'chart' => [30, 45, 38, 52, 41, 36, 60, 48, 44, 39, 55, 50, 42, 61, 47, 38, 53, 49, 58, 44, 40, 57, 63, 46, 52, 49, 61, 55, 48, 59],
    'log' => [['10:40', 'Payment received: Al Noor Clinic, SAR 45,000', 'ok'], ['10:37', 'Maintenance request: water leak, Malqa B 210', 'warn'], ['10:31', 'Lease of Eastern Foods ends in 50 days', ''], ['10:26', 'Cheque deposited: Red Sea Holding, SAR 120,000', 'ok']],
    'tick' => ['ms' => 1500, 'feedEvery' => 4, 'chartMax' => 30000, 'cards' => [['olaya', 2, 0, 9000, 'collected'], ['malqa', 2, 0, 5000, 'collected'], ['khobar', 2, 0, 6000, 'collected'], ['retail', 2, 0, 4000, 'collected'], ['jeddah', 2, 0, 5000, 'collected']]],
    'feed' => [['log' => 'Payment received: Gulf Medical, SAR 32,000', 'kind' => 'ok', 'fx' => ['overdue' => -32000]], ['log' => 'Maintenance request: air conditioning, Olaya 1507', 'kind' => 'warn', 'fx' => ['maint' => 1]], ['log' => 'Rent invoice viewed by tenant in Khobar Plaza'], ['log' => 'Maintenance closed: lighting, Retail Strip', 'kind' => 'ok', 'fx' => ['maint' => -1]], ['log' => 'Bank transfer matched to lease L 3381', 'kind' => 'ok']],
],
'assets-management' => [
    'title' => 'Asset Control', 'sub' => '9 locations, 3,860 assets', 'warn' => ['missing'],
    'kpis' => [['assets', 'Assets', 3860, 'int', 'In 9 locations'], ['nbv', 'Net book value', 48200000, 'sarm', 'After October run'], ['counted', 'Counted', 2910, 'int', 'Annual count in progress'], ['missing', 'Not found yet', 14, 'int', 'Under review'], ['maint', 'Due for maintenance', 14, 'int', 'This week']],
    'cards_title' => 'Locations, annual count',
    'cards' => [
        ['id' => 'hq', 'title' => 'Riyadh head office', 'status' => 'run', 'label' => 'Counting', 'bar' => 0, 'max' => 940, 'rows' => [['Counted', 812], ['Assets', 940], ['Not found', 3], ['Custodians', 214]],
            'actions' => [['label' => 'Mark 2 assets found', 'kind' => 'ok', 'log' => '2 laptops found in meeting room 4, head office', 'rows' => [2 => -2], 'fx' => ['missing' => -2], 'once' => true], ['label' => 'Transfer laptop to new custodian', 'log' => 'Laptop LT 2210 transferred to Sara Al Qahtani, handover form signed', 'once' => true]]],
        ['id' => 'wh', 'title' => 'Riyadh warehouse', 'status' => 'run', 'label' => 'Counting', 'bar' => 0, 'max' => 610, 'rows' => [['Counted', 498], ['Assets', 610], ['Not found', 4], ['Forklifts', 12]],
            'actions' => [['label' => 'Raise maintenance work order', 'log' => 'Work order for forklift FL 07 raised, technician assigned', 'fx' => ['maint' => -1], 'once' => true]]],
        ['id' => 'fac', 'title' => 'Factory', 'status' => 'run', 'label' => 'Counting', 'bar' => 0, 'max' => 1180, 'rows' => [['Counted', 902], ['Assets', 1180], ['Not found', 5], ['Machines', 64]],
            'actions' => [['label' => 'Revalue press P 04', 'log' => 'Press P 04 revalued after overhaul, depreciation schedule updated', 'once' => true]]],
        ['id' => 'jed', 'title' => 'Jeddah depot', 'status' => 'idle', 'label' => 'Not started', 'bar' => 0, 'max' => 420, 'rows' => [['Counted', 0], ['Assets', 420], ['Not found', 0], ['Vehicles', 26]],
            'actions' => [['label' => 'Start count', 'kind' => 'ok', 'log' => 'Annual count started at Jeddah depot, 2 counters assigned', 'status' => 'run', 'state' => 'Counting', 'once' => true]]],
        ['id' => 'dmm', 'title' => 'Dammam branch', 'status' => 'run', 'label' => 'Counting', 'bar' => 0, 'max' => 260, 'rows' => [['Counted', 238], ['Assets', 260], ['Not found', 2], ['Custodians', 41]],
            'actions' => [['label' => 'Close count and post', 'kind' => 'ok', 'log' => 'Dammam count closed, differences posted for review', 'status' => 'chg', 'state' => 'Count closed', 'once' => true]]],
        ['id' => 'fleet', 'title' => 'Vehicle fleet', 'status' => 'stop', 'label' => '3 overdue service', 'bar' => 0, 'max' => 450, 'rows' => [['Counted', 450], ['Assets', 450], ['Not found', 0], ['Overdue service', 3]],
            'actions' => [['label' => 'Book service for 3 trucks', 'kind' => 'ok', 'log' => 'Service booked for 3 trucks at the Jeddah workshop', 'status' => 'run', 'state' => 'Counted', 'rows' => [3 => -3], 'fx' => ['maint' => -3], 'once' => true]]],
    ],
    'inbox_title' => 'Approvals',
    'inbox' => [
        ['title' => 'Asset transfer', 'sub' => 'Forklift FL 07 to Jeddah depot', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Approved', 'log' => 'Forklift FL 07 transferred to Jeddah depot, cost centre updated']]],
        ['title' => 'Disposal, truck TR 03', 'sub' => 'NBV SAR 12,000, sale price SAR 30,000', 'actions' => [['label' => 'Approve', 'kind' => 'ok', 'done' => 'Disposed', 'log' => 'Truck TR 03 sold, gain of SAR 18,000 posted', 'fx' => ['assets' => -1, 'nbv' => -12000]], ['label' => 'Reject', 'kind' => 'bad', 'done' => 'Rejected', 'log' => 'Disposal of truck TR 03 rejected']]],
        ['title' => 'New asset, SAR 640,000', 'sub' => 'CNC machine, Factory, capitalise', 'actions' => [['label' => 'Capitalise', 'kind' => 'ok', 'done' => 'Capitalised', 'log' => 'CNC machine capitalised: tag FA 4871, depreciation over 10 years', 'fx' => ['assets' => 1, 'nbv' => 640000]]]],
        ['title' => 'Insurance renewal', 'sub' => 'Vehicle fleet policy ends 31 Oct', 'actions' => [['label' => 'Renew', 'kind' => 'ok', 'done' => 'Renewed', 'log' => 'Fleet insurance renewed for 12 months']]],
    ],
    'chart_title' => 'Assets scanned per minute', 'chart_kpi' => 'counted', 'chart' => [40, 52, 61, 48, 55, 66, 58, 44, 62, 70, 64, 57, 49, 68, 72, 60, 55, 63, 71, 66, 59, 74, 68, 61, 57, 69, 73, 65, 60, 70],
    'log' => [['10:41', 'Scanned 24 assets in the factory, line 2', 'ok'], ['10:36', 'Laptop LT 1987 not found at its location', 'warn'], ['10:30', 'Depreciation for October posted: SAR 412,000', 'ok'], ['10:22', 'Generator G 2 service due on 16 Oct', '']],
    'tick' => ['ms' => 1500, 'feedEvery' => 4, 'chartMax' => 12, 'cards' => [['hq', 0, 0, 4, 'counted', 1], ['wh', 0, 0, 3, 'counted', 1], ['fac', 0, 0, 5, 'counted', 1], ['jed', 0, 0, 3, 'counted', 1], ['dmm', 0, 0, 2, 'counted', 1]]],
    'feed' => [['log' => 'Scanned 18 assets at the Riyadh warehouse', 'kind' => 'ok'], ['log' => 'Monitor MN 552 found in a different room, location updated'], ['log' => 'Asset tag damaged: chair CH 1188, new tag printed', 'kind' => 'warn'], ['log' => 'Warranty of server SRV 03 ends in 30 days', 'kind' => 'warn'], ['log' => 'Custodian confirmed 6 assets by mobile', 'kind' => 'ok']],
],
];

$headings = [
'hr-payroll' => [
    'en' => ['process' => ['The HR process', 'From hiring to end of service', 'Cortex HR & Payroll covers the whole employee journey inside SAP Business One. Pick a stage to see what it does.'],
             'control' => ['Live HR control', 'Your people, payroll and requests on one screen', 'Attendance comes in as people check in, requests wait in one inbox and payroll is a click away. Try it.', 'Click a department, approve requests and post the payroll.']],
    'ar' => ['process' => [$R('مراحل الموارد البشرية'), $R('من التعيين حتى نهاية الخدمة'), $R('يغطي Cortex HR & Payroll رحلة الموظف كاملة داخل SAP Business One. اختر مرحلة لترى ما تقدمه.')],
             'control' => [$R('متابعة الموارد البشرية المباشرة'), $R('موظفوك ورواتبهم وطلباتهم على شاشة واحدة'), $R('يصل الحضور لحظة تسجيله وتنتظر الطلبات في صندوق واحد والرواتب على بعد ضغطة. جرّبها.'), $R('اضغط على قسم، واعتمد الطلبات، ورحّل الرواتب.')]],
    'steps' => [
        'en' => [
            $step('users', 'Hire and onboard', 'Bring new people in with everything ready on day one.', ['Job requisitions and offers', 'Employee contract and grade', 'GOSI registration [CONFIRM: GOSI integration]', 'Onboarding checklist']),
            $step('doc', 'Employee records', 'One record per employee with every document and change.', ['Personal data and family', 'Iqama, passport and contracts', 'Allowances and grade history', 'Document expiry alerts']),
            $step('clock', 'Attendance', 'Know who is at work, late or absent.', ['Attendance from devices or mobile', 'Shifts and rosters', 'Late and absence rules', 'Overtime requests']),
            $step('calendar', 'Leave', 'Request, approve and track every type of leave.', ['Leave types under the Saudi Labour Law', 'Balances and accruals', 'Approval workflow', 'Leave settlement']),
            $step('coins', 'Payroll', 'Run the monthly payroll with confidence.', ['Allowances and deductions', 'Overtime and absence in payroll', 'Loans and advances deducted', 'Payslips in Arabic and English']),
            $step('shield', 'GOSI and bank', 'Meet your obligations without extra files.', ['GOSI contributions', 'Bank salary file [CONFIRM: WPS format]', 'Payroll reports for audit']),
            $step('link', 'Posting to finance', 'Every payroll run lands in your books.', ['Posting by department and cost centre', 'Accruals for leave and end of service', 'No manual journal entries']),
            $step('briefcase', 'End of service', 'Close every employee file correctly.', ['End of service calculation', 'Final settlement with leave and loans', 'Clearance checklist', 'Experience letter']),
        ],
        'ar' => [
            $step('users', $R('التعيين والتهيئة'), $R('استقبل الموظفين الجدد وكل شيء جاهز من اليوم الأول.'), [$R('طلبات التوظيف والعروض'), $R('عقد الموظف ودرجته'), $R('التسجيل في التأمينات الاجتماعية') . ' [CONFIRM: GOSI integration]', $R('قائمة مهام التهيئة')]),
            $step('doc', $R('ملفات الموظفين'), $R('ملف واحد لكل موظف بكل وثائقه وتغييراته.'), [$R('البيانات الشخصية والعائلية'), $R('الإقامة والجواز والعقود'), $R('سجل البدلات والدرجات'), $R('تنبيهات انتهاء الوثائق')]),
            $step('clock', $R('الحضور'), $R('اعرف من في العمل ومن تأخر أو غاب.'), [$R('الحضور من الأجهزة أو الجوال'), $R('الورديات والجداول'), $R('قواعد التأخير والغياب'), $R('طلبات العمل الإضافي')]),
            $step('calendar', $R('الإجازات'), $R('اطلب كل أنواع الإجازات واعتمدها وتابعها.'), [$R('أنواع الإجازات وفق نظام العمل السعودي'), $R('الأرصدة والاستحقاقات'), $R('مسار الاعتماد'), $R('تسوية الإجازات')]),
            $step('coins', $R('الرواتب'), $R('شغّل مسير الرواتب الشهري بثقة.'), [$R('البدلات والاستقطاعات'), $R('العمل الإضافي والغياب في الرواتب'), $R('خصم السلف والقروض'), $R('قسائم رواتب بالعربية والإنجليزية')]),
            $step('shield', $R('التأمينات والبنك'), $R('أوفِ بالتزاماتك دون ملفات إضافية.'), [$R('اشتراكات التأمينات الاجتماعية'), $R('ملف الرواتب للبنك') . ' [CONFIRM: WPS format]', $R('تقارير الرواتب للتدقيق')]),
            $step('link', $R('الترحيل إلى المالية'), $R('كل مسير رواتب يصل إلى حساباتك.'), [$R('الترحيل حسب القسم ومركز التكلفة'), $R('مخصصات الإجازات ونهاية الخدمة'), $R('دون قيود يدوية')]),
            $step('briefcase', $R('نهاية الخدمة'), $R('أغلق ملف كل موظف بشكل صحيح.'), [$R('حساب مكافأة نهاية الخدمة'), $R('التسوية النهائية مع الإجازات والسلف'), $R('قائمة إخلاء الطرف'), $R('شهادة الخبرة')]),
        ],
    ],
],
'pos' => [
    'en' => ['process' => ['The retail process', 'From the shelf to the books', 'Cortex POS covers every step of selling in your branches, connected to SAP Business One. Pick a step to see what it does.'],
             'control' => ['Live retail control', 'Every branch selling, on one screen', 'Sales come in from every register, stock and cash alerts appear as they happen and approvals wait in one inbox. Try it.', 'Click a branch, approve overrides and returns, and fix the low stock.']],
    'ar' => ['process' => [$R('مراحل البيع بالتجزئة'), $R('من الرف إلى الحسابات'), $R('يغطي Cortex POS كل خطوة في البيع بفروعك متصلاً بنظام SAP Business One. اختر خطوة لترى ما تقدمه.')],
             'control' => [$R('متابعة المبيعات المباشرة'), $R('كل فروعك تبيع، على شاشة واحدة'), $R('تصل المبيعات من كل نقطة بيع وتظهر تنبيهات المخزون والنقد لحظة حدوثها وتنتظر الاعتمادات في صندوق واحد. جرّبها.'), $R('اضغط على فرع، واعتمد تعديلات الأسعار والمرتجعات، وعالج نقص المخزون.')]],
    'steps' => [
        'en' => [
            $step('box', 'Items and prices', 'Set up what you sell once, for every branch.', ['Items, barcodes and variants', 'Price lists per branch', 'Promotions and discounts', 'Push changes to all registers']),
            $step('coins', 'Open shift', 'Start the day with a counted float.', ['Cashier login and permissions', 'Opening float', 'Register and drawer assignment']),
            $step('cart', 'Sell', 'Fast checkout at every counter.', ['Barcode scanning and quick keys', 'Split payment: cash, card and mada', 'Customer lookup and loyalty [CONFIRM: loyalty]', 'Keeps selling offline [CONFIRM: offline mode]']),
            $step('receipt', 'E-invoice', 'Every receipt ready for ZATCA.', ['Simplified tax invoice with QR code', 'Reporting to ZATCA [CONFIRM: ZATCA integration]', 'Reprint and email receipts']),
            $step('link', 'Returns and exchanges', 'Handle returns under control.', ['Return against the original receipt', 'Credit notes', 'Approval above a limit']),
            $step('check', 'Close shift', 'End the day with no surprises.', ['Cash count by denomination', 'Differences shown straight away', 'Shift report']),
            $step('cloud', 'Sync to SAP Business One', 'Sales, payments and stock land in your ERP.', ['Sales and payments posted', 'Stock updated by branch', 'Daily summary by branch']),
            $step('chart', 'Replenish', 'Keep the shelves full.', ['Low stock alerts per branch', 'Transfers from the warehouse', 'Sales reports by item and hour']),
        ],
        'ar' => [
            $step('box', $R('الأصناف والأسعار'), $R('أعد ما تبيعه مرة واحدة لكل الفروع.'), [$R('الأصناف والباركود والمتغيرات'), $R('قوائم أسعار لكل فرع'), $R('العروض والخصومات'), $R('إرسال التغييرات لكل نقاط البيع')]),
            $step('coins', $R('فتح الوردية'), $R('ابدأ اليوم بعهدة نقدية معدودة.'), [$R('دخول الكاشير وصلاحياته'), $R('العهدة الافتتاحية'), $R('تخصيص نقطة البيع والدرج')]),
            $step('cart', $R('البيع'), $R('دفع سريع عند كل كاشير.'), [$R('قراءة الباركود والمفاتيح السريعة'), $R('تقسيم الدفع نقداً وبالبطاقة ومدى'), $R('البحث عن العميل والولاء') . ' [CONFIRM: loyalty]', $R('البيع دون اتصال') . ' [CONFIRM: offline mode]']),
            $step('receipt', $R('الفاتورة الإلكترونية'), $R('كل إيصال جاهز لهيئة الزكاة والضريبة والجمارك.'), [$R('فاتورة ضريبية مبسطة برمز QR'), $R('الإبلاغ للهيئة') . ' [CONFIRM: ZATCA integration]', $R('إعادة الطباعة والإرسال بالبريد')]),
            $step('link', $R('المرتجعات والاستبدال'), $R('عالج المرتجعات تحت السيطرة.'), [$R('الإرجاع على الإيصال الأصلي'), $R('الإشعارات الدائنة'), $R('الاعتماد فوق حد معين')]),
            $step('check', $R('إقفال الوردية'), $R('أنهِ اليوم دون مفاجآت.'), [$R('جرد النقد حسب الفئات'), $R('عرض الفروقات فوراً'), $R('تقرير الوردية')]),
            $step('cloud', $R('المزامنة مع SAP Business One'), $R('المبيعات والمدفوعات والمخزون تصل إلى نظامك.'), [$R('ترحيل المبيعات والمدفوعات'), $R('تحديث المخزون حسب الفرع'), $R('ملخص يومي لكل فرع')]),
            $step('chart', $R('إعادة التزويد'), $R('أبقِ الرفوف ممتلئة.'), [$R('تنبيهات نقص المخزون لكل فرع'), $R('التحويل من المستودع'), $R('تقارير المبيعات حسب الصنف والساعة')]),
        ],
    ],
],
'property-management' => [
    'en' => ['process' => ['The leasing process', 'From vacant unit to owner report', 'Cortex Property Management covers every step of leasing and operating your buildings inside SAP Business One. Pick a step to see what it does.'],
             'control' => ['Live property control', 'Every building, lease and riyal of rent on one screen', 'Rent arrives as tenants pay, maintenance requests come in and approvals wait in one inbox. Try it.', 'Click a building, lease a unit, approve renewals and quotes.']],
    'ar' => ['process' => [$R('مراحل التأجير'), $R('من الوحدة الشاغرة إلى تقرير المالك'), $R('يغطي Cortex Property Management كل خطوة في تأجير مبانيك وتشغيلها داخل SAP Business One. اختر خطوة لترى ما تقدمه.')],
             'control' => [$R('متابعة العقارات المباشرة'), $R('كل مبنى وعقد وريال إيجار على شاشة واحدة'), $R('تصل الإيجارات عند السداد وتصل طلبات الصيانة وتنتظر الاعتمادات في صندوق واحد. جرّبها.'), $R('اضغط على مبنى، وأجّر وحدة، واعتمد التجديدات وعروض الأسعار.')]],
    'steps' => [
        'en' => [
            $step('map', 'Properties and units', 'Your whole portfolio in one place.', ['Buildings, floors and units', 'Area, type and features', 'Rent price lists', 'Photos and documents']),
            $step('users', 'Leasing and reservation', 'Turn enquiries into tenants.', ['Vacant units list', 'Reservations with deposit', 'Tenant records and documents']),
            $step('doc', 'Lease contract', 'Clear contracts with every term recorded.', ['Rent, payment schedule and deposit', 'Escalation and discounts', 'Contract registration [CONFIRM: Ejar integration]']),
            $step('receipt', 'Rent invoicing', 'Invoices created from every schedule.', ['Automatic rent invoices', 'VAT where it applies', 'Service charges and utilities']),
            $step('coins', 'Collection', 'Get paid on time.', ['Receipts, transfers and cheques', 'Overdue list and reminders', 'Payment matching to leases']),
            $step('gear', 'Maintenance', 'Keep tenants happy and costs under control.', ['Requests per unit', 'Quotes and work orders', 'Cost per building']),
            $step('calendar', 'Renewal or move out', 'Never let a lease end by surprise.', ['Renewal alerts', 'Renewal with new terms', 'Move out inspection and deposit release']),
            $step('chart', 'Owner reporting', 'Know what every building earns.', ['Income and expenses per property', 'Owner statements', 'Occupancy and collection reports']),
        ],
        'ar' => [
            $step('map', $R('العقارات والوحدات'), $R('محفظتك كاملة في مكان واحد.'), [$R('المباني والأدوار والوحدات'), $R('المساحة والنوع والمواصفات'), $R('قوائم أسعار الإيجار'), $R('الصور والمستندات')]),
            $step('users', $R('التأجير والحجز'), $R('حوّل الاستفسارات إلى مستأجرين.'), [$R('قائمة الوحدات الشاغرة'), $R('الحجوزات مع العربون'), $R('سجلات المستأجرين ووثائقهم')]),
            $step('doc', $R('عقد الإيجار'), $R('عقود واضحة بكل الشروط مسجلة.'), [$R('الإيجار وجدول الدفعات والتأمين'), $R('الزيادات والخصومات'), $R('توثيق العقد') . ' [CONFIRM: Ejar integration]']),
            $step('receipt', $R('فوترة الإيجارات'), $R('فواتير تُنشأ من كل جدول دفعات.'), [$R('فواتير إيجار تلقائية'), $R('ضريبة القيمة المضافة عند انطباقها'), $R('رسوم الخدمات والمرافق')]),
            $step('coins', $R('التحصيل'), $R('احصل على مستحقاتك في وقتها.'), [$R('المقبوضات والتحويلات والشيكات'), $R('قائمة المتأخرات والتذكيرات'), $R('مطابقة المدفوعات مع العقود')]),
            $step('gear', $R('الصيانة'), $R('أرضِ المستأجرين وتحكم في التكاليف.'), [$R('طلبات لكل وحدة'), $R('عروض الأسعار وأوامر العمل'), $R('التكلفة لكل مبنى')]),
            $step('calendar', $R('التجديد أو الإخلاء'), $R('لا تفاجئك نهاية أي عقد.'), [$R('تنبيهات التجديد'), $R('التجديد بشروط جديدة'), $R('فحص الإخلاء والإفراج عن التأمين')]),
            $step('chart', $R('تقارير الملاك'), $R('اعرف ما يحققه كل مبنى.'), [$R('الإيرادات والمصروفات لكل عقار'), $R('كشوف الملاك'), $R('تقارير الإشغال والتحصيل')]),
        ],
    ],
],
'assets-management' => [
    'en' => ['process' => ['The asset lifecycle', 'From purchase to disposal', 'Cortex Assets Management follows every asset through its life inside SAP Business One. Pick a stage to see what it does.'],
             'control' => ['Live asset control', 'Every asset, count and approval on one screen', 'Scans arrive as the count moves through your locations, alerts appear and approvals wait in one inbox. Try it.', 'Click a location, start or close a count, and approve transfers and disposals.']],
    'ar' => ['process' => [$R('دورة حياة الأصل'), $R('من الشراء حتى الاستبعاد'), $R('يتابع Cortex Assets Management كل أصل طوال عمره داخل SAP Business One. اختر مرحلة لترى ما تقدمه.')],
             'control' => [$R('متابعة الأصول المباشرة'), $R('كل أصل وجرد واعتماد على شاشة واحدة'), $R('تصل عمليات المسح مع تقدم الجرد في مواقعك وتظهر التنبيهات وتنتظر الاعتمادات في صندوق واحد. جرّبها.'), $R('اضغط على موقع، وابدأ الجرد أو أغلقه، واعتمد النقل والاستبعاد.')]],
    'steps' => [
        'en' => [
            $step('cart', 'Acquisition', 'Every purchase becomes an asset in the right class.', ['Capitalise from purchase invoices', 'Asset classes and useful life', 'Cost, supplier and warranty']),
            $step('box', 'Tagging and register', 'Know exactly what you own.', ['Barcode or QR tags', 'Location and cost centre', 'Photos and documents']),
            $step('users', 'Custody', 'Every asset has someone responsible.', ['Assign to employees or sites', 'Handover and return forms', 'Custody history']),
            $step('chart', 'Depreciation', 'Book value always up to date.', ['Monthly depreciation run', 'Methods per asset class [CONFIRM: depreciation books]', 'Posting to the general ledger']),
            $step('gear', 'Maintenance', 'Look after what you own.', ['Preventive maintenance plans', 'Work orders and repair cost', 'Insurance and warranty alerts']),
            $step('map', 'Transfers', 'Move assets with a full trail.', ['Transfers between sites', 'Cost centre updated', 'Approval workflow']),
            $step('check', 'Physical count', 'Prove every asset is where it should be.', ['Count by scanning with a phone [CONFIRM: mobile counting]', 'Missing and found lists', 'Count differences for review']),
            $step('receipt', 'Disposal', 'Close the asset correctly.', ['Sale or write off', 'Gain or loss posted', 'Disposal approval']),
        ],
        'ar' => [
            $step('cart', $R('الاقتناء'), $R('كل عملية شراء تصبح أصلاً في الفئة الصحيحة.'), [$R('الرسملة من فواتير الشراء'), $R('فئات الأصول والعمر الإنتاجي'), $R('التكلفة والمورد والضمان')]),
            $step('box', $R('الترقيم والسجل'), $R('اعرف بالضبط ما تملكه.'), [$R('ملصقات باركود أو QR'), $R('الموقع ومركز التكلفة'), $R('الصور والمستندات')]),
            $step('users', $R('العهد'), $R('لكل أصل مسؤول عنه.'), [$R('الإسناد للموظفين أو المواقع'), $R('نماذج التسليم والاستلام'), $R('سجل العهد')]),
            $step('chart', $R('الإهلاك'), $R('قيمة دفترية محدثة دائماً.'), [$R('احتساب الإهلاك الشهري'), $R('طرق لكل فئة أصول') . ' [CONFIRM: depreciation books]', $R('الترحيل إلى دفتر الأستاذ')]),
            $step('gear', $R('الصيانة'), $R('حافظ على ما تملكه.'), [$R('خطط الصيانة الوقائية'), $R('أوامر العمل وتكلفة الإصلاح'), $R('تنبيهات التأمين والضمان')]),
            $step('map', $R('النقل'), $R('انقل الأصول مع سجل كامل.'), [$R('النقل بين المواقع'), $R('تحديث مركز التكلفة'), $R('مسار الاعتماد')]),
            $step('check', $R('الجرد'), $R('أثبت أن كل أصل في مكانه.'), [$R('الجرد بالمسح بالجوال') . ' [CONFIRM: mobile counting]', $R('قوائم المفقود والموجود'), $R('فروقات الجرد للمراجعة')]),
            $step('receipt', $R('الاستبعاد'), $R('أغلق الأصل بشكل صحيح.'), [$R('البيع أو الشطب'), $R('ترحيل الربح أو الخسارة'), $R('اعتماد الاستبعاد')]),
        ],
    ],
],
];

// Arabic rooms: every English text in the room swapped for its Arabic (content/cortex-rooms-ar.php); ids, keys and numbers stay
$dict = include __DIR__ . '/cortex-rooms-ar.php';
$toAr = function ($v, $key = null) use (&$toAr, $dict) {
    if (is_array($v)) { foreach ($v as $k => $x) $v[$k] = $toAr($x, is_string($k) ? $k : $key); return $v; }
    if (!is_string($v) || in_array($key, ['id', 'status', 'kind', 'logkind', 'card', 'type'], true)) return $v;
    return $dict[$v] ?? $v;
};
$out = [];
foreach ($headings as $slug => $h) {
    foreach (['en', 'ar'] as $lang) {
        [$pe, $ph, $pi] = $h[$lang]['process'];
        [$ce, $ch, $ci, $hint] = $h[$lang]['control'];
        $out[$slug][$lang] = [
            'process' => ['eyebrow' => $pe, 'h2' => $ph, 'intro' => $pi, 'steps' => $h['steps'][$lang]],
            'control' => ['type' => 'ops_control', 'eyebrow' => $ce, 'h2' => $ch, 'intro' => $ci, 'hint' => $hint, 'room' => $lang === 'ar' ? $toAr($rooms[$slug]) + ['lang' => 'ar'] : $rooms[$slug]],
        ];
    }
}
return $out;
