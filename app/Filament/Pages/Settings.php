<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Pages\Page;
use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class Settings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.settings';

    public array $settings = [];

    public function mount(): void
    { 
        $this->settings = Setting::pluck('value', 'key')->toArray();
        $this->settings['currency_symbol'] = $this->settings['currency_symbol'] ?? '$';
    }

    public function save(): void
    {
        foreach ($this->settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        session()->flash('success', 'Settings updated successfully!');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('settings.site_name')
                ->label('Site Name')
                ->required(),
            TextInput::make('settings.site_email')
                ->label('Email')
                ->email(),
            Textarea::make('settings.site_description')
                ->label('Description'),
            TextInput::make('settings.currency_symbol')
                ->default('$')
                ->label('Currency symbol'),
        ]);
    }
}
