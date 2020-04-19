<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testFillableAttribute()
    {
        $fillable = ['name','description','is_active'];
        $category = New Category();
        $this->assertEquals($fillable,$category->getFillable());
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at','created_at','updated_at'];
        $category = New Category();
        foreach ($dates as $date){
            $this->assertContains($date,$category->getDates());
        }
        $this->assertCount(count($dates),$category->getDates());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits,$categoryTraits);
    }

    public function testCastsAttribute()
    {
        $casts = ['id'=>'string'];
        $category = New Category();
        $this->assertEquals($casts,$category->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $category = New Category();
        $this->assertFalse($category->incrementing);
    }

}
