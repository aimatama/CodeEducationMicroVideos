<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;

class CategoryTest extends TestCase
{

    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class,1)->create();
        $categories = Category::all();
        $this->assertCount(1,$categories);
        $categoryKey = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'created_at',
                'updated_at',
                'deleted_at',
                'is_active'
            ],
            $categoryKey
        );
    }

    public function testCreate()
    {

     $category = Category::create(
        [
            'name' => 'test1'
        ]
     );
     $category->refresh();
     $this->assertEquals('test1',$category->name);
     $this->assertNull($category->description);
     $this->assertTrue($category->is_active);

     $category = Category::create(
        [
            'name' => 'test1',
            'description' => null
        ]
     );
     $category->refresh();
     $this->assertNull($category->description);

     $category = Category::create(
        [
            'name' => 'test1',
            'description' => 'test_description'
        ]
     );
     $category->refresh();
     $this->assertEquals('test_description',$category->description);

     $category = Category::create(
        [
            'name' => 'test1',
            'is_active' => false
        ]
     );
     $category->refresh();
     $this->assertFalse($category->is_active);

     $category = Category::create(
        [
            'name' => 'test1',
            'is_active' => true
        ]
     );
     $category->refresh();
     $this->assertTrue($category->is_active);

     $category = Category::create(
        [
            'name' => 'test1',
            'is_active' => true
        ]
     );
     $category->refresh();
     $regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';
     $this->assertTrue((bool) preg_match($regex, $category->id));
     $this->assertNotNull(Category::find($category->id));

    }

    public function testUpdate()
    {

        $category = factory(Category::class)->create(
            [
                'description' => 'test_description',
                'is_active' => false
            ]
        );

        $category->refresh();

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true
        ];

        $category->update($data);

        foreach ($data as $key => $value){
            $this->assertEquals($value, $category->{$key});
        }

    }

    public function testDelete()
    {

        $category = factory(Category::class)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);

        $category->delete();
        $categories = Category::all();

        $this->assertCount(0, $categories);
        $this->assertNotNull($category->deleted_at);
        $this->assertNotNull(Category::onlyTrashed()->first());

        $category->restore();
        $this->assertNotNull(Category::find($category->id));

    }

}
