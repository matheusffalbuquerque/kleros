<?php

namespace App\Livewire;

use Livewire\Component;

class Calculadora extends Component
{
    public $display = '0';
    public $currentValue = '';
    public $operator = '';
    public $previousValue = '';
    public $isOpen = false;

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function addNumber($number)
    {
        if ($this->display === '0' || $this->display === 'Erro') {
            $this->display = (string)$number;
            $this->currentValue = (string)$number;
        } else {
            $this->display .= $number;
            $this->currentValue .= $number;
        }
    }

    public function addDecimal()
    {
        if (!str_contains($this->currentValue, '.')) {
            $this->display .= '.';
            $this->currentValue .= '.';
        }
    }

    public function setOperator($op)
    {
        if ($this->currentValue !== '') {
            if ($this->previousValue !== '' && $this->operator !== '') {
                $this->calculate();
            }
            $this->previousValue = $this->currentValue;
            $this->currentValue = '';
            $this->operator = $op;
            $this->display .= ' ' . $op . ' ';
        }
    }

    public function calculate()
    {
        if ($this->previousValue !== '' && $this->currentValue !== '' && $this->operator !== '') {
            try {
                $prev = floatval($this->previousValue);
                $current = floatval($this->currentValue);
                $result = 0;

                switch ($this->operator) {
                    case '+':
                        $result = $prev + $current;
                        break;
                    case '-':
                        $result = $prev - $current;
                        break;
                    case '×':
                        $result = $prev * $current;
                        break;
                    case '÷':
                        if ($current == 0) {
                            $this->display = 'Erro';
                            $this->clear();
                            return;
                        }
                        $result = $prev / $current;
                        break;
                }

                // Formata o resultado
                $result = round($result, 8);
                if (floor($result) == $result) {
                    $result = intval($result);
                }

                $this->display = (string)$result;
                $this->currentValue = (string)$result;
                $this->previousValue = '';
                $this->operator = '';
            } catch (\Exception $e) {
                $this->display = 'Erro';
                $this->clear();
            }
        }
    }

    public function clear()
    {
        $this->display = '0';
        $this->currentValue = '';
        $this->operator = '';
        $this->previousValue = '';
    }

    public function backspace()
    {
        if ($this->currentValue !== '') {
            $this->currentValue = substr($this->currentValue, 0, -1);
            
            // Reconstrói o display
            if ($this->previousValue !== '' && $this->operator !== '') {
                $this->display = $this->previousValue . ' ' . $this->operator . ' ' . $this->currentValue;
            } else {
                $this->display = $this->currentValue ?: '0';
            }
        }
    }

    public function render()
    {
        return view('livewire.calculadora');
    }
}
