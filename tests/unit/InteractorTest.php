<?php

use Capriolo\Eavquent\Attribute\Attribute;
use Capriolo\Eavquent\Eavquent;
use Capriolo\Eavquent\Interactor;
use Capriolo\Eavquent\Value\Builder;
use Capriolo\Eavquent\Value\Value;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as BaseCollection;
use Mockery as m;

class InteractorTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function read_single_content()
    {
        $value = m::mock(Value::class);
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);

        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => new Attribute]));
        $entity->shouldReceive('bootEavquentIfNotBooted');
        $value->shouldReceive('getContent')->andReturn('bar');
        $entity->shouldReceive('relationLoaded')->with('foo')->andReturn(true);
        $entity->shouldReceive('getRelation')->with('foo')->andReturn($value);

        $interactor = new Interactor($builder, $entity);

        $this->assertEquals('bar', $interactor->get('foo'));
    }

    /** @test */
    public function read_collection_content()
    {
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);
        $attribute = m::mock(Attribute::class);

        $entity->shouldReceive('bootEavquentIfNotBooted');
        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => $attribute]));
        $attribute->shouldReceive('isCollection')->once()->andReturn(true);
        $entity->shouldReceive('relationLoaded')->with('foo')->andReturn(true);
        $entity->shouldReceive('getRelation')->with('foo')->andReturn(new Collection);

        $interactor = new Interactor($builder, $entity);

        $this->assertInstanceOf(BaseCollection::class, $interactor->get('foo'));
    }

    /** @test */
    public function read_raw_object()
    {
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);

        $entity->shouldReceive('bootEavquentIfNotBooted');
        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => new Attribute]));
        $entity->shouldReceive('relationLoaded')->with('foo')->andReturn(true);
        $entity->shouldReceive('getRelation')->with('foo')->andReturn('bar');

        $interactor = new Interactor($builder, $entity);

        $this->assertEquals('bar', $interactor->get('rawFooObject'));
        $this->assertEquals('bar', $interactor->get('rawfooobject'));
        $this->assertEquals('bar', $interactor->get('rawfooObject'));
        $this->assertEquals('bar', $interactor->get('rawFooobject'));
    }

    /** @test */
    public function update_content_of_existing_simple_value()
    {
        $value = m::mock(Value::class);
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);
        $attribute = m::mock(Attribute::class);

        $entity->shouldReceive('bootEavquentIfNotBooted');
        $attribute->shouldReceive('isCollection')->once()->andReturn(false);
        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => $attribute]));
        $entity->shouldReceive('relationLoaded')->with('foo')->andReturn(true);
        $entity->shouldReceive('getRelation')->with('foo')->andReturn($value);

        $value->shouldReceive('setContent')->with('bar')->once();

        $interactor = new Interactor($builder, $entity);

        $interactor->set('foo', 'bar');
    }

    /** @test */
    public function set_a_new_simple_value()
    {
        $value = m::mock(Value::class);
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);
        $attribute = m::mock(Attribute::class);

        $entity->shouldReceive('bootEavquentIfNotBooted');
        $attribute->shouldReceive('isCollection')->once()->andReturn(false);
        $attribute->shouldReceive('getName')->once()->andReturn('foo');
        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => $attribute]));
        $entity->shouldReceive('relationLoaded')->with('foo')->once()->andReturn(true);
        $entity->shouldReceive('getRelation')->with('foo')->once()->andReturn(null);
        $entity->shouldReceive('setRelation')->with('foo', $value)->once();

        $builder->shouldReceive('build')->with($entity, $attribute, 'bar')->andReturn($value);

        $interactor = new Interactor($builder, $entity);

        $interactor->set('foo', 'bar');
    }

    /** @test */
    public function replace_an_entire_collection()
    {
        $value = m::mock(Collection::class);
        $builder = m::mock(Builder::class);
        $entity = m::mock(InteractorModelStub::class);
        $attribute = m::mock(Attribute::class);

        $entity->shouldReceive('bootEavquentIfNotBooted');
        $attribute->shouldReceive('isCollection')->once()->andReturn(true);
        $entity->shouldReceive('getEntityAttributes')->andReturn(new Collection(['foo' => $attribute]));
        $entity->shouldReceive('relationLoaded')->with('foo')->once()->andReturn(true);
        $entity->shouldReceive('getRelation')->with('foo')->once()->andReturn($value);
        $value->shouldReceive('replace')->with('bar')->once();

        $interactor = new Interactor($builder, $entity);

        $interactor->set('foo', 'bar');
    }

    public function tearDown()
    {
        m::close();
    }
}

class InteractorModelStub extends Model
{
    use Eavquent;
}
