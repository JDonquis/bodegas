@extends('layout.app')

@section('content')
<!-- Dashboard Header Section -->
<section class="mb-6">
    <h1 class="text-4xl font-extrabold text-primary tracking-tight mb-2">Panel de Control</h1>
    <p class="text-on-surface-variant font-body">Resumen general del estado de la bodega y movimientos recientes.</p>
</section>

<!-- Metric Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Entradas -->
    <div class="bg-surface-container-low p-6 rounded-xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold mb-1">Entradas</p>
            <p class="text-3xl font-headline font-extrabold text-primary">{{ $entries }}</p>
        </div>
        <div class="bg-primary/5 p-3 rounded-lg">
            <span class="material-symbols-outlined text-primary text-3xl">download</span>
        </div>
    </div>

    <!-- Salidas -->
    <div class="bg-tertiary-fixed/30 p-6 rounded-xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-xs uppercase tracking-widest text-on-tertiary-fixed-variant font-bold mb-1">Salidas</p>
            <p class="text-3xl font-headline font-extrabold text-tertiary">{{ $outputs }}</p>
        </div>
        <div class="bg-tertiary/5 p-3 rounded-lg">
            <span class="material-symbols-outlined text-tertiary text-3xl">upload</span>
        </div>
    </div>

    <!-- Inventario -->
    <div class="bg-secondary-container/20 p-6 rounded-xl flex items-center justify-between border border-outline-variant/30">
        <div>
            <p class="text-xs uppercase tracking-widest text-on-secondary-container font-bold mb-1">En Inventario</p>
            <p class="text-3xl font-headline font-extrabold text-secondary">{{ $inventories }}</p>
        </div>
        <div class="bg-secondary/10 p-3 rounded-lg">
            <span class="material-symbols-outlined text-secondary text-3xl">inventory_2</span>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/30 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-xl font-bold text-primary">Estadísticas de Ventas</h2>
        
        <!-- Filter Controls -->
        <div class="flex flex-wrap gap-3 items-center">
            <div class="flex bg-surface-container rounded-xl overflow-hidden border border-outline-variant/30">
                <button type="button" onclick="setPeriod('week')" id="btn-week" class="period-btn px-4 py-2 text-sm font-bold transition-all bg-primary text-white">
                    Semana
                </button>
                <button type="button" onclick="setPeriod('month')" id="btn-month" class="period-btn px-4 py-2 text-sm font-bold transition-all hover:bg-surface-container">
                    Mes
                </button>
                <button type="button" onclick="setPeriod('custom')" id="btn-custom" class="period-btn px-4 py-2 text-sm font-bold transition-all hover:bg-surface-container">
                    Personalizado
                </button>
            </div>
            
            <!-- Custom Date Range -->
            <div id="custom-range" class="hidden flex items-center gap-2">
                <input type="date" id="start_date" class="px-3 py-2 bg-surface rounded-xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <span class="text-outline">hasta</span>
                <input type="date" id="end_date" class="px-3 py-2 bg-surface rounded-xl border border-outline-variant/30 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50">
                <button type="button" onclick="applyCustomRange()" class="px-4 py-2 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary/90 transition-all">
                    Aplicar
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/30">
            <p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-1">Total Ventas</p>
            <p id="total-sales" class="text-2xl font-black text-primary">0.00$</p>
        </div>
        <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/30">
            <p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-1">Total Ganancias</p>
            <p id="total-profits" class="text-2xl font-black text-secondary">0.00$</p>
        </div>
        <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/30">
            <p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-1">Nro. Transacciones</p>
            <p id="total-outputs" class="text-2xl font-black text-tertiary">0</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales & Profits Chart -->
        <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/30">
            <h3 class="text-sm font-bold text-on-surface-variant mb-4">Ventas vs Ganancias</h3>
            <div id="chart-sales"></div>
        </div>

        <!-- Top Products -->
        <div class="bg-surface-container-low p-4 rounded-xl border border-outline-variant/30">
            <h3 class="text-sm font-bold text-on-surface-variant mb-4">Top 3 Productos Más Vendidos</h3>
            <div id="top-products" class="space-y-3">
                <p class="text-outline text-sm text-center py-8">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border border-outline-variant/30">
        <h3 class="text-xl font-bold text-primary mb-4">Acciones Rápidas</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('entries.create') }}" class="flex flex-col items-center justify-center p-4 bg-surface-container-low rounded-xl hover:bg-primary-fixed transition-colors group">
                <span class="material-symbols-outlined text-3xl text-primary mb-2 group-hover:scale-110 transition-transform">add_circle</span>
                <span class="text-sm font-bold text-primary">Nueva Entrada</span>
            </a>
            <a href="{{ route('outputs.create') }}" class="flex flex-col items-center justify-center p-4 bg-surface-container-low rounded-xl hover:bg-primary-fixed transition-colors group">
                <span class="material-symbols-outlined text-3xl text-primary mb-2 group-hover:scale-110 transition-transform">remove_circle</span>
                <span class="text-sm font-bold text-primary">Nueva Salida</span>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
let currentPeriod = 'week';
let salesChart = null;

document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

function setPeriod(period) {
    currentPeriod = period;
    
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.classList.remove('bg-primary', 'text-white');
        btn.classList.add('hover:bg-surface-container');
    });
    
    const btn = document.getElementById('btn-' + period);
    btn.classList.add('bg-primary', 'text-white');
    btn.classList.remove('hover:bg-surface-container');
    
    const customRange = document.getElementById('custom-range');
    if (period === 'custom') {
        customRange.classList.remove('hidden');
        customRange.classList.add('flex');
    } else {
        customRange.classList.add('hidden');
        customRange.classList.remove('flex');
        loadStatistics();
    }
}

function applyCustomRange() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate) {
        loadStatistics('custom', startDate, endDate);
    }
}

function loadStatistics(period = currentPeriod, startDate = null, endDate = null) {
    let url = '/home/statistics?period=' + period;
    
    if (period === 'custom' && startDate && endDate) {
        url += '&start_date=' + startDate + '&end_date=' + endDate;
    }
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateTotals(data.totals);
        renderChart(data.labels, data.sales, data.profits);
        renderTopProducts(data.topProducts);
    })
    .catch(error => console.error('Error:', error));
}

function updateTotals(totals) {
    document.getElementById('total-sales').textContent = totals.sales.toFixed(2) + '$';
    document.getElementById('total-profits').textContent = totals.profits.toFixed(2) + '$';
    document.getElementById('total-outputs').textContent = totals.outputs;
}

function renderChart(labels, sales, profits) {
    const options = {
        chart: {
            type: 'area',
            height: 280,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#675CD9', '#12B76A'],
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: labels,
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: val => val.toFixed(0) + '$'
            }
        },
        legend: { show: true, position: 'top', horizontalAlign: 'right' },
        series: [
            { name: 'Ventas', data: sales },
            { name: 'Ganancias', data: profits }
        ]
    };
    
    if (salesChart) {
        salesChart.updateOptions(options);
    } else {
        salesChart = new ApexCharts(document.querySelector('#chart-sales'), options);
        salesChart.render();
    }
}

function renderTopProducts(products) {
    const container = document.getElementById('top-products');
    
    if (!products || products.length === 0) {
        container.innerHTML = '<p class="text-outline text-sm text-center py-8">Sin datos disponibles</p>';
        return;
    }
    
    const colors = ['#675CD9', '#12B76A', '#F04438'];
    const medals = ['🥇', '🥈', '🥉'];
    
    container.innerHTML = products.map((product, index) => `
        <div class="flex items-center gap-4 p-3 bg-surface rounded-xl border border-outline-variant/30">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg" style="background-color: ${colors[index]}20; color: ${colors[index]}">
                ${medals[index]}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-bold text-on-surface truncate">${product.name}</p>
                <p class="text-xs text-outline">${product.quantity} ${product.unit || 'uds'} realizadas</p>
            </div>
            <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: ${colors[index]}20">
                <span class="font-black text-sm" style="color: ${colors[index]}">#${index + 1}</span>
            </div>
        </div>
    `).join('');
}
</script>
@endsection
