<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersStatusEnum extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 20)->change(); // تغيير الحجم لاستيعاب القيم الجديدة
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 10)->change(); // إرجاعه إلى حالته السابقة
        });
    }
}
