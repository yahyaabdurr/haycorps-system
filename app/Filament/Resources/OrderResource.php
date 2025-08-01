<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Get;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                ->schema([
                    Forms\Components\TextInput::make('orderId')
                        ->label('Order ID')
                        ->required()
                        ->default('ORD-' . Str::upper(Str::random(6)))
                        ->maxLength(50)
                        ->disabled(),

                     Forms\Components\DatePicker::make('orderDate')
                        ->required()
                        ->default(now()->toDateString())
                        ->disabled(),
                    
                    Forms\Components\Select::make('orderStatus')
                        ->options([
                            'Pending' => 'Pending',
                            'Processing' => 'Processing',
                            'Completed' => 'Completed',
                            'Cancelled' => 'Cancelled',
                        ])
                        ->required()
                        ->default('Pending'),
                    
                    Forms\Components\DatePicker::make('completionDate')
                        ->required(),
                    Forms\Components\FileUpload::make('fileUrl')
                        ->label('File Attachment')
                        ->directory('order-attachments')
                        ->downloadable()
                        ->preserveFilenames(),
                ])
                ->columns(2),
            
            Forms\Components\Section::make('Employee & Files')
                ->schema([
                    Forms\Components\TextInput::make('picEmployee')
                        ->label('PIC Employee')
                        ->required(),
                    
                    
                ])
                ->columns(2),
            
            Forms\Components\Section::make('Customer Information')
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer Name')
                        ->relationship('customer', 'customer_name')
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('customer_name')
                                ->label('Customer Name')
                                ->required(),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email(),
                            Forms\Components\TextInput::make('phone_number')
                                ->label('Phone Number'),
                                
                            Forms\Components\TextInput::make('institution')
                                ->label('Institution'),

                             Forms\Components\TextInput::make('address1')
                                ->label('Address 1')
                                ->required(),

                            Forms\Components\TextInput::make('address2')
                                ->label('Address 2')
                                

                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            $customer = \App\Models\Customer::find($state);
                            if ($customer) {
                                $set('customer.email', $customer->email);
                                $set('customer.phone_number', $customer->phone_number);
                                $set('customer.institution', $customer->institution);
                                $set('customer.address1', $customer->address1);
                                $set('customer.address2', $customer->address2);
                            } else {
                                $set('customer.email', null);
                                $set('customer.phone_number', null);
                                $set('customer.institution', null);
                                $set('customer.address1', null);
                                $set('customer.address2', null);
                            }
                        }),

                    Forms\Components\TextInput::make('customer.email')
                        ->label('Email')
                        ->disabled(),

                    Forms\Components\TextInput::make('customer.phone_number')
                        ->label('Phone Number')
                        ->disabled(),

                    Forms\Components\TextInput::make('customer.institution')
                        ->label('Institution')
                        ->disabled(),

                    Forms\Components\TextInput::make('customer.address1')
                        ->label('Address 1')
                        ->disabled(),
                    Forms\Components\TextInput::make('customer.address2')
                        ->label('Address 2')
                        ->disabled(),
                ])
                ->columns(2),

                Forms\Components\Section::make('Order Items')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $orderItems = $get('orderItems') ?? [];
                        $totalSum = collect($orderItems)->sum('jumlah');
                        $set('total_order_sum', $totalSum);
                    })
                    ->schema([
                        Forms\Components\Repeater::make('orderItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'product_name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (!$state) {
                                            $set('item_price', null);
                                            $set('jumlah', 0);
                                            return;
                                        }
                                        
                                        $product = \App\Models\Product::find($state);
                                        if ($product) {
                                            $set('item_price', $product->price);
                                            $set('item_name', $product->product_name);
                                            // Recalculate total immediately
                                            $set('jumlah', 
                                                ($get('number_of_item') ?? 1) * $product->price
                                            );
                                        }
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('item_name')
                                    ->hidden()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('number_of_item')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('jumlah', 
                                            ($get('number_of_item') ?? 1) * ($get('item_price') ?? 0)
                                        );
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('item_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $set('jumlah', 
                                            ($get('number_of_item') ?? 1) * ($get('item_price') ?? 0)
                                        );
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(),

                                Forms\Components\TextInput::make('description')
                                    ->label('Notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns([
                                'default' => 1,
                                'sm' => 2,
                                'md' => 4,
                            ])
                            ->columnSpanFull()
                            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                if (isset($data['product_id'])) {
                                    $product = \App\Models\Product::find($data['product_id']);
                                    if ($product) {
                                        $data['item_name'] = $product->product_name;
                                    }
                                }
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                if (isset($data['product_id'])) {
                                    $product = \App\Models\Product::find($data['product_id']);
                                    if ($product) {
                                        $data['item_name'] = $product->product_name;
                                    }
                                }
                                return $data;
                            })
                            ->reorderable()
                            ->cloneable()
                            ->collapsible(),

                        Forms\Components\TextInput::make('total_order_sum')
                            ->label('Total Order Sum')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(false)
                            ->reactive(),
                    ])

                    ->columns(2),
            
        ]);
            
    }

    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orderId')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('customer.customerName')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('orderStatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Processing' => 'info',
                        'Completed' => 'success',
                        'Cancelled' => 'danger',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('totalPrice')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('orderDate')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('orderStatus')
                    ->options([
                        'Pending' => 'Pending',
                        'Processing' => 'Processing',
                        'Completed' => 'Completed',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->attribute('orderStatus'),
                
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('orderDate', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
           
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer:id,customerName']) // Eager load only needed fields
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

 // Change from protected to public
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}