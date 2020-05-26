<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Tests\TestCase;
use Lang;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use Tests\Exceptions\TestException;
use App\Http\Controllers\Api\GenreController;
use App\Models\Genre;
use App\Models\Category;


class GenreControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;

    protected function setUp():void
    {
        parent::setUp();

        $this->genre = factory(Genre::class)->create();

    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show',['genre'=>$this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function testInvalidationData()
    {

        $data = [
            'name'=>'',
        ];
        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');

        $data = [
            'name' => \str_repeat('a',256)
        ];
        $this->assertInvalidationInStoreAction($data,'max.string',['max'=>'255']);
        $this->assertInvalidationInUpdateAction($data,'max.string',['max'=>'255']);

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');

    }

    protected function assertInvalidationRequired(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['name','categories_id'],
            'required',
            []
        );
        $response
            ->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationMax(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['name'],
            'max.string',
            ['max'=>'255']
        );
    }

    protected function assertInvalidationBoolean(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['is_active'],
            'boolean'
        );
    }

    // public function testStore(){

    //     $category = factory(Category::class)->create();

    //     $data = [
    //         'name'=>'test',
    //         'categories_id' => [$category->id]
    //     ];

    //     $response = $this->assertStore(
    //         $data,
    //         $data + ['is_active' => true,'deleted_at' => null]
    //     );

    //     $response->assertJsonStructure([
    //         'created_at',
    //         'updated_at'
    //     ]);

    //     $data = [
    //         'name'=>'test',
    //         'is_active'=>false,
    //         'categories_id' => [$category->id]
    //     ];
    //     $response = $this->assertStore(
    //         $data,
    //         $data + ['is_active' => false]
    //     );

    // }

    // public function testUpdate(){

    //     $category = factory(Category::class)->create();

    //     $this->genre = factory(Genre::class)->create([
    //         'is_active'=>false
    //     ]);

    //     $data = [
    //         'name'=>'test',
    //         'categories_id' => [$category->id],
    //         'is_active'=>true
    //     ];
    //     $response = $this->assertUpdate($data,$data + ['deleted_at' => null]);
    //     $response->assertJsonStructure([
    //         'created_at',
    //         'updated_at'
    //     ]);
        
    //     $data = [
    //         'name'=>'test',
    //         'is_active'=>true
    //     ];
    //     $response = $this->assertUpdate($data,array_merge($data, $data));

    // }

    public function testSave()
    {

        $category = factory(Category::class)->create();

        $data = [
            [
                'send_data' => [
                        'name' => "name",
                        'categories_id' => [$category->id]
                    ],
                'test_data' => [
                    'name' => "name"
                ],
            ]
        ];

        foreach ($data as $key => $value){
            $response = $this->assertStore(
                $value['send_data'], $value['test_data'] + ['deleted_at' => null]
            );
            $response
                ->assertJsonStructure([
                    'created_at', 'updated_at'
                ]);
            $response = $this->assertUpdate(
                $value['send_data'], $value['test_data'] + ['deleted_at' => null]
            );
            $response
                ->assertJsonStructure([
                    'created_at', 'updated_at'
                ]);
        }

    }

   public function testRollbackStore()
   {
        $data = [
            'name'=>'test'
        ];
       $controller = \Mockery::mock(GenreController::class)->makePartial()->shouldAllowMockingProtectedMethods();
       $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException());
       $controller->shouldReceive('validate')->withAnyArgs()->andReturn($data);
       $controller->shouldReceive('rulesStore')->withAnyArgs()->andReturn([]);

       $request = \Mockery::mock(Request::class);
       $request->shouldReceive('get')->withAnyArgs()->andReturnNull();

       $hasError = false;
       try {
           $controller->store($request);
       }catch (TestException $e){
           $this->assertCount(1, Genre::all());
           $hasError = true;
       }
       $this->assertTrue($hasError);
   }

   public function testRollbackUpdate()
   {
        $data = [
            'name'=>'test'
        ];
       $controller = \Mockery::mock(GenreController::class)->makePartial()->shouldAllowMockingProtectedMethods();
       $controller->shouldReceive('handleRelations')->once()->andThrow(new TestException());
       $controller->shouldReceive('validate')->withAnyArgs()->andReturn($data);
       $controller->shouldReceive('rulesUpdate')->withAnyArgs()->andReturn([]);

       $request = \Mockery::mock(Request::class);
       $request->shouldReceive('get')->withAnyArgs()->andReturnNull();

       $hasError = false;
       $this->genre->refresh();
       try {
           $controller->update($request, $this->genre->id);
       }catch (TestException $e){
           $this->assertEquals($this->genre->toArray(), Genre::find($this->genre->id)->toArray());
           $hasError = true;
       }
       $this->assertTrue($hasError);
   }

    public function testDelete()
    {

        $response = $this->json('DELETE', route('genres.destroy',['genre' => $this->genre->id]));

        $response->assertStatus(204);

        $this->genre->refresh();

        $this->assertNull(Genre::find($this->genre->id));

        $genres = Genre::all();
        $this->assertCount(0, $genres);

        $this->assertNotNull($this->genre->deleted_at);
        $this->assertNotNull(Genre::onlyTrashed()->first());

    }

    protected function routeStore(){
        return route('genres.store');
    }

    protected function routeUpdate(){
        return route('genres.update',['genre'=>$this->genre]);
    }

    protected function model(){
        return Genre::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

}
