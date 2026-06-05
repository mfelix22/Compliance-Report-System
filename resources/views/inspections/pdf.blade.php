<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $inspection->reference_no }} – Inspection Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

        .page-header { background: #1b6840; color: #fff; padding: 18px 24px; margin-bottom: 20px; }
        .page-header h1 { font-size: 18px; font-weight: bold; }
        .page-header p { font-size: 11px; opacity: 0.85; margin-top: 3px; }

        .section { margin: 0 24px 18px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em;
            color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 10px; }

        .meta-grid { display: table; width: 100%; border-collapse: collapse; }
        .meta-row { display: table-row; }
        .meta-label { display: table-cell; width: 140px; color: #6b7280; padding: 3px 0; }
        .meta-value { display: table-cell; font-weight: bold; color: #111827; padding: 3px 0; }

        .summary-boxes { display: table; width: 100%; border-collapse: separate; border-spacing: 6px; }
        .summary-box { display: table-cell; text-align: center; border: 1px solid #e5e7eb;
            border-radius: 6px; padding: 10px 6px; width: 25%; }
        .summary-box .num { font-size: 22px; font-weight: bold; }
        .summary-box .lbl { font-size: 9px; color: #6b7280; margin-top: 2px; }
        .box-assessed .num { color: #374151; }
        .box-c .num { color: #065f46; }
        .box-nc .num { color: #dc2626; }
        .box-na .num { color: #9ca3af; }

        table.findings-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.findings-table thead tr { background: #e8f5ee; }
        table.findings-table th { padding: 7px 8px; text-align: left; font-size: 9px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.06em; color: #374151;
            border-bottom: 2px solid #1b6840; }
        table.findings-table td { padding: 7px 8px; vertical-align: top;
            border-bottom: 1px solid #f3f4f6; }
        table.findings-table tr:nth-child(even) td { background: #f9fafb; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 9px; font-weight: bold; }
        .badge-open { background: #fef3c7; color: #b45309; }
        .badge-closed { background: #d1fae5; color: #065f46; }
        .badge-c { background: #d1fae5; color: #065f46; }
        .badge-nc { background: #fee2e2; color: #b91c1c; }
        .badge-na { background: #f3f4f6; color: #6b7280; }
        .badge-complied { background: #d1fae5; color: #065f46; }
        .badge-not_complied { background: #fee2e2; color: #b91c1c; }
        .badge-pending { background: #e0e7ff; color: #3730a3; }

        .checklist-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .checklist-table thead tr { background: #e8f5ee; }
        .checklist-table th { padding: 6px 8px; text-align: left; font-size: 9px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.06em; color: #374151;
            border-bottom: 2px solid #1b6840; }
        .checklist-table td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .checklist-table tr:nth-child(even) td { background: #f9fafb; }

        .footer { margin: 30px 24px 16px; border-top: 1px solid #e5e7eb; padding-top: 10px;
            font-size: 9px; color: #9ca3af; display: table; width: calc(100% - 48px); }
        .footer-left { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }

        .no-findings { text-align: center; padding: 16px; color: #9ca3af; font-style: italic; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="page-header">
        <h1>Food Safety Inspection Report</h1>
        <p>{{ $inspection->reference_no }} &nbsp;·&nbsp; {{ $inspection->outlet->name }} &nbsp;·&nbsp; {{ $inspection->inspection_date->format('d M Y') }}</p>
    </div>

    {{-- Inspection Details --}}
    <div class="section">
        <div class="section-title">Inspection Details</div>
        <div class="meta-grid">
            <div class="meta-row">
                <div class="meta-label">Title</div>
                <div class="meta-value">{{ $inspection->title }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Reference No.</div>
                <div class="meta-value">{{ $inspection->reference_no }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Outlet</div>
                <div class="meta-value">{{ $inspection->outlet->name }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Inspection Date</div>
                <div class="meta-value">{{ $inspection->inspection_date->format('d F Y') }}{{ $inspection->audit_time ? ' · ' . $inspection->audit_time : '' }}</div>
            </div>
            <div class="meta-row">
                <div class="meta-label">Auditor(s)</div>
                <div class="meta-value">{{ $inspection->auditors->pluck('name')->join(', ') }}</div>
            </div>
            @if ($inspection->reporter_name)
            <div class="meta-row">
                <div class="meta-label">Reported by</div>
                <div class="meta-value">{{ $inspection->reporter_name }}</div>
            </div>
            @endif
            <div class="meta-row">
                <div class="meta-label">Status</div>
                <div class="meta-value">
                    <span class="badge {{ $inspection->status === 'open' ? 'badge-open' : 'badge-closed' }}">
                        {{ ucfirst($inspection->status) }}
                    </span>
                </div>
            </div>
            @if ($inspection->notes)
            <div class="meta-row">
                <div class="meta-label">Notes</div>
                <div class="meta-value" style="font-weight:normal; color:#374151;">{{ $inspection->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Compliance Summary --}}
    @php
        $totalCategories = $policies->count();
        $assessed        = $statusByPolicy->count();
        $cCount          = $statusByPolicy->where('status', 'C')->count();
        $ncCount         = $statusByPolicy->where('status', 'NC')->count();
        $naCount         = $statusByPolicy->where('status', 'NA')->count();
    @endphp
    <div class="section">
        <div class="section-title">Compliance Summary</div>
        <div class="summary-boxes">
            <div class="summary-box box-assessed">
                <div class="num">{{ $assessed }}/{{ $totalCategories }}</div>
                <div class="lbl">Assessed</div>
            </div>
            <div class="summary-box box-c">
                <div class="num">{{ $cCount }}</div>
                <div class="lbl">Compliant</div>
            </div>
            <div class="summary-box box-nc">
                <div class="num">{{ $ncCount }}</div>
                <div class="lbl">Non-Compliant</div>
            </div>
            <div class="summary-box box-na">
                <div class="num">{{ $naCount }}</div>
                <div class="lbl">N/A</div>
            </div>
        </div>
    </div>

    {{-- Checklist --}}
    <div class="section">
        <div class="section-title">Audit Checklist</div>
        <table class="checklist-table">
            <thead>
                <tr>
                    <th style="width:32px">#</th>
                    <th>Category</th>
                    <th style="width:90px">Status</th>
                    <th style="width:80px">Findings</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($policies as $policy)
                    @php
                        $catStatus   = $statusByPolicy->get($policy->id);
                        $catFindings = $findingsByPolicy->get($policy->id, collect());
                    @endphp
                    <tr>
                        <td style="color:#9ca3af; font-family:monospace;">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $policy->name }}</td>
                        <td>
                            @if ($catStatus)
                                <span class="badge badge-{{ strtolower($catStatus->status) }}">{{ $catStatus->status }}</span>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td style="color:#374151;">{{ $catFindings->count() ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Findings --}}
    @if ($inspection->findings->count() > 0)
    <div class="section">
        <div class="section-title">Findings ({{ $inspection->findings->count() }})</div>
        <table class="findings-table">
            <thead>
                <tr>
                    <th style="width:28px">#</th>
                    <th>Finding</th>
                    <th style="width:75px">Root Cause</th>
                    <th style="width:90px">Department</th>
                    <th style="width:70px">Due Date</th>
                    <th style="width:55px">Status</th>
                    <th style="width:75px">Verification</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inspection->findings->sortBy('number') as $finding)
                <tr>
                    <td style="color:#9ca3af; font-family:monospace;">{{ $finding->number }}</td>
                    <td>
                        {{ $finding->finding }}
                        @if ($finding->corrective_action)
                            <div style="margin-top:3px; color:#6b7280; font-size:9px;"><strong>CA:</strong> {{ $finding->corrective_action }}</div>
                        @endif
                        @if ($finding->preventive_action)
                            <div style="margin-top:2px; color:#6b7280; font-size:9px;"><strong>PA:</strong> {{ $finding->preventive_action }}</div>
                        @endif
                    </td>
                    <td>{{ ucfirst($finding->root_cause) }}</td>
                    <td>{{ $finding->department?->name ?? '—' }}</td>
                    <td>{{ $finding->due_date?->format('d M Y') ?? '—' }}</td>
                    <td><span class="badge badge-{{ $finding->status }}">{{ ucfirst($finding->status) }}</span></td>
                    <td>
                        @php $vs = $finding->verification_status; @endphp
                        <span class="badge badge-{{ $vs }}">{{ ucfirst(str_replace('_', ' ', $vs)) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">Food Control – Compliance Report System</div>
        <div class="footer-right">Generated {{ now()->format('d M Y, H:i') }}</div>
    </div>

</body>
</html>
