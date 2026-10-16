<?php

session_start();

include "config/database.php";

$message = "";
$messageType = "";


// =====================================
// REGISTER
// =====================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];


    // =================================
    // CHECK EMPTY FIELDS
    // =================================

    if (
        empty($name) ||
        empty($email) ||
        empty($password)
    ) {

        $message =
            "Please fill all fields.";

        $messageType =
            "danger";

    }

    else {


        // =================================
        // CHECK EXISTING EMAIL
        // =================================

        $checkStmt =
            $conn->prepare(
                "SELECT id
                 FROM users
                 WHERE email = ?
                 LIMIT 1"
            );


        $checkStmt->bind_param(
            "s",
            $email
        );


        $checkStmt->execute();


        $checkResult =
            $checkStmt->get_result();


        if ($checkResult->num_rows > 0) {

            $message =
                "Email already registered.";

            $messageType =
                "warning";

            $checkStmt->close();

        }

        else {

            $checkStmt->close();


            // =================================
            // HASH PASSWORD
            // =================================

            $hashedPassword =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            // =================================
            // INSERT USER
            // =================================

            $stmt =
                $conn->prepare(
                    "INSERT INTO users
                    (name, email, password, role)
                    VALUES (?, ?, ?, 'customer')"
                );


            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashedPassword
            );


            if ($stmt->execute()) {

                $message =
                    "Registration successful! You can now login.";

                $messageType =
                    "success";

            }

            else {

                $message =
                    "Registration failed: " .
                    $conn->error;

                $messageType =
                    "danger";

            }


            $stmt->close();

        }

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

    <title>Register - My Cafe</title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


<!-- ================================= -->
<!-- NAVBAR -->
<!-- ================================= -->

<nav class="navbar navbar-dark bg-dark">

    <div class="container">

        <a
            href="index.php"
            class="navbar-brand fw-bold">

            ☕ My Cafe

        </a>


        <a
            href="login.php"
            class="btn btn-warning">

            Login

        </a>

    </div>

</nav>



<!-- ================================= -->
<!-- REGISTER -->
<!-- ================================= -->

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow border-0">

                <div class="card-body p-4">


                    <h2
                        class="text-center fw-bold mb-4">

                        📝 Create Account

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



                    <!-- FORM -->

                    <form method="POST">


                        <!-- NAME -->

                        <div class="mb-3">

                            <label
                                class="form-label">

                                Name

                            </label>


                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Enter your name"
                                required>

                        </div>



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



                        <!-- BUTTON -->

                        <button
                            type="submit"
                            class="btn btn-primary w-100">

                            Register

                        </button>


                    </form>



                    <p
                        class="text-center mt-3">

                        Already have an account?

                        <a href="login.php">

                            Login here

                        </a>

                    </p>


                </div>

            </div>

        </div>

    </div>

</div>


</body>

</html>