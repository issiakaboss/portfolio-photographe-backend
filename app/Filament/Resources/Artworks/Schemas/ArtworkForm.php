<?php

namespace App\Filament\Resources\Artworks\Schemas;

use App\Models\Artwork;
use App\Services\ArtworkMediaService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                    ->label('Image HD')
                    ->acceptedFileTypes(['image/*'])
                    ->maxSize(102400)
                    ->required()
                    ->disk(fn(Get $get, ?Artwork $record) => ($get('is_private') ?? $record?->is_private) ? 'private' : 'public')
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $disk = ($get('is_private') ?? false) ? 'private' : 'public';
                        $path = $file->store('artworks', $disk);

                        return app(ArtworkMediaService::class)->optimize($path, $disk) ?? $path;
                    })
                    ->directory('artworks')
                    ->visibility('public'),

                Select::make('category')
                    ->label('Catégorie')
                    ->options([
                        'photos' => 'Photos',
                        'crafts' => 'Artisanat',
                        'paintings' => 'Peintures',
                        'projects' => 'Projets',
                    ])
                    ->default('photos')
                    ->required(),

                TextInput::make('price')
                    ->label('Prix')
                    ->numeric()
                    ->minValue(0),

                Toggle::make('is_for_sale')
                    ->label('Disponible à la vente'),

                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'available' => 'Disponible',
                        'sold' => 'Vendu',
                    ])
                    ->default('available')
                    ->required(),

                TextInput::make('dimensions')
                    ->label('Dimensions'),

                TextInput::make('materials')
                    ->label('Matériaux'),

                Toggle::make('is_private')
                    ->label('Œuvre privée (Client exclusif)')
                    ->required()
                    ->reactive(),
            ]);
    }
}
