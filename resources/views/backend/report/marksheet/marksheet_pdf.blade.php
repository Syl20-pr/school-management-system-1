@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-warning">
                        <div class="box-header">
                            <h4 class="box-title">Gestion des Vues <strong>PDF Des Bulletins</strong></h4>
                            <!-- Download PDF Button -->
                            <a href="{{ route('marksheet.download', request()->all()) }}" class="btn btn-primary float-right">Télécharger PDF</a>
                        </div>
                        <div class="box-body" style="border: solid 1px; padding: 10px;">
                            <div class="row">
                                <div class="col-md-2 text-center" style="float: right;">
                                    <img src="{{ url('upload/giant-logo.png') }}" style="width: 120px; height: 100px;">
                                </div>
                                <div class="col-md-4 text-center" style="float: left;">
                                    <h4><strong>GRAPMULT ECOLE</strong></h4>
                                    <h6><strong>Amedenta Togo</strong></h6>
                                    <h5><strong><u><i>Relevé de Notes</i></u></strong></h5>
                                    <h6><strong>{{ $studentMarks->first()->exam_type->name ?? 'N/A' }}</strong></h6>
                                </div>
                                <div class="col-md-12">
                                    <hr style="border: solid 1px; width: 100%; color: #ddd; margin-bottom: 0px;">
                                    <p style="text-align: right;"><u><i>Fait à Lomé, Le : </i>{{ date('d M Y') }} </u></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <table border="1" width="100%" cellpadding="8" cellspacing="2">
                                        <tr>
                                            <td width="50%">Numéro d'Identification</td>
                                            <td width="50%">{{ $studentMarks->first()->student->id_no ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td width="50%">Nom</td>
                                            <td width="50%">{{ $studentMarks->first()->student->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td width="50%">Classe</td>
                                            <td width="50%">{{ $studentMarks->first()->student_class->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td width="50%">Session</td>
                                            <td width="50%">{{ $studentMarks->first()->year->name ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <table border="1" width="100%" cellpadding="8" cellspacing="2">
                                        <thead>
                                            <tr>
                                                <th>Grade (Lettre)</th>
                                                <th>Marge de Notes</th>
                                                <th>Grade (Point)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($grades as $grade)
                                                <tr>
                                                    <td>{{ $grade->grade_name }}</td>
                                                    <td>{{ $grade->start_marks }} - {{ $grade->end_marks }}</td>
                                                    <td>{{ number_format($grade->grade_point, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <table border="1" width="100%" cellpadding="1" cellspacing="1">
                                        <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Matières</th>
                                                <th>Marks</th>
                                                <th>Coef.</th>
                                                <th>Grade (Lettre)</th>
                                                <th>Grade (Point)</th>
                                                <th>Mentions</th>
                                                <th>Rang</th>
                                                <th>Nom Prof.& Signt.</th>
                                            </tr>
                                        </thead>
                                            <tbody>
                                            @foreach($studentMarks as $key => $mark)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $mark->subject_name }}</td>
                                                    <td>{{ $mark->marks ?? 'N/A' }}</td>
                                                    <td>{{ $mark->subjective_mark }}</td>
                                                    <td>{{ $mark->grade_name }}</td>
                                                    <td>{{ $mark->grade_point }}</td>
                                                    <td>{{ $mark->remarks ?? 'N/A' }}</td>
                                                    <td>{{ $mark->rank ?? 'N/A'}}</td>
                                                    <td>{{ $mark->assignedSubject->assign_teacher->teacher->name ?? 'N/A'  }}</td>
                                                </tr>
                                                <!-- Summary Rows -->
                                                
                                            @endforeach
                                            </tbody>
                                    </table>

                                            <table border="1" width="100%" cellpadding="8" cellspacing="2">
                                                <tr>
                                                    <td colspan="3"><strong style="padding-left: 30px;">Somme :</strong></td>
                                                    <td colspan="4" style="padding-left: 30px;"><strong>{{ $totalMarks }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><strong style="padding-left: 30px;">Note en Point</strong></td>
                                                    <td colspan="4" style="padding-left: 30px;"><strong>{{ number_format($averagePoint, 2) }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><strong style="padding-left: 30px;">Point for Letter Grade</strong></td>
                                                    <td colspan="4" style="padding-left: 30px;"><strong>{{ $finalGrade->grade_name ?? 'N/A' }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><strong style="padding-left: 30px;">Appréciation</strong></td>
                                                    <td colspan="4" style="padding-left: 30px;"><strong>{{ $finalGrade->remarks ?? 'N/A' }}</strong></td>
                                                </tr>
                                            </table>
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <p>Imprimé le : {{ date("d M Y") }}</p>
                                </div>
                                <div style="width: 40%; text-align: center;">
                                    <hr style="border: solid 1px; width: 60%; color: #000;">
                                    <p>Signature du Directeur</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
