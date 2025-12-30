// Add Credits Page JavaScript

// API Base URL - change for deployment
const API_BASE_URL = 'https://seashell-app-mqlu9.ondigitalocean.app';

window.loadAddCreditsPage = function() {
    console.log('Add Credits page loaded');

    const token = localStorage.getItem('jwt_token');
    const userData = window.getUserFromToken();

    if (!token || !userData) {
        window.location.hash = '#login';
        return;
    }

    loadUserBalance();
    loadRecentTransactions();

    $('#topUpForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        handleAddCredits();
    });
};

function loadUserBalance() {
    const userData = window.getUserFromToken();
    const userId = userData.user_id;
    const token = localStorage.getItem('jwt_token');

    $.ajax({
        url: `${API_BASE_URL}/users/${userId}`,
        method: 'GET',
        headers: {
            'Authentication': token
        },
        success: function(response) {
            if (response.success && response.data) {
                const balance = parseFloat(response.data.balance).toFixed(2);
                $('#currentBalance').text('$' + balance);
            }
        },
        error: function(xhr) {
            console.error('Error loading balance:', xhr);
            showError('Failed to load current balance');
        }
    });
}

function loadRecentTransactions() {
    const userData = window.getUserFromToken();
    const userId = userData.user_id;
    const token = localStorage.getItem('jwt_token');

    $.ajax({
        url: `${API_BASE_URL}/transactions/user/${userId}`,
        method: 'GET',
        headers: {
            'Authentication': token
        },
        success: function(response) {
            if (response.success && response.data) {
                displayRecentTransactions(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading transactions:', xhr);
            $('#recentTransactionsList').html('<div class="text-center text-muted py-3"><small>No transactions found</small></div>');
        }
    });
}

function displayRecentTransactions(transactions) {
    if (!transactions || transactions.length === 0) {
        $('#recentTransactionsList').html('<div class="text-center text-muted py-3"><small>No transactions yet</small></div>');
        return;
    }

    const recentTransactions = transactions.slice(-5).reverse();

    let html = '<ul class="list-unstyled small">';

    recentTransactions.forEach(transaction => {
        const amount = parseFloat(transaction.amount);
        const sign = amount >= 0 ? '+' : '';
        const colorClass = amount >= 0 ? 'text-success' : 'text-danger';
        const date = formatDate(transaction.created_at);
        const type = formatTransactionType(transaction.type);

        html += `
            <li class="mb-1">
                <span class="${colorClass} fw-bold">${sign}$${Math.abs(amount).toFixed(2)}</span>
                <span class="text-muted">${type}</span>
                <span class="text-muted">— ${date}</span>
            </li>
        `;
    });

    html += '</ul>';
    $('#recentTransactionsList').html(html);
}

function formatTransactionType(type) {
    const types = {
        'top_up': 'Top Up',
        'booking_payment': 'Booking Payment',
        'refund': 'Refund',
        'admin_adjustment': 'Admin Adjustment'
    };
    return types[type] || type;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { month: 'short', day: 'numeric', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function handleAddCredits() {
    const amount = parseFloat($('#amount').val());
    const paymentMethod = $('#method').val();

    if (!amount || amount < 5) {
        showError('Minimum top-up amount is $5.00');
        return;
    }

    if (!paymentMethod) {
        showError('Please select a payment method');
        return;
    }

    setSubmitButtonLoading(true);
    hideMessages();

    const token = localStorage.getItem('jwt_token');

    $.ajax({
        url: `${API_BASE_URL}/transactions/add-credits`,
        method: 'POST',
        headers: {
            'Authentication': token,
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            amount: amount,
            payment_method: paymentMethod,
            description: `Top-up via ${paymentMethod}`
        }),
        success: function(response) {
            setSubmitButtonLoading(false);

            if (response.success) {
                showSuccess(`$${amount.toFixed(2)} successfully added to your balance!`);

                if (response.new_balance) {
                    $('#currentBalance').text('$' + parseFloat(response.new_balance).toFixed(2));
                }

                if (typeof window.updateNavbarBalance === 'function') {
                    window.updateNavbarBalance();
                }

                $('#topUpForm')[0].reset();
                loadRecentTransactions();
            } else {
                showError(response.message || 'Failed to add credits');
            }
        },
        error: function(xhr) {
            setSubmitButtonLoading(false);
            const errorMessage = xhr.responseJSON?.message || 'Failed to add credits. Please try again.';
            showError(errorMessage);
        }
    });
}

function setSubmitButtonLoading(loading) {
    if (loading) {
        $('#submitButtonText').addClass('d-none');
        $('#submitButtonSpinner').removeClass('d-none');
        $('#topUpForm button[type="submit"]').prop('disabled', true);
    } else {
        $('#submitButtonText').removeClass('d-none');
        $('#submitButtonSpinner').addClass('d-none');
        $('#topUpForm button[type="submit"]').prop('disabled', false);
    }
}

function showError(message) {
    $('#creditsError').text(message).removeClass('d-none');
    $('#creditsSuccess').addClass('d-none');

    setTimeout(() => {
        $('#creditsError').addClass('d-none');
    }, 5000);
}

function showSuccess(message) {
    $('#creditsSuccess').text(message).removeClass('d-none');
    $('#creditsError').addClass('d-none');

    setTimeout(() => {
        $('#creditsSuccess').addClass('d-none');
    }, 5000);
}

function hideMessages() {
    $('#creditsError').addClass('d-none');
    $('#creditsSuccess').addClass('d-none');
}