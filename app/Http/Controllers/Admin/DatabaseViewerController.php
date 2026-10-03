<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Read-only browser for the application database.
 *
 * Safety model:
 *  - Only GET routes exist (see routes/web.php); nothing here writes.
 *  - Table names come from the schema, never straight from the request.
 *  - Every query runs through the query builder (no raw user input) inside
 *    a read-only transaction on PostgreSQL as defense in depth.
 *  - Secret-looking columns are masked and some tables are hidden entirely.
 */
class DatabaseViewerController extends Controller
{
    private const PER_PAGE = 25;

    private const MAX_CELL_LENGTH = 200;

    private const MASK = '••••••';

    /** Tables that never appear (requesting one returns 404). */
    private const HIDDEN_TABLES = [
        'sessions',
        'password_reset_tokens',
        'cache',
        'cache_locks',
    ];

    /** Columns whose names match this pattern have their values masked. */
    private const MASKED_COLUMN_PATTERN = '/password|token|secret|recovery_codes|credential|payload|api_key/i';

    public function index(): Response
    {
        $tables = $this->readOnly(fn () => collect($this->visibleTables())
            ->map(fn (string $name) => [
                'name' => $name,
                'rows' => DB::table($name)->count(),
                'columns' => count(Schema::getColumnListing($name)),
            ])
            ->values());

        return Inertia::render('Admin/Database/Index', [
            'tables' => $tables,
        ]);
    }

    public function show(string $table): Response
    {
        abort_unless(in_array($table, $this->visibleTables(), true), 404);

        return $this->readOnly(function () use ($table) {
            $columns = collect(Schema::getColumns($table))
                ->map(fn (array $column) => [
                    'name' => $column['name'],
                    'type' => $column['type_name'],
                    'masked' => $this->isMasked($column['name']),
                ])
                ->values();

            $query = DB::table($table);
            foreach ($this->orderColumns($table, $columns->pluck('name')->all()) as $orderColumn) {
                $query->orderBy($orderColumn);
            }

            $paginator = $query
                ->paginate(self::PER_PAGE)
                ->through(fn (object $row) => $this->presentRow((array) $row));

            return Inertia::render('Admin/Database/Show', [
                'table' => $table,
                'columns' => $columns,
                'rows' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ],
            ]);
        });
    }

    /**
     * @return list<string>
     */
    private function visibleTables(): array
    {
        return collect(Schema::getTables())
            ->pluck('name')
            ->reject(fn (string $name) => in_array($name, self::HIDDEN_TABLES, true))
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Stable ordering for pagination: the primary key if there is one,
     * otherwise the first column.
     *
     * @param  list<string>  $columnNames
     * @return list<string>
     */
    private function orderColumns(string $table, array $columnNames): array
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['primary'] ?? false) && ! empty($index['columns'])) {
                return $index['columns'];
            }
        }

        return [$columnNames[0]];
    }

    private function isMasked(string $column): bool
    {
        return preg_match(self::MASKED_COLUMN_PATTERN, $column) === 1;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function presentRow(array $row): array
    {
        $presented = [];

        foreach ($row as $column => $value) {
            $presented[$column] = $this->isMasked($column)
                ? ($value === null ? null : self::MASK)
                : $this->presentValue($value);
        }

        return $presented;
    }

    private function presentValue(mixed $value): mixed
    {
        if (is_resource($value)) {
            return '[binary]';
        }

        if (is_string($value)) {
            if (! mb_check_encoding($value, 'UTF-8')) {
                return '[binary]';
            }

            return Str::limit($value, self::MAX_CELL_LENGTH);
        }

        return $value;
    }

    /**
     * Run the callback inside a read-only transaction where the driver
     * supports it (PostgreSQL). Other drivers rely on this controller
     * only ever issuing SELECTs.
     */
    private function readOnly(callable $callback): mixed
    {
        return DB::transaction(function () use ($callback) {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('SET TRANSACTION READ ONLY');
            }

            return $callback();
        });
    }
}
