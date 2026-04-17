<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--
        HEADER.PHP – Module <head> HTML dùng chung cho trang Public
        Gọi bằng: include_once "includes/header.php";
        Biến tùy chỉnh (khai báo TRƯỚC khi include file này):
          $pageTitle = "Tên trang"; // Tiêu đề tab trình duyệt
    -->
    <title>
        <?php echo isset($pageTitle) ? e($pageTitle) . ' – ' : ''; ?>
        Cho thuê Cao ốc Văn phòng
    </title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- CSS tùy chỉnh cho trang Public -->
    <style>
        /* ── BIẾN MÀU THƯƠNG HIỆU ────────────────────────── */
        :root {
            --brand-primary:   #1a3c6e;   /* Xanh navy đậm */
            --brand-secondary: #e8a020;   /* Vàng gold */
            --brand-light:     #f0f4f8;   /* Xám nhạt nền */
        }

        /* ── TỔNG QUÁT ───────────────────────────────────── */
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff;
            color: #333;
        }

        /* ── CARD PHÒNG ──────────────────────────────────── */
        .card-phong {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
        }
        .card-phong:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
        }
        .card-phong .badge-trong {
            background-color: #28a745;
        }
        .card-phong .gia-thue {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a3c6e;
        }

        /* ── NÚT CTA ─────────────────────────────────────── */
        .btn-brand {
            background-color: var(--brand-primary);
            color: #fff;
            border: none;
        }
        .btn-brand:hover {
            background-color: #142e55;
            color: #fff;
        }
        .btn-gold {
            background-color: var(--brand-secondary);
            color: #fff;
            border: none;
            font-weight: 600;
        }
        .btn-gold:hover {
            background-color: #c88a10;
            color: #fff;
        }

        /* ── FOOTER ──────────────────────────────────────── */
        footer {
            background-color: var(--brand-primary);
            color: #cdd5e0;
        }
        footer a {
            color: var(--brand-secondary);
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>