<?php
function renderHeaderProfile() {
    ?>
    <header>
    <div class="logo">
        <a href="mentor.php">
            <img src="../../../../assets/images/logo.svg" alt="KelasSore Logo">
        </a>
    </div>
        <div class="buttons">
            <?php
            if (!isset($_SESSION['mentor_id'])) {
                echo '<a href="/views/pages/login/login.php" class="login">Log in</a>';
            } else {
                
            }
            ?>
        </div>
    </header>
    <?php
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f7f7f7;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 10%;
    background-color: #ffffff;
    border-bottom: 1px solid #ddd;
}

header .logo img {
    max-height: 50px;
}

header .buttons {
    display: flex;
    gap: 10px;
}

header .buttons a {
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
}

header .buttons .join {
    background-color: #001A45;
    color: #fff;
}

header .buttons .login {
    background-color: #fff;
    border: 2px solid #001A45;
    color: #001A45;
}

header .buttons a:hover {
    opacity: 0.8;
}

/* Hamburger menu styles */
.hamburger-menu {
    display: none;
    font-size: 30px;
    cursor: pointer;
}

/* Mobile menu styles */
nav {
    background-color: #ffffff;
    padding: 10px 10%;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
}

nav a {
    margin: 0 10px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

@media (max-width: 768px) {
    header .buttons {
        display: none;
    }

    header .hamburger-menu {
        display: block;
    }

    nav {
        display: none;
        background-color: #fff;
        padding: 20px;
        position: absolute;
        top: 60px;
        left: 0;
        width: 100%;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }

    nav.active {
        display: block;
    }

    nav a {
        display: block;
        margin: 10px 0;
        text-decoration: none;
        color: #333;
        font-weight: 500;
    }
}

/* Other existing CSS for your page */
.hero {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 50px 10%;
    flex-wrap: wrap;
}

.hero .text {
    max-width: 50%;
}

.hero .text h1 {
    font-size: 3rem;
    margin-bottom: 20px;
}

.hero .text p {
    font-size: 1.2rem;
    margin-bottom: 20px;
    color: #555;
}

.hero .images {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    align-items: center;
    width: 100%;
}

.hero .images img {
    width: 100%;
    border-radius: 10px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero {
        flex-direction: column;
        text-align: center;
    }

    .hero .text {
        max-width: 100%;
        margin-bottom: 20px;
    }

    .hero .images {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .hero .text h1 {
        font-size: 2rem;
    }

    .hero .text p {
        font-size: 0.9rem;
    }

    .hero .images {
        grid-template-columns: 1fr;
    }

    header .buttons a {
        font-size: 14px;
    }
}
</style>
