<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->default(0.00); // إضافة حقل السعر بقيمة افتراضية 0.00
        });
    }

    public function down()
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn('price'); // حذف العمود إذا قمنا بعمل تراجع (rollback)
        });
    }
};
