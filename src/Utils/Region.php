<?php

namespace Steganography\Utils;

class Region{

    public $x;
    public $y;
    public $w;
    public $h;

    public function __construct($x,$y,$w,$h)
    {
        $this->x = $x;
        $this->y = $y;
        $this->w = $w;
        $this->h = $h;
    }

}



?>