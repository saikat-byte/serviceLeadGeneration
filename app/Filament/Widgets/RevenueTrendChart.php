<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Enums\PaymentStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueTrendChart extends ChartWidget
{
    // Non-static heading property
    protected ?string $heading = 'Monthly Revenue Trend (Last 6 Months)';
    
    // Sort 3 dewa holo jate eta ager widget gulor por show kore
    protected static ?int $sort = 3;

    // Polling disable kora holo server load komanor jonno
    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        // 1. Prepare an array for the last 6 months to ensure zero values are shown if no data exists
        $months = collect(range(5, 0))->mapWithKeys(function ($i) {
            $date = Carbon::now()->subMonths($i);
            return [$date->format('Y-m') => [
                'label' => $date->format('M Y'),
                'amount' => 0
            ]];
        });

        // 2. Fetch aggregated data from the database using a single optimized query
        $totals = Payment::select(
                DB::raw('DATE_FORMAT(paid_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', PaymentStatus::PAID->value)
            ->where('paid_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->pluck('total', 'month');

        // 3. Merge database totals into our prepared months array
        $months = $months->map(function ($item, $key) use ($totals) {
            $item['amount'] = round((float) $totals->get($key, 0), 2);
            return $item;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Gross Revenue (₹)',
                    'data' => $months->pluck('amount')->toArray(),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)', // Emerald with opacity
                    'borderColor' => '#10b981', // Emerald Solid
                    'tension' => 0.4, // Creates a smooth curve
                ],
            ],
            'labels' => $months->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}