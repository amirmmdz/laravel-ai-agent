<?php

use Illuminate\Support\Str;

function make_label($name)
{
    $name = str_contains($name, '.') ? str($name)->before('.')->value() : $name;
    $pure_name = str_replace('_', ' ', str_replace('_id', '', $name));
    return __(Str::title($pure_name));
}