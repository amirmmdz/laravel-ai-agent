<?php

namespace App\Providers;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {

        Field::configureUsing(function (Field $field): void {
            $field->label(fn(Component $component) => make_label($component->getName()));
        });

        Column::configureUsing(function (Column $column): void {
            $column->label(fn() => make_label($column->getName()))
                ->placeholder(__("No Data"));
        });
    }
}
