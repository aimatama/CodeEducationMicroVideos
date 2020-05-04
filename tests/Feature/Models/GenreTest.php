<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Genre;

class GenreTest extends TestCase
{    
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class,1)->create();
        $genres = Genre::all();
        $this->assertCount(1,$genres);
        $genreKey = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'created_at',
                'updated_at',
                'deleted_at',
                'is_active'
            ],
            $genreKey
        );
    }

    public function testCreate()
    {

        $genre = Genre::create(
            [
                'name' => 'test1'
            ]
         );
         $genre->refresh();
         $regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
         $this->assertTrue((bool) preg_match($regex, $genre->id));
         $this->assertEquals(36, strlen($genre->id));
         $this->assertNotNull(Genre::find($genre->id));   

        $genre = Genre::create(
            [
                'name' => 'test1'
            ]
        );
        $genre->refresh();
        $this->assertEquals('test1',$genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create(
            [
                'name' => 'test1',
                'is_active' => false
            ]
        );
        $genre->refresh();
        $this->assertFalse($genre->is_active);

        $genre = Genre::create(
            [
                'name' => 'test1',
                'is_active' => true
            ]
        );
        $genre->refresh();
        $this->assertTrue($genre->is_active);

    }

    public function testUpdate()
    {

        $genre = factory(Genre::class)->create(
            [
                'is_active' => false
            ]
        );

        $genre->refresh();

        $data = [
            'name' => 'test_name_updated',
            'is_active' => true
        ];

        $genre->update($data);

        foreach ($data as $key => $value){
            $this->assertEquals($value, $genre->{$key});
        }

    }

    public function testDelete()
    {

        $genre = factory(Genre::class)->create();
        $genres = Genre::all();
        $this->assertCount(1, $genres);

        $genre->delete();
        $this->assertNull(Genre::find($genre->id));

        $genres = Genre::all();
        $this->assertCount(0, $genres);

        $this->assertNotNull($genre->deleted_at);
        $this->assertNotNull(Genre::onlyTrashed()->first());

        $genre->restore();
        $this->assertNotNull(Genre::find($genre->id));

    }

}
