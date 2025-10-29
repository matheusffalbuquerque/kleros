{{-- Wrapper mínimo para componentes Livewire carregados em modais --}}
@livewireStyles
<div class="livewire-modal-content">
    @livewire($component)
</div>
@livewireScripts
