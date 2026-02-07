<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Realizzato da Luigi La Gioia

return new class extends Migration {
    public function up(): void {
        Schema::create('project_publication', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['project_id','publication_id']);
        });
    }
    public function down(): void
    { 
        Schema::dropIfExists('project_publication');
    }
};
