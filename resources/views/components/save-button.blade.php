<button {{ $attributes->merge(['type' => 'submit', 'class' => 'dark:bg-red-200']) }}>
    {{ $slot }}
</button>
