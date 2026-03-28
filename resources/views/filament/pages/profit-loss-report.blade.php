<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Select Report Period') }}
            </x-slot>

            <form wire:submit="generateReport">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit" icon="heroicon-o-arrow-path">
                        {{ __('Generate Report') }}
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        @if($reportData)
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('Total Sales') }}
                    </x-slot>
                    <div class="text-3xl font-bold text-success-600">
                        € {{ number_format($reportData['total_sales'], 2) }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        {{ $reportData['invoice_count'] }} invoices
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('Cost of Goods') }}
                    </x-slot>
                    <div class="text-3xl font-bold text-danger-600">
                        € {{ number_format($reportData['cost_of_goods'], 2) }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Gross Profit Margin: {{ $reportData['gross_profit_margin'] }}%
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('Operating Expenses') }}
                    </x-slot>
                    <div class="text-3xl font-bold text-warning-600">
                        € {{ number_format($reportData['total_expenses'] + $reportData['total_salaries'], 2) }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Expenses: € {{ number_format($reportData['total_expenses'], 2) }}<br>
                        Salaries: € {{ number_format($reportData['total_salaries'], 2) }}
                    </div>
                </x-filament::section>

                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('Net Profit') }}
                    </x-slot>
                    <div class="text-3xl font-bold {{ $reportData['net_profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                        € {{ number_format($reportData['net_profit'], 2) }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Net Profit Margin: {{ $reportData['net_profit_margin'] }}%
                    </div>
                </x-filament::section>
            </div>

            <!-- Detailed Breakdown -->
            {{-- <x-filament::section>
                <x-slot name="heading">
                    Profit & Loss Statement
                </x-slot>

                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="font-semibold">Total Sales Revenue</span>
                        <span class="text-success-600 font-bold">€ {{ number_format($reportData['total_sales'], 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="pl-4">Less: Cost of Goods Sold</span>
                        <span class="text-danger-600">- € {{ number_format($reportData['cost_of_goods'], 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-gray-400 bg-gray-50 px-4 font-semibold">
                        <span>Gross Profit</span>
                        <span class="{{ $reportData['gross_profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                            € {{ number_format($reportData['gross_profit'], 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="pl-4">Less: Operating Expenses</span>
                        <span class="text-danger-600">- € {{ number_format($reportData['total_expenses'], 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="pl-4">Less: Salaries</span>
                        <span class="text-danger-600">- € {{ number_format($reportData['total_salaries'], 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center py-3 border-t-2 border-primary-600 bg-primary-50 px-4 text-lg font-bold">
                        <span>Net Profit (Loss)</span>
                        <span class="{{ $reportData['net_profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                            € {{ number_format($reportData['net_profit'], 2) }}
                        </span>
                    </div>
                </div>
            </x-filament::section> --}}

            <!-- Sales Breakdown by Product -->
            @if(!empty($salesBreakdown))
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('Sales Breakdown by Product') }}
                    </x-slot>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left py-3 px-4">{{ __('Product') }}</th>
                                    <th class="text-left py-3 px-4">{{ __('SKU') }}</th>
                                    <th class="text-right py-3 px-4">{{ __('Qty Sold') }}</th>
                                    <th class="text-right py-3 px-4">{{ __('Sales') }}</th>
                                    <th class="text-right py-3 px-4">{{ __('Cost') }}</th>
                                    <th class="text-right py-3 px-4">{{ __('Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesBreakdown as $item)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4">{{ $item->product_name }}</td>
                                        <td class="py-3 px-4">{{ $item->sku }}</td>
                                        <td class="text-right py-3 px-4">{{ $item->total_quantity }}</td>
                                        <td class="text-right py-3 px-4 text-success-600">€ {{ number_format($item->total_sales, 2) }}</td>
                                        <td class="text-right py-3 px-4 text-danger-600">€ {{ number_format($item->total_cost, 2) }}</td>
                                        <td class="text-right py-3 px-4 font-semibold {{ $item->profit >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                            € {{ number_format($item->profit, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif

            <!-- Monthly Comparison -->
            @if(!empty($monthlyComparison))
                <x-filament::section>
                    <x-slot name="heading">
                        {{ __('6-Month Comparison') }}
                    </x-slot>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-gray-50">
                                    <th class="text-left py-3 px-4">{{ __('Month') }}</th>
                                    <th class="text-right py-3 px-4">{{ __('Sales') }}</th>
                                    <th class="text-right py-3 px-4">{{ __('Expenses') }}</th>
                                    <th class="text-right py-3 px-4">{{ __('Net Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyComparison as $month)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-semibold">{{ $month['month'] }}</td>
                                        <td class="text-right py-3 px-4 text-success-600">€ {{ number_format($month['sales'], 2) }}</td>
                                        <td class="text-right py-3 px-4 text-danger-600">€ {{ number_format($month['expenses'], 2) }}</td>
                                        <td class="text-right py-3 px-4 font-semibold {{ $month['profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                            € {{ number_format($month['profit'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif
        @endif
    </div>
</x-filament-panels::page>