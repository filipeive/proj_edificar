<?php

namespace App\Models\Concerns;

use App\Services\CongregationContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

trait BelongsToCongregation
{
    private static array $congregationForeignKeys = [];

    protected static function bootBelongsToCongregation(): void
    {
        static::addGlobalScope('congregation', function (Builder $builder) {
            $congregationId = app(CongregationContext::class)->id();

            if ($congregationId !== null) {
                $builder->where($builder->getModel()->qualifyColumn('congregation_id'), $congregationId);
            }
        });

        static::creating(function (Model $model) {
            $congregationId = app(CongregationContext::class)->id();
            if ($congregationId === null) {
                throw new \LogicException('Não existe uma congregação primária configurada.');
            }

            if ($model->getAttribute('congregation_id') === null) {
                $model->setAttribute('congregation_id', $congregationId);
            }
        });

        static::saving(function (Model $model) {
            $congregationId = app(CongregationContext::class)->id();
            if (!$model->exists && $model->getAttribute('congregation_id') === null && $congregationId !== null) {
                $model->setAttribute('congregation_id', $congregationId);
            }

            if ($congregationId === null || (int) $model->getAttribute('congregation_id') !== $congregationId) {
                throw ValidationException::withMessages([
                    'congregation_id' => 'O registo não pertence à congregação ativa.',
                ]);
            }

            self::assertForeignKeysBelongToCongregation($model, $congregationId);
        });
    }

    public function congregation()
    {
        return $this->belongsTo(\App\Models\Congregation::class);
    }

    private static function assertForeignKeysBelongToCongregation(Model $model, int $congregationId): void
    {
        $table = $model->getTable();
        self::$congregationForeignKeys[$table] ??= Schema::getForeignKeys($table);

        foreach (self::$congregationForeignKeys[$table] as $foreignKey) {
            $column = $foreignKey['columns'][0] ?? null;
            $foreignTable = $foreignKey['foreign_table'] ?? null;
            $foreignColumn = $foreignKey['foreign_columns'][0] ?? null;

            if (!$column || !$foreignTable || !$foreignColumn || $model->getAttribute($column) === null) {
                continue;
            }

            if (!Schema::hasColumn($foreignTable, 'congregation_id')) {
                continue;
            }

            $belongsToTenant = DB::table($foreignTable)
                ->where($foreignColumn, $model->getAttribute($column))
                ->where('congregation_id', $congregationId)
                ->exists();

            if (!$belongsToTenant) {
                throw ValidationException::withMessages([
                    $column => 'O registo selecionado pertence a outra congregação.',
                ]);
            }
        }
    }
}