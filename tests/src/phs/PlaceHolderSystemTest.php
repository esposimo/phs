<?php

namespace smn\phs;

use \smn\phs\PlaceHolderSystem;
use PHPUnit\Framework\TestCase;
use smn\phs\PlaceHolderSystemException;

class PlaceHolderSystemTest extends TestCase
{

    /**
     * @var \smn\phs\PlaceHolderSystem
     */
    protected $placeHolder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->placeHolder = new PlaceHolderSystem();
        $this->placeHolder->setPattern('Ora rimpiazzo il placeholder replace con {replace}');
    }

    public function testAddPlaceHolderValue()
    {
        $this->placeHolder->addPlaceHolder('replace','sostituto');
        $this->assertEquals($this->placeHolder->render(), 'Ora rimpiazzo il placeholder replace con sostituto','La sostituzione non è avvenuta correttamente');
        $this->assertEquals($this->placeHolder->getPlaceHolder('replace'), 'sostituto');

    }

    public function testAddPlaceHolderCallBackWithoutParameters()
    {
        $this->placeHolder->addPlaceHolder('replace', function() {
            return 5;
        });
        $this->assertEquals($this->placeHolder->render(), 'Ora rimpiazzo il placeholder replace con 5','La sostituzione non è avvenuta correttamente');

    }

    public function testAddPlaceHolderCallBackWithParametersNoPassed()
    {
        $param = 5;
        $this->expectException(PlaceHolderSystemException::class);
        $this->placeHolder->addPlaceHolder('replace', function($p) {
            return pow($p, 2);
        });
    }
    public function testAddPlaceHolderCallBackWithTooParametersPassed()
    {
        $param = 5;
        $this->expectException(PlaceHolderSystemException::class);
        $this->placeHolder->addPlaceHolder('replace', function($p) {
            return pow($p, 2);
        },[$param, 5]);
    }
    public function testAddPlaceHolderCallBackWithCorrectParametersPassed()
    {
        $param = 5;
        $this->placeHolder->addPlaceHolder('replace', function($p) {
            return pow($p, 2);
        },[&$param]);
        $this->assertEquals($this->placeHolder->render(), 'Ora rimpiazzo il placeholder replace con 25');
        $param = 7;
        $this->assertEquals($this->placeHolder->render(), 'Ora rimpiazzo il placeholder replace con 49');
    }

    public function testGetPlaceHolderSimpleValue()
    {
        $this->placeHolder->addPlaceHolder('replace',5);
        $this->assertEquals($this->placeHolder->getPlaceHolder('replace'), 5, 'Il metodo sbaglia a ricavare il valore');

    }

    public function testGetPlaceHolderCallBack() {
        $this->placeHolder->addPlaceHolder('replace', function() {
            return 5;
        });
        $this->assertIsCallable($this->placeHolder->getPlaceHolder('replace'),'Il placeholder non è una callabler');
    }

    public function testRemovePlaceHolder()
    {
        $this->placeHolder->addPlaceHolder('replace', 5);
        $this->placeHolder->removePlaceHolder('replace');
        $this->assertFalse($this->placeHolder->getPlaceHolder('replace'));

    }

    public function testRender()
    {
        $this->placeHolder->addPlaceHolder('replace','sostituto');
        $this->assertEquals($this->placeHolder->render(), 'Ora rimpiazzo il placeholder replace con sostituto','La sostituzione non è avvenuta correttamente');
    }

    public function testSetPattern()
    {
        $pattern = 'Il nuovo pattern da {replace} è stato creato';
        $this->placeHolder->setPattern($pattern);
        $this->assertEquals($this->placeHolder->getPattern(), $pattern);

    }

    public function testHasPlaceHolder()
    {
        $this->placeHolder->addPlaceHolder('replace', 5);
        $this->assertTrue($this->placeHolder->hasPlaceHolder('replace'));
        $this->placeHolder->removePlaceHolder('replace');
        $this->assertFalse($this->placeHolder->hasPlaceHolder('replace'));

    }

    public function testGetPattern()
    {
        $this->testSetPattern();
    }
}
