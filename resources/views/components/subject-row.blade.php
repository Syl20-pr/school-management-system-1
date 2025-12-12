<tr>
    <td>{{ $subject['subject_name'] }}</td>
    <td>{{ implode(', ', $subject['exam_marks']->toArray() ?? []) }}</td>
    <td>{{ number_format($subject['average_marks'], 2) }}</td>
    <td>{{ $subject['subjective_mark_coefficient'] }}</td>
    <td>{{ number_format($subject['weighted_average'], 2) }}</td>
    <td>{{ $subject['rank'] }}</td>
    <td>{{ $subject['appreciation'] }}</td>
</tr>
