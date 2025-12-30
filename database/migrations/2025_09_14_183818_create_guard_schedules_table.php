<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('guard_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('region')->nullable();
            $table->string('week_label')->nullable();    // ex: "Semaine 37" ou libellé libre
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->string('image_path')->nullable();    // image uploadée
            $table->longText('ocr_text')->nullable();    // texte brut OCR stocké
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('guard_schedules');
    }
};
