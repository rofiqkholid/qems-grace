<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internal Audit Findings Export</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th style="font-weight: bold; background-color: #f2f2f2;">No</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Req Number</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Department</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Finding Category</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Auditor</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Auditee</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Finding</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Note</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Action Plan</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Target Date</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Status</th>
                <th style="font-weight: bold; background-color: #f2f2f2;">Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->req_number }}</td>
                    <td>{{ $row->department }}</td>
                    <td>{{ $row->finding_category }}</td>
                    <td>{{ $row->auditor }}</td>
                    <td>{{ $row->auditee }}</td>
                    <td>{{ $row->finding }}</td>
                    <td>{{ $row->detail_note }}</td>
                    <td>{{ $row->action_plan }}</td>
                    <td>{{ $row->due_date }}</td>
                    <td>{{ $row->status }}</td>
                    <td>{{ $row->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
