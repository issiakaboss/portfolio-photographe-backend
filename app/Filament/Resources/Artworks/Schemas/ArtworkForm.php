<?php

namespace App\Filament\Resources\Artworks\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\FileUpload;
use Filament\Schemas\Components\Toggle;
use Filament\Forms\Get;

class ArtworkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
           TextInput::make('title')
                    ->label('Titre')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->label('Fichier (Image / Vidéo)')
                    ->image()
                    ->required()
                    ->disk(fn (Get $get) => $get('is_private') ? 'private' : 'public')
                    ->directory('artworks'),

                TextInput::make('category')
                    ->label('Catégorie')
                    ->default('general')
                    ->required(),

                Toggle::make('is_private')
                    ->label('Œuvre privée (Client exclusif)')
                    ->required()
                    ->reactive(),
            ]);
    }
}
