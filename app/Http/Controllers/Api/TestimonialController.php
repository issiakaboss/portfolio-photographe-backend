<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Testimonial::query()
                ->where('is_approved', true)
                ->latest()
                ->get(['id', 'author', 'role', 'quote', 'avatar', 'created_at']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'author' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'quote' => 'required|string|max:500',
        ]);

        // Tableau d'avatars préenregistrés par défaut pour les soumissions utilisateurs
        $defaultAvatars = [
            'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=200&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=200&auto=format&fit=crop'
        ];

        Testimonial::create([
            'author' => $validated['author'],
            'role' => $validated['role'] ?? 'Client / Partenaire',
            'quote' => $validated['quote'],
            'avatar' => $defaultAvatars[array_rand($defaultAvatars)],
            'is_approved' => true,
        ]);

        return response()->json([
            'message' => 'Merci ! Votre témoignage a été publié.',
        ], 201);
    }
}
