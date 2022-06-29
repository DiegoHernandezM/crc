<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Area;
use App\Models\AssociateType;
use App\Models\Board;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Shift;
use App\Models\Subarea;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $account = Account::create(['name' => 'CEDIS']);
        $account2 = Account::create(['name' => 'CENTRO']);
        $account3 = Account::create(['name' => 'TOREO']);

        User::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@crcccp.com',
            'owner' => true,
        ]);

        $user = User::find(1);
        $role = Role::create(['name' => 'Super Admin']);
        $role2 = Role::create(['name' => 'Admin']);
        $role3 = Role::create(['name' => 'Leader']);
        $role4 = Role::create(['name' => 'Assistant']);
        $user->assignRole('Super Admin');

        $areas = [
            [
                'name' => 'Picking'
            ],
            [
                'name' => 'Sorter'
            ],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }

        $subareas = [
            [
                'name' => 'Recoleccion',
                'area_id' => 1,
            ],
            [
                'name' => 'Coordinacion',
                'area_id' => 1,
            ],
            [
                'name' => 'Almacenaje',
                'area_id' => 1,
            ],
            [
                'name' => 'Staff',
                'area_id' => 1,
            ],
            [
                'name' => 'Encargado',
                'area_id' => 1,
            ],
            [
                'name' => 'Permanencia',
                'area_id' => 1,
            ],
            [
                'name' => 'Cierre',
                'area_id' => 2,
            ],
            [
                'name' => 'Induccion',
                'area_id' => 2,
            ],
            [
                'name' => 'Ubicacion',
                'area_id' => 2,
            ],
            [
                'name' => 'Staff',
                'area_id' => 2,
            ],
            [
                'name' => 'Encargado',
                'area_id' => 2,
            ],
            [
                'name' => 'Permanencia',
                'area_id' => 2,
            ],

        ];

        foreach ($subareas as $subarea) {
            Subarea::create($subarea);
        }

        $associateTypes = [
            [
                'name' => 'Picker',
            ],
            [
                'name' => 'Encargado'
            ],
            [
                'name' => 'Almacenaje'
            ],
            [
                'name' => 'Order Picker'
            ],
            [
                'name' => 'Montacarga'
            ],
        ];

        foreach ($associateTypes as $associate) {
            AssociateType::create($associate);
        }

        $shifts = [
            [
                'name' => 'Dia 8am - 6pm',
                'checkin' => '08:00',
                'checkout' => '18:00',
                'area_id' => 1,
            ],
            [
                'name' => 'Noche 9pm - 7am',
                'checkin' => '21:00',
                'checkout' => '07:00',
                'area_id' => 1,
            ],
            [
                'name' => 'Mixto dia 7am - 6pm',
                'checkin' => '07:00',
                'checkout' => '18:00',
                'area_id' => 1,
            ],
            [
                'name' => 'Temp. dia 7am - 7pm',
                'checkin' => '07:00',
                'checkout' => '19:00',
                'area_id' => 1,
            ],
            [
                'name' => 'Temp. noche 7pm - 7pm',
                'checkin' => '19:00',
                'checkout' => '07:00',
                'area_id' => 1,
            ],
            [
                'name' => 'Corp 8am - 6pm',
                'checkin' => '08:00',
                'checkout' => '18:00',
                'area_id' => null,
            ],
            [
                'name' => 'Corp 9am - 7pm',
                'checkin' => '09:00',
                'checkout' => '19:00',
                'area_id' => null,
            ],
            [
                'name' => 'MATUTINO',
                'checkin' => '06:00',
                'checkout' => '14:00',
                'area_id' => 2,
            ],
            [
                'name' => 'NOCTURNO',
                'checkin' => '22:00',
                'checkout' => '06:00',
                'area_id' => 2,
            ],
            [
                'name' => 'VESPERTINO',
                'checkin' => '13:00',
                'checkout' => '21:00',
                'area_id' => 2,
            ],
            [
                'name' => 'DIURNO',
                'checkin' => '08:00',
                'checkout' => '16:00',
                'area_id' => 2,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }

        $bonus = [
            [
                'quantity' => 700,
                'bono'     => 100,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 800,
                'bono'     => 150,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 900,
                'bono'     => 200,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 1000,
                'bono'     => 250,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 1100,
                'bono'     => 300,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 1200,
                'bono'     => 350,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 1300,
                'bono'     => 400,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 1400,
                'bono'     => 550,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 1600,
                'bono'     => 600,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 1700,
                'bono'     => 650,
                'area_id'  => 1,
                'subarea_id'  => 1,
            ],
            [
                'quantity' => 0,
                'bono'     => 300,
                'area_id'  => 1,
                'subarea_id'  => 4,
            ],
            [
                'quantity' => 0,
                'bono'     => 400,
                'area_id'  => 1,
                'subarea_id'  => 5,
            ],
            [
                'quantity' => 20,
                'bono'     => 150,
                'area_id'  => 2,
                'subarea_id'  => 8,
            ],
            [
                'quantity' => 24,
                'bono'     => 300,
                'area_id'  => 2,
                'subarea_id'  => 8,
            ],
            [
                'quantity' => 28,
                'bono'     => 450,
                'area_id'  => 2,
                'subarea_id'  => 8,
            ],
            [
                'quantity' => 32,
                'bono'     => 600,
                'area_id'  => 2,
                'subarea_id'  => 8,
            ],
            [
                'quantity' => 36,
                'bono'     => 750,
                'area_id'  => 2,
                'subarea_id'  => 8,
            ],
            [
                'quantity' => 40,
                'bono'     => 900,
                'area_id'  => 2,
                'subarea_id'  => 8,
            ],
            [
                'quantity' => 44,
                'bono'     => 1050,
                'area_id'  => 2,
                'subarea_id'  => 8,
            ],
            [
                'quantity' => 0,
                'bono'     => 300,
                'area_id'  => 2,
                'subarea_id'  => 10,
            ],
            [
                'quantity' => 0,
                'bono'     => 400,
                'area_id'  => 2,
                'subarea_id'  => 11,
            ],
        ];

        foreach ($bonus as $bon) {
            Board::create($bon);
        }

        Permission::create(['name' => 'Usuarios.Crear']);
        Permission::create(['name' => 'Usuarios.Actualizar']);
        Permission::create(['name' => 'Usuarios.Borrar']);
        Permission::create(['name' => 'Usuarios.Ver Lista']);

        Permission::create(['name' => 'Dashboard']);

        Permission::create(['name' => 'Asociados.Ver Lista']);
        Permission::create(['name' => 'Asociados.Crear']);
        Permission::create(['name' => 'Asociados.Actualizar']);
        Permission::create(['name' => 'Asociados.Borrar']);

        Permission::create(['name' => 'Checador']);
        Permission::create(['name' => 'Productividad.Tablero']);
        Permission::create(['name' => 'Productividad.Horas Extra']);
        Permission::create(['name' => 'Productividad.Picking']);
        Permission::create(['name' => 'Productividad.Plantilla']);
        Permission::create(['name' => 'Productividad.Sorter']);

        Permission::create(['name' => 'Reportes.Asistencias']);

        Permission::create(['name' => 'Catalogos.Horarios']);
        Permission::create(['name' => 'Catalogos.Tipo Asociado']);
        Permission::create(['name' => 'Catalogos.Area']);
        Permission::create(['name' => 'Catalogos.Subarea']);
    }
}
