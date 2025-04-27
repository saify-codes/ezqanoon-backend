<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement(<<<'SQL'
            CREATE VIEW lawyers_view AS
            SELECT 
                lawyers.id,
                lawyers.name,
                lawyers.email,
                lawyers.phone,
                lawyers.avatar,
                lawyers.qualification,
                lawyers.specialization,
                lawyers.availability_from,
                lawyers.availability_to,
                lawyers.price,
                lawyers.city,
                lawyers.country,
                lawyers.location,
                lawyers.description,
                lawyers.is_profile_completed,
                COALESCE(ROUND(AVG(ratings.rating)), 0) AS rating
            FROM lawyers
            LEFT JOIN ratings ON lawyers.id = ratings.lawyer_id
            GROUP BY lawyers.id
        SQL);
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS lawyers_view');
    }
};