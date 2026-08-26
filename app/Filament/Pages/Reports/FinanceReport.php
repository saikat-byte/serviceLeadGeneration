<?php

namespace App\Filament\Pages\Reports;

use App\Models\Payment;
use App\Enums\PaymentStatus;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class FinanceReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static \UnitEnum|string|null $navigationGroup = 'Reports & Analytics';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'Finance Report';

    // FIX: Ekhane 'static' keyword remove kora holo
    protected string $view = 'filament.pages.reports.finance-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Sudhumatro Paid transactions gulo nibo
                Payment::query()
                    ->where('status', PaymentStatus::PAID->value)
                    ->with(['customer', 'booking.provider'])
            )
            ->columns([
                TextColumn::make('paid_at')
                    ->label('Paid Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),

                TextColumn::make('booking_id')
                    ->label('Booking ID')
                    ->searchable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('booking.provider.name')
                    ->label('Provider')
                    ->searchable(),

                TextColumn::make('gateway')
                    ->label('Gateway')
                    ->badge()
                    ->color('info'),

                TextColumn::make('amount')
                    ->label('Gross Revenue (₹)')
                    ->numeric(2)
                    ->sortable()
                    // Nicher line ti automatically column er niche Total jogfol dekhabe
                    ->summarize(Sum::make()->label('Total')->numeric(2)),
            ])
            ->filters([
                Filter::make('paid_at')
                    ->form([
                        DatePicker::make('from')->label('From Date'),
                        DatePicker::make('until')->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('paid_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('paid_at', '<=', $date),
                            );
                    }),

                    SelectFilter::make('booking.provider_id')
                    ->label('Filter by Provider')
                    ->relationship('booking.provider', 'name')
                    ->searchable(),
                    SelectFilter::make('booking.customer_id')
                    ->label('Filter by Customer')
                    ->relationship('booking.customer', 'name')
                    ->searchable(),
            ])
            ->defaultSort('paid_at', 'desc');
    }
}