<?php

namespace Tests;

use stdClass;
use PHPUnit\Framework\TestCase;
use Jshannon63\JsonCollect\JsonCollect;

class foo{}

class JsonCollectTest extends TestCase
{
    protected $basic_example;
    protected $multi_example;

    public function setUp()
    {
        parent::setUp();

        $string = '{"foo":"bar","baz":"zaz"}';
        $this->string_example = $string;
        $this->basic_example = json_decode($string);

        $string = '{"foo":"bar","baz":"zaz","fiz":{"pop":"pow"},"abc":{"xyz":"456","cba":"321"}}';
        $this->multi_example = json_decode($string);

        $string = file_get_contents('./tests/json.txt');
        $this->big_example = json_decode($string);
    }

    public function testConstructor()
    {
        // newing up without value works or (null)
        $test = new JsonCollect;
        $this->assertInstanceOf(JsonCollect::class, $test);

        // newing up without value works or (null)
        $test = new JsonCollect($this->string_example);
        $this->assertInstanceOf(JsonCollect::class, $test);

        // newing up with stdClass works
        $this->assertInstanceOf(stdClass::class, $this->basic_example);
        $test = new JsonCollect($this->basic_example);
        $this->assertEquals(2, $test->count());

        // newing up with json string works
        $test = new JsonCollect('{"foo": "bar", "baz": "zaz"}');
        $this->assertEquals(2, $test->count());

     }

    public function testBadJsonStringFails(){
        // newing up with bad string fails
        $this->expectException(\InvalidArgumentException::class);
        new JsonCollect('this is a bad string');
    }

    public function testInvalidObjectInConstructorFails(){
        //newing up with invalid object fails
        $this->expectException(\InvalidArgumentException::class);
        new JsonCollect(new foo);
    }

    public function testCallMethods()
    {
        // verify Get __call methods works
        $test = new JsonCollect($this->basic_example);
        $this->assertEquals('bar', $test->getfoo());
        $this->assertEquals('zaz', $test->getbaz());

        // verify Set __call method works
        $test->setfoo('yaz');
        $test->setbaz('fiz');
        $this->assertEquals('yaz', $test->getfoo());
        $this->assertEquals('fiz', $test->getbaz());

        $this->assertEquals('foo', $test->search('yaz'));
    }

    public function testBadMethodCall(){
        // verify bad method exception is thrown on unknown method
        $test = new JsonCollect($this->basic_example);
        $this->expectException(\BadMethodCallException::class);
        $test->badmethodfoo();
    }

    public function testMultiLevel()
    {
        $test = new JsonCollect($this->multi_example);

        $this->assertEquals('456', $test->getabc()->getxyz());
    }

    public function testBigJson()
    {
        $test = new JsonCollect($this->big_example);

        $this->assertEquals('Denise Clemons', $test->first()->getfriends()->last()->getname());
    }

    public function testExports()
    {
        $test = new JsonCollect($this->basic_example);
        $this->assertEquals(json_encode($this->basic_example), $test->export());

        $test = new JsonCollect($this->multi_example);
        $this->assertEquals(json_encode($this->multi_example), $test->export());

        $test = new JsonCollect($this->big_example);
        $this->assertEquals(json_encode($this->big_example), $test->export());
    }
}
