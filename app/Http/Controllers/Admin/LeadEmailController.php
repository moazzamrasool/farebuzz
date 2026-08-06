<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesLeadAccess;
use App\Http\Controllers\Controller;
use App\Mail\LeadMailable;
use App\Models\MessageTemplate;
use App\Models\PackageEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadEmailController extends Controller
{
    use AuthorizesLeadAccess;

    public function compose(PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $mergeFields = $this->mergeFields($packageEnquiry);

        $templates = MessageTemplate::channel('email')->active()->orderBy('name')->get()
            ->map(fn (MessageTemplate $template) => [
                'id' => $template->id,
                'name' => $template->name,
                'subject' => $template->subject ? strtr($template->subject, collect($mergeFields)->mapWithKeys(fn ($v, $k) => ["{{$k}}" => $v])->all()) : '',
                'body' => $template->render($mergeFields),
            ]);

        return response()->json(['templates' => $templates]);
    }

    public function send(Request $request, PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'message_template_id' => 'nullable|exists:message_templates,id',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->getRealPath();
            $attachmentName = $request->file('attachment')->getClientOriginalName();
        }

        $mailFailed = false;

        try {
            Mail::to($packageEnquiry->email)->send(new LeadMailable(
                $packageEnquiry,
                $data['subject'],
                $data['body'],
                $attachmentPath,
                $attachmentName
            ));
        } catch (\Throwable $e) {
            $mailFailed = true;
            Log::error('Lead email failed: '.$e->getMessage(), ['lead_id' => $packageEnquiry->id]);
        }

        $packageEnquiry->logActivity(
            'email',
            'Email sent: '.$data['subject'],
            [
                'subject' => $data['subject'],
                'template_id' => $data['message_template_id'] ?? null,
                'attachment' => $attachmentName,
                'mail_failed' => $mailFailed,
            ],
            'email'
        );

        return back()->with(
            $mailFailed ? 'error' : 'success',
            $mailFailed ? 'Email could not be sent — see the timeline entry for details.' : 'Email sent.'
        );
    }

    private function mergeFields(PackageEnquiry $packageEnquiry): array
    {
        return [
            'customer_name' => $packageEnquiry->name,
            'package' => $packageEnquiry->holidayPackage->title ?? 'your enquiry',
            'travel_date' => optional($packageEnquiry->travel_date)->format('d M Y') ?? 'your travel date',
        ];
    }
}
