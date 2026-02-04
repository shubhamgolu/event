<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        .header { background: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-card h3 { margin: 0 0 10px 0; color: #6c757d; font-size: 14px; text-transform: uppercase; }
        .stat-card .value { font-size: 32px; font-weight: bold; color: #333; }
        .stat-card .icon { font-size: 24px; margin-bottom: 10px; }
        .table-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px; border-bottom: 1px solid #dee2e6; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .badge-online { background: #d1ecf1; color: #0c5460; }
        .badge-offline { background: #f8d7da; color: #721c24; }
        .progress { height: 20px; background: #e9ecef; border-radius: 10px; overflow: hidden; }
        .progress-bar { height: 100%; }
        .nav-links { display: flex; gap: 10px; margin-bottom: 20px; }
        .nav-links a { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .nav-links a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Reports Dashboard</h1>
            <p>Event Management System Analytics</p>
            
            <div class="nav-links">
                <a href="{{ route('admin.reports.event-registrations') }}">📅 Event Registrations</a>
                <a href="{{ route('admin.reports.survey-responses') }}">📝 Survey Responses</a>
                <a href="{{ route('admin.reports.certificates') }}">🏆 Certificates</a>
                <a href="{{ route('admin.reports.export.event-registrations') }}">📤 Export CSV</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📅</div>
                <h3>Total Events</h3>
                <div class="value">{{ $stats['total_events'] }}</div>
            </div>
            <div class="stat-card">
                <div class="icon">👥</div>
                <h3>Total Participants</h3>
                <div class="value">{{ $stats['total_participants'] }}</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <h3>Checked In</h3>
                <div class="value">{{ $stats['checked_in_participants'] }}</div>
            </div>
            <div class="stat-card">
                <div class="icon">📝</div>
                <h3>Survey Responses</h3>
                <div class="value">{{ $stats['survey_responses'] }}</div>
            </div>
            <div class="stat-card">
                <div class="icon">🏆</div>
                <h3>Certificates Generated</h3>
                <div class="value">{{ $stats['certificates_generated'] }}</div>
            </div>
        </div>

        <!-- Recent Events Table -->
        <div class="table-container">
            <h2>Recent Events</h2>
            <table>
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Registrations</th>
                        <th>Survey Responses</th>
                        <th>Certificates</th>
                        <th>Response Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentEvents as $event)
                    @php
                        $responseRate = $event->participants_count > 0 
                            ? round(($event->survey_responses_count / $event->participants_count) * 100, 2)
                            : 0;
                        $progressColor = $responseRate > 70 ? '#28a745' : ($responseRate > 40 ? '#ffc107' : '#dc3545');
                    @endphp
                    <tr>
                        <td><strong>{{ $event->name }}</strong></td>
                        <td>{{ $event->date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $event->type === 'online' ? 'online' : 'offline' }}">
                                {{ ucfirst($event->type) }}
                            </span>
                        </td>
                        <td>{{ $event->participants_count }}</td>
                        <td>{{ $event->survey_responses_count }}</td>
                        <td>{{ $event->certificates_count }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="progress" style="flex: 1;">
                                    <div class="progress-bar" style="width: {{ min($responseRate, 100) }}%; background: {{ $progressColor }};"></div>
                                </div>
                                <span>{{ $responseRate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>