<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->schema([
                        Forms\Components\TextInput::make('product_code')
                            ->label('Product Code')
                            ->default('PROD-' . Str::upper(Str::random(6)))
                            ->disabled()
                            ->dehydrated(),
                            
                        Forms\Components\TextInput::make('product_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options([
                                'clothing' => 'Clothing',
                                'electronics' => 'Electronics',
                                'food' => 'Food',
                                'other' => 'Other'
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                            
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Product Image')
                            ->directory('products')
                            ->image()
                            ->imageEditor(),
                            
                        Forms\Components\Toggle::make('active')
                            ->default(true),
                    ])->columns(2),
                    
                

                Forms\Components\Section::make('Pricing & Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('cost')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->label('Cost Price'),
                            
                        Forms\Components\TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->label('Selling Price'),
                            
                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->required()
                            ->minValue(0),
                    ])->columns(3),
                
                Forms\Components\Section::make('Physical Attributes')
                    ->schema([
                       Forms\Components\TextInput::make('weight')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100000) // 100kg in grams
                        ->suffix('grams'),
                        
                        Forms\Components\TextInput::make('volume')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000000) // 1m³ in cm³
                            ->suffix('cm³'),
    
                    ])->columns(2),    
                    


                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image'),
                    
                Tables\Columns\TextColumn::make('product_code')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('product_name')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('stock')
                ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost')
                    ->money('IDR')
                    ->label('Cost')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('weight')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} g" : '-')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('volume')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} cm³" : '-')
                    ->sortable(),   

                    
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'clothing' => 'Clothing',
                        'electronics' => 'Electronics',
                        'food' => 'Food',
                        'other' => 'Other'
                    ]),
                    
                Tables\Filters\TernaryFilter::make('active'),
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
            // RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}