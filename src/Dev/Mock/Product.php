<?php

namespace Webbhuset\Whaskell\Dev\Mock;

use Webbhuset\Bifrost\Utils\DataGenerator;

class Product
{
    protected $seed;
    protected $noOfEntities;
    protected $dataGenerator;
    protected $types = [
        'Alter-Ego',
        'Ability',
        'Bacteria',
        'Beer',
        'Bondage',
        'Bra',
        'Cake',
        'Call',
        'Cheese',
        'Cow',
        'Crap',
        'Date',
        'Dinner',
        'Douchebag',
        'Disease',
        'Dreams',
        'Dress',
        'E-Mail',
        'Event',
        'Expectations',
        'Experience',
        'Fact',
        'Failure',
        'Family',
        'Fish',
        'Food',
        'Friend',
        'Game',
        'Gentleman',
        'Gift',
        'Glasses',
        'Glue',
        'Hat',
        'Habit',
        'Happiness',
        'Hardware',
        'Holiday',
        'Ice Cream',
        'Illness',
        'Image',
        'Itch',
        'Jacket',
        'Jeans',
        'Joke',
        'Judgement',
        'Key',
        'Kick',
        'Kingdom',
        'Knowledge',
        'Lie',
        'Life',
        'Liquid',
        'Love',
        'Lotion',
        'Makeup',
        'Memory',
        'Milk',
        'Moment',
        'Movie',
        'Needle',
        'Name',
        'Observation',
        'Offer',
        'Odor',
        'Onion',
        'Package',
        'Painting',
        'Pet',
        'Picture',
        'Poison',
        'Powder',
        'Promise',
        'Qualification',
        'Question',
        'Rage',
        'Rash',
        'Realization',
        'Sandwich',
        'Salad',
        'Satisfaction',
        'Servant',
        'Service',
        'Shelf',
        'Shoes',
        'Smell',
        'Struggle',
        'T-Shirt',
        'Task',
        'Technology',
        'Things',
        'Thought',
        'Top',
        'Trip',
        'Truth',
        'Underdog',
        'Underwear',
        'Union',
        'Verb',
        'Vacation',
        'Variety',
        'Verdict',
        'Video',
        'Virus',
        'Weapon',
        'Wakeup-call',
        'Wine',
    ];

    protected $colors = [
        'Black',
        'Blue',
        'Brown',
        'Green',
        'Grey',
        'Pink',
        'Red',
        'White',
        'Yellow',
    ];

    protected $sizes = [
        'X-Small',
        'Small',
        'Meduim',
        'Large',
        'X-Large',
        'XX-Large',
        'S',
        'M',
        'L',
        'XL',
        'Stor',
        'Större',
        'Störst',
        'Liten',
        'Mindre',
        'Minst',
    ];

    protected $intensifiers = [
        'Almost',
        'Bloody',
        'Bad-Ass',
        'Casually',
        'Damn',
        'Darn',
        'Dreadfully',
        'Extremely',
        'Fantastically',
        'Hardly',
        'Horribly',
        'Insanely',
        'Mostly',
        'Not a Very',
        'Not Even',
        'Not so',
        'Occasionally',
        'One',
        'Quite',
        'Radically',
        'Really',
        'Ridiculously',
        'So',
        'So not',
        'Some',
        'Somewhat',
        'Super',
        'Super-Duper',
        'Terribly',
        'The Most',
        'The Worst',
        'Too',
        'Totally',
        'Unusually',
        'Unbelievably',
        'Very',
        'Whatever',
    ];

    protected $adjectives = [
        'Acid',
        'Ambitious',
        'American',
        'Awful',
        'Bad',
        'Big',
        'Boring',
        'Broken',
        'Colorful',
        'Cranky',
        'Crappy',
        'Crazy',
        'Deadly',
        'Delicious',
        'Disappointing',
        'Disgusting',
        'Dirty',
        'Dreamy',
        'Enormous',
        'Evil',
        'Exciting',
        'Exotic',
        'Fancy',
        'Fake',
        'Fishy',
        'Fleshy',
        'Flexible',
        'Flimsy',
        'Funky',
        'Funny',
        'Genuine',
        'Giant',
        'Great',
        'Happy',
        'Harsh',
        'Half',
        'Ignorant',
        'Illegal',
        'Imaginary',
        'Immoral',
        'Inappropriate',
        'Insane',
        'Itchy',
        'Japanese',
        'Jealous',
        'Keen',
        'Killing',
        'Kick-ass',
        'Late',
        'Lazy',
        'Liquid',
        'Lying',
        'Mighty',
        'Mysterious',
        'Naked',
        'Neat',
        'Necessary',
        'Negative',
        'Natural',
        'Nice',
        'Obvious',
        'Odd',
        'Offensive',
        'Old',
        'Painful',
        'Parallel',
        'Perfect',
        'Poetic',
        'Quick',
        'Racial',
        'Radical',
        'Rare',
        'Ridiculous',
        'Sad',
        'Sarcastic',
        'Sexy',
        'Sparkling',
        'Splendid',
        'Sticky',
        'Stinky',
        'Strange',
        'Tall',
        'Tasty',
        'Ugly',
        'Unbreakable',
        'Uncomfortable',
        'Unfair',
        'Untrustworthy',
        'Unusable',
        'Valuable',
        'Vast',
        'Vicious',
        'Violent',
        'Warm',
        'Wicked',
    ];

    public function __construct($seed)
    {
        $this->dataGenerator = new DataGenerator;
        $this->dataGenerator->setGlobalSeed($seed);
        $generator = $this->dataGenerator;

        $scramble = function($seed) use ($generator) {
            $rnd = $generator->getRandomBytesGenerator(null, $seed);
            return function($a, $b) use ($rnd) {
                $v1 = $rnd->current() - 127;
                $rnd->next();
                $v2 = $rnd->current() - 127;
                $rnd->next();

                return $v1 * $v2;
            };
        };
        usort($this->types, $scramble('types'));
        usort($this->colors, $scramble('colors'));
        usort($this->intensifiers, $scramble('intensifiers'));
        usort($this->adjectives, $scramble('adjectives'));
        usort($this->sizes, $scramble('sizes'));
    }

    public function __invoke($count)
    {
        if (is_array($count)) {
            $count = reset($count);
        }
        for ($i = 0; $i < $count; $i++) {
            $generator = $this->dataGenerator->setRowSeed($i);

            $product = [
                'name'                  => $this->getName($i),
                'description'           => $generator->getLongText('description', true),
                'short_description'     => $generator->getSentence('short_description'),
                'sku'                   => $this->getSku($generator, $i),
                'price'                 => $this->getPrice($generator, 'price'),
                'special_price'         => $this->getPrice($generator, 'special_price'),
                'special_from_date'     => $generator->getDate('2015-12-01', '2017-02-01', 'special_from_date'),
                'special_to_date'       => $generator->getDate('2015-12-01', '2017-02-01', 'special_to_date'),
                'cost'                  => $this->getPrice($generator, 'cost'),
                'weight'                => round($generator->getFloat(10, 50000, 'weight'), 2),
                'manufacturer'          => ucfirst($generator->getWord(4, 4, 'manufacturer')),
                'meta_title'            => $generator->getString(5, 12, 'meta_title'),
                'meta_keyword'          => $generator->getString(5, 12, 'meta_keyword'),
                'meta_description'      => $generator->getString(50, 120, 'meta_description'),
                'image'                 => $generator->getString(5, 12, 'image'),
                'small_image'           => $generator->getString(5, 12, 'small_image'),
                'thumbnail'             => $generator->getString(5, 12, 'thumbnail'),
                'color'                 => $this->colors[$i % count($this->colors)],
                'size'                  => $this->sizes[$i % count($this->sizes)],
                'qty'                   => $generator->getInt(0, 150, 'qty'),
                'is_in_stock'           => $generator->getBool('is_in_stock'),
                'tax_class_id'          => 'Taxable Goods',
                'status'                => 'Enabled',
                'visibility'            => 'Catalog, Search',
                'attribute_set_id'      => 'Default',
            ];

            yield $product;
        }
    }

    protected function getPrice($generator, $field)
    {
        $base = $generator->getInt(0, 9999, $field);

        switch (true) {
            case $base < 2000:
                $price = ($base % 9) + 1.95;
                break;

            case $base < 4000:
                $price = ($base % 9) + 1.99;
                break;

            case $base < 6000:
                $price = (($base % 9) + 1) * 10 + 9;
                break;

            case $base < 8000:
                $price = (($base % 9) + 1) * 100 + 99;
                break;

            default:
                $price = (($base % 89) + 11) * 100 + 90;
                break;

        }

        return (float)$price;
    }

    protected function getSku($generator, $idx)
    {
        return sprintf('1%06d-1%04d-%04d', $idx + 1,  $idx / 10000, $generator->getInt(0, 9999,'sku'));
    }

    protected function getName($i)
    {
        $name       = [];

        $tCnt = count($this->types);
        $aCnt = count($this->adjectives);
        $iCnt = count($this->intensifiers);

        $base = $i;
        $tIdx = $base % $tCnt;

        $base = floor($base / $tCnt);
        $aIdx = ($base + $i) % $aCnt;

        $base = floor($base / $aCnt);
        $iIdx = ($base + $i) % $iCnt;

        $base = $aCnt * $tCnt;

        switch (true) {
            case $i < $base * ($i % 2):
                $wordCount = 2;
                break;

            case $i < $base * $iCnt * ($i % 2):
                $wordCount = 3;
                break;

            default:
                $wordCount = 4;
        }

        $cIdx = $i % count($this->colors);

        switch ($wordCount) {
            case 2:
                $name[] = $this->adjectives[$aIdx];
                $name[] = $this->types[$tIdx];
                break;

            case 3:
                $name[] = $this->intensifiers[$iIdx];
                $name[] = $this->adjectives[$aIdx];
                $name[] = $this->types[$tIdx];
                break;

            case 4:
                $useAdjective = $i % 5;
                $name[] = $this->intensifiers[$iIdx];
                $name[] = $this->adjectives[$aIdx];
                if ($useAdjective) {
                    $aIdx = 1 + $aIdx % count($this->adjectives);
                    $name[] = $this->adjectives[$aIdx];
                } else {
                    $name[] = $this->colors[$i % count($this->colors)];
                }
                $name[] = $this->types[$tIdx];
                break;
        }

        return implode(' ', $name);
    }
}
