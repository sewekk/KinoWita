import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['cinemaWrapper', 'roleSelect'];

    connect() {
        this.toggle();
        this.element.addEventListener('submit', this.#validateOnSubmit.bind(this));
    }

    disconnect() {
        this.element.removeEventListener('submit', this.#validateOnSubmit.bind(this));
    }

    toggle() {
        const isStaff = this.roleSelectTarget.value === 'ROLE_STAFF';
        this.cinemaWrapperTarget.hidden = !isStaff;

        if (!isStaff) {
            const select = this.cinemaWrapperTarget.querySelector('select');
            if (select) select.value = '';
        }

        this.#clearError();
    }

    #validateOnSubmit(event) {
        const isStaff = this.roleSelectTarget.value === 'ROLE_STAFF';
        const cinemaSelect = this.cinemaWrapperTarget.querySelector('select');

        if (isStaff && (!cinemaSelect || !cinemaSelect.value)) {
            event.preventDefault();
            this.cinemaWrapperTarget.hidden = false;
            this.#showError('Pracownik musi mieć przypisaną placówkę.');
            cinemaSelect?.focus();
        }
    }

    #showError(message) {
        this.#clearError();
        const error = document.createElement('p');
        error.className = 'text-xs font-mono text-red-600 mt-1 js-cinema-error';
        error.textContent = message;
        this.cinemaWrapperTarget.appendChild(error);
    }

    #clearError() {
        this.cinemaWrapperTarget.querySelector('.js-cinema-error')?.remove();
    }
}
