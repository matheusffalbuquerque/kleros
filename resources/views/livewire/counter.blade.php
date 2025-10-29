<div class="card" style="max-width: 300px; margin: 20px auto; padding: 20px; text-align: center;">
    <h3>Contador Livewire</h3>
    <div style="font-size: 24px; margin: 20px 0;">
        <strong>{{ $count }}</strong>
    </div>
    <div style="display: flex; gap: 10px; justify-content: center;">
        <button wire:click="decrement" class="btn" style="background: #ff4757; color: white;">
            <i class="bi bi-dash"></i> Diminuir
        </button>
        <button wire:click="increment" class="btn" style="background: #2ed573; color: white;">
            <i class="bi bi-plus"></i> Aumentar
        </button>
    </div>
    <p style="margin-top: 15px; font-size: 12px; color: #666;">
        Este é um exemplo de componente Livewire funcionando!
    </p>
</div>
