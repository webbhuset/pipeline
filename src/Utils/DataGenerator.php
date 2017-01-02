<?php
namespace Webbhuset\Bifrost\Core\Utils;

class DataGenerator
{
    protected $globalSeed = '';
    protected $rowSeed    = '';

    public function setGlobalSeed($seed)
    {
        $this->globalSeed = (string) $seed;
        return $this;
    }

    public function setRowSeed($seed)
    {
        $this->rowSeed = (string) $seed;
        return $this;
    }

    protected function getSeed()
    {
        return $this->globalSeed . $this->rowSeed;
    }

    public function getString($minLen, $maxLen, $fieldSeed)
    {
        $len  = $this->getInt($minLen, $maxLen, $fieldSeed);
        $seed = $this->getSeed() . $fieldSeed;
        $data = md5($seed);
        $data = str_pad('', $len, $data);

        return substr($data, 0, $len);
    }

    public function getInt($min, $max, $fieldSeed)
    {
        $seed   = $this->getSeed() . $fieldSeed;
        $data   = crc32($seed);
        $diff   = $max - $min;

        return $min + ($data % $diff);
    }

    public function getBool($fieldSeed)
    {
        $seed   = $this->getSeed() . $fieldSeed;
        $data   = crc32($seed);

        return (bool) ($data % 2);
    }

    public function getDate($min, $max, $fieldSeed)
    {
        $min  = strtotime($min);
        $max  = strtotime($max);
        $data = $this->getInt($min, $max, $fieldSeed);

        return date('Y-m-d',$data);
    }

    public function getFloat($min, $max, $fieldSeed)
    {
        $data = $this->getInt($min, $max, $fieldSeed);
        if (0 == $data) {
            return 0.5;
        }

        $magnitude = floor(log10($data));
        $fraction  = (1 / $data) * 10**$magnitude;

        return $data + $fraction;
    }


}
