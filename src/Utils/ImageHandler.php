<?php
namespace Steganography\Utils;

class ImageHandler{

    protected $im;
    protected $str;
    protected $pos;

    const START = "S";
    const STOP = "M";

    public function __construct($im,$str = "")
    {
        $this->im = $im;
        $this->str = $str;
        $region = new Region(0,0,$this->getWidth(),$this->getHeight());
        $this->pos = new Position($region);
    }

    public function encode()
    {
        $this->pos->rewind();
        $len = mb_strlen($this->str);

        $header = pack("aIa", self::START, $len, self::START);
        $footer = pack("aa", self::STOP, self::STOP);
        $payload = $header . $this->str . $footer;

        /*echo "str:" . $len . PHP_EOL;
        echo "hf:" . strlen($header . $footer) . PHP_EOL;
        echo "payload:" . $payload . PHP_EOL;*/

        $alen = strlen($payload);
        $top = ceil(count($this->pos))/3;
        if( $top < $alen - 10 )
        {
            throw new Exception("numbers of char is too much , need to be lower than " . ($top - strlen($header . $footer - 10)));
        }

        for($i = 0;$i < $alen ; $i++)
        {
            $byte = ord($payload[$i]);
            $this->writeByte($byte);
        }
        return $this;

    }

    public function decode()
    {
        $this->pos->rewind();

        $payload = "";

        for($cursor = 0; $cursor < 6; $cursor ++) {
            $byte = $this->readByte();
            $payload .= chr($byte);
        }

        $header = unpack("achar/Ilen/achars", $payload);
        $payload = "";

        /*print_r($header);return;*/

        for($cursor = 0; $cursor < $header['len']; $cursor ++) {
            $byte = $this->readByte();
            $payload .= chr($byte);
        }

        return $payload;

    }
    public function readByte()
    {
        list($x,$y) = $this->pos->current();
        $pixel = $this->getColor($x,$y);
        $value = $pixel->readBit() << 5;
        $this->pos->next();

        list($x,$y) = $this->pos->current();
        $pixel = $this->getColor($x,$y);
        $value += $pixel->readBit() << 2;
        $this->pos->next();

        list($x,$y) = $this->pos->current();
        $pixel = $this->getColor($x,$y);
        $value += $pixel->readBit() >> 1 ;
        $this->pos->next();

        return $value;
    }

    public function writeByte($byte)
    {
        list($x,$y) = $this->pos->current();
        $pixel = $this->getColor($x,$y);
        $pixel->writeBit(($byte >> 5) & 7);
        $this->setColor($x,$y,$pixel);
        $this->pos->next();

        list($x,$y) = $this->pos->current();
        $pixel = $this->getColor($x,$y);
        $pixel->writeBit(($byte >> 2) & 7);
        $this->setColor($x,$y,$pixel);
        $this->pos->next();

        list($x,$y) = $this->pos->current();
        $pixel = $this->getColor($x,$y);
        $pixel->writeBit(($byte << 1) & 7);
        $this->setColor($x,$y,$pixel);
        $this->pos->next();
    }

    public function save($path)
    {
        //未验证
        $ext = pathinfo($path)['extension'];
        if(!in_array($ext,array("png","jpg")))
        {
            return false;
        }

        if($ext == "png")
        {
            imagepng($this->im,$path);
        }else if($ext == "jpg")
        {
            imagejpeg($this->im,$path);
        }
        return true;
    }

    public function getColor($x,$y)
    {
        $dec = imagecolorat($this->im,$x,$y);
        $a = ($dec >> 24) & 0x7F;
        $r = ($dec >> 16) & 0xFF;
        $g = ($dec >> 8) & 0xFF;
        $b = $dec & 0xFF;

        return new Color($r,$g,$b,$a);
    }

    public function setColor($x, $y, Color $color) {
        $r = (int) $color->r;
        $g = (int) $color->g;
        $b = (int) $color->b;
        $a = (int) $color->a;

        if($a > 0) {
            $im_color = imagecolorallocatealpha($this->im,$r,$g,$b,$a);
        } else {
            $im_color = imagecolorallocate($this->im,$r,$g,$b);
        }
        imagesetpixel($this->im,$x,$y,$im_color);
        imagecolordeallocate($this->im, $im_color);
    }

    public function getWidth()
    {
        return imagesx($this->im);
    }

    public function getHeight()
    {
        return imagesy($this->im);
    }



}





?>