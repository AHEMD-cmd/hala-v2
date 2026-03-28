<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Salary;

class SalesReset extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static string $view = 'filament.pages.sales-reset';

    protected static ?string $navigationLabel = 'Sales Reset';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 98;

    public function getTitle(): string
    {
        return __('Sales Reset');
    }

    public static function getNavigationLabel(): string
    {
        return __('Sales Reset');
    }

    public static function getNavigationGroup(): string
    {
        return __('System');
    }

    public static function getModelLabel(): string
    {
        return __('Sales Reset');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Sales Resets');
    }

    public function resetSales(): void
    {
        try {
            DB::transaction(function () {
                // Archive all invoices (change status to 'deleted' instead of actually deleting)
                $invoiceCount = Invoice::query()->delete();

                // Log the activity
                activity('system')
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'invoices_archived' => $invoiceCount,
                        'reset_date' => now()->toDateTimeString(),
                    ])
                    ->log('Sales reset performed - All invoices archived');
            });

            Notification::make()
                ->title('Sales Reset Successful')
                ->success()
                ->body('All sales data has been archived. Inventory remains unchanged.')
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Sales Reset Failed')
                ->danger()
                ->body('Error: ' . $e->getMessage())
                ->send();
        }
    }

    public function resetAll(): void
    {
        try {
            DB::transaction(function () {
                // Archive invoices
                $invoiceCount = Invoice::query()->delete();

                // Archive expenses
                $expenseCount = Expense::query()->delete();

                // Archive salaries
                $salaryCount = Salary::query()->delete();

                // Log the activity
                activity('system')
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'invoices_archived' => $invoiceCount,
                        'expenses_deleted' => $expenseCount,
                        'salaries_deleted' => $salaryCount,
                        'reset_date' => now()->toDateTimeString(),
                    ])
                    ->log('Full system reset performed - All financial data cleared');
            });

            Notification::make()
                ->title('Full Reset Successful')
                ->success()
                ->body('All sales, expenses, and salaries have been cleared. Inventory remains unchanged.')
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Full Reset Failed')
                ->danger()
                ->body('Error: ' . $e->getMessage())
                ->send();
        }
    }

    public function getStats(): array
    {
        return [
            'active_invoices' => Invoice::where('status', 'active')->count(),
            'total_sales' => Invoice::where('status', 'active')->get()->sum('total'),
            'total_expenses' => Expense::sum('amount'),
            'total_salaries' => Salary::sum('amount'),
            'products_count' => \App\Models\Product::count(),
            'total_stock_value' => \App\Models\Product::sum(DB::raw('stock_quantity * cost_price')),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}