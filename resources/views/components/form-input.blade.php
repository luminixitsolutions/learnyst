@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => ''])

<div class="space-y-1.5">
    <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
    @if($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" rows="4" placeholder="{{ $placeholder }}"
                  {{ $required ? 'required' : '' }}
                  class="panel-input">{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select name="{{ $name }}" id="{{ $name }}" {{ $required ? 'required' : '' }} class="panel-select w-full">
            {{ $slot }}
        </select>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
               placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} class="panel-input">
    @endif
    @error($name)<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
