<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_status_history', function (Blueprint $table) {
            $table->id();
            
            // Élève concerné
            $table->unsignedBigInteger('student_id');
            
            // Année et classe où le changement a eu lieu
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('class_id');
            
            // Changement de statut
            $table->enum('old_status', ['N', 'D'])->nullable()->comment('Ancien statut');
            $table->enum('new_status', ['N', 'D'])->comment('Nouveau statut');
            
            // Raison du changement
            $table->enum('change_reason', ['promotion', 'repeat', 'initial', 'correction', 'cancellation'])
                ->comment('promotion=promu, repeat=redoublement, initial=première inscription, correction=correction manuelle, cancellation=annulation décision');
            
            // Référence à l'historique de promotion (si applicable)
            $table->unsignedBigInteger('promotion_history_id')->nullable();
            
            // Qui a fait le changement
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamp('changed_at');
            
            // Commentaire optionnel
            $table->text('comment')->nullable();
            
            $table->timestamps();
            
            // Index pour les recherches rapides
            $table->index('student_id');
            $table->index(['student_id', 'year_id']);
            $table->index('change_reason');
            
            // Clés étrangères
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('year_id')->references('id')->on('student_years')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('student_classes')->onDelete('cascade');
            $table->foreign('promotion_history_id')->references('id')->on('student_promotion_histories')->onDelete('set null');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_status_history');
    }
};