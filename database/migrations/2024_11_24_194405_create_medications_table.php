<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الدواء التجاري
            $table->string('arabic_name')->nullable(); // الاسم باللغة العربية
            $table->string('generic_name')->nullable(); // الاسم العلمي
            $table->string('active_ingredient')->nullable(); // المادة الفعالة
            $table->string('manufacturer')->nullable(); // الشركة المصنعة
            $table->string('dosage_form'); // شكل الجرعة (أقراص، كبسولات، إلخ)
            $table->string('category'); // الفئة (مسكن، مضاد حيوي، إلخ)
            $table->integer('search_count')->default(0); // عدد مرات البحث
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('medications');
    }
};
