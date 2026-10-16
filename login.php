<?php

session_start();

include "config/database.php";

$message = "";
$messageType = "";


// =====================================
// LOGIN
// =====================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // Check empty fields

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";
        $messageType = "danger";

    }

    else {

        // Find user

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?
             LIMIT 1"
        );


        $stmt->bind_param(
            "s",
            $email
        );


        $stmt->execute();


        $result = $stmt->get_result();


        // Check user

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();


            // Check password

            if (password_verify(
                $password,
                $user["password"]
            )) {


                // Save login information

                $_SESSION["user_id"] =
                    $user["id"];

                $_SESSION["user_name"] =
                    $user["name"];

                $_SESSION["user_email"] =
                    $user["email"];

                $_SESSION["user_role"] =
                    $user["role"];


                // Go to dashboard

                header(
                    "Location: dashboard.php"
                );

                exit();

            }

            else {

                $message =
                    "Incorrect password.";

                $messageType =
                    "danger";

            }

        }

        else {

            $message =
                "Email not found.";

            $messageType =
                "danger";

        }


        $stmt->close();

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login - My Cafe</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold">

            ☕ My Cafe

        </a>


        <a
            href="register.php"
            class="btn btn-warning">

            Register

        </a>

    </div>

</nav>



<!-- ================= LOGIN ================= -->

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">


            <div class="card shadow border-0">

                <div class="card-body p-4">


                    <h2
                        class="text-center fw-bold mb-4">

                        🔐 Login

                    </h2>


                    <!-- MESSAGE -->

                    <?php if (!empty($message)) { ?>

                        <div
                            class="alert alert-<?php
                            echo $messageType;
                            ?>">

                            <?php
                            echo htmlspecialchars(
                                $message
                            );
                            ?>

                        </div>

                    <?php } ?>


                    <!-- LOGIN FORM -->

                    <form method="POST">


                        <!-- EMAIL -->

                        <div class="mb-3">

                            <label
                                class="form-label">

                                Email

                            </label>


                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter your email"
                                required>

                        </div>



                        <!-- PASSWORD -->

                        <div class="mb-3">

                            <label
                                class="form-label">

                                Password

                            </label>


                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Enter your password"
                                required>

                        </div>



                        <!-- LOGIN BUTTON -->

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            Login

                        </button>


                    </form>



                    <!-- REGISTER -->

                    <p
                        class="text-center mt-3">

                        Don't have an account?

                        <a
                            href="register.php">

                            Register here

                        </a>

                    </p>


                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>