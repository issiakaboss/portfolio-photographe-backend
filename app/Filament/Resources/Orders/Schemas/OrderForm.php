<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')
                    ->label('N° de commande')
                    ->disabled(),
                TextInput::make('customer_name')
                    ->label('Acheteur')
                    ->disabled(),
                TextInput::make('customer_email')
                    ->label('Email')
                    ->disabled(),
                TextInput::make('amount')
                    ->label('Montant')
                    ->disabled(),
                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'paid' => 'Payée',
                        'failed' => 'Échouée',
                        'shipped' => 'Expédiée',
                    ])
                    ->disabled(),
                Textarea::make('shipping_address')
                    ->label('Adresse de livraison')
                    ->formatStateUsing(fn (?array $state): string => implode("\n", array_filter([
                        $state['address_line_1'] ?? null,
                        trim(($state['postal_code'] ?? '').' '.($state['admin_area_2'] ?? '')),
                        $state['country_code'] ?? null,
                    ])))
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }
}
