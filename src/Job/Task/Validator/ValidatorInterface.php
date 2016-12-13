<?php
namespace Webbhuset\Bifrost\Core\Job\Task\Validator;

interface ValidatorInterface
{
    public function __construct($params);
    public function init($args);
    public function sanitize($entity);
    public function validate($entity);
}
