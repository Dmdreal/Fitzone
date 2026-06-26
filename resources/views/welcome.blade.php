<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fitzone Gym Management - Kenswed</title>
    <style>
        :root {
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --panel: #ffffff;
            --soft: #f6f8fb;
            --blue: #1263e6;
            --green: #22c55e;
            --amber: #f59e0b;
            --red: #ef4444;
            --violet: #7c3aed;
            --nav: #102033;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background: #eef2f7;
        }

        .app {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 220px 1fr;
        }

        .sidebar {
            background: linear-gradient(180deg, #152941 0%, #0b1826 100%);
            color: #dbeafe;
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 26px;
            color: #fff;
            font-weight: 800;
            letter-spacing: 0;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #ef4444;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 10px 24px rgba(239, 68, 68, .28);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 7px;
            color: #cbd5e1;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .nav-item.active {
            background: linear-gradient(90deg, #1d65df, #3b82f6);
            color: #fff;
            box-shadow: 0 12px 24px rgba(29, 101, 223, .24);
        }

        .main { padding: 24px; }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .search {
            width: min(420px, 100%);
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px 14px;
            color: var(--muted);
            box-shadow: 0 8px 30px rgba(15, 23, 42, .05);
        }

        .admin-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
        }

        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            display: grid;
            place-items: center;
            color: #fff;
            font-weight: 800;
        }

        h1, h2, h3 { margin: 0; letter-spacing: 0; }
        h1 { font-size: 24px; margin-bottom: 18px; }
        h2 { font-size: 18px; margin-bottom: 14px; }
        h3 { font-size: 15px; margin-bottom: 12px; }

        .section {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .06);
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .metric {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 22px;
        }

        .metric small, .muted { color: var(--muted); }
        .metric strong { display: block; font-size: 25px; margin: 3px 0; }
        .up { color: #16a34a; font-weight: 700; font-size: 12px; }

        .grid-2 {
            display: grid;
            grid-template-columns: 1.15fr .85fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }

        .wide-grid {
            display: grid;
            grid-template-columns: 1.1fr 1fr 1fr;
            gap: 16px;
        }

        .chart {
            height: 230px;
            position: relative;
            border-bottom: 1px solid var(--line);
            background:
                linear-gradient(to top, rgba(18, 99, 230, .1), transparent 68%),
                repeating-linear-gradient(to top, transparent 0 45px, #edf2f7 46px);
            border-radius: 8px;
            overflow: hidden;
        }

        .line {
            position: absolute;
            inset: 18px 20px 30px 20px;
        }

        .line svg {
            width: 100%;
            height: 100%;
        }

        .months {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            margin-top: 10px;
            color: var(--muted);
            font-size: 12px;
            text-align: center;
        }

        .donut-wrap {
            display: grid;
            grid-template-columns: 170px 1fr;
            align-items: center;
            gap: 22px;
        }

        .donut {
            width: 160px;
            aspect-ratio: 1;
            border-radius: 50%;
            background: conic-gradient(var(--green) 0 77%, var(--red) 77% 89%, var(--amber) 89% 100%);
            display: grid;
            place-items: center;
        }

        .donut div {
            width: 94px;
            aspect-ratio: 1;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            text-align: center;
            font-weight: 800;
            font-size: 24px;
        }

        .legend {
            display: grid;
            gap: 12px;
            font-size: 14px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            display: inline-block;
            margin-right: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
        }

        th { color: #475569; font-size: 12px; }

        .status {
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .paid, .active-status { background: #dcfce7; color: #15803d; }
        .inactive { background: #fee2e2; color: #b91c1c; }

        .person {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 14px;
        }

        .person .avatar { width: 42px; height: 42px; font-size: 14px; }

        .member-card {
            display: grid;
            grid-template-columns: 130px 1fr;
            min-height: 270px;
            overflow: hidden;
        }

        .mini-sidebar {
            background: var(--nav);
            color: #dbeafe;
            padding: 18px 12px;
        }

        .mini-sidebar .nav-item {
            padding: 9px 8px;
            font-size: 12px;
        }

        .member-body { padding: 18px; }

        .blue-card {
            color: #fff;
            border-radius: 8px;
            padding: 18px;
            background: linear-gradient(135deg, #1263e6, #0ea5e9);
            min-height: 112px;
        }

        .mini-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 14px 0;
        }

        .mini-stat {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
            background: #fff;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        label {
            display: grid;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
        }

        input, select {
            border: 1px solid var(--line);
            border-radius: 7px;
            padding: 11px 12px;
            font: inherit;
            width: 100%;
            color: #475569;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 14px;
        }

        .btn {
            border: 0;
            border-radius: 7px;
            padding: 11px 16px;
            font-weight: 800;
            color: #fff;
            background: var(--blue);
            cursor: pointer;
        }

        .btn.secondary { color: #334155; background: #f1f5f9; }
        .plans {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .plan {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 16px;
            display: grid;
            gap: 10px;
        }

        .plan.featured {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .12);
        }

        .price { font-size: 22px; font-weight: 900; }
        .checks { display: grid; gap: 7px; color: #475569; font-size: 13px; }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            font-size: 12px;
        }

        .day {
            min-height: 32px;
            border-radius: 7px;
            display: grid;
            place-items: center;
            background: #f8fafc;
        }

        .day.present { background: #dcfce7; color: #15803d; font-weight: 800; }
        .day.absent { background: #fee2e2; color: #b91c1c; font-weight: 800; }

        .workout-list { display: grid; gap: 8px; }
        .workout {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .workout.active { border-color: #93c5fd; background: #eff6ff; }

        .flow {
            display: grid;
            gap: 12px;
            place-items: center;
            text-align: center;
        }

        .flow-row {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .flow-box {
            border: 1px solid #93c5fd;
            background: #eff6ff;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 800;
            min-width: 120px;
        }

        .flow-box.green { border-color: #86efac; background: #dcfce7; }
        .flow-box.amber { border-color: #fcd34d; background: #fef3c7; }
        .flow-box.red { border-color: #fca5a5; background: #fee2e2; }

        .connector {
            width: 2px;
            height: 18px;
            background: #94a3b8;
        }

        @media (max-width: 1100px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { position: static; height: auto; }
            .metrics, .grid-2, .grid-3, .wide-grid { grid-template-columns: 1fr; }
            .member-card { grid-template-columns: 1fr; }
            .mini-sidebar { display: none; }
        }

        @media (max-width: 720px) {
            .main { padding: 14px; }
            .topbar { align-items: stretch; flex-direction: column; }
            .metrics, .mini-stats, .plans, .form-grid, .donut-wrap { grid-template-columns: 1fr; }
            .section { padding: 14px; }
            table { min-width: 640px; }
            .table-scroll { overflow-x: auto; }
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="brand"><span class="brand-mark">G</span><span>GYM<br><small>FITNESS</small></span></div>
            @foreach (['Dashboard', 'Users', 'Trainers', 'Members', 'Plans', 'Payments', 'Attendance', 'Workouts', 'Reports', 'Settings'] as $item)
                <div class="nav-item {{ $loop->first ? 'active' : '' }}"><span>{{ ['⌂','♙','◆','●','▣','₹','◴','☊','▤','⚙'][$loop->index] }}</span>{{ $item }}</div>
            @endforeach
        </aside>

        <main class="main">
            <div class="topbar">
                <input class="search" value="" placeholder="Search members, plans, payments...">
                <div class="admin-chip"><span>🔔</span><span>💬</span><span class="avatar">A</span><strong>Admin</strong></div>
            </div>

            <h1>Gym Management System</h1>

            <section class="metrics">
                <article class="metric"><div class="metric-icon" style="background:#dbeafe;color:var(--blue)">♙</div><div><small>Total Members</small><strong>256</strong><span class="up">+12 this month</span></div></article>
                <article class="metric"><div class="metric-icon" style="background:#dcfce7;color:var(--green)">●</div><div><small>Active Members</small><strong>198</strong><span class="up">+8 this month</span></div></article>
                <article class="metric"><div class="metric-icon" style="background:#ede9fe;color:var(--violet)">◆</div><div><small>Total Trainers</small><strong>12</strong><span class="up">+1 this month</span></div></article>
                <article class="metric"><div class="metric-icon" style="background:#ffedd5;color:var(--amber)">₹</div><div><small>Monthly Revenue</small><strong>₹ 2,45,000</strong><span class="up">+18% this month</span></div></article>
            </section>

            <div class="grid-2">
                <section class="section">
                    <h2>Monthly Revenue</h2>
                    <div class="chart">
                        <div class="line">
                            <svg viewBox="0 0 700 220" preserveAspectRatio="none" aria-hidden="true">
                                <polyline points="0,170 105,138 210,140 315,78 420,132 525,82 610,112 700,36" fill="none" stroke="#1263e6" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                                <polygon points="0,170 105,138 210,140 315,78 420,132 525,82 610,112 700,36 700,220 0,220" fill="rgba(18,99,230,.14)"/>
                            </svg>
                        </div>
                    </div>
                    <div class="months"><span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span></div>
                </section>

                <section class="section">
                    <h2>Membership Status</h2>
                    <div class="donut-wrap">
                        <div class="donut"><div>256<br><small>Total</small></div></div>
                        <div class="legend">
                            <span><i class="dot" style="background:var(--green)"></i>Active <strong>198</strong> (77%)</span>
                            <span><i class="dot" style="background:var(--red)"></i>Expired <strong>32</strong> (12%)</span>
                            <span><i class="dot" style="background:var(--amber)"></i>Upcoming <strong>26</strong> (11%)</span>
                        </div>
                    </div>
                </section>
            </div>

            <div class="grid-3">
                <section class="section member-card">
                    <div class="mini-sidebar">
                        @foreach (['Dashboard', 'My Profile', 'My Plan', 'Workouts', 'Attendance', 'Payments'] as $item)
                            <div class="nav-item {{ $loop->first ? 'active' : '' }}">{{ $item }}</div>
                        @endforeach
                    </div>
                    <div class="member-body">
                        <div class="person"><span class="avatar">AV</span><div><small>Welcome back,</small><h3>Amit Verma</h3></div></div>
                        <div class="blue-card"><small>Active Plan</small><h2>Gold Plan</h2><strong>Valid till 20 Aug 2024</strong></div>
                        <div class="mini-stats">
                            <div class="mini-stat"><small>Total Workouts</small><h3>24</h3></div>
                            <div class="mini-stat"><small>Attendance</small><h3>18 / 24</h3></div>
                            <div class="mini-stat"><small>Next Payment</small><h3>₹ 5,000</h3></div>
                        </div>
                        <h3>Today's Workout</h3>
                        <p class="muted">Chest & Shoulder • Bench Press • Shoulder Press • Push Ups</p>
                    </div>
                </section>

                <section class="section">
                    <h2>Trainer Dashboard</h2>
                    <div class="person"><span class="avatar">RT</span><div><small>Welcome back,</small><h3>Rahul Trainer</h3></div></div>
                    <div class="mini-stats">
                        <div class="mini-stat"><small>Total Members</small><h3>38</h3></div>
                        <div class="mini-stat"><small>Today's Sessions</small><h3>6</h3></div>
                        <div class="mini-stat"><small>Attendance</small><h3>32 / 38</h3></div>
                    </div>
                    <div class="person"><span class="avatar">AV</span><div><strong>Amit Verma</strong><br><small>Gold Plan</small></div></div>
                    <div class="person"><span class="avatar">NG</span><div><strong>Neha Gupta</strong><br><small>Silver Plan</small></div></div>
                </section>

                <section class="section">
                    <h2>Flow Chart</h2>
                    <div class="flow">
                        <div class="flow-box green">Start</div><div class="connector"></div>
                        <div class="flow-box">Login / Register</div><div class="connector"></div>
                        <div class="flow-box">User Role</div>
                        <div class="flow-row">
                            <div class="flow-box amber">Admin</div>
                            <div class="flow-box">Trainer</div>
                            <div class="flow-box green">Member</div>
                        </div>
                        <div class="flow-box red">Logout</div>
                    </div>
                </section>
            </div>

            <div class="grid-2">
                <section class="section">
                    <h2>Members List</h2>
                    <div class="table-scroll">
                        <table>
                            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Plan</th><th>Status</th><th>Action</th></tr></thead>
                            <tbody>
                                <tr><td>Amit Verma</td><td>amit@email.com</td><td>9876543210</td><td>Gold Plan</td><td><span class="status active-status">Active</span></td><td>•••</td></tr>
                                <tr><td>Priya Singh</td><td>priya@gmail.com</td><td>9876543211</td><td>Silver Plan</td><td><span class="status active-status">Active</span></td><td>•••</td></tr>
                                <tr><td>Rahul Sharma</td><td>rahul@gmail.com</td><td>9876543212</td><td>Gold Plan</td><td><span class="status active-status">Active</span></td><td>•••</td></tr>
                                <tr><td>Neha Gupta</td><td>neha@gmail.com</td><td>9876543213</td><td>Basic Plan</td><td><span class="status inactive">Inactive</span></td><td>•••</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="section">
                    <h2>Add New Member</h2>
                    <form>
                        <div class="form-grid">
                            <label>Full Name<input placeholder="Enter full name"></label>
                            <label>Email<input placeholder="Enter email"></label>
                            <label>Phone<input placeholder="Enter phone number"></label>
                            <label>Select Plan<select><option>Gold Plan</option><option>Silver Plan</option><option>Basic Plan</option></select></label>
                            <label>Trainer<select><option>Rahul Trainer</option><option>Sonia Coach</option></select></label>
                            <label>Start Date<input type="date"></label>
                        </div>
                        <div class="actions"><button class="btn secondary" type="button">Cancel</button><button class="btn" type="button">Save Member</button></div>
                    </form>
                </section>
            </div>

            <div class="wide-grid">
                <section class="section">
                    <h2>Plans / Memberships</h2>
                    <div class="plans">
                        <div class="plan"><h3>Basic Plan</h3><div class="price">₹ 1,000 <small>/month</small></div><div class="checks"><span>✓ Gym Access</span><span>✓ Basic Equipment</span><span>✓ 1 Day Pass</span></div><button class="btn">Choose Plan</button></div>
                        <div class="plan"><h3>Silver Plan</h3><div class="price">₹ 2,000 <small>/month</small></div><div class="checks"><span>✓ Personal Training</span><span>✓ 3 Day Pass</span><span>✓ Cardio Access</span></div><button class="btn" style="background:var(--green)">Choose Plan</button></div>
                        <div class="plan featured"><h3>Gold Plan</h3><div class="price">₹ 3,500 <small>/month</small></div><div class="checks"><span>✓ All Equipment</span><span>✓ Personal Training</span><span>✓ Nutrition Guide</span></div><button class="btn" style="background:var(--amber)">Choose Plan</button></div>
                    </div>
                </section>

                <section class="section">
                    <h2>Attendance</h2>
                    <div class="calendar">
                        @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat', '26','27','28','29','30','1','2','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30'] as $day)
                            <div class="day {{ in_array($day, ['1','15','19','22','26','29']) ? 'present' : (in_array($day, ['10','16']) ? 'absent' : '') }}">{{ $day }}</div>
                        @endforeach
                    </div>
                    <p class="muted" style="margin-bottom:0"><strong style="color:var(--ink)">75%</strong> attendance • 18 present • 4 absent</p>
                </section>

                <section class="section">
                    <h2>Workouts</h2>
                    <div class="workout-list">
                        <div class="workout active"><strong>Chest Day</strong><small>5 Exercises</small></div>
                        <div class="workout"><strong>Back Day</strong><small>5 Exercises</small></div>
                        <div class="workout"><strong>Leg Day</strong><small>6 Exercises</small></div>
                        <div class="workout"><strong>Shoulder Day</strong><small>4 Exercises</small></div>
                    </div>
                    <table style="margin-top:12px">
                        <tbody>
                            <tr><td>Bench Press</td><td>4 sets</td><td>12 reps</td></tr>
                            <tr><td>Incline Dumbbell Press</td><td>4 sets</td><td>12 reps</td></tr>
                            <tr><td>Push Ups</td><td>3 sets</td><td>15 reps</td></tr>
                        </tbody>
                    </table>
                </section>
            </div>

            <section class="section" style="margin-top:16px">
                <h2>Payment History</h2>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>Date</th><th>Plan</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr><td>12 May 2024</td><td>Gold Plan</td><td>₹ 5,000</td><td>UPI</td><td><span class="status paid">Paid</span></td></tr>
                            <tr><td>12 Apr 2024</td><td>Gold Plan</td><td>₹ 5,000</td><td>Card</td><td><span class="status paid">Paid</span></td></tr>
                            <tr><td>12 Mar 2024</td><td>Gold Plan</td><td>₹ 5,000</td><td>UPI</td><td><span class="status paid">Paid</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
