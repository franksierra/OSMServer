<?php

use App\Models\OSM\Node;
use App\Models\OSM\NodeTag;
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
        // $this->call(UsersTableSeeder::class);
        $nodes = factory(Node::class)->times(100)->create()
            ->each(function (Node $node) {
                $node->tags()->saveMany(
                    factory(NodeTag::class, 10)->make()
                );
            });
    }
}
