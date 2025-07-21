<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerRelationManager extends RelationManager
{
    protected static string $relationship = 'customer';

     public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_name')
                    ->required()
                    ->maxLength(100),
                    
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('address1')  
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address2')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->maxLength(20),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('address1')
                    ->searchable(),
            ])
            ->filters([])
            ->headerActions([]) // Remove create action (customer should exist first)
            ->actions([
                Tables\Actions\EditAction::make(), // Allow editing customer
            ]);
    }
}
