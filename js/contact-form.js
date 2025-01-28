class ContactForm {
    constructor() {
        this.form = document.getElementById('contactForm');
        this.submitButton = this.form.querySelector('button[type="submit"]');
        this.botCheckInput = this.form.querySelector('#botCheck');
        this.initialize();
    }

    initialize() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    validateBotCheck() {
        const answer = this.botCheckInput.value;
        return answer === '4';
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        if (!this.validateBotCheck()) {
            const toast = document.getElementById('infoToast');
            const toastInstance = bootstrap.Toast.getOrCreateInstance(toast);
            
            toast.classList.remove('bg-success', 'bg-danger');
            toast.classList.add('bg-danger');
            toast.querySelector('.toast-body').textContent = 'Неверный ответ на проверочный вопрос';

            toastInstance.show();
            return;
        }

        this.submitButton.disabled = true;

        const formData = {
            name: this.form.querySelector('#name').value,
            contact: this.form.querySelector('#contact').value,
            message: this.form.querySelector('#message').value,
            botCheck: this.botCheckInput.value
        };

        try {
            const response = await fetch('/handlers/contact-form.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            // Show toast notification
            const toast = document.getElementById('infoToast');
            const toastInstance = bootstrap.Toast.getOrCreateInstance(toast);
            
            toast.classList.remove('bg-success', 'bg-danger');
            toast.classList.add(result.success ? 'bg-success' : 'bg-danger');
            toast.querySelector('.toast-body').textContent = result.message;
            
            toastInstance.show();

            if (result.success) {
                this.form.reset();
            }

        } catch (error) {
            console.error('Error:', error);
            const toast = document.getElementById('infoToast');
            const toastInstance = bootstrap.Toast.getOrCreateInstance(toast);
            
            toast.classList.remove('bg-success', 'bg-danger');
            toast.classList.add('bg-danger');
            toast.querySelector('.toast-body').textContent = 'Ошибка. Попробуйте еще раз.';
            
            toastInstance.show();
        } finally {
            this.submitButton.disabled = false;
        }
    }
}
document.addEventListener('DOMContentLoaded', () => {
    console.log('Contact form handler initialized');
    new ContactForm();
});
