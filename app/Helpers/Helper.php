<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Response builder with consistent structure.
 *
 * @param  string  $status  Success or error status.
 * @param  mixed  $data  The data to return.
 * @param  int  $statusCode  HTTP status code.
 */
function buildResponse(string $status, $data = null, $message = null, int $statusCode = 200): JsonResponse
{
    $response = [];

    if ($status === 'success') {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data,
            'code' => $statusCode,
        ];
    } elseif ($status === 'error') {

        $response = [
            'status' => false,
            'message' => $message,
            'data' => $data ?? 'Server Error',
            'code' => $statusCode,
        ];
    }

    return response()->json($response, $statusCode);
}

/**
 * Helper function to create a model and return a JSON response.
 *
 * @param  string  $modelClass  The model class to create.
 * @param  array  $data  The data to pass to the create method.
 */
function createAndRespond(string $modelClass, array $data): JsonResponse
{
    try {
        $result = $modelClass::create($data);

        return buildResponse('success', $result, 'Successfully Created', 201);
    } catch (Exception $e) {
        return buildResponse('error', null, $e->getMessage(), 500);
    }
}
/**
 * Update a model and respond with a JSON response.
 * Optionally deletes the old file if a new one is provided.
 *
 * @param  string  $modelClass  The model class to update.
 * @param  array  $data  The data to update.
 * @param  int|string  $id  The ID of the record to update.
 * @param  string|null  $fileField  Optional file field to replace and delete old file.
 */
function updateAndRespond(string $modelClass, array $data, $id, ?string $fileField = null): JsonResponse
{
    try {
        $model = $modelClass::find($id);

        if (! $model) {
            return buildResponse('error', null, 'Record not found', 404);
        }

        // Handle file replacement
        if ($fileField && isset($data[$fileField]) && $model->$fileField && File::exists(public_path($model->$fileField))) {
            File::delete(public_path($model->$fileField));
        }

        $updated = $model->update($data);

        return $updated
            ? buildResponse('success', $model->fresh(), 'Successfully Updated')
            : buildResponse('error', null, 'Failed to update the record', 500);
    } catch (Exception $e) {
        return buildResponse('error', null, $e->getMessage(), 500);
    }
}

/**
 * Deletes a record by ID or key from a model class and optionally deletes a file.
 *
 * @param  string  $modelClass  The model class to delete from.
 * @param  int|string  $id  The ID of the record to delete.
 * @param  string  $key  The key to use for deletion, defaults to 'id'.
 * @param  string|null  $fileField  Optional file field to delete from public folder.
 */
function deleteById(string $modelClass, $id, ?string $fileField = null, string $key = 'id'): JsonResponse
{
    try {
        $model = $modelClass::where($key, $id)->first();

        if (! $model) {
            return buildResponse('error', null, 'Record not found', 404);
        }

        // Delete file if present
        if ($fileField) {
            deleteFile($model->$fileField);
        }

        $deleted = $model->delete();

        return $deleted
            ? buildResponse('success', null, 'Record deleted successfully')
            : buildResponse('error', null, 'Unable to delete record', 500);
    } catch (Exception $e) {
        return buildResponse('error', null, $e->getMessage(), 500);
    }
}

/**
 * Send a custom JSON response using standard structure.
 *
 * @param  mixed  $data  The data to send as the response data.
 * @param  bool  $success  Whether the response indicates success or error.
 * @param  int  $statusCode  HTTP status code (default: 200 for success, 500 for error).
 */
function sendRespond($data): JsonResponse
{

    return buildResponse('success', $data);
}

/**
 * Upload a file to the specified folder within public directory.
 *
 * @param  UploadedFile  $file  The uploaded file instance.
 * @param  string  $folder  Folder path relative to the `public` directory.
 * @param  string|null  $customName  Optional custom file name without extension.
 * @return string|null Relative file path or null on failure.
 */
function uploadFile(UploadedFile $file, string $folder, ?string $customName = null): ?string
{
    try {
        $folderPath = public_path($folder);

        if (! File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
        }

        $fileName = $customName
            ? $customName.'.'.$file->getClientOriginalExtension()
            : time().'.'.$file->getClientOriginalExtension();

        $file->move($folderPath, $fileName);

        return '/'.trim($folder, '/').'/'.$fileName;
    } catch (Exception $e) {
        Log::error('uploadFile failed: '.$e->getMessage());

        return null;
    }
}

/**
 * Delete a file from the public path or storage path.
 *
 * @param  string|null  $filePath  Path to the file (relative to public/ or storage/)
 * @param  bool  $isPublic  Whether the path is from the public folder (default: true)
 * @return bool True if deleted, false otherwise
 */
function deleteFile(?string $filePath, bool $isPublic = true): bool
{
    if (! $filePath) {
        return false;
    }

    try {
        if (File::exists($filePath)) {
            File::delete($filePath);
            Log::info("File deleted: {$filePath}");

            return true;
        }

        $fullPath = $isPublic ? public_path($filePath) : storage_path($filePath);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
            Log::info(message: "File deleted: {$fullPath}");

            return true;
        }
    } catch (Exception $e) {
        Log::error("File deletion failed: {$filePath} - ".$e->getMessage());
    }

    return false;
}

/**
 * Delete multiple files from public or storage path.
 *
 * @param  array  $filePaths  Array of file paths (relative to public/ or storage/)
 * @param  bool  $isPublic  Whether the paths are from the public folder (default: true)
 * @return array An array with 'deleted' and 'failed' file paths
 */
function deleteFiles(array $filePaths, bool $isPublic = true): array
{
    $deleted = [];
    $failed = [];

    foreach ($filePaths as $filePath) {
        if (! $filePath) {
            continue;
        }

        try {
            $fullPath = $isPublic ? public_path($filePath) : storage_path($filePath);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
                $deleted[] = $filePath;
                Log::info("File deleted: {$fullPath}");
            } else {
                $failed[] = $filePath;
                Log::warning("File not found: {$fullPath}");
            }
        } catch (Exception $e) {
            $failed[] = $filePath;
            Log::error("File deletion failed: {$filePath} - ".$e->getMessage());
        }
    }

    return [
        'deleted' => $deleted,
        'failed' => $failed,
    ];
}

function getFileType(string $path): string
{
    $extension = pathinfo($path, PATHINFO_EXTENSION);

    return match (strtolower($extension)) {
        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
        'mp4', 'mov', 'avi', 'mkv' => 'video',
        'mp3', 'wav', 'ogg' => 'audio',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt' => 'file',
        default => 'file',
    };
}

/**
 * Upload a file to S3 (or default filesystem disk).
 *
 * @param  string  $folder  e.g. 'avatars'
 * @param  string|null  $customName  optional filename (without extension)
 * @param  string|null  $disk  optional disk, defaults to env(FILESYSTEM_DISK)
 * @return string|null Full public URL or null if failed
 */
function uploadFileToS3(UploadedFile $file, string $folder, ?string $customName = null, ?string $disk = null): ?string
{
    $disk = $disk ?? config('filesystems.default');

    $extension = $file->getClientOriginalExtension() ?: $file->extension();
    $baseName = $customName
        ? Str::slug(pathinfo($customName, PATHINFO_FILENAME))
        : (string) Str::uuid();

    $fileName = $baseName.'.'.$extension;
    $path = trim($folder, '/').'/'.$fileName;

    try {
        // Remove the ['visibility' => 'public'] options array
        Storage::disk($disk)->putFileAs($folder, $file, $fileName);

        // Get public URL
        return Storage::disk($disk)->url($path);
    } catch (Exception $e) {
        Log::error('File upload failed: '.$e->getMessage());

        return null;
    }
}

/**
 * Upload to Public Folder
 * Upload an image and return its URL.
 *
 * @param  UploadedFile  $image
 * @param  string  $directory
 * @return string
 */
function uploadImage($file, $folder)
{
    if (! $file->isValid()) {
        return null;
    }

    $imageName = Str::slug(time()).rand().'.'.$file->extension();
    $path = public_path('uploads/'.$folder);
    if (! file_exists($path)) {
        mkdir($path, 0755, true);
    }
    $file->move($path, $imageName);

    return 'uploads/'.$folder.'/'.$imageName;
}

// alamin

if (! function_exists('generateCode')) {
    function generateCode(): string
    {
        return strtoupper(substr(Str::uuid()->toString(), 0, 6));
    }
}

if (! function_exists('flashMessage')) {
    /**
     * Flash a notification message
     *
     * @param  string  $message
     * @param  string  $type  ('success', 'error', 'info', 'warning')
     * @param  int  $timeout  (milliseconds)
     * @param  string  $position  ('top-right', 'top-left', etc.)
     */
    function flashMessage($message, $type = 'success', $timeout = 3000, $position = 'top-right')
    {
        flash()
            ->use('theme.google')
            ->option('position', $position)
            ->option('timeout', $timeout)
            ->$type($message);
    }
}
