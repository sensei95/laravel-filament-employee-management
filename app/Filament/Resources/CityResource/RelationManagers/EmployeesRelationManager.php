<?php

namespace App\Filament\Resources\CityResource\RelationManagers;

use App\Models\City;
use App\Models\Country;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::getCountryIdSelect(),
                self::getStateIdSelect(),
                self::getCityIdSelect(),
                self::getDepartmentIdSelect(),
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(table: Employee::class, ignoreRecord: true),
                TextInput::make('address')
                    ->required(),
                TextInput::make('zip_code')
                    ->required(),
                DatePicker::make('birth_date')
                    ->required(),
                DatePicker::make('hired_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('department.name')->sortable(),
                TextColumn::make('created_at')->date(),
                TextColumn::make('hired_at')->date(),
            ])
            ->filters([
                SelectFilter::make('department_id')->relationship('department', 'name')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function getCountryIdSelect(): Select
    {
        return Select::make('country_id')
            ->label('Country')
            ->options(Country::all()->pluck('name','id')->toArray())
            ->searchable()
            ->reactive()
            ->afterStateUpdated(function(callable $set){
                return $set('state_id', null);
            })
            ->required();
    }
    private static function getCityIdSelect(): Select
    {
        return  Select::make('city_id')
            ->label('city')
            ->required()
            ->options(function(callable $get){
                $state = State::find($get('state_id'));

                if(!$state) {
                    return City::all()->pluck('name','id');
                }

                return $state->cities->pluck('name','id');

            })
            ->disabled(function(callable $get){
                return $get('state_id') == null ? true : false;
            });
    }
    private static function getStateIdSelect(): Select
    {
        return Select::make('state_id')
            ->label('State')
            ->required()
            ->options(function(callable $get){
                $country = Country::find($get('country_id'));

                if(!$country) {
                    return State::all()->pluck('name','id');
                }

                return $country->states->pluck('name','id');

            })
            ->reactive()
            ->afterStateUpdated(function(callable $set){
                return $set('city_id', null);
            })
            ->disabled(function(callable $get){
                return $get('country_id') == null ? true : false;
            });
    }
    private static function getDepartmentIdSelect(): Select
    {
        return Select::make('department_id')
            ->searchable()
            ->required()
            ->relationship('department','name');
    }
}
