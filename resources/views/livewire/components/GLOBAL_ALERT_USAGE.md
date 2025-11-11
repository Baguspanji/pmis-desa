# Global Alert Component

A reusable alert/modal component that can be triggered via Livewire dispatch or Laravel session flash.

## Features
- Center screen modal display
- Customizable icon, title, and content
- Support for different alert types (success, error, warning, info, confirm)
- OK button or OK/Cancel buttons (for confirmation dialogs)
- Can be triggered via dispatch or session flash

## Usage

### Method 1: Using Livewire Dispatch

#### Simple Alert
```php
// In your Livewire component
$this->dispatch('show-alert', [
    'type' => 'success',  // success, error, warning, info
    'title' => 'Berhasil!',
    'content' => 'Data berhasil disimpan.',
    'icon' => 'check-circle', // optional, defaults based on type
]);
```

#### Confirmation Dialog
```php
// In your Livewire component
$this->dispatch('show-confirm', [
    'type' => 'warning',
    'title' => 'Konfirmasi Hapus',
    'content' => 'Apakah Anda yakin ingin menghapus data ini?',
    'callback' => 'confirmed-delete', // Event to dispatch when OK is clicked
]);

// Handle the confirmation
protected $listeners = ['confirmed-delete' => 'deleteData'];

public function deleteData()
{
    // Perform delete operation
}
```

### Method 2: Using Session Flash

```php
// In your controller or Livewire component
session()->flash('alert', [
    'type' => 'success',
    'title' => 'Berhasil!',
    'content' => 'Operasi berhasil dilakukan.',
]);

return redirect()->route('dashboard');
```

### Alert Types

- **success**: Green icon, for successful operations
- **error**: Red icon, for error messages
- **warning**: Yellow icon, for warnings
- **info**: Blue icon, for informational messages
- **confirm**: Blue icon, shows OK/Cancel buttons

### Custom Icons

You can use any Flux icon:
```php
$this->dispatch('show-alert', [
    'type' => 'info',
    'title' => 'Informasi',
    'content' => 'Ini adalah pesan informasi.',
    'icon' => 'bell', // Custom icon
]);
```

### Examples

#### Success Alert
```php
$this->dispatch('show-alert', [
    'type' => 'success',
    'title' => 'Berhasil!',
    'content' => 'Pengguna berhasil ditambahkan.',
]);
```

#### Error Alert
```php
$this->dispatch('show-alert', [
    'type' => 'error',
    'title' => 'Terjadi Kesalahan',
    'content' => 'Gagal menyimpan data. Silakan coba lagi.',
]);
```

#### Warning Alert
```php
$this->dispatch('show-alert', [
    'type' => 'warning',
    'title' => 'Peringatan',
    'content' => 'Perubahan tidak dapat dibatalkan.',
]);
```

#### Delete Confirmation
```php
public function confirmDelete($id)
{
    $this->dispatch('show-confirm', [
        'type' => 'warning',
        'title' => 'Konfirmasi Hapus',
        'content' => 'Apakah Anda yakin ingin menghapus item ini?',
        'callback' => 'delete-confirmed-' . $id,
    ]);
}

protected $listeners = ['delete-confirmed-*' => 'handleDelete'];

public function handleDelete($id)
{
    // Delete the item
    Item::destroy($id);
    
    // Show success message
    $this->dispatch('show-alert', [
        'type' => 'success',
        'title' => 'Berhasil!',
        'content' => 'Item berhasil dihapus.',
    ]);
}
```

## Integration in Existing Components

Replace existing delete methods with confirmation:

```php
// Before
public function delete($userId)
{
    try {
        User::findOrFail($userId)->delete();
        session()->flash('message', 'Pengguna berhasil dihapus.');
        $this->fetchData();
    } catch (\Exception $e) {
        session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

// After
public function delete($userId)
{
    $this->dispatch('show-confirm', [
        'type' => 'warning',
        'title' => 'Konfirmasi Hapus',
        'content' => 'Apakah Anda yakin ingin menghapus pengguna ini?',
        'callback' => 'delete-confirmed',
    ]);
    
    $this->userToDelete = $userId;
}

protected $listeners = ['delete-confirmed' => 'performDelete'];

public function performDelete()
{
    try {
        User::findOrFail($this->userToDelete)->delete();
        $this->dispatch('show-alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'content' => 'Pengguna berhasil dihapus.',
        ]);
        $this->fetchData();
    } catch (\Exception $e) {
        $this->dispatch('show-alert', [
            'type' => 'error',
            'title' => 'Terjadi Kesalahan',
            'content' => $e->getMessage(),
        ]);
    }
}
```
