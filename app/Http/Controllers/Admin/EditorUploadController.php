<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Backs CKEditor 5's SimpleUploadAdapter (see initRichText() in public/admin/custom.js,
// window.FB_IMAGE_UPLOAD_URL). Shared by every .rich-text-editor across the admin — not
// tied to any one module's own form/model.
class EditorUploadController extends Controller
{
    public function image(Request $request)
    {
        // SimpleUploadAdapter only surfaces a reason from a {"error":{"message":"..."}}
        // body — Laravel's default {"errors":{"upload":[...]}} shape means the browser
        // just shows a bare "Couldn't upload file" with no detail, so validation is
        // handled manually here instead of via $request->validate(). Field name is
        // "upload" — that's SimpleUploadAdapter's own FormData key, not "file".
        $validator = Validator::make($request->all(), [
            'upload' => 'required|image|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => ['message' => $validator->errors()->first('upload')]], 400);
        }

        $path = $request->file('upload')->store('editor-uploads', 'public');

        // SimpleUploadAdapter's success contract: a 200 JSON body with a "url" key.
        return response()->json(['url' => asset('storage/'.$path)]);
    }
}
