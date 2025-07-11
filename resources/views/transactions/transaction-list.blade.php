<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="card-title mb-2 mb-md-0">Historial de Transacciones</h5>
            
            <div class="d-flex gap-2 flex-wrap">
                <!-- Dropdown de Filtros Básicos -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel"></i> Filtros Básicos
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Tipo de Transacción</h6></li>
                        <li><a class="dropdown-item" href="?type=income">Ingresos</a></li>
                        <li><a class="dropdown-item" href="?type=expense">Gastos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Cuentas</h6></li>
                        @foreach($accounts as $account)
                            <li><a class="dropdown-item" href="?account_id={{ $account->id }}">{{ $account->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Botón para Filtros Avanzados -->
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#advancedFiltersModal">
                    <i class="bi bi-funnel-fill"></i> Filtros Avanzados
                </button>
                
                <button id="clearFilters" class="btn btn-outline-danger">
                    <i class="bi bi-x-circle"></i> Limpiar
                </button>
                
                <a href="{{ route('transacciones.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nueva
                </a>
            </div>
        </div>
    </div>
    
    <!-- Modal para Filtros Avanzados -->
    <div class="modal fade" id="advancedFiltersModal" tabindex="-1" aria-labelledby="advancedFiltersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="advancedFiltersModalLabel">Filtros Avanzados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('transacciones.index') }}" id="advancedFilterForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Búsqueda por texto -->
                            <div class="col-md-12">
                                <label for="search" class="form-label">Búsqueda</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="{{ request('search') }}" placeholder="Buscar en descripción...">
                            </div>
                            
                            <!-- Rango de fechas -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Fecha Inicio</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Fecha Fin</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                       value="{{ request('end_date') }}">
                            </div>
                            
                            <!-- Rango de montos -->
                            <div class="col-md-6">
                                <label for="min_amount" class="form-label">Monto Mínimo</label>
                                <input type="number" step="0.01" name="min_amount" id="min_amount" class="form-control" 
                                       value="{{ request('min_amount') }}" placeholder="0.00">
                            </div>
                            <div class="col-md-6">
                                <label for="max_amount" class="form-label">Monto Máximo</label>
                                <input type="number" step="0.01" name="max_amount" id="max_amount" class="form-control" 
                                       value="{{ request('max_amount') }}" placeholder="0.00">
                            </div>
                            
                            <!-- Mantener los filtros actuales -->
                            <input type="hidden" name="type" value="{{ request('type') }}">
                            <input type="hidden" name="account_id" value="{{ request('account_id') }}">
                            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Filtros aplicados - Versión corregida -->
        @if(request()->anyFilled(['type', 'account_id', 'category_id', 'search', 'start_date', 'end_date', 'min_amount', 'max_amount']))
            <div class="mb-3">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted">Filtros aplicados:</small>
                    
                    @if(request('type') && request('type') != 'all')
                        <span class="badge bg-primary">
                            Tipo: {{ request('type') == 'income' ? 'Ingresos' : 'Gastos' }}
                            <a href="?{{ http_build_query(request()->except('type')) }}" class="text-white ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    
                    @if(request('account_id') && $account = $accounts->firstWhere('id', request('account_id')))
                        <span class="badge bg-primary">
                            Cuenta: {{ $account->name }}
                            <a href="?{{ http_build_query(request()->except('account_id')) }}" class="text-white ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    
                    @if(request('category_id'))
                        @php
                            $category = collect($categories)->flatten()->firstWhere('id', request('category_id'));
                        @endphp
                        @if($category)
                            <span class="badge bg-primary">
                                Categoría: {{ $category->name }}
                                <a href="?{{ http_build_query(request()->except('category_id')) }}" class="text-white ms-2">
                                    <i class="bi bi-x"></i>
                                </a>
                            </span>
                        @endif
                    @endif
                    
                    @if(request('search'))
                        <span class="badge bg-info text-dark">
                            Búsqueda: "{{ request('search') }}"
                            <a href="?{{ http_build_query(request()->except('search')) }}" class="text-dark ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    
                    @if(request('start_date') || request('end_date'))
                        <span class="badge bg-info text-dark">
                            Rango: {{ request('start_date') ?? 'Inicio' }} a {{ request('end_date') ?? 'Fin' }}
                            <a href="?{{ http_build_query(request()->except(['start_date', 'end_date'])) }}" class="text-dark ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                    
                    @if(request('min_amount') || request('max_amount'))
                        <span class="badge bg-info text-dark">
                            Monto: 
                            {{ request('min_amount') ? '≥ '.number_format(request('min_amount'), 2) : '' }}
                            {{ request('min_amount') && request('max_amount') ? ' - ' : '' }}
                            {{ request('max_amount') ? '≤ '.number_format(request('max_amount'), 2) : '' }}
                            <a href="?{{ http_build_query(request()->except(['min_amount', 'max_amount'])) }}" class="text-dark ms-2">
                                <i class="bi bi-x"></i>
                            </a>
                        </span>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Tabla de transacciones -->
        @if($transactions->isEmpty())
            <div class="alert alert-info">No hay transacciones con los filtros aplicados</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th class="text-end">Monto</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('d/m/Y') }}</td>
                            <td>{{ $transaction->description ?: '-' }}</td>
                            <td>{{ $transaction->category->name }}</td>
                            <td class="text-end {{ $transaction->category->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->category->type === 'income' ? '+' : '-' }} 
                                {{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('transacciones.edit', $transaction->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('transacciones.destroy', $transaction->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta transacción?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-2 mb-md-0 text-muted">
                    Mostrando {{ $transactions->firstItem() }} a {{ $transactions->lastItem() }} de {{ $transactions->total() }} registros
                </div>
                <div>
                    {{ $transactions->withQueryString()->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(el => new bootstrap.Tooltip(el));

    // Inicializar datepickers
    if (window.flatpickr) {
        flatpickr("#start_date, #end_date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            locale: "es"
        });
    }

    // Validación del formulario avanzado
    const advancedFilterForm = document.getElementById('advancedFilterForm');
    if (advancedFilterForm) {
        advancedFilterForm.addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                alert('La fecha de inicio no puede ser mayor a la fecha final');
                return false;
            }
            
            const minAmount = parseFloat(document.getElementById('min_amount').value);
            const maxAmount = parseFloat(document.getElementById('max_amount').value);
            
            if (minAmount && maxAmount && minAmount > maxAmount) {
                e.preventDefault();
                alert('El monto mínimo no puede ser mayor al monto máximo');
                return false;
            }
        });
    }

    // Limpiar filtros
    document.getElementById('clearFilters')?.addEventListener('click', function() {
        window.location.href = "{{ route('transacciones.index') }}";
    });
});
</script>
@endsection