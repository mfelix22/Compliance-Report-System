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
        .checklist-table td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .checklist-table tr.cat-row:nth-child(odd) td { background: #fff; }
        .checklist-table tr.cat-row:nth-child(even) td { background: #f9fafb; }

        .finding-sub-row td { background: #fffbeb !important; border-bottom: 1px solid #fde68a; padding: 5px 8px 5px 28px; }
        .finding-sub-row td.finding-num { color: #b45309; font-family: monospace; font-weight: bold; width: 32px; padding-left: 12px; }
        .finding-detail { font-size: 9.5px; color: #1a1a1a; }
        .finding-detail .finding-desc { font-weight: bold; margin-bottom: 2px; }
        .finding-detail .finding-meta { color: #6b7280; font-size: 9px; }
        .finding-detail .finding-actions { color: #374151; font-size: 9px; margin-top: 2px; }

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

    {{-- Checklist with inline findings --}}
    <div class="section">
        <div class="section-title">Audit Checklist &amp; Findings</div>
        <table class="checklist-table">
            <thead>
                <tr>
                    <th style="width:32px">#</th>
                    <th>Category / Finding</th>
                    <th style="width:90px">Status</th>
                    <th style="width:90px">Dept / Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($policies as $policy)
                    @php
                        $catStatus   = $statusByPolicy->get($policy->id);
                        $catFindings = $findingsByPolicy->get($policy->id, collect());
                    @endphp

                    {{-- Category row --}}
                    <tr class="cat-row">
                        <td style="color:#9ca3af; font-family:monospace; font-weight:bold;">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td style="font-weight:bold; color:#111827;">{{ $policy->name }}</td>
                        <td>
                            @if ($catStatus)
                                <span class="badge badge-{{ strtolower($catStatus->status) }}">{{ $catStatus->status }}</span>
                            @else
                                <span style="color:#d1d5db;">—</span>
                            @endif
                        </td>
                        <td style="color:#6b7280; font-size:9px;">
                            @if($catFindings->count() > 0)
                                {{ $catFindings->count() }} finding(s)
                            @else
                                —
                            @endif
                        </td>
                    </tr>

                    {{-- Inline findings under NC/NA categories --}}
                    @if ($catFindings->count() > 0)
                        @foreach ($catFindings->sortBy('number') as $finding)
                        <tr class="finding-sub-row">
                            <td class="finding-num">{{ $finding->number }}</td>
                            <td colspan="3">
                                <div class="finding-detail">
                                    <div class="finding-desc">{{ $finding->finding }}</div>
                                    @if(isset($photoPaths[$finding->id]))
                                        <img src="{{ $photoPaths[$finding->id] }}" alt="Finding photo" style="max-width:280px; max-height:180px; margin:4px 0; border-radius:4px; border:1px solid #e5e7eb;">
                                    @endif
                                    <div class="finding-meta">
                                        @if ($finding->department)
                                            {{ $finding->department->name }}
                                        @endif
                                        @if ($finding->due_date)
                                            &nbsp;·&nbsp; Due: {{ $finding->due_date->format('d M Y') }}
                                        @endif
                                        @if ($finding->root_cause)
                                            &nbsp;·&nbsp; Root cause: {{ ucfirst($finding->root_cause) }}
                                        @endif
                                        &nbsp;·&nbsp;
                                        <span class="badge badge-{{ $finding->status }}">{{ ucfirst($finding->status) }}</span>
                                        @php $vs = $finding->verification_status; @endphp
                                        &nbsp;<span class="badge badge-{{ $vs }}">{{ ucfirst(str_replace('_', ' ', $vs)) }}</span>
                                    </div>
                                    @if ($finding->corrective_action)
                                        <div class="finding-actions"><strong>CA:</strong> {{ $finding->corrective_action }}</div>
                                    @endif
                                    @if ($finding->preventive_action)
                                        <div class="finding-actions"><strong>PA:</strong> {{ $finding->preventive_action }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif

                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">Food Control – Compliance Report System</div>
        <div class="footer-right">Generated {{ now()->format('d M Y, H:i') }}</div>
    </div>

</body>
</html>
