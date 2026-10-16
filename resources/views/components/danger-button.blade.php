<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-xs rounded-hospital shadow-xs tracking-wider transition-colors focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
