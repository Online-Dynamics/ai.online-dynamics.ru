class CallMeForm {
    constructor() {
        this.form = document.getElementById('callMeForm');
        this.phoneInput = document.getElementById('phoneNumber');
        this.submitButton = this.form.querySelector('button[type="submit"]');
        this.toast = document.getElementById('infoToast');
        this.toastInstance = new bootstrap.Toast(this.toast);
        
        this.initialize();
    }

    initialize() {
        // Initialize phone mask
        IMask(this.phoneInput, {
            mask: '+{7} (000) 000-00-00'
        });

        // Add form submission handler
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    showToast(message, isSuccess = true) {
        this.toast.classList.remove('bg-success', 'bg-danger');
        this.toast.classList.add(isSuccess ? 'bg-success' : 'bg-danger');
        this.toast.querySelector('.toast-body').textContent = message;
        this.toastInstance.show();
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.submitButton.disabled = true;
        
        try {
            const response = await fetch('/handlers/call-me.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    phone: this.phoneInput.value
                })
            });

            const result = await response.json();

            if (result.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('callMeModal'));
                modal.hide();
                this.showToast(result.message, true);
                this.form.reset();
            } else {
                this.showToast(result.message, false);
            }
        } catch (error) {
            this.showToast('Ошибка. Попробуйте еще раз.', false);
            console.error('Error:', error);
        } finally {
            this.submitButton.disabled = false;
        }
    }
}
// Initialize form handler when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new CallMeForm();
});
