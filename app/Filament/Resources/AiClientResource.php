<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiClientResource\Pages;
use App\Filament\Resources\AiClientResource\RelationManagers;
use App\Models\AiClient;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class AiClientResource extends Resource
{
    protected static ?string $model = AiClient::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->options(function () {
                        return User::all()->map(function (User $user) {
                            return $user->name . ' ' . $user->family;
                        })->toArray();
                    })
                    ->searchable(),
                Forms\Components\Hidden::make('uuid')
                    ->default(Str::uuid()->toString()),
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Hidden::make('token')
                    ->default(Str::uuid()->toString()),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(1),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->getStateUsing(function ($record) {
                        return $record->user?->name . ' ' . $record->user?->family;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('token')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiClients::route('/'),
            'create' => Pages\CreateAiClient::route('/create'),
            'edit' => Pages\EditAiClient::route('/{record}/edit'),
        ];
    }
}
