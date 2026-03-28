<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Services\ReportService;

class ProfitLossReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.profit-loss-report';

    public function getTitle(): string
    {
        return __('Profit & Loss Report');
    }

    public static function getNavigationLabel(): string
    {
        return __('Profit & Loss Report');
    }

    public static function getNavigationGroup(): string
    {
        return __('Reports');
    }

    public static function getModelLabel(): string
    {
        return __('Profit & Loss Report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Profit & Loss Reports');
    }

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public array $reportData = [];
    public array $salesBreakdown = [];
    public array $monthlyComparison = [];

    public function mount(): void
    {
        // Default to current month
        $this->form->fill([
            'startDate' => now()->startOfMonth()->toDateString(),
            'endDate' => now()->endOfMonth()->toDateString(),
        ]);

        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Select Period'))
                    ->schema([
                        DatePicker::make('startDate')
                            ->label(__('Start Date'))
                            ->required()
                            ->native(false),

                        DatePicker::make('endDate')
                            ->label(__('End Date'))
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $reportService = app(ReportService::class);

        $startDate = $this->data['startDate'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->data['endDate'] ?? now()->endOfMonth()->toDateString();

        $this->reportData = $reportService->getProfitLossReport($startDate, $endDate);
        $this->salesBreakdown = $reportService->getSalesBreakdown($startDate, $endDate);
        $this->monthlyComparison = $reportService->getMonthlyComparison(6);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
