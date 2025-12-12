@extends('admin.admin_master')
@section('admin')

<!-- (keeps your styles) -->

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-white">
                <h4>Statistiques de promotion - {{ $year->name }}</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('student.promotion.statistics') }}">
                    <select name="year_id" onchange="this.form.submit()">
                        @foreach($years as $y)
                            <option value="{{ $y->id }}" {{ $year->id == $y->id ? 'selected' : '' }}>{{ $y->name }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card stat-card-promoted"><div class="card-body text-center"><h5>Promus</h5><div class="stat-number">{{ $statistics['promoted'] }}</div></div></div>
                    </div>
                    <div class="col-md-4"><div class="card stat-card-repeated"><div class="card-body text-center"><h5>Redoublants</h5><div class="stat-number">{{ $statistics['repeated'] }}</div></div></div></div>
                    <div class="col-md-4"><div class="card stat-card-excluded"><div class="card-body text-center"><h5>Exclus</h5><div class="stat-number">{{ $statistics['excluded'] }}</div></div></div></div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8">
                        <canvas id="promotionChart" style="height:300px;"></canvas>
                    </div>
                    <div class="col-md-4">
                        <p>Total élèves: {{ $statistics['total'] }}</p>
                    </div>
                </div>

                <h4 class="mt-5">Statistiques par classe</h4>
                <div id="class-statistics-loader">Chargement...</div>
                <table class="table table-bordered d-none" id="class-statistics-table">
                    <thead><tr><th>Classe</th><th>Promus</th><th>Redoublants</th><th>Exclus</th><th>Total</th><th>Taux</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart
    const ctx = document.getElementById('promotionChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Promus','Redoublants','Exclus'],
            datasets: [{ data: [{{ $statistics['promoted'] }}, {{ $statistics['repeated'] }}, {{ $statistics['excluded'] }}], backgroundColor: ['#4CAF50','#FF9800','#F44336'] }]
        }
    });

    // Fetch class-level statistics via AJAX
    fetch("{{ route('student.promotion.statistics.ajax', ['year_id' => $year->id]) }}")
        .then(resp => resp.json())
        .then(data => {
            const table = document.getElementById('class-statistics-table');
            const tbody = table.querySelector('tbody');
            tbody.innerHTML = '';
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${row.class_name}</td><td>${row.promoted}</td><td>${row.repeated}</td><td>${row.excluded}</td><td>${row.total}</td><td>${row.total ? ( (row.promoted/row.total*100).toFixed(1) + '%' ) : '0%'}</td>`;
                tbody.appendChild(tr);
            });
            document.getElementById('class-statistics-loader').classList.add('d-none');
            table.classList.remove('d-none');
        })
        .catch(err => {
            document.getElementById('class-statistics-loader').textContent = 'Erreur de chargement';
            console.error(err);
        });
});
</script>

@endsection
