<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Members (Data Pelanggan & Poin Membership)
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();
                $table->string('phone', 30)->unique()->index();
                $table->string('name', 100);
                $table->unsignedInteger('total_orders')->default(0);
                $table->unsignedInteger('rewards_claimed')->default(0);
                $table->timestamp('last_ordered_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tambah kolom is_member_reward pada tabel menus (Penanda menu gratis yang disediakan admin)
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                if (!Schema::hasColumn('menus', 'is_member_reward')) {
                    $table->boolean('is_member_reward')->default(false)->after('is_available');
                }
            });
        }

        // 3. Tambah kolom reward pada tabel orders
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'is_member_reward_order')) {
                    $table->boolean('is_member_reward_order')->default(false)->after('total_price');
                }
                if (!Schema::hasColumn('orders', 'reward_menu_id')) {
                    $table->foreignId('reward_menu_id')->nullable()->after('is_member_reward_order')->constrained('menus')->nullOnDelete();
                }
                if (!Schema::hasColumn('orders', 'reward_menu_name')) {
                    $table->string('reward_menu_name', 100)->nullable()->after('reward_menu_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'reward_menu_id')) {
                    $table->dropForeign(['reward_menu_id']);
                    $table->dropColumn(['reward_menu_id']);
                }
                if (Schema::hasColumn('orders', 'is_member_reward_order')) {
                    $table->dropColumn(['is_member_reward_order']);
                }
                if (Schema::hasColumn('orders', 'reward_menu_name')) {
                    $table->dropColumn(['reward_menu_name']);
                }
            });
        }

        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                if (Schema::hasColumn('menus', 'is_member_reward')) {
                    $table->dropColumn(['is_member_reward']);
                }
            });
        }

        Schema::dropIfExists('members');
    }
};
