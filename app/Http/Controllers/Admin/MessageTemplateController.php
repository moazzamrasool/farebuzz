<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;

class MessageTemplateController extends Controller
{
    public function index()
    {
        $messageTemplates = MessageTemplate::latest()->paginate(15);

        return view('admin.message-templates.index', compact('messageTemplates'));
    }

    public function create()
    {
        return view('admin.message-templates.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        MessageTemplate::create($data);

        return redirect()->route('crm.message-templates.index')
            ->with('success', 'Message template created successfully.');
    }

    public function edit(MessageTemplate $messageTemplate)
    {
        return view('admin.message-templates.edit', compact('messageTemplate'));
    }

    public function update(Request $request, MessageTemplate $messageTemplate)
    {
        $data = $this->validated($request);

        $messageTemplate->update($data);

        return redirect()->route('crm.message-templates.index')
            ->with('success', 'Message template updated successfully.');
    }

    public function destroy(MessageTemplate $messageTemplate)
    {
        $messageTemplate->delete();

        return redirect()->route('crm.message-templates.index')
            ->with('success', 'Message template deleted successfully.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'channel' => 'required|in:whatsapp,email',
            'subject' => 'nullable|required_if:channel,email|string|max:255',
            'body' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        if ($data['channel'] !== 'email') {
            $data['subject'] = null;
        }

        return $data;
    }
}
