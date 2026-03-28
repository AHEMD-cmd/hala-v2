<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Salary;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get Profit & Loss report for a given period
     */
    public function getProfitLossReport(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

        // Total Sales (only active invoices)
        $totalSales = Invoice::where('status', 'active')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->get()
            ->sum('total');

        // Cost of Goods Sold
        $costOfGoods = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.status', 'active')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->selectRaw('SUM(invoice_items.quantity * products.cost_price) as total_cost')
            ->value('total_cost') ?? 0;

        // Total Expenses
        $totalExpenses = Expense::whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Total Salaries
        // $totalSalaries = Salary::sum('amount'); 
        $totalSalaries = Salary::whereBetween('month', [$startDate, $endDate])
            ->sum('amount');

        // Calculate Net Profit
        $grossProfit = $totalSales - $costOfGoods;
        $netProfit = $grossProfit - $totalExpenses - $totalSalaries;

        // Invoice breakdown
        $invoiceCount = Invoice::where('status', 'active')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->count();

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'total_sales' => round($totalSales, 2),
            'cost_of_goods' => round($costOfGoods, 2),
            'gross_profit' => round($grossProfit, 2),
            'total_expenses' => round($totalExpenses, 2),
            'total_salaries' => round($totalSalaries, 2),
            'net_profit' => round($netProfit, 2),
            'invoice_count' => $invoiceCount,
            'gross_profit_margin' => $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 2) : 0,
            'net_profit_margin' => $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 2) : 0,
        ];
    }

    /**
     * Get sales breakdown by product
     */
    public function getSalesBreakdown(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth()->toDateString();
        $endDate = $endDate ?? now()->endOfMonth()->toDateString();

        return DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id')
            ->where('invoices.status', 'active')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->select(
                'products.name as product_name',
                'products.sku',
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.quantity * invoice_items.unit_price) as total_sales'),
                DB::raw('SUM(invoice_items.quantity * products.cost_price) as total_cost'),
                DB::raw('SUM(invoice_items.quantity * invoice_items.unit_price) - SUM(invoice_items.quantity * products.cost_price) as profit')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_sales')
            ->get()
            ->toArray();
    }

    /**
     * Get monthly comparison
     */
    public function getMonthlyComparison(int $months = 6): array
    {
        $results = [];

        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($i);
            $startDate = $date->copy()->startOfMonth()->toDateString();
            $endDate = $date->copy()->endOfMonth()->toDateString();

            $report = $this->getProfitLossReport($startDate, $endDate);

            $results[] = [
                'month' => $date->format('F Y'),
                'sales' => $report['total_sales'],
                'profit' => $report['net_profit'],
                'expenses' => $report['total_expenses'],
            ];
        }

        return array_reverse($results);
    }
}
