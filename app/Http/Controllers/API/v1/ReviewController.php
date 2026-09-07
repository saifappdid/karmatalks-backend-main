<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    use ResponseTrait;
    public function addReview(Request $request)
    {
        $request->validate([
            'astro_id'                        => 'required|exists:astros,id',
            'rating'                          => 'required|integer|min:1|max:5',
            'message'                         => 'required|string|max:1000',
            'booking_id'                      => 'nullable|exists:bookings,id',
            'instant_consultation_history_id' => 'nullable|exists:instant_consultation_histories,id',
        ]);
        $user = auth('sanctum')->user();

        $checkIfReviewExists = Review::where('user_id', $user->id)
            ->where('astro_id', $request->astro_id)
            ->when($request->booking_id, function ($q) use ($request) {
                $q->where('booking_id', $request->booking_id);
            })
            ->when($request->instant_consultation_history_id, function ($q) use ($request) {
                $q->where('instant_consultation_history_id', $request->instant_consultation_history_id);
            })
            ->first();
        if ($checkIfReviewExists) {
            throw ValidationException::withMessages(['You have already reviewed this astro for the given session.']);
        }

        $review = Review::create([
            'user_id'                         => $user->id,
            'astro_id'                        => $request->astro_id,
            'rating'                          => $request->rating,
            'review'                          => $request->message,
            'booking_id'                      => $request->booking_id ?? null,
            'instant_consultation_history_id' => $request->instant_consultation_history_id ?? null,
        ]);
        return $this->success($review, 'Review added successfully');
    }
    public function getReviews(Request $request)
    {
        $request->validate([
            'astro_id' => 'required|exists:astros,id',
        ]);
        $reviews = Review::with('user:id,name,image', 'booking', 'instantConsultationHistory')
            ->where('astro_id', $request->astro_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return $this->success($reviews, 'Reviews fetched successfully');
    }
}
