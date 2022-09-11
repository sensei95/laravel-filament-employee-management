<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make()->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->required()
                            ->maxLength(255)
                            ->email()
                            ->unique(table: User::class, ignoreRecord: true),
                        self::getPasswordField(),
                        self::getPasswordConfirmationField()
                    ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('created_at')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    private static function getPasswordField()
    {
        return TextInput::make('password')
                    ->required(function(Page $livewire){
                        return $livewire instanceof CreateRecord;
                    })
                    ->minLength(8)
                    ->maxLength(255)
                    ->password()
                    ->same('password_confirmation')
                    ->dehydrateStateUsing(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state));
    }
    private static function getPasswordConfirmationField()
    {
        return TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->required(function(Page $livewire){
                        return $livewire instanceof CreateRecord;
                    })
                    ->minLength(8)
                    ->maxLength(255)
                    ->password()
                    ->dehydrated(false);
    }
}
