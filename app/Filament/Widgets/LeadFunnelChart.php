<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\MatchRecord;
use App\Models\ServiceRequest;
use Filament\Widgets\ChartWidget;

class LeadFunnelChart extends ChartWidget
{
    // Ekhane 'static' keyword remove kora holo
    protected ?string $heading = 'Lead Funnel Conversion';
    
    // Sort property always static hoy
    protected static ?int $sort = 2;

    // Polling disable kora holo server load komanor jonno
    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $requestsCount = ServiceRequest::count();
        $leadsCount = Lead::count();
        $matchesCount = MatchRecord::count();
        $bookingsCount = Booking::count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Count',
                    'data' => [$requestsCount, $leadsCount, $matchesCount, $bookingsCount],
                    'backgroundColor' => [
                        '#3b82f6', // Blue
                        '#10b981', // Emerald
                        '#f59e0b', // Amber
                        '#8b5cf6', // Violet
                    ],
                    'borderWidth' => 0,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => ['Service Requests', 'Leads Created', 'Provider Matches', 'Bookings'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}