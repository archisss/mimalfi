<div wire:ignore>
<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <!-- Cobros del día -->
        <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
            <h3 class="text-lg font-semibold mb-2">Cobros del Día</h3>
            <canvas id="loansPerDayChart"></canvas>
        </div>
        <!-- Préstamos por Autorizar -->
        <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
            <h3 class="text-lg font-semibold mb-2">Préstamos por Autorizar</h3>
            
            <p class="text-4xl font-bold text-center mt-8">{{ $pendingLoans }}</p>
        </div>
        <!-- Cobrado al Día -->
        <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
            <h3 class="text-lg font-semibold mb-2">Cobrado al Día</h3>
            <p class="text-4xl font-bold text-center mt-8">${{ number_format($paymentsToday, 2) }}</p>
        </div>
    </div>
</div>


 @assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets
@push('scripts')
<!-- @script -->
 
<!-- <script>
    const loansPerDayCtx = document.getElementById('loansPerDayChart').getContext('2d');
    const loansPerDayChart = new Chart(loansPerDayCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($loansPerDay->keys()) !!},
            datasets: [{
                label: 'Préstamos',
                data: {!! json_encode($loansPerDay->values()) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
</script> -->
<!-- @endscript -->
@endpush


</div>