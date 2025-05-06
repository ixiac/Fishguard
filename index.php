<?php
require_once 'assets/db.php';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"], $_POST["password"])) {
    $inputUsername = $conn->real_escape_string($_POST["username"]);
    $inputPassword = $conn->real_escape_string($_POST["password"]);

    $sql = "SELECT * FROM users WHERE username = '$inputUsername'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($inputPassword, $user["password"])) {
            // Start session and store user data
            session_start();
            $_SESSION["UID"] = $user["UID"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];

            // Redirect based on role
            if ($user["role"] == 1) {
                header("Location: admin/dashboard.php");
            } elseif ($user["role"] == 2) {
                header("Location: users/dashboard.php");
            } else {
                $loginError = "Unauthorized role.";
            }
            exit();
        } else {
            $loginError = "Invalid password.";
        }
    } else {
        $loginError = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FishGuard - Smart Fishing Regulation System</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <!-- Header -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg shadow" style="background-color: #fbfbfb;">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="assets/img/navlogo.png" alt="FishGuard Logo" width="50" height="50" class="me-2">
                    <span class="fs-4">Fish<span class="text-primary">Guard</span></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto me-5">
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#how-it-works">How It Works</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#testimonials">Testimonials</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                    </ul>
                    <button class="btn btn-warning fw-bold" data-bs-toggle="modal"
                        data-bs-target="#loginModal">Login</button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="bi bi-shield-lock login-icon"></i>Login to FishGuard
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($loginError)): ?>
                        <div class="alert alert-danger"><?php echo $loginError; ?></div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="loginUsername" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="loginUsername" name="username"
                                    placeholder="Enter your username" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="loginPassword" name="password"
                                    placeholder="Enter your password" required>
                            </div>
                        </div>
                        <div class="login-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                            <a href="#" class="text-primary">Forgot password?</a>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
                    </form>

                    <div class="register-divider">
                        <span>OR</span>
                    </div>

                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account?</p>
                        <a href="register.php" class="btn btn-warning mt-2">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">
                        <i class="bi bi-person-plus login-icon"></i>Create an Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" placeholder="Enter first name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" placeholder="Enter last name">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Email address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="registerEmail"
                                    placeholder="name@example.com">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="registerPassword"
                                    placeholder="Create a password">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" class="form-control" id="confirmPassword"
                                    placeholder="Confirm your password">
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="termsCheck">
                            <label class="form-check-label" for="termsCheck">I agree to the <a href="#">Terms of
                                    Service</a> and <a href="#">Privacy Policy</a></label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    </form>

                    <div class="register-divider">
                        <span>OR</span>
                    </div>

                    <div class="social-login">
                        <a href="#" class="social-login-btn">
                            <i class="bi bi-google"></i>
                        </a>
                        <a href="#" class="social-login-btn">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-login-btn">
                            <i class="bi bi-twitter"></i>
                        </a>
                    </div>

                    <div class="text-center mt-4">
                        <p class="mb-0">Already have an account?</p>
                        <button class="btn btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#loginModal"
                            data-bs-dismiss="modal">Login</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 mb-4">Simplify Fishing Compliance with FishGuard</h1>
                    <p class="lead mb-5">The all-in-one solution for anglers and regulatory agencies to monitor, manage,
                        and maintain sustainable fishing practices.</p>
                    <button class="btn btn-warning btn-lg px-5 fw-bold">Get Started</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container text-center">
            <div class="section-title">
                <h2>Key Features</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow feature-card">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h3 class="h4 text-primary mb-3">Lorem ipsum dolor sit amet</h3>
                            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod
                                tempor incididunt ut labore et dolore magna aliqua.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow feature-card">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <h3 class="h4 text-primary mb-3">Lorem ipsum dolor sit amet</h3>
                            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod
                                tempor incididunt ut labore et dolore magna aliqua.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow feature-card">
                        <div class="card-body p-4">
                            <div class="feature-icon">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                            <h3 class="h4 text-primary mb-3">Lorem ipsum dolor sit amet</h3>
                            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod
                                tempor incididunt ut labore et dolore magna aliqua.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-5 bg-white">
        <div class="container text-center">
            <div class="section-title">
                <h2>How It Works</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="step-number">1</div>
                        <h3 class="h5 mb-3">Lorem ipsum dolor sit amet</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="step-number">2</div>
                        <h3 class="h5 mb-3">Lorem ipsum dolor sit amet</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="step-number">3</div>
                        <h3 class="h5 mb-3">Lorem ipsum dolor sit amet</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="step-number">4</div>
                        <h3 class="h5 mb-3">Lorem ipsum dolor sit amet</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5">
        <div class="container text-center">
            <div class="section-title">
                <h2>What Anglers Are Saying</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card h-100 shadow">
                        <div class="card-body p-4">
                            <p class="card-text fst-italic mb-4">"Lorem ipsum dolor sit amet, consectetur adipiscing
                                elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</p>
                            <div class="d-flex align-items-center">
                                <div class="testimonial-avatar">
                                    <img src="/api/placeholder/50/50" alt="James Wilson" class="img-fluid">
                                </div>
                                <div class="text-start">
                                    <h4 class="h6 mb-0 text-primary">James Wilson</h4>
                                    <p class="small mb-0">Recreational Angler</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 shadow">
                        <div class="card-body p-4">
                            <p class="card-text fst-italic mb-4">"Lorem ipsum dolor sit amet, consectetur adipiscing
                                elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</p>
                            <div class="d-flex align-items-center">
                                <div class="testimonial-avatar">
                                    <img src="/api/placeholder/50/50" alt="Maria Rodriguez" class="img-fluid">
                                </div>
                                <div class="text-start">
                                    <h4 class="h6 mb-0 text-primary">Maria Rodriguez</h4>
                                    <p class="small mb-0">Charter Boat Captain</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 shadow">
                        <div class="card-body p-4">
                            <p class="card-text fst-italic mb-4">"Lorem ipsum dolor sit amet, consectetur adipiscing
                                elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."</p>
                            <div class="d-flex align-items-center">
                                <div class="testimonial-avatar">
                                    <img src="/api/placeholder/50/50" alt="Dr. Robert Chen" class="img-fluid">
                                </div>
                                <div class="text-start">
                                    <h4 class="h6 mb-0 text-primary">Dr. Robert Chen</h4>
                                    <p class="small mb-0">Marine Conservation Officer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-white">
        <div class="container">
            <div class="section-title text-center">
                <h2>Get In Touch</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-body p-4">
                            <form>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" placeholder="Your Name">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="Your Email">
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" placeholder="Subject">
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" rows="4"
                                        placeholder="Your Message"></textarea>
                                </div>
                                <button type="submit" class="btn btn-warning fw-bold w-100">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="py-5 bg-dark text-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h3 class="h5 text-warning mb-3">FishGuard</h3>
                    <p class="mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor
                        incididunt ut labore et dolore magna aliqua.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-link">
                            <i class="bi bi-google"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="bi bi-instagram"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h3 class="h5 text-warning mb-3">Quick Links</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features" class="text-white text-decoration-none">Features</a></li>
                        <li class="mb-2"><a href="#how-it-works" class="text-white text-decoration-none">How It
                                Works</a></li>
                        <li class="mb-2"><a href="#testimonials"
                                class="text-white text-decoration-none">Testimonials</a></li>
                        <li class="mb-2"><a href="#pricing" class="text-white text-decoration-none">Pricing</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h3 class="h5 text-warning mb-3">Resources</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Blog</a></li>
                        <li class="mb-2"><a href="#" class="text-whitetext-decoration-none">Help Center</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">FAQ</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">User Guides</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h3 class="h5 text-warning mb-3">Contact Info</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2 d-flex align-items-center">
                            <i class="bi bi-geo-alt me-2"></i> 123 Hirap, Batangas City
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="bi bi-envelope me-2"></i> info@fishguard.com
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="bi bi-telephone me-2"></i> (555) 123-4567
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-light opacity-25">
            <div class="text-center"></div>
        </div>
        <p class="mb-0">&copy; 2025 FishGuard. All rights reserved.</p>
        </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Script to handle modal switching -->
    <script>
        // This script handles toggling between login and register modals
        document.addEventListener('DOMContentLoaded', function () {
            // Get references to modals
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            const registerModal = new bootstrap.Modal(document.getElementById('registerModal'));

            // Listen for clicks on "Register Now" button in login modal
            document.querySelectorAll('[data-bs-target="#registerModal"]').forEach(button => {
                button.addEventListener('click', function () {
                    loginModal.hide();
                    setTimeout(() => {
                        registerModal.show();
                    }, 500);
                });
            });

            // Listen for clicks on "Login" button in register modal
            document.querySelectorAll('[data-bs-target="#loginModal"]').forEach(button => {
                button.addEventListener('click', function () {
                    registerModal.hide();
                    setTimeout(() => {
                        loginModal.show();
                    }, 500);
                });
            });

            // Update hero section button to open login modal
            document.querySelector('.hero .btn-warning').addEventListener('click', function () {
                loginModal.show();
            });
        });
    </script>
</body>

</html>