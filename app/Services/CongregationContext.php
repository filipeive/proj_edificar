<?php

namespace App\Services;

use App\Models\Congregation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Closure;

class CongregationContext
{
    private ?Congregation $congregation = null;

    public function set(Congregation $congregation): void
    {
        $this->congregation = $congregation;
    }

    public function get(): ?Congregation
    {
        return $this->congregation;
    }

    public function id(): ?int
    {
        if ($this->congregation) {
            return (int) $this->congregation->getKey();
        }

        if (!Schema::hasTable('congregations')) {
            return null;
        }

        $id = DB::table('congregations')->where('is_primary', true)->value('id');

        return $id ? (int) $id : null;
    }

    public function hasEdificar(): bool
    {
        if ($this->congregation) {
            return (bool) ($this->congregation->has_edificar_project ?? false);
        }

        if (!Schema::hasTable('congregations') || !Schema::hasColumn('congregations', 'has_edificar_project')) {
            return true;
        }

        return (bool) DB::table('congregations')->where('is_primary', true)->value('has_edificar_project');
    }

    public function run(Congregation $congregation, Closure $callback): mixed
    {
        $previous = $this->congregation;
        $this->set($congregation);

        try {
            return $callback();
        } finally {
            $this->congregation = $previous;
        }
    }
}