<?php

namespace App\Filament\Resources\Artworks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;

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
                    ->acceptedFileTypes(['image/*', 'video/mp4', 'video/quicktime', 'video/webm'])
                    ->maxSize(102400) 
                    ->required()
                    ->disk(fn(Get $get, ?\App\Models\Artwork $record) => ($get('is_private') ?? $record?->is_private) ? 'private' : 'public')
                    ->directory('artworks')
                    ->visibility('public'),

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
