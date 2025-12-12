<!-- resources/views/backend/report/bulletin/bulletin/partials/header.blade.php -->
@props(['yearName', 'className', 'termTypeName', 'principalTeacherName'])

<div class="bulletin-header flex justify-between items-start mb-6">
    <div class="header-left text-xs">
        <p class="font-bold">MINISTERE DE L'ENSEIGNEMENT</p>
        <p>PRIMAIRE ET SECONDAIRE</p>
        <hr class="my-1">
        <p>DRE - GRAND LOME/IESG-AGOÉ NYIVE</p>
        <p>LYCÉE DE NANEGBE</p>
        <p>Tél:+228 90296872, AGOÉ NYIVE/TOGO</p>
    </div>

    <div class="header-center text-center">
        <img src="{{ public_path('upload/Logo_Nanegbeoo.png') }}" alt="School Logo" class="mx-auto h-20">
        <p class="font-bold">L Y N A</p>
        <p class="text-sm"><b>Travail - Discipline - Succès</b></p>
        
        @php
            $cycle1Classes = ['6ème A', '6ème B', '6ème C', '6ème D', '5ème A', '5ème B', '5ème C', '5ème D', '4ème A', '4ème B', '4ème C', '4ème D', '3ème A', '3ème B', '3ème C', '3ème D', '3ème E'];
            $isCycle1 = in_array($className, $cycle1Classes);
        @endphp

        <h4 class="mt-2 font-bold border-2 border-black px-4 py-2 inline-block">
            @if($isCycle1)
                @if($termTypeName === 'Trimestre 1')
                    BULLETIN DU PREMIER TRIMESTRE | Classe: {{ $className }}
                @elseif($termTypeName === 'Trimestre 2')
                    BULLETIN DU DEUXIÈME TRIMESTRE | Classe: {{ $className }}
                @elseif($termTypeName === 'Trimestre 3')
                    BULLETIN DU TROISIÈME TRIMESTRE | Classe: {{ $className }}
                @else
                    BULLETIN SCOLAIRE
                @endif
            @else
                @if($termTypeName === 'Semestre 1')
                    BULLETIN DU PREMIER SEMESTRE | Classe: {{ $className }}
                @elseif($termTypeName === 'Semestre 2')
                    BULLETIN DU DEUXIÈME SEMESTRE | Classe: {{ $className }}
                @else
                    BULLETIN SCOLAIRE
                @endif
            @endif
        </h4>
    </div>

    <div class="header-right text-xs text-right">
        <p class="font-bold">RÉPUBLIQUE TOGOLAISE</p>
        <p>Travail - Liberté - Patrie</p>
        <hr class="my-1">
        <div class="border border-black px-3 py-1 inline-block">
            <h4 class="m-0">A/S: <b>{{ $yearName }}</b></h4>
        </div> 
    </div>
</div>