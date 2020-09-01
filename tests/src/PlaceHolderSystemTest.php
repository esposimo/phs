<?php

namespace smn\phs;

use PHPUnit\Framework\TestCase;

use ReflectionException;

class PlaceHolderSystemTest extends TestCase
{

    /**
     * @var PlaceHolderSystem
     */
    protected PlaceHolderSystem $placeHolder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->placeHolder = new PlaceHolderSystem();
        $this->placeHolder->setPattern('Ora rimpiazzo il placeholder replace con {replace}');
    }

    /**
     * Test if a placeholder are added
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testAddPlaceHolderValue()
    {
        $this->placeHolder->addPlaceHolder('replace', 'sostituto');
        $this->assertEquals('Ora rimpiazzo il placeholder replace con sostituto', $this->placeHolder->render(), 'La sostituzione non è avvenuta correttamente');
        $this->assertEquals('sostituto', $this->placeHolder->getPlaceHolder('replace'));
    }

    /**
     * Test a callback placeholder without parameter
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testAddPlaceHolderCallBackWithoutParameters()
    {
        $this->placeHolder->addPlaceHolder('replace', function () {
            return 5;
        });
        $this->assertEquals('Ora rimpiazzo il placeholder replace con 5', $this->placeHolder->render(), 'La sostituzione non è avvenuta correttamente');
    }

    /**
     * Test a callback placeholder need parameter, without them
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testAddPlaceHolderCallBackWithParametersNoPassed()
    {
        $this->expectException(PlaceHolderSystemException::class);
        $this->placeHolder->addPlaceHolder('replace', function ($p) {
            return pow($p, 2);
        });
    }

    /**
     * Test a callback placeholder with more parameter that need
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testAddPlaceHolderCallBackWithTooParametersPassed()
    {
        $param = 5;
        $this->expectException(PlaceHolderSystemException::class);
        $this->placeHolder->addPlaceHolder('replace', function ($p) {
            return pow($p, 2);
        }, [$param, 5]);
    }

    /**
     * Test a callback placeholder with correct parameter
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testAddPlaceHolderCallBackWithCorrectParametersPassed()
    {
        $param = 5;
        $this->placeHolder->addPlaceHolder('replace', function ($p) {
            return pow($p, 2);
        }, [&$param]);
        $this->assertEquals('Ora rimpiazzo il placeholder replace con 25', $this->placeHolder->render());
        $param = 7;
        $this->assertEquals('Ora rimpiazzo il placeholder replace con 49', $this->placeHolder->render());
    }

    /**
     * Test placeholder with a simple value
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testGetPlaceHolderSimpleValue()
    {
        $this->placeHolder->addPlaceHolder('replace', 5);
        $this->assertEquals(5, $this->placeHolder->getPlaceHolder('replace'), 'Il metodo sbaglia a ricavare il valore');
    }


    /**
     * Test if placeholder are added
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testGetPlaceHolderCallBack()
    {
        $this->placeHolder->addPlaceHolder('replace', function () {
            return 5;
        });
        $this->assertIsCallable($this->placeHolder->getPlaceHolder('replace'), 'Il placeholder non è una callable');
    }

    /**
     * Test if a placeholder are removed
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testRemovePlaceHolder()
    {
        $this->placeHolder->addPlaceHolder('replace', 5);
        $this->placeHolder->removePlaceHolder('replace');
        $this->assertFalse($this->placeHolder->getPlaceHolder('replace'));
    }

    /**
     * Test if pattern is rendered
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testRender()
    {
        $this->placeHolder->addPlaceHolder('replace', 'sostituto');
        $this->assertEquals('Ora rimpiazzo il placeholder replace con sostituto', $this->placeHolder->render(), 'La sostituzione non è avvenuta correttamente');
    }

    /**
     * Test if pattern is configured
     */
    public function testSetPattern()
    {
        $pattern = 'Il nuovo pattern da {replace} è stato creato';
        $this->placeHolder->setPattern($pattern);
        $this->assertEquals($this->placeHolder->getPattern(), $pattern);
    }

    /**
     * Test hasPlaceHolder method
     * @throws PlaceHolderSystemException
     * @throws ReflectionException
     */
    public function testHasPlaceHolder()
    {
        $this->placeHolder->addPlaceHolder('replace', 5);
        $this->assertTrue($this->placeHolder->hasPlaceHolder('replace'));
        $this->placeHolder->removePlaceHolder('replace');
        $this->assertFalse($this->placeHolder->hasPlaceHolder('replace'));
    }

    /**
     * Test if a configured pattern is returned
     */
    public function testGetPattern()
    {
        $this->testSetPattern();
    }
}
