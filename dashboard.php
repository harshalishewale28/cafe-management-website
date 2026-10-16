<?php

session_start();


// Check whether customer is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Customer Dashboard - My Cafe</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand fw-bold"
           href="index.php">

            ☕ My Cafe

        </a>


        <div>

            <a href="menu.php"
               class="btn btn-outline-light me-2">

                ☕ Menu

            </a>


            <a href="logout.php"
               class="btn btn-danger">

                Logout

            </a>

        </div>

    </div>

</nav>



<!-- ================= DASHBOARD ================= -->

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card shadow border-0">

                <div class="card-body text-center p-5">


                    <h1 class="fw-bold">

                        Welcome,
                        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
                        👋

                    </h1>


                    <p class="text-muted">

                        You are successfully logged in to My Cafe.

                    </p>


                    <hr>


                    <h4 class="mb-4">

                        Customer Information

                    </h4>


                    <p>

                        <strong>Name:</strong>

                        <?php
                        echo htmlspecialchars($_SESSION["user_name"]);
                        ?>

                    </p>


                    <p>

                        <strong>Email:</strong>

                        <?php
                        echo htmlspecialchars($_SESSION["user_email"]);
                        ?>

                    </p>


                    <p>

                        <strong>Role:</strong>

                        <?php
                        echo htmlspecialchars($_SESSION["user_role"]);
                        ?>

                    </p>


                    <div class="mt-4">

                        <a href="menu.php"
                           class="btn btn-primary me-2">

                            ☕ Browse Menu

                        </a>


                        <a href="logout.php"
                           class="btn btn-danger">

                            Logout

                        </a>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>