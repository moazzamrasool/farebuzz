<?php

namespace App\Http\Controllers\Admin;

class ExclusionController extends PackageFeatureController
{
    protected string $type = 'exclusion';
    protected string $routeName = 'exclusions';
    protected string $label = 'Exclusion';
}
