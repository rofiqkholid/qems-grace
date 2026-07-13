<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internal Audit Findings Export</title>
</head>
<body>
    <!-- Header Block Table -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="3" rowspan="4" align="center" valign="middle" style="border: 1px solid #000; font-weight: bold; font-size: 9px; text-align: center; vertical-align: middle;">
                <img src="{{ public_path('image/sai_logo.png') }}" width="80" height="40" style="display: block; margin: 0 auto;"><br>
                PT. SUMMIT ADYAWINSA INDONESIA
            </td>
            <td colspan="11" rowspan="4" align="center" valign="middle" style="border: 1px solid #000; font-size: 18px; font-weight: bold; text-align: center; vertical-align: middle;">
                Rekap CAR Internal /Eksternal Audit
            </td>
            <td colspan="4" style="border: 1px solid #000; font-size: 9px; font-weight: bold; vertical-align: middle;">Nomor Dokumen : FO-08-02</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid #000; font-size: 9px; font-weight: bold; vertical-align: middle;">Department : MANAGEMENT</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid #000; font-size: 9px; font-weight: bold; vertical-align: middle;">Tanggal Terbit : 27 November 2014</td>
        </tr>
        <tr>
            <td colspan="4" style="border: 1px solid #000; font-size: 9px; font-weight: bold; vertical-align: middle;">Nomor Revisi : 01</td>
        </tr>
        <tr>
            <td colspan="3" style="border: none;"></td>
            <td colspan="11" style="border: none;"></td>
            <td colspan="4" style="border: 1px solid #000; font-size: 9px; font-weight: bold; vertical-align: middle;">Halaman : 1 dari 1</td>
        </tr>
    </table>

    <br>

    <!-- Data Table -->
    <table border="1" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">No</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">DATE OF AUDIT</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">CAR NO</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">CLAUSE NO</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Audit Category</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Part No/Part Name/Process Checked</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Dept</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Auditee</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Auditee Superior</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Auditor</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">FINDING CATEGOR</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Findings</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">DEADLINE CAR</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">CAR STATUS</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Corrective Action</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Deadline Correction</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Preventive Action</th>
                <th style="font-weight: bold; background-color: #1F4E78; color: #FFFFFF; text-align: center; border: 1px solid #000;">Deadline Corrective</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
                @php
                    $corrective = array_filter([$row->corrective_action_one, $row->corrective_action_two, $row->corrective_action_three]);
                    $preventive = array_filter([$row->preventive_action_one, $row->preventive_action_two, $row->preventive_action_three]);
                    
                    $corrective_str = '';
                    foreach($corrective as $cIdx => $act) {
                        $corrective_str .= ($cIdx + 1) . ". " . $act . "\n";
                    }
                    $corrective_str = rtrim($corrective_str);

                    $preventive_str = '';
                    foreach($preventive as $pIdx => $act) {
                        $preventive_str .= ($pIdx + 1) . ". " . $act . "\n";
                    }
                    $preventive_str = rtrim($preventive_str);
                @endphp
                <tr>
                    <td style="text-align: center; border: 1px solid #000;">{{ $index + 1 }}</td>
                    <td style="text-align: center; border: 1px solid #000;">{{ $row->audit_date ? \Carbon\Carbon::parse($row->audit_date)->format('d M Y') : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->req_number ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->clause_title ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->audit_type ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->scope_item ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->department ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->auditee ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->superior_name ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->auditor ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->finding_category ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->finding ?? '-' }}</td>
                    <td style="text-align: center; border: 1px solid #000;">{{ $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d M Y') : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->status ?? '-' }}</td>
                    <td style="border: 1px solid #000;">{!! nl2br(e($corrective_str)) ?: '-' !!}</td>
                    <td style="text-align: center; border: 1px solid #000;">{{ $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d M Y') : '-' }}</td>
                    <td style="border: 1px solid #000;">{!! nl2br(e($preventive_str)) ?: '-' !!}</td>
                    <td style="text-align: center; border: 1px solid #000;">{{ $row->due_date ? \Carbon\Carbon::parse($row->due_date)->format('d M Y') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
