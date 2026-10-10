<?php

namespace App\Http\Middleware;

use App\Services\SchoolYearManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSelectedSchoolYearIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $schoolYear = SchoolYearManager::activeSchoolYear();

        if (!$schoolYear?->is_active) {
            abort(403, 'The selected school year is inactive and read-only.');
        }

        return $next($request);
    }
}
