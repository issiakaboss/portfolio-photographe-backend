<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_number')
                    ->label('Commande')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('artwork.title')
                    ->label('Œuvre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Acheteur')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('shipping_address')
                    ->label('Adresse de livraison')
                    ->formatStateUsing(fn (?array $state): string => implode(', ', array_filter([
                        $state['address_line_1'] ?? null,
                        trim(($state['postal_code'] ?? '').' '.($state['admin_area_2'] ?? '')),
                        $state['country_code'] ?? null,
                    ])))
                    ->wrap(),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->suffix(' €')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'shipped' => 'info',
                        'failed' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'En attente',
                        'paid' => 'Payée',
                        'failed' => 'Échouée',
                        'shipped' => 'Expédiée',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
