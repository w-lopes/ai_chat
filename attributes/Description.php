<?php

namespace attributes;

use Attribute;

#[Attribute]
class Description
{
    public function __construct(public string $value) {}
}
