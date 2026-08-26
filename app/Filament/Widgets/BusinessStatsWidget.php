<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Enums\CommissionStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStatsWidget extends BaseWidget
{
    // Widget ke dashboard er ekdom upore rakhar jonno sort = 1
    protected static ?int $sort = 1;

    // Shared hosting e server load komanor jonno polling interval disabled (null)
    protected ?string $pollingInterval = null; 

    protected function getStats(): array
    {
        // 1. Gross Revenue (Total successful payments)
        $grossRevenue = Payment::where('status', PaymentStatus::PAID->value)->sum('amount');

        // 2. Platform Commission (Total earned and settled)
        $platformCommission = Commission::whereIn('status', [
            CommissionStatus::EARNED->value, 
            CommissionStatus::SETTLED->value
        ])->sum('amount');

        // 3. Completed Bookings
        $completedBookings = Booking::whereIn('status', [
            BookingStatus::WORK_COMPLETED->value, 
            BookingStatus::PAID->value, 
            BookingStatus::CLOSED->value
        ])->count();

        // 4. Total Users
        $totalUsers = User::count();

        return [
            Stat::make('Gross Revenue', '₹' . number_format((float) $grossRevenue, 2))
                ->description('Total successful payments')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Platform Commission', '₹' . number_format((float) $platformCommission, 2))
                ->description('Total earned & settled')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('info'),

            Stat::make('Completed Bookings', $completedBookings)
                ->description('Successfully delivered services')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Users', $totalUsers)
                ->description('Registered platform users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}