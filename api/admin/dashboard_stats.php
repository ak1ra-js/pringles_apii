<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "../../config/connection.php";

try {

    // Total Produk
    $productQuery =
        "SELECT COUNT(*) AS total_products
         FROM products";

    $productResult =
        mysqli_query(
            $conn,
            $productQuery
        );

    $totalProducts =
        mysqli_fetch_assoc(
            $productResult
        )['total_products'];

    // Total Pesanan
    $orderQuery =
        "SELECT COUNT(*) AS total_orders
         FROM transactions";

    $orderResult =
        mysqli_query(
            $conn,
            $orderQuery
        );

    $totalOrders =
        mysqli_fetch_assoc(
            $orderResult
        )['total_orders'];

    // Pendapatan
    $revenueQuery = "

        SELECT
        COALESCE(
            SUM(total_amount),
            0
        ) AS revenue

        FROM transactions

        WHERE status = 'completed'

    ";

    $revenueResult =
        mysqli_query(
            $conn,
            $revenueQuery
        );

    $revenue =
        mysqli_fetch_assoc(
            $revenueResult
        )['revenue'];

    // Total Customer
    $customerQuery = "

        SELECT COUNT(*) AS total_customers

        FROM users

        WHERE role='buyer'

    ";

    $customerResult =
        mysqli_query(
            $conn,
            $customerQuery
        );

    $totalCustomers =
        mysqli_fetch_assoc(
            $customerResult
        )['total_customers'];

    echo json_encode([

        "status" => "success",

        "data" => [

            "total_products" =>
                (int)$totalProducts,

            "total_orders" =>
                (int)$totalOrders,

            "revenue" =>
                (int)$revenue,

            "total_customers" =>
                (int)$totalCustomers
        ]
    ]);

} catch (Exception $e) {

    echo json_encode([

        "status" => "error",

        "message" =>
            $e->getMessage()
    ]);
}
