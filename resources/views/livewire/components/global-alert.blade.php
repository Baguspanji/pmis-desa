<?php

use Livewire\Volt\Component;

new class extends Component {
    public $showAlert = false;
    public $alertType = 'info'; // success, error, warning, info, confirm
    public $alertIcon = 'information-circle';
    public $alertTitle = '';
    public $alertContent = '';
    public $showCancel = false;
    public $confirmCallback = null;

    protected $listeners = [
        'show-alert' => 'showAlert',
        'show-confirm' => 'showConfirm',
    ];

    public function mount()
    {
        // Check for session flash messages
        if (session()->has('alert')) {
            $alert = session('alert');
            $this->showAlertFromSession($alert);
        }
    }

    public function showAlert($data)
    {
        $this->alertType = $data['type'] ?? 'info';
        $this->alertTitle = $data['title'] ?? '';
        $this->alertContent = $data['content'] ?? '';
        $this->showCancel = false;

        // Set icon based on type if not provided
        if (isset($data['icon'])) {
            $this->alertIcon = $data['icon'];
        } else {
            $this->alertIcon = $this->getDefaultIcon($this->alertType);
        }

        $this->showAlert = true;
    }

    public function showConfirm($data)
    {
        $this->alertType = $data['type'] ?? 'warning';
        $this->alertTitle = $data['title'] ?? 'Konfirmasi';
        $this->alertContent = $data['content'] ?? '';
        $this->showCancel = true;
        $this->confirmCallback = $data['callback'] ?? null;

        if (isset($data['icon'])) {
            $this->alertIcon = $data['icon'];
        } else {
            $this->alertIcon = $this->getDefaultIcon($this->alertType);
        }

        $this->showAlert = true;
    }

    public function showAlertFromSession($alert)
    {
        $this->alertType = $alert['type'] ?? 'info';
        $this->alertTitle = $alert['title'] ?? '';
        $this->alertContent = $alert['content'] ?? '';
        $this->showCancel = $alert['showCancel'] ?? false;

        if (isset($alert['icon'])) {
            $this->alertIcon = $alert['icon'];
        } else {
            $this->alertIcon = $this->getDefaultIcon($this->alertType);
        }

        $this->showAlert = true;
    }

    public function closeAlert()
    {
        $this->showAlert = false;
        $this->reset(['alertType', 'alertIcon', 'alertTitle', 'alertContent', 'showCancel', 'confirmCallback']);
    }

    public function confirm()
    {
        if ($this->confirmCallback) {
            $this->dispatch($this->confirmCallback);
        }
        $this->closeAlert();
    }

    private function getDefaultIcon($type)
    {
        return match($type) {
            'success' => 'check-circle',
            'error' => 'x-circle',
            'warning' => 'exclamation-triangle',
            'confirm' => 'question-mark-circle',
            default => 'information-circle',
        };
    }

    private function getIconColor($type)
    {
        return match($type) {
            'success' => 'text-green-600',
            'error' => 'text-red-600',
            'warning' => 'text-yellow-600',
            'confirm' => 'text-blue-600',
            default => 'text-blue-600',
        };
    }

    private function getButtonVariant($type)
    {
        return match($type) {
            'success' => 'primary',
            'error' => 'danger',
            'warning' => 'primary',
            'confirm' => 'primary',
            default => 'primary',
        };
    }

    private function getIconBgClass($type)
    {
        return match($type) {
            'success' => 'bg-green-100',
            'error' => 'bg-red-100',
            'warning' => 'bg-yellow-100',
            default => 'bg-blue-100',
        };
    }

    private function getIconTextClass($type)
    {
        return match($type) {
            'success' => 'text-green-600',
            'error' => 'text-red-600',
            'warning' => 'text-yellow-600',
            default => 'text-blue-600',
        };
    }
}; ?>

<div>
    <flux:modal name="global-alert" wire:model="showAlert" class="min-w-[400px] max-w-[500px]" :closable="false">
        <div class="flex flex-col items-center text-center space-y-4 py-4">
            <!-- Icon -->
            @if ($alertIcon)
                <div class="flex items-center justify-center w-16 h-16 rounded-full {{ $this->getIconBgClass($alertType) }}">
                    <flux:icon
                        :name="$alertIcon"
                        class="w-10 h-10 {{ $this->getIconTextClass($alertType) }}"
                    />
                </div>
            @endif

            <!-- Title -->
            @if ($alertTitle)
                <flux:heading size="lg" class="font-semibold">
                    {{ $alertTitle }}
                </flux:heading>
            @endif

            <!-- Content -->
            @if ($alertContent)
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $alertContent }}
                </div>
            @endif
        </div>

        <!-- Buttons -->
        <div class="flex gap-2 mt-6">
            @if ($showCancel)
                <flux:button variant="ghost" wire:click="closeAlert" class="flex-1">
                    Batal
                </flux:button>
                <flux:button
                    :variant="$this->getButtonVariant($alertType)"
                    wire:click="confirm"
                    class="flex-1"
                >
                    OK
                </flux:button>
            @else
                <flux:button
                    :variant="$this->getButtonVariant($alertType)"
                    wire:click="closeAlert"
                    class="w-full"
                >
                    OK
                </flux:button>
            @endif
        </div>
    </flux:modal>
</div>
