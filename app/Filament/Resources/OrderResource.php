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
use Filament\Forms\Components\Livewire;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';


    // Helper: total from full state
    public static function updateOrderTotalFromState(?array $orderItems, Set $set): void
    {
        // \Log::info('OrderItems state:', $orderItems ?? []);
        logger()->debug('Updating order total from state', ['orderItems' => $orderItems]);
        $total = 0;
        foreach ($orderItems ?? [] as $item) {
            $total += $item['jumlah'] ?? 0;
        }
        logger()->debug('Computed total_order_sum:', ['total' => $total]);
        $set('total_order_sum', $total);
    }


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
                            ->relationship(
                                name: 'customer',
                                titleAttribute: 'customer_name',
                                modifyQueryUsing: fn(Builder $query) => $query->select(['sk_customer', 'customer_name'])
                            )
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
                                $customer = \App\Models\Customer::select([
                                    'sk_customer',
                                    'email',
                                    'phone_number',
                                    'institution',
                                    'address1',
                                    'address2'
                                ])->find($state);

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
                    ->schema([
                        Forms\Components\Repeater::make('orderItems')
                            ->relationship()
                            ->live(debounce: 300)
                            ->afterStateUpdated(function (?array $state, Set $set) {
                                \App\Filament\Resources\OrderResource::updateOrderTotalFromState($state, $set);
                            })
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                // Ensure all required fields are set before creation
                                if (isset($data['product_id'])) {
                                    $product = \App\Models\Product::find($data['product_id']);
                                    if ($product) {
                                        $data['item_name'] = $product->product_name;
                                        if (!isset($data['item_price'])) {
                                            $data['item_price'] = $product->price;
                                        }
                                    }
                                }
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                // Ensure all required fields are set before saving
                                if (isset($data['product_id'])) {
                                    $product = \App\Models\Product::find($data['product_id']);
                                    if ($product) {
                                        $data['item_name'] = $product->product_name;
                                        if (!isset($data['item_price'])) {
                                            $data['item_price'] = $product->price;
                                        }
                                    }
                                }
                                return $data;
                            })
                            ->schema([

                                Forms\Components\Select::make('product_selection') // Changed from product_id
                                    ->label('Product')
                                    ->searchable()
                                    ->options(\App\Models\Product::pluck('product_name', 'sk_product'))
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (!$state) {
                                            $set('item_price', null);
                                            $set('item_name', null);
                                            $set('jumlah', 0);
                                            return;
                                        }

                                        $product = \App\Models\Product::find($state);
                                        if ($product) {
                                            $set('item_price', $product->price);
                                            $set('item_name', $product->product_name);

                                            $quantity = $get('number_of_item') ?? 1;
                                            $set('jumlah', $quantity * $product->price);

                                            \App\Filament\Resources\OrderResource::updateOrderTotalFromState(
                                                $get('../../orderItems'),
                                                $set
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
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $itemPrice = $get('item_price') ?? 0;
                                        $set('jumlah', $state * $itemPrice);

                                        \App\Filament\Resources\OrderResource::updateOrderTotalFromState(
                                            $get('../../orderItems'),
                                            $set
                                        );
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('item_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->live(debounce: 300)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $qty = $get('number_of_item') ?? 1;
                                        $set('jumlah', $qty * $state);
                                        \App\Filament\Resources\OrderResource::updateOrderTotalFromState(
                                            $get('../../orderItems'),
                                            $set
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
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('total_order_sum')
                            ->label('Total Order Sum')
                            ->numeric()
                            ->prefix('Rp')
                            ->reactive()
                            ->dehydrated()
                            ->default(0)
                    ])

                    ->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.customer_name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('order_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Processing' => 'info',
                        'Completed' => 'success',
                        'Cancelled' => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_status')
                    ->options([
                        'Pending' => 'Pending',
                        'Processing' => 'Processing',
                        'Completed' => 'Completed',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->attribute('order_status'),

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
            ->defaultSort('order_date', 'desc');
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
            ->with(['customer:sk_customer,customer_name'])
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
