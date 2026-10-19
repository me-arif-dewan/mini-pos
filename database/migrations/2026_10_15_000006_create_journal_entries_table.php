<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('reference', 40)->unique();
            $table->date('entry_date');
            $table->string('description');
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('entry_date');
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->decimal('debit', 14, 2)->default(0);
            $table->decimal('credit', 14, 2)->default(0);
            $table->decimal('balance_after', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['account_id', 'id']);
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE ledger_entries ADD CONSTRAINT chk_ledger_one_side CHECK ((debit > 0 AND credit = 0) OR (credit > 0 AND debit = 0))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('journal_entries');
    }
};
