<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TransactionsController extends Controller
{
    protected function applyAdvancedFilters($query, Request $request)
    {
        // Búsqueda por texto (descripción o notas)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%'.$request->search.'%')
                  ->orWhere('notes', 'like', '%'.$request->search.'%');
            });
        }

        // Filtro por rango de montos
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Filtro por etiquetas
        if ($request->filled('tags')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->whereIn('id', (array)$request->tags);
            });
        }

        return $query;
    }

public function index(Request $request)
{
    $user = $request->user();
    
    $query = Transaction::with(['category', 'account', 'tags'])
              ->where('user_id', $user->id)
              ->latest();

    // Filtros básicos
    if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
        $query->whereHas('category', fn($q) => $q->where('type', $request->type));
    }

    if ($request->filled('account_id')) {
        $query->where('account_id', $request->account_id);
    }

    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filtros avanzados
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('description', 'like', '%'.$request->search.'%')
              ->orWhere('notes', 'like', '%'.$request->search.'%');
        });
    }

    if ($request->filled('start_date') || $request->filled('end_date')) {
        $query->where(function($q) use ($request) {
            if ($request->filled('start_date')) {
                $q->where('date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->where('date', '<=', $request->end_date);
            }
        });
    }

    if ($request->filled('min_amount')) {
        $query->where('amount', '>=', $request->min_amount);
    }

    if ($request->filled('max_amount')) {
        $query->where('amount', '<=', $request->max_amount);
    }

    if ($request->filled('tags')) {
        $query->whereHas('tags', function($q) use ($request) {
            $q->whereIn('id', (array)$request->tags);
        });
    }

    // Obtener datos para los selects
    $accounts = Account::where('user_id', $user->id)->get();
    $categories = Category::where('user_id', $user->id)->get();
    $tags = Tag::where('user_id', $user->id)->get();

    // Para peticiones AJAX (si decides implementarlo)
    if ($request->ajax()) {
        return response()->json([
            'html' => view('transactions.transaction-list', [
                'transactions' => $query->paginate(15)
            ])->render(),
            'pagination' => $query->paginate(15)->links()->toHtml()
        ]);
    }

    return view('transacciones', [
        'transactions' => $query->paginate(15),
        'accounts' => $accounts,
        'categories' => $categories->groupBy('type'),
        'tags' => $tags
    ]);
}

public function create(Request $request)
    {
        $user = $request->user();
        return view('transactions.create', [
            'categories' => Category::where('user_id', $user->id)->get(),
            'accounts' => Account::where('user_id', $user->id)->get(),
            'tags' => Tag::where('user_id', $user->id)->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        return DB::transaction(function () use ($validated, $request) {
            $user = $request->user();
            
            // Crear transacción
            $transaction = new Transaction($validated);
            $transaction->user_id = $user->id;
            $transaction->save();
            
            // Sincronizar tags
            if (isset($validated['tags'])) {
                $transaction->tags()->sync($validated['tags']);
            }
            
            // Actualizar balance de cuenta
            $account = Account::where('id', $validated['account_id'])
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();
                
            $account->balance += $validated['type'] === 'income' 
                ? $validated['amount'] 
                : -$validated['amount'];
            $account->save();

            return redirect()->route('transacciones.index')
                ->with('success', 'Transacción creada exitosamente');
        });
    }

    public function edit(Request $request, Transaction $transaction)
    {
        if ($request->user()->id !== $transaction->user_id) {
            abort(403, 'No tienes permiso para editar esta transacción');
        }
        
        return view('transactions.create', [
            'transaction' => $transaction,
            'categories' => Category::where('user_id', $request->user()->id)->get(),
            'accounts' => Account::where('user_id', $request->user()->id)->get(),
            'tags' => Tag::where('user_id', $request->user()->id)->get(),
            'editMode' => true
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($request->user()->id !== $transaction->user_id) {
            abort(403, 'No tienes permiso para actualizar esta transacción');
        }

        $validated = $this->validateRequest($request, $transaction);

        return DB::transaction(function () use ($validated, $transaction) {
            $oldAccount = $transaction->account;
            $oldAmount = $transaction->amount;
            $oldType = $transaction->type;

            // Actualizar transacción
            $transaction->update($validated);
            
            // Sincronizar tags
            $transaction->tags()->sync($validated['tags'] ?? []);
            
            // Manejar cambios de cuenta
            if ($oldAccount->id !== $validated['account_id']) {
                // Revertir en cuenta anterior
                $oldAccount->balance -= $oldType === 'income' ? $oldAmount : -$oldAmount;
                $oldAccount->save();
                
                // Aplicar en nueva cuenta
                $newAccount = Account::where('id', $validated['account_id'])
                    ->where('user_id', $transaction->user_id)
                    ->lockForUpdate()
                    ->firstOrFail();
                    
                $newAccount->balance += $validated['type'] === 'income' 
                    ? $validated['amount'] 
                    : -$validated['amount'];
                $newAccount->save();
            } else {
                // Ajustar diferencia en misma cuenta
                $difference = ($validated['type'] === 'income' ? $validated['amount'] : -$validated['amount']) 
                            - ($oldType === 'income' ? $oldAmount : -$oldAmount);
                $oldAccount->balance += $difference;
                $oldAccount->save();
            }

            return redirect()->route('transacciones.index')
                ->with('success', 'Transacción actualizada exitosamente');
        });
    }

    public function destroy(Request $request, Transaction $transaction)
    {
        if ($request->user()->id !== $transaction->user_id) {
            abort(403, 'No tienes permiso para eliminar esta transacción');
        }

        return DB::transaction(function () use ($transaction) {
            $account = $transaction->account;
            $account->balance -= $transaction->type === 'income' 
                ? $transaction->amount 
                : -$transaction->amount;
            $account->save();

            $transaction->delete();

            return back()->with('success', 'Transacción eliminada correctamente');
        });
    }

    protected function validateRequest(Request $request, ?Transaction $transaction = null): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'amount' => 'required|numeric|min:0.01|max:9999999.99',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', $request->user()->id)
            ],
            'account_id' => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $request->user()->id)
            ],
            'date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => [
                'required',
                Rule::exists('tags', 'id')->where('user_id', $request->user()->id)
            ]
        ]);
    }
}