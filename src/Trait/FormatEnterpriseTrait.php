<?php

namespace App\Trait;

trait FormatEnterpriseTrait
{
    private function formatEnterpriseName(string $enterpriseName): string
    {
        return ucfirst(strtolower($enterpriseName));
    }
}
