<?php
namespace Steganography\Utils;

class Color{

    public $r;
    public $g;
    public $b;
    public $a;//only for png

    /**
     * Color constructor.
     * @param $r
     * @param $g
     * @param $b
     * @param $a
     */
    public function __construct($r, $g, $b, $a)
    {
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        $this->a = $a;
    }

    public function writeBit($three)
    {
        $this->r = ($this->r & 0xFE) + ($three >> 2) & 1;
        $this->g = ($this->g & 0xFE) + ($three >> 1) & 1;
        $this->b = ($this->b & 0xFE) + ($three >> 0) & 1;
    }

    public function readBit()
    {
        $three = ($this->r & 1) << 2;
        $three += ($this->g & 1) << 1;
        $three += $this->b & 1;

        return $three;
    }




}




?>