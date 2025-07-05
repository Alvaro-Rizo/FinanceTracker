@extends('layouts.app')

@section('title', 'Transacciones')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1>Transacciones</h1>
            <div>
                <a href="{{ route('transacciones.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Transacción
                </a>
            </div>
        </div>
    </div>

    <!-- Card de Filtros -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filtros</h5>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                <i class="fas fa-filter"></i> <span class="d-none d-md-inline">Filtros</span>
            </button>
        </div>
        
        <div class="card-body collapse show" id="filtersCollapse">
            <form method="GET" action="{{ route('transacciones.index') }}" id="main-filter-form">
                <div class="row g-3">
                    <!-- Filtros Básicos -->
                    <div class="col-md-3">
                        <label for="type" class="form-label">Tipo</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">Todos</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Ingresos</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Gastos</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="account_id" class="form-label">Cuenta</label>
                        <select name="account_id" id="account_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="category_id" class="form-label">Categoría</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($categories as $type => $categoryGroup)
                                <optgroup label="{{ $type == 'income' ? 'Ingresos' : 'Gastos' }}">
                                    @foreach($categoryGroup as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtros Avanzados -->
                    <div class="col-md-3">
                        <label for="search" class="form-label">Búsqueda</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Descripción...">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Fecha Inicio</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" 
                               value="{{ request('start_date') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Fecha Fin</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" 
                               value="{{ request('end_date') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="min_amount" class="form-label">Monto Mínimo</label>
                        <input type="number" step="0.01" name="min_amount" id="min_amount" 
                               class="form-control" value="{{ request('min_amount') }}" placeholder="0.00">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="max_amount" class="form-label">Monto Máximo</label>
                        <input type="number" step="0.01" name="max_amount" id="max_amount" 
                               class="form-control" value="{{ request('max_amount') }}" placeholder="0.00">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="tags" class="form-label">Etiquetas</label>
                        <select name="tags[]" id="tags" class="form-select" multiple>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" 
                                        {{ in_array($tag->id, (array)request('tags')) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('transacciones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-broom"></i> Limpiar Filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Mostrar filtros aplicados -->
    @if(request()->anyFilled(['type', 'account_id', 'category_id', 'search', 'start_date', 'end_date', 'min_amount', 'max_amount', 'tags']))
    <div class="alert alert-info mb-4">
        <strong>Filtros aplicados:</strong>
        <div class="d-flex flex-wrap gap-2 mt-2">
            @if(request('type'))
                <span class="badge bg-primary">
                    Tipo: {{ request('type') == 'income' ? 'Ingresos' : 'Gastos' }}
                    <a href="?{{ http_build_query(request()->except('type')) }}" class="text-white ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif
            
            @if(request('account_id'))
                @php
                    $account = $accounts->firstWhere('id', request('account_id'));
                @endphp
                @if($account)
                    <span class="badge bg-primary">
                        Cuenta: {{ $account->name }}
                        <a href="?{{ http_build_query(request()->except('account_id')) }}" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif
            @endif
            
            @if(request('category_id'))
                @php
                    $category = collect($categories)
                        ->flatten()
                        ->firstWhere('id', request('category_id'));
                @endphp
                @if($category)
                    <span class="badge bg-primary">
                        Categoría: {{ $category->name }}
                        <a href="?{{ http_build_query(request()->except('category_id')) }}" class="text-white ms-1">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                @endif
            @endif
            
            @if(request('search'))
                <span class="badge bg-info text-dark">
                    Búsqueda: "{{ request('search') }}"
                    <a href="?{{ http_build_query(request()->except('search')) }}" class="text-dark ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif
            
            @if(request('start_date') || request('end_date'))
                <span class="badge bg-info text-dark">
                    Fechas: {{ request('start_date') ?? 'Inicio' }} a {{ request('end_date') ?? 'Fin' }}
                    <a href="?{{ http_build_query(request()->except(['start_date', 'end_date'])) }}" class="text-dark ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif
            
            @if(request('min_amount') || request('max_amount'))
                <span class="badge bg-info text-dark">
                    Monto: 
                    {{ request('min_amount') ? '≥ '.number_format(request('min_amount'), 2) : '' }}
                    {{ request('min_amount') && request('max_amount') ? ' - ' : '' }}
                    {{ request('max_amount') ? '≤ '.number_format(request('max_amount'), 2) : '' }}
                    <a href="?{{ http_build_query(request()->except(['min_amount', 'max_amount'])) }}" class="text-dark ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif
            
            @if(request('tags'))
                <span class="badge bg-info text-dark">
                    Etiquetas: {{ count((array)request('tags')) }}
                    <a href="?{{ http_build_query(request()->except('tags')) }}" class="text-dark ms-1">
                        <i class="fas fa-times"></i>
                    </a>
                </span>
            @endif
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            @include('transactions.transaction-list', [
                'transactions' => $transactions,
                'accounts' => $accounts,
                'categories' => $categories
            ])
            
            <!-- Paginación -->
            @if($transactions->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $transactions->firstItem() }} a {{ $transactions->lastItem() }} de {{ $transactions->total() }} registros
                </div>
                <div>
                    {{ $transactions->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicialización de Select2 para tags
    if ($().select2) {
        $('#tags').select2({
            placeholder: 'Seleccione etiquetas',
            width: '100%',
            theme: 'bootstrap-5',
            closeOnSelect: false
        });
    }

    // Inicialización de Flatpickr para fechas
    if (window.flatpickr) {
        flatpickr("#start_date, #end_date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            locale: "es"
        });
    }

    // Validación de fechas
    const form = document.getElementById('main-filter-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error en fechas',
                    text: 'La fecha de inicio no puede ser mayor a la fecha final'
                });
                return false;
            }
            
            const minAmount = parseFloat(document.getElementById('min_amount').value);
            const maxAmount = parseFloat(document.getElementById('max_amount').value);
            
            if (minAmount && maxAmount && minAmount > maxAmount) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error en montos',
                    text: 'El monto mínimo no puede ser mayor al monto máximo'
                });
                return false;
            }
        });
    }

    // Manejo del colapso de filtros
    const filtersCollapse = document.getElementById('filtersCollapse');
    if (filtersCollapse) {
        const state = localStorage.getItem('filtersCollapseState');
        if (state === 'hidden') {
            new bootstrap.Collapse(filtersCollapse, {toggle: false});
        }

        filtersCollapse.addEventListener('hidden.bs.collapse', function() {
            localStorage.setItem('filtersCollapseState', 'hidden');
        });

        filtersCollapse.addEventListener('shown.bs.collapse', function() {
            localStorage.setItem('filtersCollapseState', 'visible');
        });
    }
});
</script>
@endsection