<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap CAR Internal - Eksternal Audit</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5cm;
        }
        @media print {
            html, body {
                width: 297mm;
                height: 210mm;
            }
            body {
                background-color: #fff;
                color: #000;
            }
            .no-print {
                display: none !important;
            }
        }
        table {
            border: 1px solid #000000 !important;
            table-layout: fixed;
            width: 100%;
        }
        td, th {
            word-break: break-word;
            font-size: 7.5px;
            line-height: 1.2;
            padding: 4px !important;
        }
        th {
            background-color: #1F4F7A !important;
            color: #FFFFFF !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    </style>
</head>
<body class="bg-white text-slate-800 p-4">
    <!-- Print Buttons (hidden when printing) -->
    <div class="flex items-center justify-between no-print mb-4 pb-2 border-b">
        <div class="text-xs text-slate-500">Generated on {{ date('d F Y H:i') }}</div>
        <div>
            <button onclick="window.print()" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded shadow-sm">
                Print / Save PDF
            </button>
            <button onclick="window.close()" class="ml-2 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded border">
                Close
            </button>
        </div>
    </div>

    <!-- Header Box exactly like Excel template -->
    <div class="w-full flex border border-black mb-6 text-center align-middle" style="height: 65px;">
        <!-- Left: Logo & Company Name -->
        <div class="w-1/5 flex flex-col justify-center items-center border-r border-black p-1">
            <img src="{{ asset('image/sai_logo.png') }}" class="h-8 object-contain mb-0.5">
            <div class="font-bold text-[7px] leading-tight text-slate-700">PT. SUMMIT ADYAWINSA INDONESIA</div>
        </div>
        <!-- Middle: Title -->
        <div class="w-3/5 flex justify-center items-center font-bold text-lg text-slate-800 uppercase tracking-wide">
            Rekap CAR Internal /Eksternal Audit
        </div>
        <!-- Right: Document Info -->
        <div class="w-1/5 flex flex-col border-l border-black text-left text-[7px] leading-tight text-slate-700 font-semibold">
            <div class="flex-1 flex items-center px-1.5 border-b border-black">Nomor Dokumen : FO-08-02</div>
            <div class="flex-1 flex items-center px-1.5 border-b border-black">Department : MANAGEMENT</div>
            <div class="flex-1 flex items-center px-1.5 border-b border-black">Tanggal Terbit : 27 November 2014</div>
            <div class="flex-1 flex items-center px-1.5 border-b border-black">Nomor Revisi : 01</div>
            <div class="flex-1 flex items-center px-1.5">Halaman : 1 dari 1</div>
        </div>
    </div>

    <!-- Data Table -->
    <table class="w-full text-left border-collapse border border-slate-300">
        <thead>
            <tr style="background-color: #1F4F7A; color: #FFFFFF;" class="border-b border-slate-300">
                <th class="border border-slate-300 font-semibold text-center" style="width: 2.5%;">No</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 6.5%;">DATE OF AUDIT</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 8%;">CAR NO</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 8%;">CLAUSE TITLE</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 5.5%;">Audit Category</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 7.5%;">Part No/Part Name/Process Checked</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 3.5%;">Dept</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 5.5%;">Auditee</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 5.5%;">Auditee Superior</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 5.5%;">Auditor</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 5.5%;">FINDING CATEGORY</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 10.5%;">Findings</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 10.5%;">Corrective Action</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 6.5%;">Deadline Correction</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 10.5%;">Preventive Action</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 6.5%;">Deadline Corrective Action</th>
                <th class="border border-slate-300 font-semibold text-center" style="width: 6.5%;">CAR STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
                @php
                    $corrective = array_filter([$row->corrective_action_one, $row->corrective_action_two, $row->corrective_action_three]);
                    $preventive = array_filter([$row->preventive_action_one, $row->preventive_action_two, $row->preventive_action_three]);
                    
                    // Status calculation exactly like Excel
                    $statusText = '-';
                    if (($row->finding_category ?? '') === 'OFI') {
                        $statusText = '-';
                    } elseif ($row->status === 'Draft' || ($row->action_status ?? '') === 'draft') {
                        $statusText = 'Draft (Auditee)';
                    } elseif ($row->status === 'Under Review' || ($row->action_status ?? '') === 'open_verif') {
                        $statusText = 'Waiting Superior Approval';
                    } elseif ($row->status === 'Need Verification' || ($row->action_status ?? '') === 'approve_superior') {
                        $statusText = 'Waiting Auditor Approval';
                    } elseif ($row->status === 'Closed' && empty($row->qmr_approved_at)) {
                        $statusText = 'Waiting QMR Approval';
                    } elseif ($row->status === 'Closed' && !empty($row->qmr_approved_at)) {
                        $statusText = 'Closed';
                    } else {
                        $statusText = $row->status ?? '-';
                    }
                @endphp
                <tr class="border-b border-slate-200 hover:bg-slate-50/50">
                    <td class="border border-slate-200 text-center">{{ $index + 1 }}</td>
                    <td class="border border-slate-200 white-space-nowrap">{{ $row->audit_date ? \Carbon\Carbon::parse($row->audit_date)->format('d M Y') : '-' }}</td>
                    <td class="border border-slate-200 font-medium">{{ $row->req_number ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->clause_title ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->audit_type ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->scope_item ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->department ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->auditee ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->superior_name ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->auditor ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->finding_category ?? '-' }}</td>
                    <td class="border border-slate-200">{{ $row->finding ?? '-' }}</td>
                    <td class="border border-slate-200">
                        @if(count($corrective) > 0)
                            @foreach($corrective as $cIdx => $act)
                                <div>{{ ($cIdx + 1) }}. {{ $act }}</div>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td class="border border-slate-200">{{ $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d M Y') : '-' }}</td>
                    <td class="border border-slate-200">
                        @if(count($preventive) > 0)
                            @foreach($preventive as $pIdx => $act)
                                <div>{{ ($pIdx + 1) }}. {{ $act }}</div>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td class="border border-slate-200 text-center">{{ $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d M Y') : '-' }}</td>
                    <td class="border border-slate-200 font-medium">{{ $statusText }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="17" class="p-4 text-center text-slate-400">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
