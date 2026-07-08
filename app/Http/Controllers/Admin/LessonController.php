<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\LessonAttachment;
use App\Models\LessonMedia;
use App\Models\LiveClass;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function edit(CourseLesson $lesson)
    {
        $lesson->load(['section.course.settings', 'media', 'attachments', 'liveClass']);
        $course = $lesson->section->course;
        $settings = $course->settings ?? $course->settings()->create([]);

        $view = match ($lesson->lesson_type) {
            'video' => 'admin.lessons.edit-video',
            'audio' => 'admin.lessons.edit-audio',
            'pdf' => 'admin.lessons.edit-pdf',
            'live_class' => 'admin.lessons.edit-live-class',
            default => 'admin.lessons.edit-generic',
        };

        return view($view, compact('lesson', 'course', 'settings'));
    }

    public function update(Request $request, CourseLesson $lesson)
    {
        $course = $lesson->section->course;
        $settings = $course->settings ?? $course->settings()->create([]);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
            'external_url' => ['nullable', 'url'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:draft,published'],
        ]);

        $lesson->update($validated);

        if ($request->hasFile('file_path')) {
            $this->handleFileUpload($request, $lesson, $settings);
        }

        if ($request->filled('video_url') && $lesson->lesson_type === 'video') {
            $this->handleVideoUrl($lesson, $request->video_url);
        }

        ActivityLogger::log('lesson_updated', "Lesson {$lesson->title} updated", $lesson);

        return back()->with('success', 'Lesson saved successfully.');
    }

    public function updateSettings(Request $request, CourseLesson $lesson)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published'],
            'is_preview' => ['nullable'],
            'is_locked' => ['nullable'],
            'drip_enabled' => ['nullable'],
            'drip_date' => ['nullable', 'date'],
            'completion_required' => ['nullable'],
            'allow_download' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_preview'] = $request->input('is_preview') == '1' || $request->boolean('is_preview');
        $validated['is_locked'] = $request->input('is_locked') == '1' || $request->boolean('is_locked');
        $validated['drip_enabled'] = $request->input('drip_enabled') == '1' || $request->boolean('drip_enabled');
        $validated['completion_required'] = $request->input('completion_required') == '1' || $request->boolean('completion_required');
        $validated['allow_download'] = $request->input('allow_download') == '1' || $request->boolean('allow_download');

        $lesson->update($validated);

        ActivityLogger::log('lesson_settings_updated', "Settings updated for {$lesson->title}", $lesson);

        return back()->with('success', 'Lesson settings saved.');
    }

    public function uploadMedia(Request $request, CourseLesson $lesson)
    {
        $course = $lesson->section->course;
        $settings = $course->settings ?? $course->settings()->create([]);

        $rules = $this->mediaValidationRules($lesson->lesson_type, $settings);
        $validated = $request->validate($rules);

        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $path = $file->store('lessons/' . $lesson->lesson_type, 'public');

            $lesson->media()->create([
                'media_type' => $this->mediaTypeForLesson($lesson->lesson_type),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'processing_status' => 'processing',
            ]);

            $lesson->update([
                'file_path' => $path,
                'media_processing_status' => 'processing',
            ]);

            $lesson->update(['media_processing_status' => 'encryption']);
            $lesson->media()->latest()->first()?->update(['processing_status' => 'encryption']);

            $lesson->update(['media_processing_status' => 'ready']);
            $lesson->media()->latest()->first()?->update(['processing_status' => 'ready']);

            ActivityLogger::log('lesson_media_uploaded', "Media uploaded for {$lesson->title}", $lesson, [
                'type' => $lesson->lesson_type,
                'size' => $file->getSize(),
            ]);
        }

        if ($request->filled('video_url') && $lesson->lesson_type === 'video') {
            $this->handleVideoUrl($lesson, $request->video_url);
        }

        return back()->with('success', 'Media uploaded successfully.');
    }

    public function storeAttachment(Request $request, CourseLesson $lesson)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:51200'],
            'file_type' => ['nullable', 'string', 'max:50'],
            'download_allowed' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $path = $request->file('file')->store('lesson-attachments', 'public');

        $attachment = $lesson->attachments()->create([
            'title' => $validated['title'],
            'file_path' => $path,
            'file_type' => $validated['file_type'] ?? $request->file('file')->getClientOriginalExtension(),
            'download_allowed' => $request->boolean('download_allowed', true),
            'status' => $validated['status'],
            'sort_order' => $lesson->attachments()->max('sort_order') + 1,
        ]);

        ActivityLogger::log('lesson_attachment_added', "Attachment added to {$lesson->title}", $attachment);

        return back()->with('success', 'Attachment added.');
    }

    public function destroyAttachment(LessonAttachment $attachment)
    {
        $lesson = $attachment->lesson;
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        ActivityLogger::log('lesson_attachment_deleted', "Attachment removed from {$lesson->title}", $lesson);

        return back()->with('success', 'Attachment removed.');
    }

    public function updateLiveClass(Request $request, CourseLesson $lesson)
    {
        $validated = $request->validate([
            'live_class_type' => ['required', 'in:' . implode(',', array_keys(LiveClass::TYPES))],
            'super_live_capacity' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'duration_hours' => ['nullable', 'integer', 'min:0', 'max:24'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
            'recording_layout_mode' => ['nullable', 'in:' . implode(',', array_keys(LiveClass::RECORDING_LAYOUTS))],
            'embed_url' => ['nullable', 'url'],
            'new_recording' => ['boolean'],
            'enable_participant_list' => ['boolean'],
            'chat_box' => ['boolean'],
            'enable_qa' => ['boolean'],
            'show_whiteboard' => ['boolean'],
        ]);

        if (! empty($validated['starts_at']) && ! empty($validated['start_time'])) {
            $validated['starts_at'] = $validated['starts_at'] . ' ' . $validated['start_time'] . ':00';
        }
        unset($validated['start_time']);

        $validated['new_recording'] = $request->boolean('new_recording');
        $validated['enable_participant_list'] = $request->boolean('enable_participant_list');
        $validated['chat_box'] = $request->boolean('chat_box');
        $validated['enable_qa'] = $request->boolean('enable_qa');
        $validated['show_whiteboard'] = $request->boolean('show_whiteboard');

        $liveClass = $lesson->liveClass()->updateOrCreate(
            ['course_lesson_id' => $lesson->id],
            $validated
        );

        ActivityLogger::log('live_class_configured', "Live class configured for {$lesson->title}", $liveClass);

        return back()->with('success', 'Live class configuration saved.');
    }

    public function destroy(CourseLesson $lesson)
    {
        $title = $lesson->title;
        $course = $lesson->section->course;

        foreach ($lesson->media as $media) {
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }
        }
        foreach ($lesson->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        if ($lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }

        $lesson->delete();

        ActivityLogger::log('lesson_deleted', "Lesson {$title} deleted", $course);

        return redirect()->route('admin.courses.builder', $course)->with('success', 'Lesson deleted.');
    }

    protected function handleFileUpload(Request $request, CourseLesson $lesson, $settings): void
    {
        $rules = $this->mediaValidationRules($lesson->lesson_type, $settings);
        $request->validate(['file_path' => $rules['media_file'] ?? $rules['file_path'] ?? ['file', 'max:51200']]);

        $file = $request->file('file_path');
        $path = $file->store('lessons/' . $lesson->lesson_type, 'public');

        $lesson->update([
            'file_path' => $path,
            'media_processing_status' => 'ready',
        ]);

        $lesson->media()->create([
            'media_type' => $this->mediaTypeForLesson($lesson->lesson_type),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'processing_status' => 'ready',
        ]);
    }

    protected function handleVideoUrl(CourseLesson $lesson, string $url): void
    {
        $lesson->update([
            'video_url' => $url,
            'media_processing_status' => 'ready',
        ]);

        $lesson->media()->updateOrCreate(
            ['course_lesson_id' => $lesson->id, 'media_type' => 'video'],
            ['file_url' => $url, 'processing_status' => 'ready']
        );
    }

    protected function mediaTypeForLesson(string $lessonType): string
    {
        return match ($lessonType) {
            'audio' => 'audio',
            'pdf' => 'pdf',
            default => 'video',
        };
    }

    protected function mediaValidationRules(string $lessonType, $settings): array
    {
        return match ($lessonType) {
            'video' => [
                'media_file' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:' . ($settings->max_video_upload_mb * 1024)],
            ],
            'audio' => [
                'media_file' => ['nullable', 'file', 'mimes:mp3,wav,m4a', 'max:' . ($settings->max_audio_upload_mb * 1024)],
            ],
            'pdf' => [
                'media_file' => ['nullable', 'file', 'mimes:pdf', 'max:' . ($settings->max_pdf_upload_mb * 1024)],
            ],
            default => [
                'media_file' => ['nullable', 'file', 'max:51200'],
            ],
        };
    }
}
