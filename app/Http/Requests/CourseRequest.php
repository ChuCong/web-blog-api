
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'lessons' => 'required|array',
            'lessons.*.title' => 'required|string',
            'lessons.*.media_ids' => 'array',
            'lessons.*.group_questions' => 'array',
            'lessons.*.group_questions.*.title' => 'required|string',
            'lessons.*.group_questions.*.questions' => 'array',
            'lessons.*.group_questions.*.questions.*.content' => 'required|string',
            'lessons.*.group_questions.*.questions.*.answers' => 'array',
            'lessons.*.group_questions.*.questions.*.answers.*.content' => 'required|string',
        ];
    }
}
