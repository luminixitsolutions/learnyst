@extends('layouts.app')

@section('title', 'Video Lesson')
@section('page-title', 'Video Lesson')
@section('breadcrumb', $lesson->title)

@section('content')
@include('admin.lessons.partials.editor-layout', [
    'lesson' => $lesson,
    'course' => $course,
    'settings' => $settings,
    'mediaType' => 'video',
    'accept' => '.mp4,.mov,.webm,video/*',
    'showVideoUrl' => true,
    'showEmbed' => true,
])
@endsection
