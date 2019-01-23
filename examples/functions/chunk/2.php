<?php
use Webbhuset\Pipeline\Constructor as F;

$fun = F::Compose([
    F::Chunk(100),
    F::Map(function ($ids) {
        return $dbConnection->fetchValuesByIds($ids);
    }),
    F::Expand(),
]);
