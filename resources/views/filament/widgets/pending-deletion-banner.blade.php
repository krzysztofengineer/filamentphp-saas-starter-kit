<x-filament-widgets::widget>
    <div class="rounded-xl border border-danger-200 bg-danger-50 p-4 dark:border-danger-800/40 dark:bg-danger-950/40">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="mt-0.5 h-5 w-5 text-danger-600 dark:text-danger-400" />
                <div class="flex flex-col">
                    <span class="font-medium text-danger-800 dark:text-danger-200">Your account will be deleted in {{ $this->getRemainingDays() }} days</span>
                    <span class="text-sm text-danger-700/80 dark:text-danger-300/80">Click "Cancel deletion" to keep your account. After this period the data cannot be restored.</span>
                </div>
            </div>
            <div class="shrink-0">
                {{ $this->cancelDeletionAction }}
            </div>
        </div>

        <x-filament-actions::modals />
    </div>
</x-filament-widgets::widget>
