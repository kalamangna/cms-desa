<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class VisitSiteWidget extends Widget
{
    protected string $view = 'filament.widgets.visit-site-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';
}
