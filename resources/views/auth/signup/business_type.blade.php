@php
    $title = 'Help us to personalise your experience';
    $subtitle = "Tell us a bit about your business and goals. We'll use this to set up Learnyst the way that works best for you.";
    $field = 'business_type';
    $choices = $options['business_type'];
    $action = route('signup.business_type');
    $back = 'company';
    $required = true;
@endphp
@include('auth.signup.choice-body')
