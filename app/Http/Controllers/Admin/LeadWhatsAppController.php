<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\WhatsAppSenderInterface;
use App\Http\Controllers\Admin\Concerns\AuthorizesLeadAccess;
use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use App\Models\PackageEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeadWhatsAppController extends Controller
{
    use AuthorizesLeadAccess;

    public function compose(PackageEnquiry $packageEnquiry)
    {
        $this->authorizeVisibility($packageEnquiry);

        $mergeFields = $this->mergeFields($packageEnquiry);

        $templates = MessageTemplate::channel('whatsapp')->active()->orderBy('name')->get()
            ->map(fn (MessageTemplate $template) => [
                'id' => $template->id,
                'name' => $template->name,
                'body' => $template->render($mergeFields),
            ]);

        return response()->json([
            'phone' => $packageEnquiry->phone,
            'templates' => $templates,
        ]);
    }

    public function send(Request $request, PackageEnquiry $packageEnquiry, WhatsAppSenderInterface $sender)
    {
        $this->authorizeVisibility($packageEnquiry);

        $data = $request->validate([
            'message' => 'required|string',
            'message_template_id' => 'nullable|exists:message_templates,id',
        ]);

        $url = $sender->buildLink($packageEnquiry->phone, $data['message']);

        // Logged optimistically at send time — wa.me gives no delivery webhook, so this
        // records that the agent chose to send this message, not confirmed delivery.
        $packageEnquiry->logActivity(
            'whatsapp',
            'WhatsApp message sent: '.Str::limit($data['message'], 100),
            ['full_message' => $data['message'], 'template_id' => $data['message_template_id'] ?? null],
            'whatsapp'
        );

        return response()->json(['url' => $url]);
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
