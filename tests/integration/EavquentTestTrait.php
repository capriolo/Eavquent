<?php

use Capriolo\Eavquent\Value\Data\Varchar;

trait EavquentTestTrait
{
    public function setUp()
    {
        parent::setUp();

        Dummy::createDummyData();
    }

    /** @test */
    public function collections_are_linked_to_entity_and_attribute_when_lazy_load()
    {
        $company = Company::first();

        $this->assertEquals('colors', $company->rawColorsObject->getAttribute()->name);
        $this->assertEquals($company, $company->rawColorsObject->getEntity());
    }

    /** @test */
    public function collections_are_linked_to_entity_and_attribute_when_eager_load()
    {
        $company = Company::with('eav')->first();

        $this->assertEquals('colors', $company->rawColorsObject->getAttribute()->name);
        $this->assertEquals($company, $company->rawColorsObject->getEntity());
    }

    /** @test */
    public function load_all_attributes_registered_for_an_entity()
    {
        $company = Company::with('eav')->first();

        $this->assertTrue($company->relationLoaded('colors'));
        $this->assertTrue($company->relationLoaded('city'));
    }

    /** @test */
    public function load_all_attributes_registered_for_an_entity_usign_load()
    {
        $company = Company::first();
        $company->load('eav');

        $this->assertTrue($company->relationLoaded('colors'));
        $this->assertTrue($company->relationLoaded('city'));
    }

    /** @test */
    public function eagerload_all_attributes_from_withs_model_property()
    {
        $model = CompanyWithEavStub::first();

        $this->assertTrue($model->relationLoaded('colors'));
        $this->assertTrue($model->relationLoaded('city'));
    }

    /** @test */
    public function eagerload_attributes_from_withs_model_property()
    {
        $model = CompanyWithCityStub::first();

        $this->assertFalse($model->relationLoaded('colors'));
        $this->assertTrue($model->relationLoaded('city'));
    }

    /** @test */
    public function load_only_certain_attributes_for_an_entity()
    {
        $company = Company::with('city')->first();

        $this->assertFalse($company->relationLoaded('colors'));
        $this->assertTrue($company->relationLoaded('city'));
    }

    /** @test */
    public function get_the_content_of_an_attribute()
    {
        $company = Company::with('eav')->first();

        $this->assertInternalType('string', $company->city);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $company->colors);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $company->sizes);
        $this->assertNull($company->address);
        $this->assertCount(0, $company->sizes);
    }

    /** @test */
    public function get_the_raw_relation_value()
    {
        $company = Company::with('eav')->first();

        $this->assertInstanceOf(Varchar::class, $company->rawCityObject);
    }

    /** @test */
    public function attributes_are_included_in_array_as_keys()
    {
        $company = Company::with('eav')->first()->toArray();

        $this->assertArrayHasKey('city', $company);
        $this->assertArrayHasKey('colors', $company);
        $this->assertArrayHasKey('address', $company);
        $this->assertArrayHasKey('sizes', $company);
    }

    /** @test */
    public function value_collections_are_eavquent_collections()
    {
        $company = Company::with('eav')->first();

        $this->assertInstanceOf(\Capriolo\Eavquent\Value\Collection::class, $company->rawColorsObject);
    }

    /** @test */
    public function collections_are_linked_to_entity_and_attribute()
    {
        $company = Company::with('eav')->first();

        $attribute = $company->getEntityAttributes()['colors'];

        $this->assertEquals($company, $company->rawColorsObject->getEntity());
        $this->assertEquals($attribute, $company->rawColorsObject->getAttribute());
    }

    /** @test */
    public function updating_content_of_existing_simple_values()
    {
        $company = Company::with('eav')->first();
        $company->city = 'foo';

        $this->assertEquals('foo', $company->city);
        $this->assertEquals(1, $company->rawCityObject->getKey());
    }

    /** @test */
    public function setting_content_of_unexisting_simple_value()
    {
        $company = Company::with('eav')->first();

        $this->assertNull($company->rawAddressObject);
        $company->address = 'foo';

        $value = $company->rawAddressObject;

        $this->assertEquals('foo', $company->address);
        $this->assertNull($value->getKey());
        $this->assertInstanceOf(Varchar::class, $value);
    }

    /** @test */
    public function replacing_content_of_existing_collection_value()
    {
        $company = Company::with('eav')->first();

        $company->colors = ['foo', 'bar'];

        $this->assertCount(2, $company->colors);
        $this->assertEquals('foo', $company->colors[0]);
        $this->assertEquals('bar', $company->colors[1]);
    }

    /** @test */
    public function saving_updated_content_of_existing_value()
    {
        $company = Company::with('eav')->first();
        $company->city = 'foo';
        $company->save();

        $company = Company::with('eav')->first();
        $this->assertEquals('foo', $company->city);
    }

    /** @test */
    public function saving_setting_content_of_unexisting_value()
    {
        $company = Company::with('eav')->first();
        $company->address = 'foo';
        $company->save();

        $company = Company::with('eav')->first();
        $this->assertEquals('foo', $company->address);
    }

    /** @test */
    public function saving_replacing_content_of_existing_collection_value()
    {
        $company = Company::with('eav')->first();
        $company->colors = ['foo', 'bar'];
        $company->save();

        $company = Company::with('eav')->first();
        $this->assertCount(2, $company->colors);
        $this->assertEquals('foo', $company->colors[0]);
        $this->assertEquals('bar', $company->colors[1]);
    }

    /** @test */
    public function deleting_values_when_replacing_collection()
    {
        $company = Company::with('eav')->first();
        $color1 = $company->rawColorsObject[0];
        $color2 = $company->rawColorsObject[1];

        $company->colors = [];
        $company->save();

        $this->dontSeeInDatabase('eav_values_varchar', ['id' => $color1->getKey()]);
        $this->dontSeeInDatabase('eav_values_varchar', ['id' => $color2->getKey()]);
        $this->assertCount(0, $company->getRelationValue('colors'));
    }

    /** @test */
    public function deleting_value_when_setting_null()
    {
        $company = Company::with('eav')->first();
        $city = $company->rawCityObject;

        $company->city = null;
        $company->save();

        $this->dontSeeInDatabase('eav_values_varchar', ['id' => $city->getKey()]);
        $this->assertNull($company->getRelationValue('city'));
    }

    /** @test */
    public function converting_plain_attributes_to_array_json()
    {
        $company = Company::with('eav')->first();

        $result = $company->toArray();

        $this->assertFalse(array_key_exists('data', $result));
        $this->assertTrue(is_string($result['city']));
        $this->assertTrue(is_string($result['colors'][0]));
        $this->assertTrue(is_string($result['colors'][1]));
    }

    /** @test */
    public function converting_raw_attributes_to_array_json()
    {
        $company = CompanyWithRawRelationsStub::with('eav')->first();

        $result = $company->toArray();

        $this->assertFalse(array_key_exists('data', $result));
        $this->assertTrue(array_key_exists('id', $result['city']));
        $this->assertTrue(array_key_exists('id', $result['colors'][0]));
        $this->assertTrue(array_key_exists('id', $result['colors'][1]));
    }

    /** @test */
    public function create_entity_with_values()
    {
        $company = new Company;

        $company->name = 'fooCompany';
        $company->city = 'foo';
        $company->colors = ['bar', 'baz'];

        $company->save();

        $this->seeInDatabase('companies', ['name' => 'fooCompany']);
        $this->seeInDatabase('eav_values_varchar', ['entity_id' => $company->getKey(), 'content' => 'foo']);
        $this->seeInDatabase('eav_values_varchar', ['entity_id' => $company->getKey(), 'content' => 'bar']);
        $this->seeInDatabase('eav_values_varchar', ['entity_id' => $company->getKey(), 'content' => 'baz']);
    }
}

class CompanyWithRawRelationsStub extends Company
{
    public $table = 'companies';

    public $morphClass = 'Company';

    protected $rawAttributeRelations = true;
}

class CompanyWithEavStub extends Company
{
    public $table = 'companies';

    public $morphClass = 'Company';

    protected $with = ['eav'];
}

class CompanyWithCityStub extends Company
{
    public $table = 'companies';

    public $morphClass = 'Company';

    protected $with = ['city'];
}
