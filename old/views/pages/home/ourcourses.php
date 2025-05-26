<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses Section</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Quicksand:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            line-height: 1.6;
        }

        .our-courses {
            text-align: center;
            padding: 50px 0;
            background-color: #f9f9f9;
        }

        .our-courses h1 {
            text-align: center;
            font-size: 2.5rem;
            color: #001A45;
            margin-top: 50px;
            margin-bottom: 40px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .card {
            display: flex;
            align-items: flex-start;
            width: 100%;
            max-width: 320px;
            min-height: 96px;
            height: auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 16px;
            margin: 0 auto;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .card-content {
            flex: 1;
        }

        .card-content h2 {
            text-align: start;
            font-size: 1.25rem;
            color: #001A45;
            margin: 0;
            line-height: 1.3;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        .card-content p {
            text-align: start;
            font-size: 0.875rem;
            color: #555;
            margin: 8px 0 0;
            line-height: 1.5;
            font-family: 'Quicksand', sans-serif;
            font-weight: 500;
        }

        .kursus-private {
            text-align: start;
            margin-top: 40px;
            padding: 0 20px;
            max-width: 1080px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .kursus-private h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            color: #001A45;
            font-size: 1.75rem;
        }

        .lihat-semua {
            color: #3B82F6;
            text-decoration: none;
            font-family: 'Quicksand', sans-serif;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
        }

        .stagger-delay-1 {
            animation-delay: 0.1s;
        }

        .stagger-delay-2 {
            animation-delay: 0.2s;
        }

        .stagger-delay-3 {
            animation-delay: 0.3s;
        }

        .stagger-delay-4 {
            animation-delay: 0.4s;
        }

        .stagger-delay-5 {
            animation-delay: 0.5s;
        }

        .stagger-delay-6 {
            animation-delay: 0.6s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .card-container {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .our-courses h1 {
                font-size: 2rem;
            }

            .card {
                max-width: none;
            }

            .kursus-private h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .card-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .our-courses h1 {
                font-size: 1.75rem;
            }

            .card-content h2 {
                font-size: 1.1rem;
            }

            .kursus-private {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .kursus-private h2 {
                font-size: 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .our-courses {
                padding: 30px 15px;
            }

            .card {
                padding: 12px;
            }

            .our-courses h1 {
                font-size: 1.5rem;
                margin-top: 30px;
                margin-bottom: 25px;
            }

            .card-content h2 {
                font-size: 1rem;
            }

            .card-content p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <section class="our-courses fade-up">
        <h1>Kursus</h1>
        <div class="card-container">
            <div class="card fade-up stagger-delay-1">
                <img src="../../../assets/images/icon.svg" alt="Icon 1" class="card-icon">
                <div class="card-content">
                    <h2>Kelas Mahir SPSS</h2>
                    <p>Mengenal SPSS secara umum, baik antarmuka maupun fitur-fiturnya.</p>
                </div>
            </div>
            <div class="card fade-up stagger-delay-2">
                <img src="../../../assets/images/icon.svg" alt="Icon 2" class="card-icon">
                <div class="card-content">
                    <h2>Kelas Expert Eviews</h2>
                    <p>Menguasai antarmuka pengguna grafis (GUI) EViews untuk navigasi dan penanganan data yang efisien.</p>
                </div>
            </div>
            <div class="card fade-up stagger-delay-3">
                <img src="../../../assets/images/icon.svg" alt="Icon 3" class="card-icon">
                <div class="card-content">
                    <h2>Kelas Profesional Stata</h2>
                    <p>Pengenalan penting tentang Stata, Manipulasi data di Stata, Analisis data di Stata, Pemodelan regresi, Kode stata.</p>
                </div>
            </div>
            <div class="card fade-up stagger-delay-4">
                <img src="../../../assets/images/icon.svg" alt="Icon 4" class="card-icon">
                <div class="card-content">
                    <h2>Kelas Expert Smart PLS</h2>
                    <p>Overview dan Pemahaman Dasar Smart PLS, Konsep dasar structural equation modeling, Ana lisis variable intervening.</p>
                </div>
            </div>
            <div class="card fade-up stagger-delay-5">
                <img src="../../../assets/images/icon.svg" alt="Icon 5" class="card-icon">
                <div class="card-content">
                    <h2>Kelas Olah Data Kualitatif</h2>
                    <p>Overview Riset Kualitatif Metode Pengumpulan Data Riset Kualitatif, Content Analysis Riset Kualitatif.</p>
                </div>
            </div>
            <div class="card fade-up stagger-delay-6">
                <img src="../../../assets/images/icon.svg" alt="Icon 6" class="card-icon">
                <div class="card-content">
                    <h2>Kursus Mendatang</h2>
                    <p>Nantikan kursus mendatang dari Kelas Sore.</p>
                </div>
            </div>
        </div>
    </section>
</body>

</html>