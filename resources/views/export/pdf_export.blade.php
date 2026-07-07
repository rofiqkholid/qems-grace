<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Audit Findings Report</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background-color: #fff;
                color: #000;
            }
            .no-print {
                display: none;
            }
            @page {
                size: A4 landscape;
                margin: 0.8cm;
            }
        }
        .truncate-3-lines {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
        }
    </style>
</head>
<body class="bg-white text-slate-800 p-6">
    <div class="flex items-center justify-between border-b pb-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Internal Audit Findings Report</h1>
            <p class="text-sm text-slate-500 mt-1">Generated on {{ date('d F Y H:i') }}</p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded shadow-sm">
                Print / Save PDF
            </button>
            <button onclick="window.close()" class="ml-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded border">
                Close
            </button>
        </div>
    </div>

    <table class="w-full text-left border-collapse text-xs">
        <thead>
            <tr class="bg-slate-100 border-b">
                <th class="p-2 border">No</th>
                <th class="p-2 border">Req Number</th>
                <th class="p-2 border">Department</th>
                <th class="p-2 border">Finding Category</th>
                <th class="p-2 border">Auditor</th>
                <th class="p-2 border">Auditee</th>
                <th class="p-2 border">Finding</th>
                <th class="p-2 border">Note</th>
                <th class="p-2 border">Action Plan</th>
                <th class="p-2 border">Target Date</th>
                <th class="p-2 border">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $row)
                <tr class="border-b hover:bg-slate-50/50">
                    <td class="p-2 border text-center">{{ $index + 1 }}</td>
                    <td class="p-2 border font-semibold">{{ $row->req_number }}</td>
                    <td class="p-2 border">{{ $row->department }}</td>
                    <td class="p-2 border">{{ $row->finding_category }}</td>
                    <td class="p-2 border">{{ $row->auditor }}</td>
                    <td class="p-2 border">{{ $row->auditee }}</td>
                    <td class="p-2 border max-w-[150px]"><div class="truncate-3-lines">{{ $row->finding }}</div></td>
                    <td class="p-2 border max-w-[150px]"><div class="truncate-3-lines">{{ $row->detail_note }}</div></td>
                    <td class="p-2 border max-w-[150px]"><div class="truncate-3-lines">{{ $row->action_plan }}</div></td>
                    <td class="p-2 border">{{ $row->due_date }}</td>
                    <td class="p-2 border">{{ $row->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="p-4 text-center text-slate-400">No records found</td>
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
