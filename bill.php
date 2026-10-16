<?php
session_start();

include "config/database.php";

/* Check login */
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION["user_id"];

/* Check order ID */
if (!isset($_GET["order_id"]) || !is_numeric($_GET["order_id"])) {
    die("Invalid order ID.");
}

$orderId = (int)$_GET["order_id"];


/* =========================================================
   GET CUSTOMER FROM USERS TABLE
   ========================================================= */

$customerStmt = $conn->prepare("
    SELECT id, name, email
    FROM users
    WHERE id = ?
    LIMIT 1
");

if (!$customerStmt) {
    die("Database error: " . $conn->error);
}

$customerStmt->bind_param("i", $userId);
$customerStmt->execute();

$customerResult = $customerStmt->get_result();

if ($customerResult->num_rows === 0) {
    die("Customer not found.");
}

$customer = $customerResult->fetch_assoc();

$customerStmt->close();


/* =========================================================
   GET ORDER
   ========================================================= */

$orderStmt = $conn->prepare("
    SELECT
        id,
        customer_id,
        total_amount,
        order_status,
        order_date
    FROM orders
    WHERE id = ?
    AND customer_id = ?
    LIMIT 1
");

if (!$orderStmt) {
    die("Database error: " . $conn->error);
}

$orderStmt->bind_param("ii", $orderId, $userId);
$orderStmt->execute();

$orderResult = $orderStmt->get_result();

if ($orderResult->num_rows === 0) {
    die("Order not found.");
}

$order = $orderResult->fetch_assoc();

$orderStmt->close();


/* =========================================================
   GET ORDER ITEMS
   ========================================================= */

$itemStmt = $conn->prepare("
    SELECT
        oi.id,
        oi.order_id,
        oi.food_id,
        oi.quantity,
        oi.price,
        f.name AS food_name
    FROM order_items oi
    INNER JOIN food_items f
        ON oi.food_id = f.id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
");

if (!$itemStmt) {
    die("Database error: " . $conn->error);
}

$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();

$itemResult = $itemStmt->get_result();

$orderItems = [];

while ($row = $itemResult->fetch_assoc()) {
    $orderItems[] = $row;
}

$itemStmt->close();


/* =========================================================
   CALCULATE TOTAL
   ========================================================= */

$total = 0;

foreach ($orderItems as $item) {
    $total += ((float)$item["price"] * (int)$item["quantity"]);
}


/* =========================================================
   CLEAR CART AFTER SUCCESSFUL ORDER
   ========================================================= */

$clearCart = isset($_GET["clear_cart"]) &&
             $_GET["clear_cart"] === "1";

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Bill - Cafe Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f5f5;
        }

        .bill-container {
            max-width: 800px;
            margin: 40px auto;
        }

        .bill-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.10);
        }

        .cafe-title {
            font-size: 32px;
            font-weight: bold;
        }

        .bill-title {
            font-size: 24px;
            font-weight: bold;
        }

        .total-row {
            font-size: 20px;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 7px 15px;
            border-radius: 20px;
            background: #ffc107;
            color: #000;
            font-weight: bold;
        }

        @media print {

            body {
                background: white;
            }

            .no-print {
                display: none !important;
            }

            .bill-card {
                box-shadow: none;
                padding: 0;
            }

            .bill-container {
                margin: 0;
                max-width: 100%;
            }
        }

    </style>

</head>

<body>


<div class="container bill-container">

    <div class="bill-card">

        <!-- HEADER -->

        <div class="text-center mb-4">

            <div class="cafe-title">
                ☕ Cafe Management
            </div>

            <div class="bill-title">
                Order Bill
            </div>

            <p class="text-muted mb-0">
                Thank you for your order!
            </p>

        </div>


        <hr>


        <!-- CUSTOMER DETAILS -->

        <div class="row mb-4">

            <div class="col-md-6">

                <h6 class="fw-bold">
                    Customer Details
                </h6>

                <p class="mb-1">
                    <strong>Name:</strong>
                    <?php
                    echo htmlspecialchars($customer["name"]);
                    ?>
                </p>

                <p class="mb-1">
                    <strong>Email:</strong>
                    <?php
                    echo htmlspecialchars($customer["email"]);
                    ?>
                </p>

            </div>


            <div class="col-md-6 text-md-end">

                <h6 class="fw-bold">
                    Order Details
                </h6>

                <p class="mb-1">
                    <strong>Order ID:</strong>
                    #<?php echo $order["id"]; ?>
                </p>

                <p class="mb-1">

                    <strong>Date:</strong>

                    <?php
                    echo date(
                        "d M Y, h:i A",
                        strtotime($order["order_date"])
                    );
                    ?>

                </p>

                <p class="mb-1">

                    <strong>Status:</strong>

                    <span class="status">
                        <?php
                        echo htmlspecialchars(
                            $order["order_status"]
                        );
                        ?>
                    </span>

                </p>

            </div>

        </div>


        <!-- ORDER ITEMS -->

        <h5 class="fw-bold mb-3">
            Ordered Items
        </h5>


        <?php if (count($orderItems) > 0): ?>

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>#</th>

                            <th>Food Item</th>

                            <th class="text-center">
                                Quantity
                            </th>

                            <th class="text-end">
                                Price
                            </th>

                            <th class="text-end">
                                Amount
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    $number = 1;

                    foreach ($orderItems as $item):

                        $price = (float)$item["price"];

                        $quantity = (int)$item["quantity"];

                        $itemTotal = $price * $quantity;

                    ?>

                        <tr>

                            <td>
                                <?php echo $number; ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $item["food_name"]
                                );
                                ?>
                            </td>

                            <td class="text-center">
                                <?php echo $quantity; ?>
                            </td>

                            <td class="text-end">
                                ₹<?php
                                echo number_format(
                                    $price,
                                    2
                                );
                                ?>
                            </td>

                            <td class="text-end">
                                ₹<?php
                                echo number_format(
                                    $itemTotal,
                                    2
                                );
                                ?>
                            </td>

                        </tr>

                    <?php

                        $number++;

                    endforeach;

                    ?>

                    </tbody>

                </table>

            </div>


            <!-- TOTAL -->

            <div class="row justify-content-end">

                <div class="col-md-5">

                    <div class="d-flex justify-content-between">

                        <span>
                            Subtotal
                        </span>

                        <span>
                            ₹<?php
                            echo number_format(
                                $total,
                                2
                            );
                            ?>
                        </span>

                    </div>


                    <hr>


                    <div class="d-flex justify-content-between total-row">

                        <span>
                            Total
                        </span>

                        <span>
                            ₹<?php
                            echo number_format(
                                $total,
                                2
                            );
                            ?>
                        </span>

                    </div>

                </div>

            </div>


        <?php else: ?>

            <div class="alert alert-warning">

                No items found for this order.

            </div>

        <?php endif; ?>


        <!-- BUTTONS -->

        <div class="text-center mt-4 no-print">

            <button
                onclick="window.print()"
                class="btn btn-primary me-2"
            >
                🖨 Print Bill
            </button>


            <a
                href="menu.php"
                class="btn btn-success me-2"
            >
                🍔 Order More
            </a>


            <a
                href="dashboard.php"
                class="btn btn-secondary"
            >
                Dashboard
            </a>

        </div>


        <!-- FOOTER -->

        <div class="text-center text-muted mt-4">

            <small>
                Thank you for visiting our cafe!
            </small>

        </div>

    </div>

</div>


<?php if ($clearCart): ?>

<script>

    /*
       Clear cart only after a newly
       completed order.
    */
    localStorage.removeItem("cafeCart");

</script>

<?php endif; ?>


</body>

</html>