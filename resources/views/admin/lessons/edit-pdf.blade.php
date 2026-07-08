@extends('layouts.app')

@section('title', 'PDF Lesson')
@section('page-title', 'PDF Lesson')
@section('breadcrumb', $lesson->title)

@section('content')
@include('admin.lessons.partials.editor-layout', [
    'lesson' => $lesson,
    'course' => $course,
    'settings' => $settings,
    'mediaType' => 'pdf',
    'accept' => '.pdf,.ppt,.pptx,application/pdf',
    'showVideoUrl' => false,
    'showEmbed' => false,
])
@endsection
