// JavaScript principal do site

// Formatação monetária em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const moneyInputs = document.querySelectorAll('input[data-type="money"]');

    moneyInputs.forEach(function(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            e.target.value = 'R$ ' + value;
        });
    });

    // Formatação de telefone
    const phoneInputs = document.querySelectorAll('input[data-type="phone"]');

    phoneInputs.forEach(function(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');

            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
            }

            e.target.value = value;
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Toggle do menu mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
        });
    }

    // Inicialização de tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltipEl = document.createElement('div');
            tooltipEl.className = 'tooltip';
            tooltipEl.textContent = tooltipText;
            document.body.appendChild(tooltipEl);

            const rect = this.getBoundingClientRect();
            tooltipEl.style.top = rect.bottom + 10 + 'px';
            tooltipEl.style.left = rect.left + (rect.width / 2) - (tooltipEl.offsetWidth / 2) + 'px';
            tooltipEl.style.opacity = '1';
        });

        tooltip.addEventListener('mouseleave', function() {
            const tooltipEl = document.querySelector('.tooltip');
            if (tooltipEl) {
                tooltipEl.remove();
            }
        });
    });

    // Máscaras de input
    const phoneInputsOriginal = document.querySelectorAll('input[type="tel"]');
    phoneInputsOriginal.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            if (value.length > 7) {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
            } else if (value.length > 2) {
                e.target.value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            } else if (value.length > 0) {
                e.target.value = `(${value}`;
            } else {
                e.target.value = '';
            }
        });
    });

    // Formatação de valores monetários
    const moneyInputsOriginal = document.querySelectorAll('input[data-type="money"]');
    moneyInputsOriginal.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (parseInt(value) / 100).toFixed(2);
            e.target.value = value.replace('.', ',');
        });

        input.addEventListener('focus', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value === '') {
                e.target.value = '0,00';
            }
        });

        input.addEventListener('blur', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (parseInt(value || '0') / 100).toFixed(2);
            e.target.value = 'R$ ' + value.replace('.', ',');
        });
    });

    // Validação de formulários
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validação de campos obrigatórios
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');

                    // Adiciona mensagem de erro se não existir
                    let errorMessage = field.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        errorMessage.textContent = 'Este campo é obrigatório.';
                        field.parentNode.appendChild(errorMessage);
                    }
                } else {
                    field.classList.remove('error');
                    const errorMessage = field.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });

            // Validação de email
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value.trim() && !isValidEmail(field.value)) {
                    isValid = false;
                    field.classList.add('error');

                    // Adiciona mensagem de erro se não existir
                    let errorMessage = field.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        errorMessage.textContent = 'Por favor, insira um e-mail válido.';
                        field.parentNode.appendChild(errorMessage);
                    }
                }
            });

            // Validação de senhas
            const passwordField = form.querySelector('input[name="password"]');
            const confirmPasswordField = form.querySelector('input[name="password_confirm"]');
            if (passwordField && confirmPasswordField) {
                if (passwordField.value !== confirmPasswordField.value) {
                    isValid = false;
                    confirmPasswordField.classList.add('error');

                    // Adiciona mensagem de erro se não existir
                    let errorMessage = confirmPasswordField.parentNode.querySelector('.error-message');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.className = 'error-message';
                        errorMessage.textContent = 'As senhas não conferem.';
                        confirmPasswordField.parentNode.appendChild(errorMessage);
                    }
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // Função para validar e-mail
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Inicialização de dropdowns
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('show');

            // Fecha outros dropdowns
            dropdownToggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    const otherDropdown = otherToggle.nextElementSibling;
                    otherDropdown.classList.remove('show');
                }
            });
        });
    });

    // Fecha dropdowns ao clicar fora
    document.addEventListener('click', function(e) {
        dropdownToggles.forEach(toggle => {
            if (!toggle.contains(e.target)) {
                const dropdown = toggle.nextElementSibling;
                dropdown.classList.remove('show');
            }
        });
    });

    // Inicialização de tabs
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            const tabPane = document.getElementById(tabId);

            // Remove a classe active de todos os botões e painéis
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });

            // Adiciona a classe active ao botão e painel clicados
            this.classList.add('active');
            tabPane.classList.add('active');
        });
    });

    // Inicialização de galeria de fotos
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainPhoto = document.getElementById('main-photo');

    if (thumbnails.length > 0 && mainPhoto) {
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Remove a classe active de todas as thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));

                // Adiciona a classe active à thumbnail clicada
                this.classList.add('active');

                // Atualiza a foto principal
                mainPhoto.src = this.getAttribute('data-src');
            });
        });
    }

    // Inicialização do simulador de financiamento
    const calculateBtn = document.getElementById('calculate-btn');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', function() {
            const priceElement = document.querySelector('.listing-price h2');
            if (!priceElement) return;

            const priceText = priceElement.textContent;
            const price = parseFloat(priceText.replace(/[^\d,]/g, '').replace(',', '.'));

            const downPayment = parseFloat(document.getElementById('down_payment').value) || 0;
            const installments = parseInt(document.getElementById('installments').value) || 48;
            const interestRate = parseFloat(document.getElementById('interest_rate').value) || 1.2;

            // Valor financiado
            const financedAmount = price - downPayment;

            // Cálculo da parcela (Sistema Francês de Amortização - Tabela Price)
            const monthlyRate = interestRate / 100;
            const installmentValue = financedAmount * (monthlyRate * Math.pow(1 + monthlyRate, installments)) / (Math.pow(1 + monthlyRate, installments) - 1);

            // Valor total
            const totalAmount = installmentValue * installments + downPayment;

            // Atualiza os resultados
            document.getElementById('financed_amount').textContent = formatCurrency(financedAmount);
            document.getElementById('installment_value').textContent = formatCurrency(installmentValue);
            document.getElementById('total_amount').textContent = formatCurrency(totalAmount);
        });
    }

    // Função para formatar moeda
    function formatCurrency(value) {
        return 'R$ ' + value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
});