<?php

namespace Steganography\Utils;

class Position implements Iterator ,Countable {

    public $region = null;
    public $x;
    public $y;

    public function __construct(Region $r)
    {
        $this->region = $r;
    }

    public function rewind()
    {
        $this->x = 0;
        $this->y = 0;
    }

    public function next()
    {
        if($this->valid())
        {
            if($this->x == ($this->region->w - 1) && ($this->y != $this->region->h - 1) )
            {
                $this->y += 1;
                $this->x = 1;
            }else if($this->x == ($this->region->w - 1) && $this->y == ($this->region->h - 1) )
            {
                $this->x = -1;
                $this->y = -1;
            }else{
                $this->x += 1;
            }
        }
    }

    public function key()
    {
        return ($this->y * $this->region->w) + $this->x;
    }

    public function valid()
    {
        return ($this->x + $this->y) >= 0;
    }

    public function current()
    {
        return array(
            $this->x + $this->region->x,
            $this->y + $this->region->y
        );
    }

    public function count()
    {
        return $this->region->w * $this->region->h;
    }

}




?>