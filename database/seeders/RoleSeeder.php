<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        foreach (['admin'=>'Administrator','vendor'=>'Vendor','customer'=>'Customer'] as $name => $label) {
            Role::firstOrCreate(['name'=>$name], ['label'=>$label]);
        }
    }
}
