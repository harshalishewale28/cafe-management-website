<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>sky light Cafe - Sky light Cafe Management System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            ☕ My Cafe
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="menu.php">
                        Menu
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Contact
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-warning ms-lg-2" href="#">
                        Login
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- ================= HERO SECTION ================= -->

<section class="bg-light py-5">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-md-6">

                <h1 class="display-4 fw-bold">
                    Welcome to My Cafe ☕
                </h1>

                <p class="lead">
                    Delicious food, fresh coffee and a wonderful
                    cafe experience.
                </p>

                <a href="menu.php"
                   class="btn btn-primary btn-lg">

                    View Menu

                </a>

                <a href="#about"
                   class="btn btn-outline-dark btn-lg ms-2">

                    About Us

                </a>

            </div>


            <div class="col-md-6 text-center">

                <div class="display-1">
                    ☕🍕🍔
                </div>

            </div>

        </div>

    </div>

</section>


<!-- ================= FEATURES ================= -->

<section class="py-5">

    <div class="container">

        <h2 class="text-center mb-5">
            Why Choose Our Cafe?
        </h2>


        <div class="row">


            <!-- Feature 1 -->

            <div class="col-md-4 mb-4">

                <div class="card text-center h-100 shadow-sm">

                    <div class="card-body">

                        <div class="display-4">
                            ☕
                        </div>

                        <h4 class="card-title mt-3">
                            Fresh Coffee
                        </h4>

                        <p class="card-text">
                            Enjoy freshly prepared coffee
                            made with quality ingredients.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Feature 2 -->

            <div class="col-md-4 mb-4">

                <div class="card text-center h-100 shadow-sm">

                    <div class="card-body">

                        <div class="display-4">
                            🍕
                        </div>

                        <h4 class="card-title mt-3">
                            Delicious Food
                        </h4>

                        <p class="card-text">
                            Choose from our delicious
                            food and snacks.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Feature 3 -->

            <div class="col-md-4 mb-4">

                <div class="card text-center h-100 shadow-sm">

                    <div class="card-body">

                        <div class="display-4">
                            🛎️
                        </div>

                        <h4 class="card-title mt-3">
                            Easy Ordering
                        </h4>

                        <p class="card-text">
                            Order your favourite food
                            quickly and easily.
                        </p>

                    </div>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- ================= ABOUT ================= -->

<section id="about" class="bg-light py-5">

    <div class="container">

        <div class="row">

            <div class="col-md-12 text-center">

                <h2>
                    About Our Cafe
                </h2>

                <p class="lead mt-3">

                    My Cafe is a cafe management website
                    designed to make food ordering,
                    billing and cafe management easier.

                </p>

            </div>

        </div>

    </div>

</section>


<!-- ================= FOOTER ================= -->

<footer class="bg-dark text-white text-center py-4">

    <p class="mb-0">
        © 2026 My Cafe | Cafe Management System
    </p>

</footer>


<!-- Bootstrap JavaScript -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>