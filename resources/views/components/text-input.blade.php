@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full px-3 py-2 sm:px-4 sm:py-2 text-base sm:text-sm min-h-[44px] sm:min-h-[auto]']) !!}>
