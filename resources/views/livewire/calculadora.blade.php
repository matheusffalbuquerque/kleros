<div>
    {{-- Botão Flutuante --}}
    <button wire:click="toggle" class="btn-calculadora-float" title="Calculadora">
        <i class="bi bi-calculator"></i>
    </button>

    {{-- Modal da Calculadora --}}
    @if($isOpen)
    <div class="calculadora-overlay" wire:click="close"></div>
    <div class="calculadora-container">
        <div class="calculadora-header">
            <h4><i class="bi bi-calculator"></i> Calculadora</h4>
            <button wire:click="close" class="btn-close-calc">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <div class="calculadora-display">
            {{ $display }}
        </div>
        
        <div class="calculadora-buttons">
            <button wire:click="clear" class="btn-calc btn-function">C</button>
            <button wire:click="backspace" class="btn-calc btn-function">⌫</button>
            <button wire:click="setOperator('÷')" class="btn-calc btn-operator">÷</button>
            <button wire:click="setOperator('×')" class="btn-calc btn-operator">×</button>
            
            <button wire:click="addNumber(7)" class="btn-calc">7</button>
            <button wire:click="addNumber(8)" class="btn-calc">8</button>
            <button wire:click="addNumber(9)" class="btn-calc">9</button>
            <button wire:click="setOperator('-')" class="btn-calc btn-operator">-</button>
            
            <button wire:click="addNumber(4)" class="btn-calc">4</button>
            <button wire:click="addNumber(5)" class="btn-calc">5</button>
            <button wire:click="addNumber(6)" class="btn-calc">6</button>
            <button wire:click="setOperator('+')" class="btn-calc btn-operator">+</button>
            
            <button wire:click="addNumber(1)" class="btn-calc">1</button>
            <button wire:click="addNumber(2)" class="btn-calc">2</button>
            <button wire:click="addNumber(3)" class="btn-calc">3</button>
            <button wire:click="calculate" class="btn-calc btn-equals" style="grid-row: span 2;">=</button>
            
            <button wire:click="addNumber(0)" class="btn-calc" style="grid-column: span 2;">0</button>
            <button wire:click="addDecimal" class="btn-calc">.</button>
        </div>
    </div>
    @endif

    <style>
        /* Botão Flutuante */
        .btn-calculadora-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 999;
            transition: all 0.3s ease;
        }
        
        .btn-calculadora-float:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.4);
        }
        
        /* Overlay */
        .calculadora-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }
        
        /* Container da Calculadora */
        .calculadora-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 240px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header */
        .calculadora-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: var(--primary-color);
            color: white;
        }
        
        .calculadora-header h4 {
            margin: 0;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-close-calc {
            background: transparent;
            border: none;
            color: white;
            font-size: 0.9rem;
            cursor: pointer;
            padding: 3px;
            transition: transform 0.2s ease;
        }
        
        .btn-close-calc:hover {
            transform: scale(1.2);
        }
        
        /* Display */
        .calculadora-display {
            background: rgba(0, 0, 0, 0.05);
            padding: 12px;
            text-align: right;
            font-size: 1.4rem;
            font-weight: 600;
            color: #333;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            word-break: break-all;
            overflow-x: auto;
        }
        
        /* Botões */
        .calculadora-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.8);
        }
        
        .btn-calc {
            padding: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-calc:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            background: white;
        }
        
        .btn-calc:active {
            transform: translateY(0);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
        }
        
        .btn-calc.btn-function {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .btn-calc.btn-operator {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }
        
        .btn-calc.btn-equals {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .calculadora-container {
                bottom: 70px;
                right: 15px;
                width: 220px;
            }
            
            .btn-calculadora-float {
                bottom: 15px;
                right: 15px;
                width: 42px;
                height: 42px;
                font-size: 1rem;
            }
            
            .calculadora-buttons {
                gap: 5px;
                padding: 10px;
            }
            
            .btn-calc {
                padding: 10px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .calculadora-container {
                bottom: 65px;
                right: 10px;
                left: 10px;
                width: auto;
            }
            
            .btn-calculadora-float {
                bottom: 10px;
                right: 10px;
                width: 40px;
                height: 40px;
                font-size: 0.95rem;
            }
        }
    </style>
</div>

