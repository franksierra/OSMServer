<?php

namespace Tests\Feature;

use App\Models\OSM\Node;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

//        $o = Node::raw("select GROUP_CONCAT(id) from way_tags WHERE MATCH('V*ctor Emilio Estrada') LIMIT 10000;")->get();
        $xx = DB::connection('sphinx')->select("select * from way_tags WHERE MATCH('V*ctor Emilio Estrada') LIMIT 10000;");

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
