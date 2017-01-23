<?php
namespace Webbhuset\Bifrost\Core\Utils;

class DataGenerator
{
    protected $vowels = [
        'a' => 8.2,
        'o' => 7.5,
        'u' => 2.8,
        'e' => 12.7,
        'i' => 7.0,
        'y' => 2.0,
    ];
    protected $consonants = [
        'b' => 1.5,
        'c' => 2.8,
        'd' => 4.3,
        'f' => 2.2,
        'g' => 2.0,
        'h' => 6.1,
        'j' => 1.0,
        'k' => 1.0,
        'l' => 4.0,
        'm' => 2.4,
        'n' => 6.7,
        'p' => 1.9,
        'q' => 1.0,
        'r' => 6.0,
        's' => 6.3,
        't' => 9.1,
        'v' => 1.0,
        'w' => 2.4,
        'x' => 1.0,
        'z' => 1.0,
    ];

    protected $globalSeed = '';
    protected $rowSeed    = '';
    protected $pCache     = [];
    protected $pIdx       = 0;

    public function __construct()
    {
        $applyFreq = function($letters) {
            $min = max(1, min($letters));
            $result = [];
            foreach ($letters as $letter => $freq) {
                $count = round($freq / $min);
                $result = array_merge($result, array_fill(0, $count, $letter));
            }

            return $result;
        };
        $this->vowels = $applyFreq($this->vowels);
        $this->consonants = $applyFreq($this->consonants);
    }

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

    public function getRandomBytesGenerator($len = null, $fieldSeed = '')
    {
        $seed        = $this->getSeed() . $fieldSeed;
        $randomBytes = md5($seed, true);
        $idx         = 0;

        while (!is_int($len) || $len-- > 0) {
            $randomByte = ord($randomBytes[$idx]);
            yield ($randomByte);
            if ($idx == 15) {
                $randomBytes   = md5($randomBytes.$seed, true);
                $idx    = 0;
            } else {
                $idx += 1;
            }
        }
    }

    public function getString($minLen, $maxLen, $fieldSeed)
    {
        $len  = $this->getInt($minLen, $maxLen, $fieldSeed);
        $seed = $this->getSeed() . $fieldSeed;
        $data = md5($seed, true);
        $string = '';

        for ($i = 0; $i < $len; $i++) {
            $idx = $i % 16;
            $ord = ord($data[$idx]);
            $ord = $ord % 95;
            $ord += 32;
            $string .= chr($ord);
            if ($idx == 0) {
                $data = md5($data.$seed, true);
            }
        }
        return $string;
    }

    public function getLetters($minLen, $maxLen, $fieldSeed)
    {
        $len  = $this->getInt($minLen, $maxLen, $fieldSeed);
        $seed = $this->getSeed() . $fieldSeed;
        $data = md5($seed, true);
        $string = '';

        for ($i = 0; $i < $len; $i++) {
            $idx = $i % 16;
            $ord = ord($data[$idx]);
            $ord = $ord % 26;
            $ord += 97;
            $string .= chr($ord);
            if ($idx == 0) {
                $data = md5($data.$seed, true);
            }
        }

        return $string;
    }

    public function getWord($minLen, $maxLen, $fieldSeed)
    {
        $len  = $minLen == $maxLen
              ? $minLen
              : $this->getInt($minLen, $maxLen, $fieldSeed);

        $seed = $this->getSeed() . $fieldSeed;
        $data = md5($seed, true);
        $string = '';

        for ($i = 0; $i < $len; $i++) {
            $idx        = $i % 16;
            $ord        = ord($data[$idx]);
            $vIdx       = $ord % count($this->vowels);
            $cIdx       = $ord % count($this->consonants);
            $string    .= $this->consonants[$cIdx];

            if (strlen($string) < $len) {
                $string .= $this->vowels[$vIdx];
                $i += 1;
            }

            if ($idx == 0) {
                $data = md5($data.$seed, true);
            }
        }

        return $string;
    }

    public function getSentence($fieldSeed)
    {
        $seed = $this->getSeed() . $fieldSeed;
        $sentence = [];

        $gen = $this->getRandomBytesGenerator(null, $seed);

        $len = 4 + $gen->current() % 10;
        $gen->next();
        $sentence[] = ucfirst($this->getWord(2, 12, $seed.$len));

        for ($i = 0; $i < $len; $i++) {
            $wordSeed = $gen->current();
            $gen->next();
            $word = $this->getWord(2, 12, $seed.$wordSeed);
            $sentence[] = $word;
        }

        return implode(' ', $sentence) . '.';
    }

    public function getParagraph($fieldSeed, $asHtml = false)
    {
        if (count($this->pCache) > 5000) {
            $this->pIdx = ($this->pIdx + 1) % count($this->pCache);
            return $this->pCache[$this->pIdx];
        }
        $seed = $this->getSeed() . $fieldSeed;
        $paragraph = [];

        $gen = $this->getRandomBytesGenerator(null, $seed);

        $len = 2 + $gen->current() % 5;
        $gen->next();

        for ($i = 0; $i < $len; $i++) {
            $sentenceSeed = $gen->current();
            $gen->next();
            $sentence = $this->getSentence($seed.$sentenceSeed);
            $paragraph[] = $sentence;
        }

        $p = implode(' ', $paragraph);

        if ($asHtml) {
            $p = '<p>'.$p.'</p>';
        }

        $p = $p . "\n";
        $this->pCache[] = $p;

        return $p;
    }

    public function getLongText($fieldSeed, $asHtml = false)
    {
        $seed = $this->getSeed() . $fieldSeed;
        $text = [];

        $gen = $this->getRandomBytesGenerator(null, $seed);

        $len = 1 + $gen->current() % 8;
        $gen->next();

        if ($asHtml) {
            $text[] = '<h3>' . $this->getSentence($seed.$len) . "</h3>\n";
            $text[] = $this->getParagraph($seed.$len, $asHtml);
        }

        for ($i = 0; $i < $len; $i++) {
            $paragraphSeed = $gen->current();
            $gen->next();
            $paragraph = $this->getParagraph($seed.$paragraphSeed, $asHtml);

            if ($asHtml) {
                $text[] = '<h4>' . $this->getSentence($seed.$paragraphSeed) . "</h4>\n";
            }
            $text[] = $paragraph;
        }

        $text = implode('', $text);

        return $text;
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

        return date('Y-m-d 00:00:00',$data);
    }

    public function getFloat($min, $max, $fieldSeed)
    {
        $data = $this->getInt($min, $max, $fieldSeed);
        if (0 == $data) {
            return 0.5;
        }

        $magnitude = floor(log10($data));
        $fraction  = (1 / $data) * pow(10, $magnitude);

        return $data + $fraction;
    }


}
