<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
                    // 'users',
                    // 'permissions', 
                    'roles', 
                    'permission_role', 
                    // 'lotteries', 
                    // 'draws', 
                    // 'blockslotteries', 
                    // 'blocksplays', 
                    // 'branches', 
                    // 'generals', 
                    // 'days', 
                    // 'types', 
                    // 'entities', 
                    // 'drawsrelations', 
                    // 'frecuencies',
                    // 'coins',
                    // 'settings',
                    ]);

        // $this->call('CoinsSeeder');
        // $this->call('SettingsSeeder');
        // $this->call('DrawsSeeder');

        // $this->call('CountriesSeeder');
        // $this->call('PermissionSeeder');
        // $this->call('LotteriesSeeder');
        
        $this->call('RolesSeeder');
        $this->call('PermissionRoleSeeder');
        // $this->call('UsersSeeder');
        // $this->call('BlockslotteriesSeeder');
        // $this->call('BranchesSeeder');
        // $this->call('GeneralsSeeder');
        // $this->call('DaysSeeder');
        // $this->call('FrecuencySeeder');
        // $this->call('TypesSeeder');
        // $this->call('EntitySeeder');
    }


    public function truncateTables(array $tables){
        $servidores = \App\Server::on("mysql")->get();
        foreach ($servidores as $ser):
        $servidor = $ser->descripcion;

            DB::connection($servidor)->statement('SET FOREIGN_KEY_CHECKS = 0');
            foreach($tables as $t){
                DB::connection($servidor)->table($t)->truncate();
            }
        
        endforeach;
    }
}
