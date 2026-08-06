<?php

namespace App\Http\Requests\Admin;

use App\Services\Seo\RobotsTxtBuilder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class RobotsTxtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'robots_txt' => 'nullable|string|max:20000',
            'confirm_full_block' => 'sometimes|boolean',
        ];
    }

    // A bare "Disallow: /" under the default user-agent would deindex the whole site —
    // block the save unless the admin has explicitly ticked the confirmation checkbox.
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $content = (string) $this->input('robots_txt', '');

            if ($content !== '' && RobotsTxtBuilder::blocksEverything($content) && !$this->boolean('confirm_full_block')) {
                $validator->errors()->add(
                    'robots_txt',
                    'This robots.txt blocks all crawlers from the entire site ("Disallow: /" under User-agent: *). Tick the confirmation box below if this is really what you want — otherwise Google will deindex your site.',
                );
            }
        });
    }
}
