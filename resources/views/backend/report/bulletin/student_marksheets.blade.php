<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Marksheets</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .page { width: 100%; page-break-after: always; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>

@foreach ($marksheets as $marksheet)
<div class="page">
    <div class="header">
        <h3>Marksheet for {{ $marksheet['student_name'] }}</h3>
        <p>Class: {{ $className }}, Year: {{ $yearName }}, Exam: {{ $examTypeName }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Interrogation 1er Sem</th>
                <th>Devoir 1er Sem</th>
                <th>Compo 1er Sem</th>
                <th>Coef</th>
                <th>Average Marks</th>
                <th>Total (Coef x Avg)</th>
                <th>Rank</th>
                <th>Assigned Teacher</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($marksheet['subjects'] as $subject)
                <tr>
                    <td>{{ $subject['subject_name'] }}</td>
                    <td>{{ $subject['marks']['Interrogation 1Sem'] ?? '-' }}</td>
                    <td>{{ $subject['marks']['Devoir 1er Sem'] ?? '-' }}</td>
                    <td>{{ $subject['marks']['Compo 1er Sem'] ?? '-' }}</td>
                    <td>{{ $subject['subjective_mark'] }}</td>
                    <td>{{ number_format($subject['average'], 2) }}</td>
                    <td>{{ number_format($subject['total'], 2) }}</td>
                    <td>{{ $subject['rank'] ?? '-' }}</td> <!-- Display Rank -->
                    <td>{{ $subject['teacher'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

</body>
</html>
