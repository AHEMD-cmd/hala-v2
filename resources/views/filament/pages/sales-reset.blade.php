<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Warning Banner -->
        <x-filament::section>
            <div class="bg-danger-50 border-l-4 border-danger-600 p-6 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-danger-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-danger-800">{{ __('Warning: Destructive Action') }}</h3>
                        <div class="mt-2 text-sm text-danger-700">
                            <p>{{ __('This action will archive or delete financial data. Please make sure you have a backup before proceeding.') }}</p>
                            <p class="mt-2 font-semibold">{{ __('Note: Product inventory will NOT be affected.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Current Stats -->
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Current System Status') }}
            </x-slot>

            @php
                $stats = $this->getStats();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-primary-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">{{ __('Active Invoices') }}</div>
                    <div class="text-2xl font-bold text-primary-600">{{ $stats['active_invoices'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total: € {{ number_format($stats['total_sales'], 2) }}</div>
                </div>

                <div class="bg-warning-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">{{ __('Expenses') }}</div>
                    <div class="text-2xl font-bold text-warning-600">€ {{ number_format($stats['total_expenses'], 2) }}</div>
                </div>

                <div class="bg-info-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">{{ __('Salaries') }}</div>
                    <div class="text-2xl font-bold text-info-600">€ {{ number_format($stats['total_salaries'], 2) }}</div>
                </div>

                <div class="bg-success-50 p-4 rounded-lg">
                    <div class="text-sm text-gray-600">{{ __('Products in Stock') }}</div>
                    <div class="text-2xl font-bold text-success-600">{{ $stats['products_count'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ __('Value') }}: € {{ number_format($stats['total_stock_value'], 2) }}</div>
                </div>
            </div>
        </x-filament::section>

        <!-- Reset Options -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Option 1: Reset Sales Only -->
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Reset Sales Only') }}
                </x-slot>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600">
                        {{ __('This will delete all invoices and start a new sales cycle.') }} 
                    </p>

                    <div class="bg-info-50 p-4 rounded-lg text-sm">
                        <strong class="text-info-800">{{ __('What will be deleted:') }}</strong>
                        <ul class="list-disc list-inside mt-2 text-info-700 space-y-1">
                            <li>{{ __('All active invoices') }}</li>
                            <li>{{ __('Pending delete requests') }}</li>
                        </ul>
                        
                        <strong class="text-success-800 block mt-3">{{ __('What will remain:') }}</strong>
                        <ul class="list-disc list-inside mt-2 text-success-700 space-y-1">
                            <li>{{ __('All products and inventory') }}</li>
                            <li>{{ __('All expenses records') }}</li>
                            <li>{{ __('All salary records') }}</li>
                            <li>{{ __('Customers and special orders') }}</li>
                        </ul>
                    </div>

                    <x-filament::button 
                        wire:click="resetSales"
                        color="warning"
                        icon="heroicon-o-arrow-path"
                        wire:confirm="Are you sure you want to delete all invoices? This action cannot be undone."
                    >
                        {{ __('Delete Invoices Only') }}
                    </x-filament::button>
                </div>
            </x-filament::section>

            <!-- Option 2: Full Reset -->
            <x-filament::section>
                <x-slot name="heading">
                    {{ __('Full Financial Reset') }}
                </x-slot>

                <div class="space-y-4">
                    <p class="text-sm text-gray-600">
                        {{ __('This will clear ALL financial data including invoices, expenses, and salaries.') }}
                    </p>

                    <div class="bg-danger-50 p-4 rounded-lg text-sm">
                        <strong class="text-danger-800">{{ __('What will be deleted:') }}</strong>
                        <ul class="list-disc list-inside mt-2 text-danger-700 space-y-1">
                            <li>{{ __('All invoices (deleted)') }}</li>
                            <li>{{ __('All expenses (permanently deleted)') }}</li>
                            <li>{{ __('All salary records (permanently deleted)') }}</li>
                        </ul>
                        
                        <strong class="text-success-800 block mt-3">{{ __('What will remain:') }}</strong>
                        <ul class="list-disc list-inside mt-2 text-success-700 space-y-1">
                            <li>{{ __('All products and inventory') }}</li>
                            <li>{{ __('Customers and special orders') }}</li>
                            <li>{{ __('Users and permissions') }}</li>
                        </ul>
                    </div>

                    <x-filament::button 
                        wire:click="resetAll"
                        color="danger"
                        icon="heroicon-o-trash"
                        wire:confirm="⚠️ WARNING: This will delete ALL financial data! Are you absolutely sure?"
                    >
                        {{ __('Full Reset (Dangerous)') }}
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>

        <!-- Info Section -->
        {{-- <x-filament::section>
            <x-slot name="heading">
                {{ __('Important Notes') }}
            </x-slot>

            <div class="prose prose-sm max-w-none">
                <ul>
                    <li><strong>{{ __('Inventory is Safe:') }}</strong> {{ __('Product stock quantities will NOT be affected by any reset operation.') }}</li>
                    <li><strong>{{ __('Archived Data:') }}</strong> {{ __('Archived invoices remain in the database and can be viewed in Activity Logs.') }}</li>
                    <li><strong>{{ __('Backup Recommended:') }}</strong> {{ __('Always create a database backup before performing a reset.') }}</li>
                    <li><strong>{{ __('No Undo:') }}</strong> {{ __('These operations cannot be reversed once executed.') }}</li>
                    <li><strong>{{ __('Audit Trail:') }}</strong> {{ __('All reset operations are logged in the Activity Log with full details.') }}</li>
                </ul>
            </div>
        </x-filament::section> --}}
    </div>
</x-filament-panels::page>