<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelas Sore</title>
    <link rel="stylesheet" href="../../../../assets/css/dashboard/dashbod.css"> 
</head>
<body>

<!-- // Include content.php -->
    <section class="your-class">
        <div class="container">
            <div class="section-header">
                <h2>Your Class</h2>
     
            </div>
            <div class="class-grid">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="class-card">
                        <img src="../../../../assets/images/kursus.svg" alt="Class Image">
                        <h3>The Complete 2023 PHP Full Stack Web Developer Bootcamp</h3>
                        <p>Sarah Lee</p>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <section class="your-class">
    <div class="container">
        <div class="section-header">
            <h2>Informasi Terkini</h2>
        </div>
        <!-- Scrollable Class Grid -->
        <div class="class-grid">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="class-card">
                    <img src="../../../../assets/images/brita.svg" alt="Class Image">
                    <h3>The Complete 2023 PHP Full Stack Web Developer Bootcamp</h3>
                    <p>Sarah Lee</p>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

</body>
</html>
