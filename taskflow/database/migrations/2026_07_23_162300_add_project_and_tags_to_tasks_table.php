<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'project')) {
                $table->string('project')->default('Platform')->after('assignee');
            }

            if (!Schema::hasColumn('tasks', 'tags')) {
                $table->json('tags')->nullable()->after('project');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'tags')) {
                $table->dropColumn('tags');
            }

            if (Schema::hasColumn('tasks', 'project')) {
                $table->dropColumn('project');
            }
        });
    }
};
