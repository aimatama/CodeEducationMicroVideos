<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Lang;
use Tests\Traits\TestValidations;
use Tests\Traits\TestSaves;
use App\Models\CastMember;

class CastMemberControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $castMember;

    protected function setUp():void
    {
        parent::setUp();

        $this->castMember = factory(CastMember::class)->create();

    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));
        $response
            ->assertStatus(200)
            ->assertJson([$this->castMember->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show',['cast_member'=>$this->castMember->id]));
        $response
            ->assertStatus(200)
            ->assertJson($this->castMember->toArray());
    }

    public function testInvalidationData()
    {

        $data = [
            'name'=>''
        ];
        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');

        $data = [
            'name' => \str_repeat('a',256)
        ];
        $this->assertInvalidationInStoreAction($data,'max.string',['max'=>'255']);
        $this->assertInvalidationInUpdateAction($data,'max.string',['max'=>'255']);

        $data = [
            'type'=>''
        ];
        $this->assertInvalidationInStoreAction($data,'required');
        $this->assertInvalidationInUpdateAction($data,'required');

        $data = [
            'type' => '10'
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data,'boolean');
        $this->assertInvalidationInUpdateAction($data,'boolean');

    }

    protected function assertInvalidationRequired(TestResponse $response){
        $this->assertInvalidationFields(
            $response,
            ['name','type'],
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

    public function testStore(){

        $data = [
            'name'=>'test',
            'type'=>1
        ];
        $response = $this->assertStore($data,$data + ['type' => 1,'is_active' => true,'deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

        $data = [
            'name'=>'test',
            'type'=>2,
            'is_active'=>false
        ];
        $response = $this->assertStore($data,$data + ['type' => 2,'is_active' => false]);

    }

    public function testUpdate(){

        $this->castMember = factory(CastMember::class)->create([
            'name'=>'name_test',
            'type'=>1,
            'is_active'=>false
        ]);

        $data = [
            'name'=>'name_test2',
            'type'=>'2',
            'is_active'=>true
        ];
        $response = $this->assertUpdate($data,$data + ['deleted_at' => null]);
        $response->assertJsonStructure([
            'created_at',
            'updated_at'
        ]);

    }

    public function testDelete()
    {

        $response = $this->json('DELETE', route('cast_members.destroy',['cast_member' => $this->castMember->id]));

        $response->assertStatus(204);

        $this->castMember->refresh();

        $this->assertNull(CastMember::find($this->castMember->id));

        $categories = CastMember::all();
        $this->assertCount(0, $categories);

        $this->assertNotNull($this->castMember->deleted_at);
        $this->assertNotNull(CastMember::onlyTrashed()->first());

    }

    protected function routeStore(){
        return route('cast_members.store');
    }

    protected function routeUpdate(){
        return route('cast_members.update',['cast_member'=>$this->castMember]);
    }

    protected function model(){
        return CastMember::class;
    }

}
