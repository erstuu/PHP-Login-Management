<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    function testRender() 
    {
        View::render('Home/index', [
            "title" => "PHP Login Management"
        ]);

        $this->expectOutputRegex('[PHP Login Management]');
        $this->expectOutputRegex('[Login Management]');
        $this->expectOutputRegex('[html]');
        $this->expectOutputRegex('[boody]');
        $this->expectOutputRegex('[Login Management]');
        $this->expectOutputRegex('[Login]');
        $this->expectOutputRegex('[Register]');
    }
}