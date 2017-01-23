<?php

namespace Webbhuset\Bifrost\Core\Component\Read\Mock;

use Webbhuset\Bifrost\Core\Utils\DataGenerator;
use Webbhuset\Bifrost\Core\Component\ComponentInterface;

class Customer implements ComponentInterface
{
    protected $prefixes = [
        'Dr.',
        'Mr.',
        'Ms.',
        'Mrs.',
        'Sr.',
        'Sra.',
        'Sa.',
        'Herr',
        'Frau',
        'Fru',
        'Fröken',
    ];

    public function __construct($seed)
    {
        $this->dataGenerator = new DataGenerator;
        $this->dataGenerator->setGlobalSeed($seed);
    }

    public function process($count)
    {
        if (is_array($count)) {
            $count = reset($count);
        }
        for ($i = 0; $i < $count; $i++) {
            $generator = $this->dataGenerator->setRowSeed($i);

            $customer = [
                'firstname'             => ucfirst($generator->getWord(4, 8, 'firstname')),
                'lastname'              => ucfirst($generator->getWord(4, 8, 'lastname')),
                'middlename'            => ucfirst($generator->getWord(0, 5, 'middlename')),
                'prefix'                => $this->getPrefix($i),
                'email'                 => "customer-{$i}@example.com",
                'group_id'              => 'General',
                'website_id'            => 'Main Website',
                'store_id'              => 0,
                'dob'                   => $generator->getDate('1900-01-01', '2000-01-01', 'dob'),
                'street'                => $generator->getWord(10, 20, 'street') . ' ' . $generator->getInt(1, 99, 'street'),
                'postcode'              => $generator->getInt(10000, 99999, 'postcode'),
                'country'               => $this->getCountry($i),
                'password_hash'         => $this->getPasswordHash('apa123', $generator),
                'attribute_set_id'      => 'Default',
                'gender'                => $i % 2 ? 'Male' : 'Female',
            ];

            yield $customer;
        }
    }

    public function getPrefix($i)
    {
        if ($i % 2 == 1) {
            return '';
        }

        $i /= 2;

        $idx = $i % count($this->prefixes);

        return $this->prefixes[$idx];
    }

    public function getCountry($i)
    {
        $countries = [
            'SE', 'NO', 'DK', 'FI', 'DE', 'US', 'ES', 'FR', 'NL', 'PL', 'RU'
        ];

        $idx = $i % count($countries);

        return $countries[$idx];
    }

    public function getPasswordHash($pwd, $generator)
    {
        $salt = $generator->getLetters(32, 32, 'salt');
        $hash = md5($salt.$pwd) . ':' . $salt;

        return $hash;
    }
}
