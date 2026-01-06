<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserTablePreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTablePreferencesController extends Controller
{
    public function show(string $tableKey)
    {
        $preference = UserTablePreference::where('user_id', Auth::id())
            ->where('table_key', $tableKey)
            ->first();

        $visible = $preference && $preference->visible_columns
            ? json_decode($preference->visible_columns, true)
            : null;

        return response()->json([
            'table_key' => $tableKey,
            'visible' => $visible,
        ]);
    }

    public function store(Request $request, string $tableKey)
    {
        $data = $request->validate([
            'visible' => ['array'],
        ]);

        $visible = array_values(array_filter($data['visible'] ?? [], function ($value) {
            return is_numeric($value);
        }));

        $visible = array_map('intval', $visible);

        UserTablePreference::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'table_key' => $tableKey,
            ],
            [
                'visible_columns' => json_encode($visible),
            ]
        );

        return response()->json(['ok' => true]);
    }
}
