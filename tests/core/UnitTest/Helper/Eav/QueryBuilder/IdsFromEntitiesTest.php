<?php

namespace Webbhuset\Bifrost\Core\Test\UnitTest\Helper\Eav\QueryBuilder;

use Webbhuset\Bifrost\Core\Helper\Db;
use Webbhuset\Bifrost\Core\Data\Eav;

class IdsFromEntitiesTest
{
    public static function __constructTest($test)
    {
    }

    public static function buildQueryTest($test)
    {
        //$keys       = ['website_id', 'email'];
        $keys       = ['sku', 'key_one', 'key_two'];
        $attributes = self::getAttributes();
        $adapter    = new TestAdapter;
        $entities   = self::getEntities();

        $result = <<<'SQL'
SELECT MAX(`e`.`entity_id`) AS `entity_id` FROM
(
  SELECT 0 AS `pos`,'SKU-1111' AS `sku` UNION ALL
  SELECT 1,'SKU-2222' UNION ALL
  SELECT 2,'SKU-3333'
) AS `_k`
LEFT JOIN `entity` AS `e` ON `e`.`sku` = `_k`.`sku`
GROUP BY `_k`.`pos`
ORDER BY `_k`.`pos` ASC
SQL;

        $test->newInstance(['sku'], $attributes, $adapter)
            ->testThatArgs($entities)
            ->returnsValue($result)
        ;
        $result = <<<'SQL'
SELECT MAX(`e`.`entity_id`) AS `entity_id` FROM
(
  SELECT 0 AS `pos`,'1' AS `website_id`,'e1@example.com' AS `email` UNION ALL
  SELECT 1,'2','e2@example.com' UNION ALL
  SELECT 2,'3','e3@example.com'
) AS `_k`
LEFT JOIN `entity` AS `e` ON `e`.`website_id` = `_k`.`website_id` AND `e`.`email` = `_k`.`email`
GROUP BY `_k`.`pos`
ORDER BY `_k`.`pos` ASC
SQL;

        $test->newInstance(['website_id', 'email'], $attributes, $adapter)
            ->testThatArgs($entities)
            ->returnsValue($result)
        ;

        $result = <<<'SQL'
SELECT MAX(`at_key_two`.`entity_id`) AS `entity_id` FROM
(
  SELECT 0 AS `pos`,'K1AA' AS `key_one`,'11' AS `key_two` UNION ALL
  SELECT 1,'K1BB','22' UNION ALL
  SELECT 2,'K1CC','33'
) AS `_k`
LEFT JOIN `entity_varchar` AS `at_key_one` ON `at_key_one`.`attribute_id` = 2 AND `at_key_one`.`value` = `_k`.`key_one`
LEFT JOIN `entity_int` AS `at_key_two` ON `at_key_two`.`entity_id` = `at_key_one`.`entity_id` AND `at_key_two`.`attribute_id` = 3 AND `at_key_two`.`value` = `_k`.`key_two`
GROUP BY `_k`.`pos`
ORDER BY `_k`.`pos` ASC
SQL;

        $test->newInstance(['key_one', 'key_two'], $attributes, $adapter)
            ->testThatArgs($entities)
            ->returnsValue($result)
        ;
    }

    protected static function getAttributes()
    {
        $testData = [
            'name'          => 'varchar',
            'key_one'       => 'varchar',
            'key_two'       => 'int',
            'sku'           => 'static',
            'email'         => 'static',
            'website_id'    => 'static',
        ];

        $attributes = [];
        $id = 1;

        foreach ($testData as $code => $type) {
            $attributes[] = new Eav\Attribute([
                'id'            => $id,
                'code'          => $code,
                'table'         => $type == 'static' ? 'entity' : "entity_{$type}",
                'backendType'   => $type,
                'scope'         => new Eav\Attribute\Scope([]),
            ]);

            $id += 1;
        }

        return $attributes;
    }

    protected static function getEntities()
    {
        return [
            [
                'sku'           => 'SKU-1111',
                'name'          => 'Entity 1',
                'key_one'       => 'K1AA',
                'key_two'       => 11,
                'email'         => 'e1@example.com',
                'website_id'    => 1,
            ],
            [
                'sku'           => 'SKU-2222',
                'name'          => 'Entity 2',
                'key_one'       => 'K1BB',
                'key_two'       => 22,
                'email'         => 'e2@example.com',
                'website_id'    => 2,
            ],
            [
                'sku'           => 'SKU-3333',
                'name'          => 'Entity 3',
                'key_one'       => 'K1CC',
                'key_two'       => 33,
                'email'         => 'e3@example.com',
                'website_id'    => 3,
            ],
        ];
    }
}

class TestAdapter implements Db\AdapterInterface
{
    public function quote($string)
    {
        return "'{$string}'";
    }

    public function quoteIdentifier($string)
    {
        return "`{$string}`";
    }
}
