@extends('layouts.app')

@section('title', 'Audio Lesson')
@section('page-title', 'Audio Lesson')
@section('breadcrumb', $lesson->title)

@section('content')
@include('admin.lessons.partials.editor-layout', [
    'lesson' => $lesson,
    'course' => $course,
    'settings' => $settings,
    'mediaType' => 'audio',
    'accept' => '.mp3,.wav,.m4a,audio/*',
    'showVideoUrl' => false,
])
@endsection
