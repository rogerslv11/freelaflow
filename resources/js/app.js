import './bootstrap';

document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        Alpine.store('toast').show(payload.message, payload.type ?? 'success');
    });

    Livewire.on('modal-show', (data) => {
        const payload = Array.isArray(data) ? data[0] : data;
        Alpine.store('modal').show(payload.id);
    });

    Livewire.on('modal-close', () => {
        Alpine.store('modal').close();
    });
});

document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        open: false,
        toggle() { this.open = !this.open; document.body.classList.toggle('overflow-hidden', this.open); },
        close() { this.open = false; document.body.classList.remove('overflow-hidden'); },
    });

    Alpine.store('modal', {
        open: null,
        show(id) {
            this.open = id;
            document.body.classList.add('overflow-hidden');
        },
        close() {
            this.open = null;
            document.body.classList.remove('overflow-hidden');
        },
    });

    Alpine.store('toast', {
        items: [],
        show(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.remove(id), 3500);
        },
        remove(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    });
});
