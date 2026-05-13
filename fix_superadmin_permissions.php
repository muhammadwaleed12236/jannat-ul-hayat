<?php
/**
 * Fix Super Admin Permissions Script
 * Run this on live server: php fix_superadmin_permissions.php
 * OR access via browser: yourdomain.com/fix_superadmin_permissions.php (then delete it!)
 * 
 * This fixes the 403 Forbidden error on /sale page for Super Admin
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "<pre>\n";
echo "=== Super Admin Permission Fix ===\n\n";

// Step 1: Clear permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "✅ Permission cache cleared\n\n";

// Step 2: Show existing roles
echo "--- Existing Roles ---\n";
$roles = Role::all();
foreach ($roles as $r) {
    echo "  ID: {$r->id} | Name: '{$r->name}'\n";
}
echo "\n";

// Step 3: Ensure 'Super Admin' role exists (with exact name)
$superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
echo "✅ 'Super Admin' role ensured (ID: {$superAdminRole->id})\n\n";

// Step 4: Ensure critical permissions exist
$criticalPermissions = [
    'sales.view', 'sales.create', 'sales.edit', 'sales.delete',
    'sales.returns.view', 'sales.returns.create',
    'purchases.view', 'purchases.create', 'purchases.edit', 'purchases.delete',
    'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
    'products.view', 'products.create', 'products.edit', 'products.delete',
];

echo "--- Creating/Verifying Permissions ---\n";
foreach ($criticalPermissions as $perm) {
    $p = Permission::firstOrCreate(['name' => $perm]);
    echo "  ✅ {$perm}\n";
}
echo "\n";

// Step 5: Sync ALL permissions to Super Admin
$allPermissions = Permission::all();
$superAdminRole->syncPermissions($allPermissions);
echo "✅ All " . $allPermissions->count() . " permissions assigned to 'Super Admin' role\n\n";

// Step 6: Check who has Super Admin role
echo "--- Users with 'Super Admin' role ---\n";
$superAdmins = User::role('Super Admin')->get();
if ($superAdmins->isEmpty()) {
    echo "  ⚠️  WARNING: No users found with 'Super Admin' role!\n";
    echo "  Run: php artisan db:seed --class=SuperadminSeeder\n";
} else {
    foreach ($superAdmins as $u) {
        echo "  ✅ {$u->name} ({$u->email}) - ID: {$u->id}\n";
    }
}
echo "\n";

// Step 7: Clear permission cache again
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "✅ Permission cache cleared again\n\n";

// Step 8: Test sales.view permission check
echo "--- Testing 'sales.view' permission ---\n";
$hasPerm = $superAdminRole->hasPermissionTo('sales.view');
echo "  Super Admin has 'sales.view': " . ($hasPerm ? "YES ✅" : "NO ❌") . "\n\n";

echo "=== Fix Complete! ===\n";
echo "\n⚠️  IMPORTANT: Delete this file from your server after use!\n";
echo "   rm fix_superadmin_permissions.php\n";
echo "</pre>";
