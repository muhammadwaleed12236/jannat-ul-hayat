<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$u = App\Models\User::where('email', 'testadmin@example.com')->first();
if ($u) {
    $u->usertype = 'admin';
    $u->syncPermissions(\Spatie\Permission\Models\Permission::all());
    $u->save();
    echo 'Admin updated';
} else {
    echo 'User not found';
}
