<?php

namespace App\Http\Controllers\Admin;

class InclusionController extends PackageFeatureController
{
    protected string $type = 'inclusion';
    protected string $routeName = 'inclusions';
    protected string $label = 'Inclusion';
}
