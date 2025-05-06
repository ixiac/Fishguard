<?php
session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] != 1) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Header -->
    <?php include 'layouts/head.php'; ?>

    <!-- Custom CSS -->
    <link href="../assets/css/admindash.css" rel="stylesheet">
    <style>
        .about-section {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .about-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .about-section h2 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .about-section p {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #6c757d;
        }

        .about-section img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 20px;
        }

        .about-logo {
            max-width: 150px;
            margin: 0 auto 20px;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .team-member {
            margin-bottom: 20px;
        }

        .team-member img {
            max-width: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .team-member div {
            display: inline-block;
            vertical-align: top;
        }
    </style>
</head>

<body>
    <?php include 'layouts/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'layouts/sidebar.php'; ?>

            <!-- Main Content -->
            <main id="content" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">About Us</h1>
                </div>

                <!-- About Section -->
                <div class="about-section">
                    <img src="../assets/img/logo.png" alt="Fishguard Logo" width="200" height="200" class="about-logo">
                    <h2>Welcome to Fish<span class="text-primary">Guard</span></h2>
                    <p>
                        Fishguard is your trusted platform for managing and monitoring aquatic resources.
                        Our mission is to provide innovative solutions to ensure sustainable practices
                        and efficient management of fisheries. With a dedicated team and cutting-edge
                        technology, we aim to make a positive impact on the environment and the community.
                    </p>
                    <p>
                        Whether you're an administrator or a fisherman, Fishguard
                        offers tools and insights tailored to your needs. Join us in our journey to
                        safeguard aquatic ecosystems for future generations.
                    </p>
                    <i class="bi bi-people-fill"></i> Fishguard Community
                </div>

                <!-- Vision and Team Section -->
                <div class="row mb-5">
                    <div class="col-md-7">
                        <div class="card">
                            <h2>Our Vision</h2>
                            <p class="text-muted mt-2">
                                At Fishguard, we envision a world where aquatic ecosystems thrive in harmony
                                with human activities. Our goal is to empower communities and organizations
                                with the tools and knowledge needed to protect and sustain marine life for
                                generations to come.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card">
                            <h3>Our Team</h3>
                            <div class="team-member">
                                <img src="../assets/img/goat.jpg" alt="Team Member 1">
                                <div>
                                    <strong>Bien Marlon Maranan</strong><br>
                                    <small class="text-muted">Developer</small><br>
                                </div>
                            </div>
                            <div class="team-member">
                                <img src="../assets/img/lebron.jpg" alt="Team Member 2">
                                <div>
                                    <strong>LeBron James</strong><br>
                                    <small class="text-muted">Moral Support</small><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <?php include 'layouts/footer.php'; ?>
            </main>
        </div>
    </div>

    <!-- Dark Overlay for Mobile Sidebar -->
    <div class="overlay"></div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle sidebar on button click
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('active');
                document.querySelector('.overlay').classList.toggle('active');
            });

            // Close sidebar when clicking on overlay
            document.querySelector('.overlay').addEventListener('click', function () {
                document.getElementById('sidebar').classList.remove('active');
                this.classList.remove('active');
            });
        });
    </script>
</body>

</html>