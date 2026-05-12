<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Xyoraa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Snowfire\Beautymail\Beautymail;

class TestController extends Controller
{
   public function index(Request $request)
   {
      $beautymail = app()->make(Beautymail::class);
      $beautymail->send('components.emails.welcome', [], function ($message) {
         $message
            ->from('hello@edyone.site')
            ->to('sk8597147@gmail.com', 'John Smith')
            ->subject('Welcome!');
      });
      return $beautymail;
   }

   public function sentMail(Request $request)
   {
      $fileUrl = null;

      // File upload
      if ($request->hasFile('file_to_upload')) {
         $file = $request->file('file_to_upload');
         $uploadedPath = $file->store('uploads', 's3');

         if ($uploadedPath !== false) {
            // Make it public
            Storage::disk('s3')->setVisibility($uploadedPath, 'public');

            // Generate public URL
            $fileUrl = Storage::disk('s3')->url($uploadedPath);

            // Save file path (not URL) in user record
            $user = User::find(1);
            if ($user) {
               $user->image = $uploadedPath;
               $user->save();
            }
         } else {
            return response()->json(['error' => 'File upload failed'], 500);
         }
      }

      // File delete using user id and image path from DB
      if ($request->has('delete_uploaded_file')) {
         $user = User::find(1);
         if ($user && $user->image) {
            Storage::disk('s3')->delete($user->image);
            $user->image = null;
            $user->save();
            $fileUrl = null;
         }
      }

      return response()->json([
         'message' => 'Operation completed!',
         'file_url' => $fileUrl,
      ]);
   }
}
