// SPApp navigation + Authentication + Dynamic Navbar
$(document).ready(function() {
    var app = $.spapp({
        defaultView: "#main",
        templateDir: "./frontend/views/"
    });

    app.run();

    // ========================================
    // UPDATE NAVBAR BASED ON AUTH STATUS
    // ========================================
    function updateNavbar() {
        const token = localStorage.getItem('jwt_token');
        const userDataStr = localStorage.getItem('user_data');

        console.log('Updating navbar... Token exists:', !!token);

        if (!token || !userDataStr) {
            showGuestNavbar();
        } else {
            try {
                const userData = JSON.parse(userDataStr);
                showAuthenticatedNavbar(userData);
                // Load balance from database after navbar is rendered
                updateNavbarBalance();
            } catch (e) {
                console.error('Error parsing user data:', e);
                showGuestNavbar();
            }
        }
    }

    function showGuestNavbar() {
        const navbarHtml = `
            <li class="nav-item"><a class="nav-link" href="#main">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#login">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="#register">Register</a></li>
        `;
        $('#dynamicNavbar').html(navbarHtml);
        console.log('Guest navbar displayed');
    }

    function showAuthenticatedNavbar(userData) {
        const isAdmin = userData.role === 'admin';

        let navbarHtml = `
            <li class="nav-item"><a class="nav-link" href="#main">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#cars">Cars</a></li>
            <li class="nav-item"><a class="nav-link" href="#bookings">My Bookings</a></li>
            <li class="nav-item"><a class="nav-link" href="#add-credits">Add Credits</a></li>
            <li class="nav-item"><a class="nav-link" href="#team">Team</a></li>
            <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        `;

        if (isAdmin) {
            navbarHtml += `<li class="nav-item"><a class="nav-link text-danger fw-bold" href="#admin">Admin Panel</a></li>`;
        }

        navbarHtml += `
            <li class="nav-item ms-lg-3 d-none d-lg-block">
                <span class="nav-link fw-bold text-success" id="userBalance">
                    <i class="bi bi-wallet2"></i> $0.00
                </span>
            </li>
            <li class="nav-item">
                <span class="nav-link text-muted">Hi, ${userData.first_name}!</span>
            </li>
            <li class="nav-item">
                <a class="btn btn-sm btn-outline-danger ms-2" href="#" id="logoutBtn">Logout</a>
            </li>
        `;

        $('#dynamicNavbar').html(navbarHtml);
        console.log('Authenticated navbar displayed for:', userData.role);
    }

    // ========================================
    // UPDATE NAVBAR BALANCE FROM DATABASE
    // ========================================
    function updateNavbarBalance() {
        const token = localStorage.getItem('jwt_token');
        const userData = localStorage.getItem('user_data');

        if (!token || !userData) {
            return;
        }

        const user = JSON.parse(userData);
        const userId = user.user_id;

        $.ajax({
            url: `http://localhost/Car-Rental-Website/backend/rest/users/${userId}`,
            method: 'GET',
            headers: {
                'Authentication': token
            },
            success: function(response) {
                if (response.success && response.data) {
                    const balance = parseFloat(response.data.balance).toFixed(2);
                    $('#userBalance').html(`<i class="bi bi-wallet2"></i> $${balance}`);
                }
            },
            error: function(xhr) {
                console.error('Error loading balance:', xhr);
            }
        });
    }

    // Make it global so add-credits.js can call it
    window.updateNavbarBalance = updateNavbarBalance;

    // ========================================
    // LOGOUT HANDLER
    // ========================================
    $(document).on('click', '#logoutBtn', function(e) {
        e.preventDefault();
        console.log('Logout clicked');

        localStorage.removeItem('jwt_token');
        localStorage.removeItem('user_data');
        updateNavbar();
        window.location.hash = '#main';
        alert('You have been logged out successfully.');
    });

    // ========================================
    // ROUTE PROTECTION
    // ========================================
    function checkRouteAccess() {
        const currentHash = window.location.hash.substring(1);
        const token = localStorage.getItem('jwt_token');
        const userDataStr = localStorage.getItem('user_data');

        console.log('Checking route access for:', currentHash);

        const protectedRoutes = ['cars', 'bookings', 'add-credits', 'team', 'contact', 'admin'];
        const adminRoutes = ['admin'];

        if (protectedRoutes.includes(currentHash) && !token) {
            alert('Please login to access this page.');
            window.location.hash = '#login';
            return false;
        }

        if (adminRoutes.includes(currentHash) && token) {
            try {
                const userData = JSON.parse(userDataStr);
                if (userData.role !== 'admin') {
                    alert('Access denied. Admin privileges required.');
                    window.location.hash = '#main';
                    return false;
                }
            } catch (e) {
                console.error('Error parsing user data:', e);
                window.location.hash = '#login';
                return false;
            }
        }

        if (token && (currentHash === 'login' || currentHash === 'register')) {
            console.log('Already logged in, redirecting to main');
            window.location.hash = '#main';
            return false;
        }

        return true;
    }

    // ========================================
    // PAGE-SPECIFIC LOADING
    // ========================================
    function loadPageContent() {
        const currentHash = window.location.hash.substring(1) || 'main';
        console.log('Loading page content for:', currentHash);

        switch(currentHash) {
            case 'cars':
                console.log('Triggering loadCarsPage...');
                if (typeof window.loadCarsPage === 'function') {
                    setTimeout(function() {
                        window.loadCarsPage();
                    }, 300);
                }
                break;
            case 'bookings':
                console.log('Triggering loadBookingsPage...');
                if (typeof window.loadBookingsPage === 'function') {
                    setTimeout(function() {
                        window.loadBookingsPage();
                    }, 300);
                }
                break;
            case 'admin':
                console.log('Triggering loadAdminPage...');
                if (typeof window.loadAdminPage === 'function') {
                    setTimeout(function() {
                        window.loadAdminPage();
                    }, 300);
                }
                break;
            case 'add-credits':
                console.log('Triggering loadAddCreditsPage...');
                if (typeof window.loadAddCreditsPage === 'function') {
                    setTimeout(function() {
                        window.loadAddCreditsPage();
                    }, 300);
                } else {
                    console.error('loadAddCreditsPage function not found!');
                }
                break;
        }
    }

    // ========================================
    // REGISTER FORM HANDLER
    // ========================================
    $(document).on('submit', '#registerForm', function(e) {
        e.preventDefault();
        console.log('Register form submitted');

        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();

        $('#errorAlert').addClass('d-none');
        $('#successAlert').addClass('d-none');

        if (password !== confirmPassword) {
            $('#errorAlert').text('Passwords do not match!').removeClass('d-none');
            return;
        }

        if (password.length < 6) {
            $('#errorAlert').text('Password must be at least 6 characters long!').removeClass('d-none');
            return;
        }

        const formData = {
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            password: password
        };

        $.ajax({
            url: 'http://localhost/Car-Rental-Website/backend/rest/auth/register',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                console.log('Registration successful:', response);
                $('#successAlert').text('Registration successful! Redirecting to login...').removeClass('d-none');
                $('#registerForm')[0].reset();

                setTimeout(function() {
                    window.location.hash = '#login';
                }, 2000);
            },
            error: function(xhr) {
                console.error('Registration error:', xhr);
                console.error('Response:', xhr.responseText);
                const errorMessage = xhr.responseText || 'Registration failed. Please try again.';
                $('#errorAlert').text(errorMessage).removeClass('d-none');
            }
        });
    });

    // ========================================
    // LOGIN FORM HANDLER
    // ========================================
    $(document).on('submit', '#loginForm', function(e) {
        e.preventDefault();
        console.log('Login form submitted');

        const email = $('#email').val();
        const password = $('#password').val();

        $('#errorAlert').addClass('d-none');

        $.ajax({
            url: 'http://localhost/Car-Rental-Website/backend/rest/auth/login',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                email: email,
                password: password
            }),
            success: function(response) {
                console.log('Login successful:', response);

                localStorage.setItem('jwt_token', response.data.token);
                localStorage.setItem('user_data', JSON.stringify(response.data.user));

                updateNavbar(); // This will now also call updateNavbarBalance()
                alert('Login successful! Welcome back, ' + response.data.user.first_name + '!');

                if (response.data.user.role === 'admin') {
                    window.location.hash = '#admin';
                } else {
                    window.location.hash = '#cars';
                }
            },
            error: function(xhr) {
                console.error('Login error:', xhr);
                const errorMessage = xhr.responseText || 'Login failed. Please check your credentials.';
                $('#errorAlert').text(errorMessage).removeClass('d-none');
            }
        });
    });

    // ========================================
    // INITIALIZE ON PAGE LOAD
    // ========================================
    updateNavbar();
    checkRouteAccess();
    loadPageContent(); // Load initial page content

    // ========================================
    // HASH CHANGE HANDLER
    // ========================================
    $(window).on('hashchange', function() {
        console.log('=== Hash changed ===');
        checkRouteAccess();
        updateNavbar();
        loadPageContent(); // Load page-specific content
    });
});