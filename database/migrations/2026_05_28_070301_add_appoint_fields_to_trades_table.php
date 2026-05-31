<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::table('trades', function (Blueprint $table) {
            $table->dateTime('appoint_time')->nullable();
            $table->string('appoint_location')->nullable();
        });
    }
    public function down() {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['appoint_time','appoint_location']);
        });
    }
};