<?php

use Livewire\Component;

/**
 * Livewire Component untuk halaman Lowongan
 */
new class extends Component
{
    public function render()
    {
        return view('livewire.pages.lowongan');
    }
};
?>

<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Lowongan</h1>
        <p class="text-gray-600 mt-1">Manajemen lowongan kerja untuk alumni</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="text-5xl mb-4">🚧</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Sedang Dikembangkan</h2>
        <p class="text-gray-600">Fitur lowongan akan segera tersedia.</p>
    </div>
</div>
