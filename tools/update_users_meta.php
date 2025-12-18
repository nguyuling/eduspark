<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$district = 'Kota Tinggi, Johor';
$schoolCode = 'JEA3060';
$count = 0;

User::chunkById(100, function ($users) use ($district, $schoolCode, &$count) {
    foreach ($users as $u) {
        $role = $u->role ?: 'student';
        $prefix = ($role === 'teacher') ? 'G' : 'P';
        $uid = $prefix . '-' . $schoolCode . '-' . substr(md5($u->id . '-' . $schoolCode), 0, 5);
        $u->role = $role;
        $u->district = $district;
        $u->school_code = $schoolCode;
        $u->user_id = $uid;
        $u->save();
        $count++;
    }
});

echo "Updated {$count} users\n";
