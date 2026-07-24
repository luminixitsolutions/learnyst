<div class="max-w-md">
    <x-form-input label="YouTube video ID" name="youtube_id" :value="old('youtube_id', $content['youtube_id'] ?? '')" required placeholder="0q4mL4wqgSo" />
    <p class="text-xs text-slate-500 mt-2">From a YouTube URL like youtube.com/watch?v=<strong>VIDEO_ID</strong></p>
</div>
