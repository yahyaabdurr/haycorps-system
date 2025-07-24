<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_id')
                    
                    ->label('Customer ID')
                    ->required()
                    ->default('CUST-' . Str::upper(Str::random(6)))
                    ->maxLength(50)
                    ->disabled(),
                Forms\Components\TextInput::make('customer_name')
                    ->required()
                    ->label('Customer Name'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Phone Number'),
                Forms\Components\TextInput::make('address1')
                    ->label('Address 1'),
                Forms\Components\TextInput::make('address2')
                    ->label('Address 2'),
                Forms\Components\TextInput::make('institution')
                    ->label('Institution'),
                Forms\Components\Toggle::make('active')
                    ->label('Active')
                    ->default(true),
            ])
            ->columns(2);
    }

   public static function mutateFormDataBeforeSave(array $data): array
    {
        \Log::info('Starting customer creation', ['data' => $data]);
    
        try {
           
            $data['created_by'] = auth()->user()?->name ?? 'SYSTEM';
            $data['last_modified_by'] = auth()->user()?->name ?? 'SYSTEM';
            
            \Log::info('Data after mutation', ['data' => $data]);
            return $data;
        } catch (\Exception $e) {
            \Log::error('Mutation error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public static function table(Table $table): Table
    {
        return $table
                ->columns([
                Tables\Columns\TextColumn::make('customer_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('institution')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address1')
                    ->label('Address 1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address2')
                    ->label('Address 2')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
