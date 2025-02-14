<?php

namespace Database\Seeders;

use App\Models\Cluster;
use App\Models\State;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $states = State::factory()->createMany([
            [
                'name' => 'Rio de Janeiro',
                'acronym' => 'RJ',
            ],
            [
                'name' => 'São Paulo',
                'acronym' => 'SP',
            ],
            [
                'name' => 'Rio Grande do Sul',
                'acronym' => 'RS',
            ],
            [
                'name' => 'Paraná',
                'acronym' => 'PR',
            ],
        ]);

        $rioDeJaneiroCities = $states[0]->cities()->createMany([
            ['name' => 'Rio de Janeiro'],
            ['name' => 'Arraial do Cabo'],
            ['name' => 'Volta Redonda'],
        ]);

        $saoPauloCities = $states[1]->cities()->createMany([
            ['name' => 'São Paulo'],
            ['name' => 'Guarulhos'],
            ['name' => 'Campinas'],
        ]);

        $rioGrandeDoSulCities = $states[2]->cities()->createMany([
            ['name' => 'Porto Alegre'],
            ['name' => 'Caxias do Sul'],
            ['name' => 'Pelotas'],
        ]);

        $paranaCities = $states[3]->cities()->createMany([
            ['name' => 'Curitiba'],
            ['name' => 'Foz do Iguaçu'],
            ['name' => 'Maringá'],
        ]);

        $clusters = Cluster::factory()->createMany([
            [
                'name' => 'Sudeste',
            ],
            [
                'name' => 'Sul',
            ]
        ]);

        $clusters[0]->cities()->attach($rioDeJaneiroCities->pluck('id')->toArray(), ['is_active' => true]);
        $clusters[0]->cities()->attach($saoPauloCities->pluck('id')->toArray(), ['is_active' => true]);

        $clusters[1]->cities()->attach($rioGrandeDoSulCities->pluck('id')->toArray(), ['is_active' => true]);
        $clusters[1]->cities()->attach($paranaCities->pluck('id')->toArray(), ['is_active' => true]);
    }
}
